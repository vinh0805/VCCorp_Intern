<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Imports\OrdersImport;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Label;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Tmp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function showViewOrders()
    {
        $currentUser = $this->authLogin('');
        $allCompanies = [];
        $allCustomers = [];
        $allProducts = [];

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'order')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý đơn hàng!");
        }

        $role = Role::query()->find($currentUser->role_id["order"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if ($this->checkReadAllPermission($currentUser, 'order')) {
            $allOrders = Order::query()
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $allOrders = Order::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }


        // Fix data
        // Set userList's name
        $userIdList = [];
        foreach ($allOrders as $order) {
            if (is_array($order['user_id']) && count($order['user_id']) > 0) {
                foreach ($order['user_id'] as $userId) {
                    if (!in_array($userId, $userIdList)) {
                        array_push($userIdList, $userId);
                    }
                }
            }
        }
        $userList = User::query()
            ->whereIn('_id', $userIdList)
            ->get();

        $tmpArray = [];
        foreach ($allOrders as $order) {
            $order['userName'] = '';
            if (is_array($order['user_id']) && count($order['user_id']) > 0) {
                foreach ($order['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $order['userName'] = implode(', ', $tmpArray);
            $tmpArray = [];
        } // end set userList's name

        // Check isset role of companies
        if ($this->checkReadAllPermission($currentUser, 'company')) {
            $allCompanies = Company::query()->take(100)->get();
        } elseif ($this->checkReadPermission($currentUser, 'company')) {
            $allCompanies = Company::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->take(100)
                ->get();
        }
        // Check isset role of customers
        if ($this->checkReadAllPermission($currentUser, 'customer')) {
            $allCustomers = Customer::query()->take(100)->get();
        } elseif ($this->checkReadPermission($currentUser, 'customer')) {
            $allCustomers = Customer::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->take(100)
                ->get();
        }
        // Check isset role of products
        if ($this->checkReadAllPermission($currentUser, 'product')) {
            $allProducts = Product::query()->take(100)->get();
        } elseif ($this->checkReadPermission($currentUser, 'product')) {
            $allProducts = Product::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->take(100)
                ->get();
        }

        foreach ($allOrders as $index => $order) {
            if (isset($order->customer_id)) {
                $customer = Customer::query()->find($order->customer_id);
                if (!isset($customer)) {
                    continue;
                }
                $customer = $customer->attributesToArray();

                foreach ($customer as $key => $value) {
                    if (in_array($key, $this->encryptedFields)) {
                        try {
                            $customer[$key] = Crypt::decrypt($value['encrypted']);
                        } catch (Exception $e) {
                            $customer[$key] = null;
                        }
                    } else {
                        $customer[$key] = $value;
                    }
                }
                $order['customer'] = $customer;
            }

            if (isset($order->customer) && isset($order->customer->company_id)) {
                $customer_company = @Company::query()->find($order->customer->company_id)['name'];
                $order['customer']['company_name'] = @$customer_company;
            }

            if (isset($order->company_id)) {
                $company = Company::query()->find($order->company_id);
                if (!isset($company)) {
                    continue;
                }
                $company = $company->attributesToArray();

                foreach ($this->encryptedFields as $field) {
                    if (isset($company[$field])) {
                        try {
                            $company[$field] = Crypt::decrypt($company[$field]);
                        } catch (Exception $e) {
                            $company[$field] = null;
                        }
                    }
                }
                $order['company'] = $company;
            }
        }

        $allUsers = User::query()
            ->take(100)
            ->get();

        $allFieldLabels = Label::query()
            ->where('collection', 'order')
            ->first();

        return view('order.orders')
            ->with('allOrders', $allOrders)
            ->with('allCompanies', $allCompanies)
            ->with('allCustomers', $allCustomers)
            ->with('allProducts', $allProducts)
            ->with('currentUser', $currentUser)
            ->with('title', 'Quản lý Đơn hàng')
            ->with('allUsers', $allUsers)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('permissionList', $permissionList);
    }

    public function searchOrder(Request $request)
    {
        $currentUser = $this->authLogin('');
        $allCompanies = [];
        $allCustomers = [];
        $allProducts = [];

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'order')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý đơn hàng!");
        }

        $search_field = isset($request['search_field']) ? $request['search_field'] : 'id';
        $search_value = $request['search_value'];

        $role = Role::query()->find($currentUser->role_id["order"]);
        $permissionList = $role['permission_list'];
        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            if (in_array($search_field, ['price', 'tax', 'total_price'])) {
                try {
                    $search_value += 0;
                } catch (Exception $e) {}
                $allOrders = Order::query()
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } elseif ($search_field == 'customer_id') {
                $customers = Customer::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($customers) > 0) {
                    $customerIdArray = [];
                    foreach ($customers as $customer) {
                        array_push($customerIdArray, $customer['_id']);
                    }
                    $allOrders = Order::query()
                        ->whereIn($search_field, $customerIdArray)
                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } else {
                    $allOrders = Order::query()->where('customer', '-1')->paginate(10);
                }
            } elseif ($search_field == 'company_id') {
                $companies = Company::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($companies) > 0) {
                    $companyIdArray = [];
                    foreach ($companies as $company) {
                        array_push($companyIdArray, $company['_id']);
                    }
                    $allOrders = Order::query()
                        ->whereIn($search_field, $companyIdArray)
                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } else {
                    $allOrders = Order::query()
                        ->where('customer','-1')
                        ->paginate(10);

                }
            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allOrders = Order::query()
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } elseif ($search_field == null) {
                $customers = Customer::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($customers) > 0) {
                    $customerIdArray = [];
                    foreach ($customers as $customer) {
                        array_push($customerIdArray, $customer['_id']);
                    }
                } else {
                    $customerIdArray = [-1];
                }
                $companies = Company::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($companies) > 0) {
                    $companyIdArray = [];
                    foreach ($companies as $company) {
                        array_push($companyIdArray, $company['_id']);
                    }
                } else {
                    $companyIdArray = [-1];
                }
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                try {
                    $allOrders = Order::query()
                        ->where(['customer_id' => ['$in' => $customerIdArray]])
                        ->orWhere(['company_id' => ['$in' => $companyIdArray]])
                        ->orWhere('time', 'regexp', '/'. $search_value . '/')
                        ->orWhere('price', $search_value)
                        ->orWhere('tax', $search_value)
                        ->orWhere('total_price', $search_value)
                        ->orWhere('address', 'regexp', '/'. $search_value . '/')
                        ->orWhere('created_at', $time)
                        ->orWhere('updated_at', $time)
                        ->orWhere('status', $search_value)
                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } catch (Exception $e) {
                    $allOrders = Order::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }
            } else {
                $allOrders = Order::query()
                    ->where($search_field, 'like', '%' . $search_value . '%')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        } else {
            if (in_array($search_field, ['price', 'tax', 'total_price'])) {
                try {
                    $search_value += 0;
                } catch (Exception $e) {}
                $allOrders = Order::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } elseif ($search_field == 'customer_id') {
                $customers = Customer::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($customers) > 0) {
                    $customerIdArray = [];
                    foreach ($customers as $customer) {
                        array_push($customerIdArray, $customer['_id']);
                    }
                    $allOrders = Order::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->whereIn($search_field, $customerIdArray)
                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } else {
                    $allOrders = Order::query()->where('customer', '-1')->paginate(10);
                }
            } elseif ($search_field == 'company_id') {
                $companies = Company::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($companies) > 0) {
                    $companyIdArray = [];
                    foreach ($companies as $company) {
                        array_push($companyIdArray, $company['_id']);
                    }
                    $allOrders = Order::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->whereIn($search_field, $companyIdArray)
                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } else {
                    $allOrders = Order::query()->where('customer', '-1')->paginate(10);
                }
            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allOrders = Order::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } elseif ($search_field == null) {
                $customers = Customer::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($customers) > 0) {
                    $customerIdArray = [];
                    foreach ($customers as $customer) {
                        array_push($customerIdArray, $customer['_id']);
                    }
                } else {
                    $customerIdArray = [-1];
                }
                $companies = Company::query()
                    ->where('name', 'like', '%' . $search_value . '%')
                    ->get(['_id']);
                if (count($companies) > 0) {
                    $companyIdArray = [];
                    foreach ($companies as $company) {
                        array_push($companyIdArray, $company['_id']);
                    }
                } else {
                    $companyIdArray = [-1];
                }
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                try {
                    $allOrders = Order::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->where(['customer_id' => ['$in' => $customerIdArray]])
                        ->orWhere(['company_id' => ['$in' => $companyIdArray]])
                        ->orWhere('time', 'regexp', '/'. $search_value . '/')
                        ->orWhere('price',$search_value)
                        ->orWhere('tax',$search_value)
                        ->orWhere('total_price',$search_value)
                        ->orWhere('address', 'regexp', '/'. $search_value . '/')
                        ->orWhere('created_at', $time)
                        ->orWhere('updated_at', $time)
                        ->orWhere('status', $search_value)
                        ->orderBy('id', 'desc')
                        ->paginate(10);

                } catch (Exception $e) {
                    $allOrders = Order::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }
            } else {
                $allOrders = Order::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, 'like', '%' . $search_value . '%')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        }


        // Fix data
        // Set userList's name
        if ($allOrders->total() > 0) {
            $userIdList = [];
            foreach ($allOrders as $order) {
                if (is_array($order['user_id']) && count($order['user_id']) > 0) {
                    foreach ($order['user_id'] as $userId) {
                        if (!in_array($userId, $userIdList)) {
                            array_push($userIdList, $userId);
                        }
                    }
                }
            }
            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();

            $tmpArray = [];
            foreach ($allOrders as $order) {
                $order['userName'] = '';
                if (is_array($order['user_id']) && count($order['user_id']) > 0) {
                    foreach ($order['user_id'] as $userId) {
                        foreach ($userList as $user) {
                            if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                                array_push($tmpArray, $user['name']);
                            }
                        }
                    }
                }
                $order['userName'] = implode(', ', $tmpArray);
                $tmpArray = [];
            } // end set userList's name

            // Check isset role of companies
            if ($this->checkReadAllPermission($currentUser, 'company')) {
                $allCompanies = Company::query()->take(100)->get();
            } elseif ($this->checkReadPermission($currentUser, 'company')) {
                $allCompanies = Company::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take(100)
                    ->get();
            }
            // Check isset role of customers
            if ($this->checkReadAllPermission($currentUser, 'customer')) {
                $allCustomers = Customer::query()->take(100)->get();
            } elseif ($this->checkReadPermission($currentUser, 'customer')) {
                $allCustomers = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take(100)
                    ->get();
            }
            // Check isset role of products
            if ($this->checkReadAllPermission($currentUser, 'product')) {
                $allProducts = Product::query()->take(100)->get();
            } elseif ($this->checkReadPermission($currentUser, 'product')) {
                $allProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take(100)
                    ->get();
            }

            foreach ($allOrders as $index => $order) {
                if (isset($order->customer_id)) {
                    $customer = Customer::query()->find($order->customer_id);
                    if (!isset($customer)) {
                        continue;
                    }
                    $customer = $customer->attributesToArray();

                    foreach ($customer as $key => $value) {
                        if (in_array($key, $this->encryptedFields)) {
                            try {
                                $customer[$key] = Crypt::decrypt($value['encrypted']);
                            } catch (Exception $e) {}
                        } else {
                            $customer[$key] = $value;
                        }
                    }
                    $order['customer'] = $customer;
                }

                if (isset($order->customer) && isset($order->customer->company_id)) {
                    $customer_company = @Company::query()->find($order->customer->company_id)['name'];
                    $order['customer']['company_name'] = @$customer_company;

                }

                if (isset($order->company_id)) {
                    $company = Company::query()->find($order->company_id);
                    if (!isset($company)) {
                        continue;
                    }
                    $company = $company->attributesToArray();

                    foreach ($this->encryptedFields as $field) {
                        if (isset($company[$field])) {
                            try {
                                $company[$field] = Crypt::decrypt($company[$field]);
                            } catch (Exception $e) {
                                $company[$field] = null;
                            }
                        }
                    }
                    $order['company'] = $company;
                }
            }
        }


        $allUsers = User::query()
            ->take(100)
            ->get();

        $allFieldLabels = Label::query()
            ->where('collection', 'order')
            ->first();

        return view('order.orders')
            ->with('allOrders', $allOrders)
            ->with('allCompanies', $allCompanies)
            ->with('allCustomers', $allCustomers)
            ->with('allProducts', $allProducts)
            ->with('currentUser', $currentUser)
            ->with('title', 'Quản lý Đơn hàng')
            ->with('allUsers', $allUsers)
            ->with('permissionList', $permissionList)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('search_field', $search_field)
            ->with('search_value', $search_value);

    }


    public function createOrder(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'create')) {
            $data = [
                'message' => 'Bạn không có quyền tạo dữ liệu mới ở module Đơn hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $price = 0;
        $total_price = 0;
        $error = 0;
        $productList = [];

        // Check null data
        if (!$request['customer'] && !$request['company']) {
            return [
                'message' => 'Thêm đơn hàng mới thất bại! Chưa nhập trường khách hàng hoặc công ty.',
                'success' => false
            ];
        }
        if (!$request['product']) {
            return [
                'message' => 'Thêm đơn hàng mới thất bại! Chưa nhập trường sản phẩm.',
                'success' => false
            ];
        }

        // Validate input data
        if (!$this->validateTax($request['tax'])) {
            return response()->json([
                'message' => 'Trường thuế: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateDate($request['time'])) {
            return response()->json([
                'message' => 'Trường ngày đặt hàng: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }

        foreach ($request['number'] as $number) {
            if (!$this->validateNumber($number)) {
                return response()->json([
                    'message' => 'Trường số lượng: không đúng định dạng dữ liệu!',
                    'success' => false
                ]);
            }
        } // end validate data

        for ($i = 0; $i < count($request['product']); $i++) {
            $product = Product::query()->find($request['product'][$i]);

            // Continue if can't find product
            if (!isset($product)) {
                continue;
            }

            if ($request['number'][$i] >= $product['remain']) {
                $error = 1;
                break;
            }

            $tmp = [
                'product' => $product['_id'],
                'number' => $request['number'][$i]
            ];
            array_push($productList, $tmp);

            $price += $product['price'] * $request['number'][$i];
            $total_price = $price * (1 + $request['tax'] / 100);
        }

        if ($error) {
            $data = [
                'message' => 'Thêm đơn hàng mới thất bại!\nSố lượng hàng tồn kho không đủ.',
                'success' => false
            ];
            return response()->json($data);
        }

        $newOrder = new Order([
            'id' => Order::getLastOrderId() + 1,
            'customer_id' => $request['customer'],
            'company_id' => $request['company'],
            'time' => $request['time'],
            'products' => $productList,
            'price' => $price,
            'tax' => $request['tax'],
            'total_price' => $total_price,
            'address' => $request['address'],
            'user_id' => isset($request['users']) ? $request['users'] : [],
            'status' => $request['status']
        ]);

        if ($newOrder->save()) {
            // Update remain of product
            for ($i = 0; $i < count($request['product']); $i++) {
                $product = Product::query()->find($request['product'][$i]);
                $product['remain'] -= $request['number'][$i];
                $product->save();
            }

            $data = [
                'message' => 'Thêm đơn hàng mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Thêm đơn hàng mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }

    public function deleteOrder($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'delete') &&
            !$this->checkAuthorize($currentUser, 'order', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module đơn hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $order = Order::query()->find($_id);
        if (!isset($_id) || !isset($order)) {
            $data = [
                'message' => 'Xóa thông tin đơn hàng thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'order', 'delete') &&
            !$this->checkAuthorize($currentUser, 'order', 'delete_all') &&
            !in_array($currentUser->_id, $order['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đối với đơn hàng này!',
                'success' => false
            ]);
        }

        try {
            if ($order->delete()) {
                $data = [
                    'message' => 'Xóa đơn hàng thành công!',
                    'success' => true
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {

        }

        $data = [
            'message' => 'Xóa đơn hàng thất bại! Dữ liệu không tồn tại.',
            'success' => false
        ];

        return response()->json($data);
    }


    public function deleteAllOrder(Request $request)
    {
        $currentUser = $this->authLogin('');
        $error = false;
        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'order', 'delete') &&
            !$this->checkAuthorize($currentUser, 'order', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $orderList = Order::query()
            ->whereIn('_id', $request['idList'])
            ->get();

        foreach ($orderList as $order) {
            if (!isset($order)) {
                continue;
            }

            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'order', 'delete') &&
                !$this->checkAuthorize($currentUser, 'order', 'delete_all') &&
                !in_array($currentUser->_id, $order['user_id'])) {
                continue;
            }

            try {
                if (!$order->delete()) {
                    $error = true;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        if ($error) {
            return [
                'message' => 'Có lỗi trong quá trình xóa dữ liệu!',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Xóa các bản ghi đã chọn thành công!',
                'success' => true
            ];
        }
    }


    public function saveOrder(Request $request, $_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'update') &&
            !$this->checkAuthorize($currentUser, 'order', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Đơn hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $order = Order::query()->find($_id);
        if (!isset($_id) || !isset($order)) {
            $data = [
                'message' => 'Sửa thông tin đơn hàng thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        $price = 0;
        $total_price = 0;
        $error = 0;

        // Validate input data
        if (!$this->validateTax($request['tax'])) {
            return response()->json([
                'message' => 'Trường thuế: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateDate($request['time'])) {
            return response()->json([
                'message' => 'Trường ngày đặt hàng: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }

        foreach ($request['number'] as $number) {
            if (!$this->validateNumber($number)) {
                return response()->json([
                    'message' => 'Trường số lượng: không đúng định dạng dữ liệu!',
                    'success' => false
                ]);
            }
        } // end validate data

        $productList = [];
        for ($i = 0; $i < count($request['product']); $i++) {
            $product = Product::query()->find($request['product'][$i]);

            // Continue if can't find product
            if (!isset($product)) {
                continue;
            }

            if ($request['number'][$i] >= $product['remain']) {
                $error = 1;
                break;
            }

            $tmp = [
                'product' => $product['_id'],
                'number' => $request['number'][$i]
            ];
            array_push($productList, $tmp);

            $price += $product['price'] * $request['number'][$i];
            $total_price = $price * (1 + $request['tax'] / 100);
        }

        if ($error) {
            $data = [
                'message' => 'Sửa thông tin đơn hàng thất bại!\nSố lượng hàng tồn kho không đủ.',
                'success' => false
            ];
            return response()->json($data);
        }

        $order['customer_id'] = $request['customer'];
        $order['company_id'] = $request['company'];
        $order['time'] = $request['time'];
        $order['products'] = $productList;
        $order['price'] = $price;
        $order['tax'] = $request['tax'];
        $order['total_price'] = $total_price;
        $order['user_id'] = isset($request['users']) ? $request['users'] : [];
        $order['status'] = $request['status'];

        if ($order->save()) {
            $data = [
                'message' => 'Sửa thông tin đơn hàng thành công!',
                'success' => true
            ];
            return response()->json($data);
        } else {
            $data = [
                'message' => 'Sửa thông tin đơn hàng thất bại!!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function getDataToEditOrder($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'update') &&
            !$this->checkAuthorize($currentUser, 'order', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Đơn hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $editOrder = Order::query()->find($_id);

        if (isset($editOrder)) {            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'order', 'update') &&
                !$this->checkAuthorize($currentUser, 'order', 'update_all') &&
                !in_array($currentUser->_id, $editOrder['user_id'])) {
                return response()->json([
                    'message' => 'Bạn không có quyền sửa đối với đơn hàng này!',
                    'success' => false
                ]);
            }

            return response()->json($editOrder);
        } else {
            $data = [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
            return response()->json($data);
        }
    }

    public function getProductsListOrder($_id)
    {
        $currentUser = $this->authLogin('');
        $productList = [];

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'order')) {
            $data = [
                'message' => 'Bạn không có quyền đọc dữ liệu ở module Đơn hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'product')) {
            $data = [
                'message' => 'Bạn không có quyền đọc dữ liệu ở module Sản phẩm!',
                'success' => false
            ];
            return response()->json($data);
        }

        $order = Order::query()->find($_id);

        if (!isset($order) || !isset($order->products)) {
            return null;
        }

        $products = $order->products;

        for ($i = 0; $i < count($products); $i++) {
            $tmp = Product::query()->find($products[$i]['product']);

            if (!isset($tmp)) {
                continue;
            }

            $tmp['number'] = $products[$i]['number'];
            array_push($productList, $tmp);
        }

        return response()->json($productList);
    }

    public function exportOrder()
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'export') &&
            !$this->checkAuthorize($currentUser, 'order', 'export_all')) {
            return [
                'message' => 'Bạn không có quyền xuất dữ liệu ở module Đơn hàng!',
                'success' => false
            ];
        }

        $file_name = 'export_orders.xlsx';

        if (Excel::store(new OrdersExport($currentUser->_id), $file_name)) {
            return [
                'success' => true,
                'path' => url('/storage/' . $file_name)
            ];
        } else {
            return [
                'message' => 'Xuất dữ liệu thất bại!',
                'success' => false
            ];
        }
    }

    public function importOrder(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'order', 'import')) {
            return [
                'message' => 'Bạn không có quyền nhập dữ liệu đơn hàng!',
                'success' => false
            ];
        }

        $allOrders = Order::all();
        $random_key = Str::random(10);

        if ($request->file('import_file')->getClientOriginalExtension() != 'xlsx') {
            return [
                'message' => 'Sai loại file! Chỉ chấp nhận file đuôi .xlsx!',
                'success' => false
            ];
        }

        $collection = Excel::toCollection(new OrdersImport, $request->file('import_file'));

        // Check input file
        if (!$collection || count($collection) < 1 || count($collection[0]) < 1) {
            return [
                'message' => 'Dữ liệu không đúng định dạng hoặc không có dữ liệu!',
                'success' => false
            ];
        }
        $keys = $collection[0][0]->keys();
        $arrayKey = [
            'id',
            'customer',
            'company',
            'products',
            'price',
            'tax',
            'total_price',
            'time',
            'address',
            'user',
            'status',
            'created_at',
            'updated_at'
        ];

        if ($keys->intersect($arrayKey) != $keys) {
            return [
                'message' => 'Dữ liệu không đúng định dạng!',
                'success' => false
            ];
        }

        for ($i = 0; $i < count($collection[0]); $i++) {

            // Check data
            $user_id = User::query()
                ->where('name', $collection[0][$i]['user'])
                ->first()->_id;
            $company = Company::query()
                ->where('name', $collection[0][$i]['company'])
                ->first();
            $company_id = isset($company) ? $company->_id : null;
            $customer = Customer::query()
                ->where('name', $collection[0][$i]['customer'])
                ->first();
            $customer_id = isset($customer) ? $customer->_id : null;

            if ($request['field_id']) {
                foreach ($allOrders as $order) {
                    if ($order->id && $order->id == $collection[0][$i]['id']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'id';
                        break;
                    }
                }
            }

            if ($request['field_customer_id']) {
                foreach ($allOrders as $order) {
                    if ($order->customer_id && $order->customer_id == $collection[0][$i]['customer_id']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'customer_id';
                        break;
                    }
                }
            }

            if ($request['field_company_id']) {
                foreach ($allOrders as $order) {
                    if ($order->company_id && $order->company_id == $collection[0][$i]['company_id']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'company_id';
                        break;
                    }
                }
            }

            if ($request['field_products']) {
                foreach ($allOrders as $order) {
                    if ($order->products && $order->products == $collection[0][$i]['products']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'products';
                        break;
                    }
                }
            }

            if ($request['field_price']) {
                foreach ($allOrders as $order) {
                    if ($order->price && $order->price == $collection[0][$i]['price']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'price';
                        break;
                    }
                }
            }

            if ($request['field_tax']) {
                foreach ($allOrders as $order) {
                    if ($order->tax && $order->tax == $collection[0][$i]['tax']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'tax';
                        break;
                    }
                }
            }

            if ($request['field_total_price']) {
                foreach ($allOrders as $order) {
                    if ($order->total_price && $order->total_price == $collection[0][$i]['total_price']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'total_price';
                        break;
                    }
                }
            }

            if ($request['field_time']) {
                foreach ($allOrders as $order) {
                    if ($order->time && $order->time == $collection[0][$i]['time']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'time';
                        break;
                    }
                }
            }

            if ($request['field_address']) {
                foreach ($allOrders as $order) {
                    if ($order->address && $order->address == $collection[0][$i]['address']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'address';
                        break;
                    }
                }
            }

            if ($request['field_user_id']) {
                foreach ($allOrders as $order) {
                    if ($order->user_id && $order->user_id == $collection[0][$i]['user']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'user';
                        break;
                    }
                }
            }

            if ($request['field_status']) {
                foreach ($allOrders as $order) {
                    if ($order->status && $order->status == $collection[0][$i]['status']) {
                        $collection[0][$i]['duplicate'] = true;
                        $collection[0][$i]['duplicate_key'] = 'status';
                        break;
                    }
                }
            }

            // when not duplicate -> save
            $newOrder = new Tmp([
                'collection' => 'order',
                'id' => $collection[0][$i]['id'],
                'customer_id' => $customer_id,
                'company_id' => $company_id,
                'products' => $collection[0][$i]['products'],
                'price' => $collection[0][$i]['price'],
                'tax' => $collection[0][$i]['tax'],
                'total_price' => $collection[0][$i]['total_price'],
                'time' => $collection[0][$i]['time'],
                'address' => $collection[0][$i]['address'],
                'user_id' => $user_id,
                'status' => $collection[0][$i]['status'],
                'current_user' => $currentUser->_id,
                'random_key' => $random_key
            ]);

            $newOrder->save();
        }

        $allTmpOrders = Tmp::query()
            ->where('collection', 'order')
            ->where('current_user', $currentUser->_id)
            ->get();

        foreach ($allTmpOrders as $order) {
            $order->user = User::query()->find($order->user_id)['name'];

            $customer = Customer::query()->find($order->customer_id);
            $order->customer = isset($customer) ? $customer['name'] : null;

            $company = Company::query()->find($order->company_id);
            $order->company = isset($company) ? $company['name'] : null;
        }

        if (count($allTmpOrders) > 0) {
            return response()->json([$allTmpOrders, $random_key, 'order']);

        } else {
            return [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
        }
    }
}

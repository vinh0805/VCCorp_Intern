<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Imports\TmpImport;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Label;
use App\Models\Role;
use App\Models\Tmp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Imports\CustomersImport;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    private $allFields = ['name', 'birth', 'gender', 'job', 'address', 'email', 'phone', 'status', 'created_at', 'updated_at'];

    public function showViewCustomers()
    {
        $currentUser = $this->authLogin('');

        $allCompanies = [];

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'customer')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý khách hàng!");
        }

        $role = Role::query()->find($currentUser->role_id["customer"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            $allCustomers = Customer::query()
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $allCustomers = Customer::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        // Fix data
        foreach ($allCustomers as $customer) {
            if (isset($customer->company_id)) {
                $company = Company::query()->find($customer->company_id);
                // Decrypt at company
                $customer->company = @$company;
                try {
                    foreach ($this->encryptedFields as $field) {
                        $customer['company'][$field] = Crypt::decrypt($company[$field]['encrypted']);
                    }
                } catch (Exception $e) {
                }
            }
        }

        // Set userList's name
        $userIdList = [];
        foreach ($allCustomers as $customer) {
            if (is_array($customer['user_id']) && count($customer['user_id']) > 0) {
                foreach ($customer['user_id'] as $userId) {
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
        foreach ($allCustomers as $customer) {
            $customer['userName'] = '';
            if (is_array($customer['user_id']) && count($customer['user_id']) > 0) {
                foreach ($customer['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $customer['userName'] = implode(', ', $tmpArray);

            $tmpArray = [];
        } // end set userList's name
        // Decrypt at customer
        try {
            foreach ($allCustomers as $customer) {
                $customer['phone'] = isset($customer['phone']) && $customer['phone'] != '' ?
                    $customer['phone'] = Crypt::decrypt($customer['phone']['encrypted']) : null;
                $customer['email'] = isset($customer['email']) && $customer['email'] != '' ?
                    $customer['email'] = Crypt::decrypt($customer['email']['encrypted']) : null;
            }
        } catch (Exception $exception) {
        }

        // Check isset role of companies
        if ($this->checkAuthorize($currentUser, 'company', 'read_all')) {
            $allCompanies = Company::query()->take(100)->get();
        } elseif ($this->checkAuthorize($currentUser, 'company', 'read_all')) {
            $allCompanies = Company::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->take(100)
                ->get();
        }

        $allUsers = User::all();

        $allFieldLabels = Label::query()
            ->where('collection', 'customer')
            ->first();

        return view('customer.customers')
            ->with('allCustomers', $allCustomers)
            ->with('allCompanies', $allCompanies)
            ->with('currentUser', $currentUser)
            ->with('allUsers', $allUsers)
            ->with('title', 'Quản lý khách hàng')
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('permissionList', $permissionList);
    }


    public function searchCustomer(Request $request)
    {
        $currentUser = $this->authLogin('');

        $allCompanies = [];

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'customer')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý khách hàng!");
        }

        $search_field = isset($request['search_field']) ? $request['search_field'] : 'id';
        $search_value = $request['search_value'];

        $role = Role::query()->find($currentUser->role_id["customer"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            if (in_array($search_field, $this->encryptedFields)) {
                $allCustomers = Customer::query()
                    ->where($search_field, $search_value)
                    ->orWhere($search_field . '.hashed', md5($search_value))
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allCustomers = Customer::query()
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif (in_array($search_field, ['name', 'job', 'address'])) {
                $allCustomers = Customer::query()
                    ->where('$text', ['$search' => $search_value])
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == null) {
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                $allCustomers = Customer::query()
                    ->where([
                        '$or' => [
                            ['$text' => ['$search' => $search_value]],
                            ['name' => ['$regex' => $search_value, '$options' => 'i']],
                            ['birth' => ['$regex' => $search_value, '$options' => 'i']],
                            ['gender' => ['$regex' => $search_value, '$options' => 'i']],
                            ['job' => ['$regex' => $search_value, '$options' => 'i']],
                            ['address' => ['$regex' => $search_value, '$options' => 'i']],
                            ['email.hashed' => md5($search_value)],
                            ['phone.hashed' => md5($search_value)],
                            ['created_at' => $time],
                            ['updated_at' => $time],
                            ['status' => $search_value]
                        ]
                    ])
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } else {
                $allCustomers = Customer::query()
                    ->where($search_field, 'regexp', '/' . $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        } else {
            if (in_array($search_field, $this->encryptedFields)) {
                try {
                    $search_value = $search_field == 'id' ? $search_value + 0 : $search_value;
                } catch (Exception $e) {}
                $allCustomers = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orWhere($search_field . '.hashed', md5($search_value))
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allCustomers = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif (in_array($search_field, ['name', 'job', 'address'])) {
                $allCustomers = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where('$text', ['$search' => $search_value])
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == null) {
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                try {
                    $allCustomers = Customer::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->where([
                            '$or' => [
                                ['$text' => ['$search' => $search_value]],
                                ['name' => ['$regex' => $search_value, '$options' => 'i']],
                                ['birth' => ['$regex' => $search_value, '$options' => 'i']],
                                ['gender' => ['$regex' => $search_value, '$options' => 'i']],
                                ['job' => ['$regex' => $search_value, '$options' => 'i']],
                                ['address' => ['$regex' => $search_value, '$options' => 'i']],
                                ['email.hashed' => md5($search_value)],
                                ['phone.hashed' => md5($search_value)],
                                ['created_at' => $time],
                                ['updated_at' => $time],
                                ['status' => $search_value]
                            ]
                        ])
                        ->orderBy('id', 'desc')
                        ->paginate(10);

                } catch (Exception $e) {
                    $allCustomers = Customer::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }

            } else {
                $allCustomers = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, 'regexp', '/' . $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        }

        // Fix data
        foreach ($allCustomers as $customer) {
            if (isset($customer->company_id)) {
                $company = Company::query()->find($customer->company_id);
                // Decrypt at company
                $customer->company = @$company;
                try {
                    foreach ($this->encryptedFields as $field) {
                        $customer['company'][$field] = Crypt::decrypt($company[$field]['encrypted']);
                    }
                } catch (Exception $e) {
                }
            }

        }

        // Set userList's name
        $userIdList = [];
        foreach ($allCustomers as $customer) {
            if (is_array($customer['user_id']) && count($customer['user_id']) > 0) {
                foreach ($customer['user_id'] as $userId) {
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
        foreach ($allCustomers as $customer) {
            $customer['userName'] = '';
            if (is_array($customer['user_id']) && count($customer['user_id']) > 0) {
                foreach ($customer['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $customer['userName'] = implode(',', $tmpArray);
            $tmpArray = [];
        } // end set userList's name
        // Decrypt at customer
        try {
            foreach ($allCustomers as $customer) {
                $customer['phone'] = isset($customer['phone']) && $customer['phone'] != '' ?
                    $customer['phone'] = Crypt::decrypt($customer['phone']['encrypted']) : null;
                $customer['email'] = isset($customer['email']) && $customer['email'] != '' ?
                    $customer['email'] = Crypt::decrypt($customer['email']['encrypted']) : null;
            }
        } catch (Exception $exception) {
        }

        // Check isset role of companies
        if ($this->checkAuthorize($currentUser, 'company', 'read_all')) {
            $allCompanies = Company::query()->take(100)->get();
        } elseif ($this->checkAuthorize($currentUser, 'company', 'read_all')) {
            $allCompanies = Company::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->take(100)
                ->get();
        }

        $allUsers = User::all();

        $allFieldLabels = Label::query()
            ->where('collection', 'customer')
            ->first();

        return view('customer.customers')
            ->with('allCustomers', $allCustomers)
            ->with('allCompanies', $allCompanies)
            ->with('currentUser', $currentUser)
            ->with('allUsers', $allUsers)
            ->with('title', 'Quản lý khách hàng')
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('permissionList', $permissionList)
            ->with('search_field', $search_field)
            ->with('search_value', $search_value);
    }


    public function createCustomer(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'customer', 'create')) {
            $data = [
                'message' => 'Bạn không có quyền tạo dữ liệu mói ở module Khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        // Validate input data
        if (!$this->validateName($request['name'])) {
            return response()->json([
                'message' => 'Trường tên: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateDate($request['birth'])) {
            return response()->json([
                'message' => 'Trường ngày sinh: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateEmail($request['email'])) {
            return response()->json([
                'message' => 'Trường email: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validatePhone($request['phone'])) {
            return response()->json([
                'message' => 'Trường số điện thoại: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateAddress($request['address'])) {
            return response()->json([
                'message' => 'Trường địa chỉ: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateVietnameseCharacters($request['job'])) {
            return response()->json([
                'message' => 'Trường công việc: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }

        // Confirm if have duplicated record
        if (!$request['confirm']) {
            if (isset($request['email'])) {
                $duplicatedEmailCustomer = Customer::query()
                    ->where('email.hashed', md5($request['email']))
                    ->first();
            }
            if ($request['phone']) {
                $duplicatedPhoneCustomer = Customer::query()
                    ->where('phone.hashed', md5($request['phone']))
                    ->first();
            }

            if (isset($duplicatedEmailCustomer) && isset($duplicatedPhoneCustomer)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' và số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng email và số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedPhoneCustomer)) {
                return response()->json([
                    'message' => 'Số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedEmailCustomer)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' đã được sử dụng. Tiếp tục dùng email này?',
                    'confirm' => true
                ]);
            }
        }

        $newCustomer = new Customer([
            'id' => Customer::getLastCustomerId() + 1,
            'name' => @$request['name'],
            'birth' => @$request['birth'],
            'gender' => @$request['gender'],
            'job' => @$request['job'],
            'address' => @$request['address'],
            'email' => [
                'encrypted' => Crypt::encrypt(@$request['email']),
                'hashed' => md5(@$request['email'])
            ],
            'phone' => [
                'encrypted' => Crypt::encrypt(@$request['phone']),
                'hashed' => md5(@$request['phone'])
            ],
            'company_id' => @$request['company'],
            'user_id' => isset($request['users']) ? $request['users'] : [],
            'status' => @$request['status']
        ]);

        if ($newCustomer->save()) {
            $data = [
                'message' => 'Tạo khách hàng mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Tạo khách hàng mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function deleteCustomer($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'customer', 'delete') &&
            !$this->checkAuthorize($currentUser, 'customer', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $customer = Customer::query()->find($_id);
        if (!isset($_id) || !isset($customer)) {
            $data = [
                'message' => 'Xóa thông tin khách hàng thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'customer', 'delete') &&
            !$this->checkAuthorize($currentUser, 'customer', 'delete_all') &&
            !in_array($currentUser->_id, $customer['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đối với khách hàng này!',
                'success' => false
            ]);
        }

        try {
            if ($customer->delete()) {
                $data = [
                    'message' => 'Xóa khách hàng thành công!',
                    'success' => true
                ];
                return response()->json($data);
            }
        } catch (Exception $e) {

        }

        $data = [
            'message' => 'Xóa khách hàng thất bại! Dữ liệu không tồn tại.',
            'success' => false
        ];
        return response()->json($data);
    }


    public function deleteAllCustomer(Request $request)
    {
        $currentUser = $this->authLogin('');
        $error = false;
        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'customer', 'delete') &&
            !$this->checkAuthorize($currentUser, 'customer', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        $customerList = Customer::query()
            ->whereIn('_id', $request['idList'])
            ->get();

        foreach ($customerList as $customer) {
            if (!isset($customer)) {
                continue;
            }

            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'customer', 'delete') &&
                !$this->checkAuthorize($currentUser, 'customer', 'delete_all') &&
                !in_array($currentUser->_id, $customer['user_id'])) {
                continue;
            }

            try {
                if (!$customer->delete()) {
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


    public function saveCustomer(Request $request, $_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'customer', 'update') &&
            !$this->checkAuthorize($currentUser, 'customer', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset record
        $customer = Customer::query()->find($_id);
        if (!isset($_id) || !isset($customer)) {
            $data = [
                'message' => 'Sửa thông tin khách hàng thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'customer', 'update') &&
            !$this->checkAuthorize($currentUser, 'customer', 'update_all') &&
            !in_array($currentUser->_id, $customer['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền sửa đối với khách hàng này!',
                'success' => false
            ]);
        }

        // Validate input data
        if (!$this->validateName($request['name'])) {
            return response()->json([
                'message' => 'Trường tên: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateDate($request['birth'])) {
            return response()->json([
                'message' => 'Trường ngày sinh: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateEmail($request['email'])) {
            return response()->json([
                'message' => 'Trường email: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validatePhone($request['phone'])) {
            return response()->json([
                'message' => 'Trường số điện thoại: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateAddress($request['address'])) {
            return response()->json([
                'message' => 'Trường địa chỉ: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateVietnameseCharacters($request['job'])) {
            return response()->json([
                'message' => 'Trường công việc: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }

        // Confirm if have duplicated record
        if (!$request['confirm']) {
            if (isset($request['email'])) {
                $duplicatedEmailCustomer = Customer::query()
                    ->where('email.hashed', md5($request['email']))
                    ->where('_id', '!=', $customer['_id'])
                    ->first();
            }
            if ($request['phone']) {
                $duplicatedPhoneCustomer = Customer::query()
                    ->where('phone.hashed', md5($request['phone']))
                    ->where('_id', '!=', $customer['_id'])
                    ->first();
            }

            if (isset($duplicatedEmailCustomer) && isset($duplicatedPhoneCustomer)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' và số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng email và số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedPhoneCustomer)) {
                return response()->json([
                    'message' => 'Số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedEmailCustomer)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' đã được sử dụng. Tiếp tục dùng email này?',
                    'confirm' => true
                ]);
            }
        }


        $customer['name'] = $request['name'];
        $customer['birth'] = $request['birth'];
        $customer['gender'] = $request['gender'];
        $customer['job'] = $request['job'];
        $customer['address'] = $request['address'];
        $customer['email'] = [
            'encrypted' => Crypt::encrypt(@$request['email']),
            'hashed' => md5(@$request['email'])
        ];
        $customer['phone'] = [
            'encrypted' => Crypt::encrypt(@$request['phone']),
            'hashed' => md5(@$request['phone'])
        ];
        $customer['company_id'] = $request['company_id'];
        $customer['user_id'] = isset($request['users']) ? $request['users'] : [];
        $customer['status'] = $request['status'];

        if ($customer->save()) {
            $data = [
                'message' => 'Sửa thông tin khách hàng thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Sửa thông tin khách hàng thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function getDataToEditCustomer($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'customer', 'update') &&
            !$this->checkAuthorize($currentUser, 'customer', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Khách hàng!',
                'success' => false
            ];
            return response()->json($data);
        }


        $editCustomer = Customer::query()->find($_id);

        if (isset($editCustomer)) {
            if ($this->checkAuthorize($currentUser, 'customer', 'update') &&
                !$this->checkAuthorize($currentUser, 'customer', 'update_all') &&
                !in_array($currentUser->_id, $editCustomer['user_id'])) {
                return response()->json([
                    'message' => 'Bạn không có quyền sửa đối với khách hàng này!',
                    'success' => false
                ]);
            }

            $editCustomer['email'] = isset($editCustomer['email']) && $editCustomer['email'] != '' ?
                Crypt::decrypt($editCustomer['email']['encrypted']) : null;
            $editCustomer['phone'] = isset($editCustomer['phone']) && $editCustomer['phone'] != '' ?
                Crypt::decrypt($editCustomer['phone']['encrypted']) : null;

            return response()->json($editCustomer);

        } else {
            $data = [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function exportCustomer(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'customer', 'export') &&
            !$this->checkAuthorize($currentUser, 'customer', 'export_all')) {
            return [
                'message' => 'Bạn không có quyền xuất dữ liệu ở module Khách hàng!',
                'success' => false
            ];
        }

        $file_name = @$request['name'] . '.xlsx';
        $fields = @$request['fields'];

        $allExportedCustomer = null;
        $option = @$request['export_option'];
        $option_number = $request['option_number'] + 1 - 1;
        $chosenRecords = explode(',', @$request['checked_list']);

        $role = Role::query()->find($currentUser->role_id["company"]);
        if ($option == 'choose') {  // export chosen records only
            $allExportedCustomer = Customer::query()
                ->whereIn('_id', $chosenRecords)
                ->get($fields);
        } elseif ($option == 'all') {  // export all exportable records
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedCustomer = Customer::query()
                    ->get($fields);
            } else {
                $allExportedCustomer = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->get($fields);
            }
        } elseif ($option == 'input') {
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedCustomer = Customer::query()
                    ->take($option_number)
                    ->get($fields);
            } else {
                $allExportedCustomer = Customer::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take($option_number)
                    ->get($fields);
            }
        }

        // Unset _id field
        $allExportedCustomer->transform(function ($i) {
            unset($i->_id);
            return $i;
        });


        // Fix data field user_id
        if (in_array('user_id', $fields)) {
            $userIdList = [];
            foreach ($allExportedCustomer as $customer) {
                if (!is_array($customer->user_id)) {
                    continue;
                }
                foreach ($customer->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($allExportedCustomer as $customer) {
                $tmpArray = [];
                if (is_array($customer->user_id)) {
                    foreach ($customer->user_id as $user_id) {
                        if (!is_array($customer->user_id)) {
                            continue;
                        }
                        foreach ($userList as $user) {
                            if ($user_id == $user->_id && isset($user->email) &&
                                !in_array(Crypt::decrypt($user['email']['encrypted']), $tmpArray)) {
                                array_push($tmpArray, Crypt::decrypt($user['email']['encrypted']));
                            }
                        }
                    }
                    $customer->user_id = implode(', ', $tmpArray);
                }
            } // End fix user_id
        }

        // Fix data field company_id
        if (in_array('company_id', $fields)) {
            $companyIdList = [];
            foreach ($allExportedCustomer as $customer) {
                if (!in_array($customer['company_id'], $companyIdList)) {
                    array_push($companyIdList, $customer['company_id']);
                }
            }

            $companyList = Company::query()
                ->whereIn('_id', $companyIdList)
                ->get();

            foreach ($allExportedCustomer as $customer) {
                $company = $companyList->find($customer['company_id']);
                $customer['company_id'] = isset($company) ? $company['name'] : null;
            } // End fix company_id
        }

        // Fix data encrypted fields
        foreach ($allExportedCustomer as $customer) {
            foreach ($this->encryptedFields as $encryptedField) {
                if (isset($customer[$encryptedField])) {
                    try {
                        $customer[$encryptedField] = Crypt::decrypt($customer[$encryptedField]['encrypted']);
                    } catch (Exception $e) {
                    }
                }
            }
        }

        $dataArray = [];
        foreach ($allExportedCustomer as $customer) {
            array_push($dataArray, $customer);
        }

        $header = [];
        $label = Label::query()
            ->where('collection', 'customer')
            ->first();
        foreach ($fields as $field) {
            array_push($header, $label['labels'][$field]);
        }

        if (Excel::store(new ExcelExport($dataArray, $header), $file_name)) {
            return [
                'success' => true,
                'number' => count($dataArray),
                'path' => url('/storage/' . $file_name)
            ];
        } else {
            return [
                'message' => 'Xuất dữ liệu thất bại!',
                'success' => false
            ];
        }
    }


    public function importCustomer(Request $request)
    {
        $currentUser = $this->authLogin('');

        $defaultData = [
            'id' => "1",
            'name' => 'Khách hàng ABC',
            'birth' => '01/01/1999',
            'gender' => 'Nữ',
            'job' => 'Kinh doanh tự do',
            'address' => 'Ha Noi',
            'email' => 'khabc@gmail.com',
            'phone' => '099 999 9999',
            'company_id' => null,
            'user_id' => [],
            'status' => 'Đang hoạt động',
            'created_at' => '2021-06-23T07:59:51.975000Z',
            'updated_at' => '2021-06-12T05:09:50.169000Z'
        ];
        $allFields = $this->allFields;

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'customer', 'import')) {
            return [
                'message' => 'Bạn không có quyền nhập dữ liệu công ty!',
                'success' => false
            ];
        }

        $random_key = Str::random(10);

        // Check format of file
        if ($request->file('import_file')->getClientOriginalExtension() != 'xlsx') {
            return [
                'message' => 'Sai loại file! Chỉ chấp nhận file đuôi .xlsx!',
                'success' => false
            ];
        }

        if ($request['step_import'] == 2) {
            $collection = Excel::toCollection(new TmpImport, $request->file('import_file'));

            // Check input data
            if (!$collection || count($collection) < 1 || count($collection[0]) < 1) {
                return [
                    'message' => 'Dữ liệu không đúng định dạng hoặc không có dữ liệu!',
                    'success' => false
                ];
            }

            $allFieldLabels = Label::query()
                ->where('collection', 'customer')
                ->first();

            $headers = $collection[0][0];
            $dataToShow = $collection[0]->slice(1)->take(5);

            $view = view('import.review2')
                ->with('headers', $headers)
                ->with('allFields', $allFields)
                ->with('dataToShow', $dataToShow)
                ->with('allLabels', $allFieldLabels)
                ->render();
            return [$view, $request['step_import']];
        }

        if ($request['step_import'] == 3) {
            $fields_in_db = $request['fields_in_db'];
            $check_field_duplicate = $request['check_field_duplicate'] ?
                array_keys($request['check_field_duplicate']) : [];
            $check_field_require = $request['check_field_require'] ?
                array_keys($request['check_field_require']) : [];
            $fields = array_filter($fields_in_db, 'strlen');

            // Check selected fields from step 2
            if (count($fields) < 1) {
                return [
                    'message' => 'Chưa chọn trường để nhập dữ liệu!',
                    'step' => $request['step_import'],
                    'success' => false
                ];
            }
            foreach ($check_field_duplicate as $key) {
                if (!$fields_in_db[$key]) {
                    return [
                        'message' => 'Chưa chọn trường để nhập dữ liệu ở cột ' . ($key + 1) . '!',
                        'step' => $request['step_import'],
                        'success' => false
                    ];
                }
            }
            foreach ($check_field_require as $key) {
                if (!$fields_in_db[$key]) {
                    return [
                        'message' => 'Chưa chọn trường để nhập dữ liệu ở cột ' . ($key + 1) . '!',
                        'step' => $request['step_import'],
                        'success' => false
                    ];
                }
            }

            // Create new tmp data
            $collection = Excel::toCollection(new CustomersImport, $request->file('import_file'));

            // Save data in excel to Tmp
            $this->saveDataFromExcelToTmp('customer', $collection[0], $random_key, $fields_in_db, $check_field_require);

            $allTmpCustomers = Tmp::getTmpData('customer', $currentUser->_id, $random_key);

            // Check duplicate for each field in duplicate_fields
            foreach ($check_field_duplicate as $key => $duplicated_key) {
                $field = $fields_in_db[$duplicated_key];

                $tmpValueArray = [];    // All value of field, take from excel data
                for ($i = 1; $i < count($collection[0]); $i++) {
                    if (!in_array($collection[0][$i][$duplicated_key], $tmpValueArray)) {
                        array_push($tmpValueArray, $collection[0][$i][$duplicated_key]);
                    }
                }

                // $duplicatedCustomer: all duplicated records in db
                if (in_array($field, $this->encryptedFields)) {
                    $tmpValueArray = array_map(function ($value) {
                        return md5($value);
                    }, $tmpValueArray);
                    $duplicatedCustomer = Customer::query()
                        ->where([$field . '.hashed' => ['$in' => $tmpValueArray]])
                        ->get();

                    $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
                    foreach ($duplicatedCustomer as $customer) {
                        if ($customer[$field . '.hashed'] && !in_array($customer[$field . '.hashed'], $tmpDuplicatedValueArray)) {
                            array_push($tmpDuplicatedValueArray, $customer[$field . '.hashed']);
                        }
                    }

                    // Set value for field 'duplicated_fields'
                    foreach ($allTmpCustomers as $customer) {
                        try {
                            if (count($customer['wrong_format']) > 0 || count($customer['required_fields']) > 0) {
                                continue;
                            }
                        } catch (Exception $e) {
                        }
                        if (in_array(md5($customer[$field]), $tmpDuplicatedValueArray)) {
                            $tmpArray = $customer['duplicated_fields'];
                            array_push($tmpArray, $field);
                            $customer['duplicated_fields'] = $tmpArray;
                        }
                    }

                } else {
                    $duplicatedCustomer = Customer::query()
                        ->whereIn($field, $tmpValueArray)
                        ->get();

                    $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
                    foreach ($duplicatedCustomer as $customer) {
                        if ($customer[$field] && !in_array($customer[$field], $tmpDuplicatedValueArray)) {
                            array_push($tmpDuplicatedValueArray, $customer[$field]);
                        }
                    }

                    // Set value for field 'duplicated_fields'
                    foreach ($allTmpCustomers as $customer) {
                        try {
                            if (count($customer['wrong_format']) > 0 || count($customer['required_fields']) > 0) {
                                continue;
                            }
                        } catch (Exception $e) {
                        }
                        if (in_array($customer[$field], $tmpDuplicatedValueArray)) {
                            $tmpArray = $customer['duplicated_fields'];
                            array_push($tmpArray, $field);
                            $customer['duplicated_fields'] = $tmpArray;
                        }
                    }
                }
            }

            // Set data for field 'duplicated_record'
            foreach ($allTmpCustomers as $customer) {
                if (count($customer['duplicated_fields']) <= 0) {
                    continue;
                }

                $field = $customer['duplicated_fields'][0];

                if (in_array($field, $this->encryptedFields)) {
                    // Get duplicated record + edit encrypted fields
                    $tmpVar = null;
                    $tmpVar = Customer::query()
                        ->where($field . '.hashed', md5($customer[$field]))
                        ->first();
                } else {
                    $tmpVar = Customer::query()
                        ->where($field, $customer[$field])
                        ->first();
                }

                try {
                    foreach ($this->encryptedFields as $encryptedField) {
                        $tmpVar[$encryptedField] = isset($tmpVar[$encryptedField]) ?
                            Crypt::decrypt($tmpVar[$encryptedField]['encrypted']) : null;
                    }
                } catch (Exception $e) {
                }

                $customer['duplicated_record'] = $tmpVar->attributesToArray();
            }


            foreach ($allTmpCustomers as $customer) {
                $customer->save();
            }

            if (count($allTmpCustomers) < 1) {
                return [
                    'message' => 'Không có dữ liệu hợp lệ!',
                    'step' => $request['step_import'],
                    'success' => false
                ];
            }

            $allTmpCustomers2 = Tmp::query()
                ->where('collection', 'customer')
                ->where('current_user', $currentUser->_id)
                ->where('random_key', $random_key)
                ->paginate(10);

            $view = view('import.review3')
                ->with('allTmpData', $allTmpCustomers2)
                ->with('fields_in_db', $fields_in_db)
                ->with('defaultData', $defaultData)
                ->render();

            return [$view, $request['step_import'], 'customer', $random_key, $fields_in_db];

        } elseif ($request['step_import'] == 4) {
            $fields_in_db = $request['fields_in_db'];
            $random_key = $request['import_form_random_key'];
            $records = $request['records'];
            $error = false;
            $saved = false;
            $default_fields = $request['default_fields'] ? explode(',', $request['default_fields']) : [];
            $old_fields = $request['old_fields'] ? explode(',', $request['old_fields']) : [];
            $new_fields = $request['new_fields'] ? explode('.', $request['new_fields']) : [];
            $fields = array_filter($fields_in_db, 'strlen');
            $wrongArray = [];
            $duplicatedArray = [];
            $newArray = [];
            $allTmp = Tmp::query()
                ->where('collection', 'customer')
                ->where('current_user', $currentUser->_id)
                ->where('random_key', $random_key)
                ->get();

            // Loop with data
            foreach ($allTmp as $tmp) {

                // Check if wrong format => skip
                if (count($tmp['wrong_format']) > 0) {
                    array_push($wrongArray, $tmp['excel_row']);
                    continue;
                }

                // Check require
                if (count($tmp['required_fields']) > 0) {
                    array_push($wrongArray, $tmp['excel_row']);
                    continue;
                }

                // Check create new record or update old record
                if (count($tmp['duplicated_fields']) == 0) {
                    // Get data from tmp
                    $newCustomerData = [];
                    foreach ($allFields as $field) {
                        $newCustomerData[$field] = null;
                    }
                    foreach ($fields_in_db as $key => $field_db) {
                        // Check null field
                        if (!isset($field_db) || $field_db == '') {
                            continue;
                        }

                        $newCustomerData[$field_db] = in_array($field_db, $this->encryptedFields) ?
                            ['encrypted' => Crypt::encrypt($tmp[$field_db]), 'hashed' => md5($tmp[$field_db])] :
                            $tmp[$field_db];
                    }

                    // Check for field 'id'
                    if (!in_array('id', $fields_in_db)) {
                        $newCustomerData['id'] = Customer::getLastCustomerId() + 1;
                    }

                    // Create new record
                    array_push($newArray, $tmp['excel_row']);
                    $newCustomerData['user_id'] = [$currentUser->_id];
                    $newCustomer = new Customer($newCustomerData);
                    if (!$newCustomer->save()) {
                        $error = true;
                        break;
                    } else {
                        $saved = true;
                    }
                    continue;
                }

                // Update duplicated record
                array_push($duplicatedArray, $tmp['excel_row']);
                // Have duplicate
                // Get duplicated record
                $duplicatedCustomer = Customer::query()
                    ->find($tmp['duplicated_record']['_id']);

                foreach ($fields as $field) {
                    // Check each record
                    if ($tmp['_id'] && isset($records[$tmp['_id']]) && $records[$tmp['_id']][$field]) {
                        switch ($records[$tmp['_id']][$field]) {
                            case 'new':
                                $duplicatedCustomer[$field] = in_array($field, $this->encryptedFields) ?
                                    ['encrypted' => Crypt::encrypt($tmp[$field]), 'hashed' => md5($tmp[$field])] :
                                    $tmp[$field];
                                break;
                            case 'default':
                                $duplicatedCustomer[$field] = in_array($field, $this->encryptedFields) ?
                                    ['encrypted' => Crypt::encrypt($defaultData[$field]), 'hashed' => md5($defaultData[$field])] :
                                    $defaultData[$field];
                                break;
                            case 'old': // Do nothing
                            default:
                                break;
                        }
                    } else {
                        // Check with all records
                        if (is_array($new_fields) && in_array($field, $new_fields)) {
                            $duplicatedCustomer[$field] = in_array($field, $this->encryptedFields) ?
                                ['encrypted' => Crypt::encrypt($tmp[$field]), 'hashed' => md5($tmp[$field])] :
                                $tmp[$field];
                        } elseif (is_array($default_fields) && in_array($field, $default_fields)) {
                            $duplicatedCustomer[$field] = in_array($field, $this->encryptedFields) ?
                                ['encrypted' => Crypt::encrypt($defaultData[$field]), 'hashed' => md5($defaultData[$field])] :
                                $defaultData[$field];
                        } elseif (is_array($old_fields) && in_array($field, $old_fields)) {
                            // Do nothing
                            continue;
                        }
                    }
                }

                // Save data
                if (!$duplicatedCustomer->save()) {
                    $error = true;
                    break;
                } else {
                    $saved = true;
                }
            }

            if ($error) {
                return [
                    'message' => 'Có lỗi khi lưu dữ liệu vào db!',
                    'step' => $request['step_import'],
                    'success' => false
                ];
            } elseif (!$saved) {
                return [
                    'message' => 'Lưu dữ liệu thất bại! Không có bản ghi hợp lệ.',
                    'step' => $request['step_import'],
                    'success' => false
                ];

            }

            $wrongArray2 = [];
            $duplicatedArray2 = [];
            $newArray2 = [];
            $collection = Excel::toCollection(new CustomersImport, $request->file('import_file'));

            // Get wrong, duplicate and new array
            foreach ($wrongArray as $item) {
                array_push($wrongArray2, $collection[0][$item - 1]);
            }
            foreach ($duplicatedArray as $item) {
                array_push($duplicatedArray2, $collection[0][$item - 1]);
            }
            foreach ($newArray as $item) {
                array_push($newArray2, $collection[0][$item - 1]);
            }

            $header = [];
            foreach ($collection[0][0] as $value) {
                array_push($header, $value);
            }

            // Save data to file excel again
            $wrongPath = $duplicatedPath = $newPath = '';
            if (count($wrongArray2) > 0) {
                if (Excel::store(new ExcelExport($wrongArray2, $header), 'import_wrong_records.xlsx')) {
                    $wrongPath = url('/storage/' . 'import_wrong_records.xlsx');
                } else {
                    return [
                        'message' => 'Lưu dữ liệu bản ghi lỗi thất bại!',
                        'success' => false
                    ];
                }
            }
            if (count($duplicatedArray2) > 0) {
                if (Excel::store(new ExcelExport($duplicatedArray2, $header), 'import_duplicated_records.xlsx')) {
                    $duplicatedPath = url('/storage/' . 'import_duplicated_records.xlsx');
                } else {
                    return [
                        'message' => 'Lưu dữ liệu bản ghi trùng thất bại!',
                        'success' => false
                    ];
                }
            }
            if (count($newArray2) > 0) {
                if (Excel::store(new ExcelExport($newArray2, $header), 'import_new_records.xlsx')) {
                    $newPath = url('/storage/' . 'import_new_records.xlsx');
                } else {
                    return [
                        'message' => 'Lưu dữ liệu bản ghi mới thất bại!',
                        'success' => false
                    ];
                }
            }

            $view = view('import.review4')
                ->with('countWrongRecords', count($wrongArray))
                ->with('countDuplicatedRecords', count($duplicatedArray))
                ->with('countNewRecords', count($newArray))
                ->with('wrongPath', $wrongPath)
                ->with('duplicatedPath', $duplicatedPath)
                ->with('newPath', $newPath)
                ->render();

            return [$view, $request['step_import']];
        }

        $allTmpCustomers = Tmp::query()
            ->where('collection', 'customer')
            ->where('current_user', $currentUser->_id)
            ->where('random_key', $random_key)
            ->get();

        foreach ($allTmpCustomers as $customer) {
            $customer->user = User::query()->find($customer->user_id)['name'];
        }

        if (count($allTmpCustomers) > 0) {
            return response()->json([$allTmpCustomers, $random_key, 'customer']);

        } else {
            return [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
        }
    }
}

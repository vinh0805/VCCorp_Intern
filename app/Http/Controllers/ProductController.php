<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Imports\ProductsImport;
use App\Imports\TmpImport;
use App\Models\Label;
use App\Models\Product;
use App\Models\Role;
use App\Models\Tmp;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function showViewProducts()
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'product')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý Sản phẩm!");
        }

        $role = Role::query()->find($currentUser->role_id["product"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            $allProducts = Product::query()
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $allProducts = Product::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        // Fix data
        // Set userList's name
        $userIdList = [];
        foreach ($allProducts as $product) {
            if (is_array($product['user_id']) && count($product['user_id']) > 0) {
                foreach ($product['user_id'] as $userId) {
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
        foreach ($allProducts as $product) {
            $product['userName'] = '';
            if (is_array($product['user_id']) && count($product['user_id']) > 0) {
                foreach ($product['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $product['userName'] = implode(', ', $tmpArray);
            $tmpArray = [];
        } // end set userList's name

        $allUsers = User::query()->take(1000)->get();

        $allFieldLabels = Label::query()
            ->where('collection', 'product')
            ->first();


        return view('product.products')
            ->with('allProducts', $allProducts)
            ->with('currentUser', $currentUser)
            ->with('title', 'Quản lý Sản phẩm')
            ->with('allUsers', $allUsers)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('permissionList', $permissionList);
    }


    public function searchProduct(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'product')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý Sản phẩm!");
        }

        $search_field = isset($request['search_field']) ? $request['search_field'] : 'id';
        $search_value = $request['search_value'];

        $role = Role::query()->find($currentUser->role_id["product"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            if (in_array($search_field, ['id', 'remain'])) {
                try {
                    $search_value += 0;
                } catch (Exception $e) {}
                $allProducts = Product::query()
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allProducts = Product::query()
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == null) {
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                try {
                    $allProducts = Product::query()
                        ->where([
                            '$or' => [
                                ['$text' => ['$search' => $search_value]],
                                ['name' => ['$regex' => $search_value, '$options' => 'i']],
                                ['code' => ['$regex' => $search_value, '$options' => 'i']],
                                ['price' => $search_value],
                                ['remain' => $search_value],
                                ['created_at' => $time],
                                ['updated_at' => $time],
                                ['status' => $search_value]
                            ]
                        ])
                        ->orderBy('id', 'desc')
                        ->paginate(10);

                } catch (Exception $e) {
                    $allProducts = Product::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }

            } else {
                $allProducts = Product::query()
                    ->where($search_field, 'regexp', '/'. $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        } else {
            if (in_array($search_field, ['id', 'remain'])) {
                try {
                    $search_value += 0;
                } catch (Exception $e) {}
                $allProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == null) {
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                try {
                    $allProducts = Product::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->where([
                            '$or' => [
                                ['$text' => ['$search' => $search_value]],
                                ['name' => ['$regex' => $search_value, '$options' => 'i']],
                                ['code' => ['$regex' => $search_value, '$options' => 'i']],
                                ['price' => $search_value],
                                ['remain' => $search_value],
                                ['created_at' => $time],
                                ['updated_at' => $time],
                                ['status' => $search_value]
                            ]
                        ])
                        ->orderBy('id', 'desc')
                        ->paginate(10);

                } catch (Exception $e) {
                    $allProducts = Product::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }

            } else {
                $allProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, 'regexp', '/'. $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        }

        // Fix data
        // Set userList's name
        $userIdList = [];
        foreach ($allProducts as $product) {
            if (is_array($product['user_id']) && count($product['user_id']) > 0) {
                foreach ($product['user_id'] as $userId) {
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
        foreach ($allProducts as $product) {
            $product['userName'] = '';
            if (is_array($product['user_id']) && count($product['user_id']) > 0) {
                foreach ($product['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $product['userName'] = implode(', ', $tmpArray);
            $tmpArray = [];
        } // end set userList's name

        $allUsers = User::query()->take(1000)->get();

        $allFieldLabels = Label::query()
            ->where('collection', 'product')
            ->first();

        return view('product.products')
            ->with('allProducts', $allProducts)
            ->with('currentUser', $currentUser)
            ->with('title', 'Quản lý Sản phẩm')
            ->with('allUsers', $allUsers)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('permissionList', $permissionList)
            ->with('search_field', $search_field)
            ->with('search_value', $search_value);
    }


    public function createProduct(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'product', 'create')) {
            $data = [
                'message' => 'Bạn không có quyền tạo dữ liệu mới ở module Sản phẩm!',
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
        if (!$this->validatePrice($request['price'])) {
            return response()->json([
                'message' => 'Trường giá: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateNumber($request['remain'])) {
            return response()->json([
                'message' => 'Trường còn lại: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateImgFile($request->file('image'))) {
            return response()->json([
                'message' => 'Trường hình ảnh: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        } // end validate data

        $newProduct = new Product([
            'id' => Product::getLastProductId() + 1,
            'name' => @$request['name'],
            'code' => @$request['code'],
            'price' => @$request['price'],
            'image' => null,
            'remain' => @$request['remain'],
            'user_id' => isset($request['users']) ? $request['users'] : [],
            'status' => @$request['status']
        ]);

        $image = $request->file('image');
        if ($image) {
            $imageName = $image->getClientOriginalName();
            $request['image']->storeAs('images', $imageName, 'public');
            $newProduct['image'] = $imageName;
        }

        if ($newProduct->save()) {
            $data = [
                'message' => 'Thêm sản phẩm mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Thêm sản phẩm mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function deleteProduct($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'product', 'delete') &&
            !$this->checkAuthorize($currentUser, 'product', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module sản phẩm!',
                'success' => false
            ];
            return response()->json($data);
        }

        $product = Product::query()->find($_id);

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'product', 'delete') &&
            !$this->checkAuthorize($currentUser, 'product', 'delete_all') &&
            !in_array($currentUser->_id, $product['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đối với sản phẩm này!',
                'success' => false
            ]);
        }

        try {
            if (isset($product) && $product->delete()) {
                $data = [
                    'message' => 'Xóa sản phẩm thành công!',
                    'success' => true
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {
        }

        $data = [
            'message' => 'Xóa sản phẩm thất bại! Dữ liệu không tồn tại.',
            'success' => false
        ];

        return response()->json($data);
    }


    public function deleteAllProduct(Request $request)
    {
        $currentUser = $this->authLogin('');
        $error = false;
        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'product', 'delete') &&
            !$this->checkAuthorize($currentUser, 'product', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module sản phẩm!',
                'success' => false
            ];
            return response()->json($data);
        }

        $productList = Product::query()
            ->whereIn('_id', $request['idList'])
            ->get();

        foreach ($productList as $product) {
            if (!isset($product)) {
                continue;
            }

            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'product', 'delete') &&
                !$this->checkAuthorize($currentUser, 'product', 'delete_all') &&
                !in_array($currentUser->_id, $product['user_id'])) {
                continue;
            }

            try {
                if (!$product->delete()) {
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


    public function saveProduct(Request $request, $_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'product', 'update') &&
            !$this->checkAuthorize($currentUser, 'product', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Sản phẩm!',
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
        if (!$this->validatePrice($request['price'])) {
            return response()->json([
                'message' => 'Trường giá: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateNumber($request['remain'])) {
            return response()->json([
                'message' => 'Trường còn lại: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }
        if (!$this->validateImgFile($request->file('image'))) {
            return response()->json([
                'message' => 'Trường hình ảnh: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        } // end validate data

        $product = Product::query()->find($_id);
        if (!isset($_id) || !isset($product)) {
            $data = [
                'message' => 'Sửa thông tin sản phẩm thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        $product['name'] = $request['name'];
        $product['code'] = $request['code'];
        $product['address'] = $request['address'];
        $product['price'] = $request['price'];
        $product['remain'] = $request['remain'];
        $product['user_id'] = isset($request['users']) ? $request['users'] : [];
        $product['status'] = $request['status'];
        $image = $request->file('image');
        if ($image) {
            $imageName = $image->getClientOriginalName();
            $request['image']->storeAs('images', $imageName, 'public');
            $product['image'] = $imageName;
        }

        if ($product->save()) {
            $data = [
                'message' => 'Sửa thông tin sản phẩm thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Sửa thông tin sản phẩm thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function getDataToEditProduct($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'product', 'update') &&
            !$this->checkAuthorize($currentUser, 'product', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Công ty!',
                'success' => false
            ];
            return response()->json($data);
        }

        $editProduct = Product::query()->find($_id);

        if (isset($editProduct)) {
            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'product', 'update') &&
                !$this->checkAuthorize($currentUser, 'product', 'update_all') &&
                !in_array($currentUser->_id, $editProduct['user_id'])) {
                return response()->json([
                    'message' => 'Bạn không có quyền sửa đối với sản phẩm này!',
                    'success' => false
                ]);
            }
            return response()->json($editProduct);
        } else {
            return null;
        }
    }


    public function exportProduct(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'product', 'export') &&
            !$this->checkAuthorize($currentUser, 'product', 'export_all')) {
            return [
                'message' => 'Bạn không có quyền xuất dữ liệu ở module Sản phẩm!',
                'success' => false
            ];
        }

        $file_name = @$request['name'] . '.xlsx';
        $fields = @$request['fields'];

        $allExportedProducts = null;
        $option = @$request['export_option'];
        $option_number = $request['option_number'] + 1 - 1;
        $chosenRecords = explode(',', @$request['checked_list']);

        $role = Role::query()->find($currentUser->role_id["product"]);
        if ($option == 'choose') {  // export chosen records only
            $allExportedProducts = Product::query()
                ->whereIn('_id', $chosenRecords)
                ->get($fields);
        } elseif ($option == 'all') {  // export all exportable records
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedProducts = Product::query()
                    ->get($fields);
            } else {
                $allExportedProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->get($fields);
            }
        } elseif ($option == 'input') {
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedProducts = Product::query()
                    ->take($option_number)
                    ->get($fields);
            } else {
                $allExportedProducts = Product::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take($option_number)
                    ->get($fields);
            }
        }

        // Unset _id field
        $allExportedProducts->transform(function ($i) {
            unset($i->_id);
            return $i;
        });


        // Fix data field user_id
        if (in_array('user_id', $fields)) {
            $userIdList = [];
            foreach ($allExportedProducts as $product) {
                if (!is_array($product->user_id)) {
                    continue;
                }
                foreach ($product->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($allExportedProducts as $product) {
                $tmpArray = [];
                foreach ($product->user_id as $user_id) {
                    if (!is_array($product->user_id)) {
                        continue;
                    }
                    foreach ($userList as $user) {
                        if ($user_id == $user['_id'] && isset($user['email']) &&
                            !in_array(Crypt::decrypt($user['email']['encrypted']), $tmpArray)) {
                            array_push($tmpArray, Crypt::decrypt($user['email']['encrypted']));
                        }
                    }
                }
                $product->user_id = implode(', ', $tmpArray);
            } // End fix user_id
        }

        $dataArray = [];
        foreach ($allExportedProducts as $product) {
            array_push($dataArray, $product);
        }
        $header = [];
        $label = Label::query()
            ->where('collection', 'product')
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


    public function importProduct(Request $request)
    {
        $currentUser = $this->authLogin('');

        $defaultData = [
            'id' => 1,
            'name' => 'Sản phẩm A',
            'code' => 'SPA',
            'price' => 1000000,
            'image' => null,
            'remain' => 1000,
            'user_id' => [],
            'status' => 'Đang hoạt động',
            'created_at' => '2021-06-23T07:59:51.975000Z',
            'updated_at' => '2021-06-12T05:09:50.169000Z'
        ];
        $allFields = ['name', 'code', 'price', 'remain', 'status', 'created_at', 'updated_at'];

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'customer', 'import')) {
            return [
                'message' => 'Bạn không có quyền nhập dữ liệu sản phẩm!',
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

        if ($request['step_import'] == 2) { // Step 2: Read excel file -> get fields's name
            $collection = Excel::toCollection(new TmpImport, $request->file('import_file'));

            // Check input data
            if (!$collection || count($collection) < 1 || count($collection[0]) < 1) {
                return [
                    'message' => 'Dữ liệu không đúng định dạng hoặc không có dữ liệu!',
                    'success' => false
                ];
            }

            $allFieldLabels = Label::query()
                ->where('collection', 'product')
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

        if ($request['step_import'] == 3) { // Step 3: Get check duplicated + required field lists
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
            $collection = Excel::toCollection(new ProductsImport, $request->file('import_file'));

            // Save data in excel to Tmp
            $this->saveDataFromExcelToTmp('product', $collection[0], $random_key, $fields_in_db, $check_field_require);

            $allTmpProducts = Tmp::getTmpData('product', $currentUser->_id, $random_key);

            // Check duplicate for each field in duplicate_fields
            foreach ($check_field_duplicate as $key => $duplicated_key) {
                $field = $fields_in_db[$duplicated_key];

                $tmpValueArray = [];    // All value of field, take from excel data
                for ($i = 1; $i < count($collection[0]); $i++) {
                    if (!in_array($collection[0][$i][$duplicated_key], $tmpValueArray)) {
                        array_push($tmpValueArray, $collection[0][$i][$duplicated_key]);
                    }
                }

                // $duplicatedProducts: all duplicated records in db

                    $duplicatedProducts = Product::query()
                        ->whereIn($field, $tmpValueArray)
                        ->get();

                    $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
                    foreach ($duplicatedProducts as $product) {
                        if ($product[$field] && !in_array($product[$field], $tmpDuplicatedValueArray)) {
                            array_push($tmpDuplicatedValueArray, $product[$field]);
                        }
                    }

                    // Set value for field 'duplicated_fields'
                    foreach ($allTmpProducts as $product) {
                        if (in_array($product[$field], $tmpDuplicatedValueArray)) {
                            $tmpArray = $product['duplicated_fields'];
                            array_push($tmpArray, $field);
                            $product['duplicated_fields'] = $tmpArray;
                        }
                    }

            }

            // Set data for field 'duplicated_record'
            foreach ($allTmpProducts as $product) {
                if (count($product['duplicated_fields']) <= 0) {
                    continue;
                }

                $field = $product['duplicated_fields'][0];


                    $tmpVar = Product::query()
                        ->where($field, $product[$field])
                        ->first();


                $product['duplicated_record'] = $tmpVar->attributesToArray();
            }


            foreach ($allTmpProducts as $product) {
                $product->save();
            }

            if (count($allTmpProducts) < 1) {
                return [
                    'message' => 'Không có dữ liệu hợp lệ!',
                    'step' => $request['step_import'],
                    'success' => false
                ];
            }

            $allTmpProducts2 = Tmp::query()
                ->where('collection', 'product')
                ->where('current_user', $currentUser->_id)
                ->where('random_key', $random_key)
                ->paginate(10);

            $view = view('import.review3')
                ->with('allTmpData', $allTmpProducts2)
                ->with('fields_in_db', $fields_in_db)
                ->with('defaultData', $defaultData)
                ->render();

            return [$view, $request['step_import'], 'product', $random_key, $fields_in_db];

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
                ->where('collection', 'product')
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
                    $newProductData = [];
                    foreach ($allFields as $field) {
                        $newProductData[$field] = null;
                    }
                    foreach ($fields_in_db as $key => $field_db) {
                        // Check null field
                        if (!isset($field_db) || $field_db == '') {
                            continue;
                        }

                        $newProductData[$field_db] = $tmp[$field_db];
                    }

                    // Check for field 'id'
                    if (!in_array('id', $fields_in_db)) {
                        $newProductData['id'] = Product::getLastProductId() + 1;
                    }

                    // Create new record
                    array_push($newArray, $tmp['excel_row']);
                    $newProductData['user_id'] = [$currentUser->_id];
                    $newProduct = new Product($newProductData);
                    if (!$newProduct->save()) {
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
                $duplicatedProduct = Product::query()
                    ->find($tmp['duplicated_record']['_id']);

                foreach ($fields as $field) {
                    // Check each record
                    if ($tmp['_id'] && isset($records[$tmp['_id']]) && $records[$tmp['_id']][$field]) {
                        switch ($records[$tmp['_id']][$field]) {
                            case 'new':
                                $duplicatedProduct[$field] = $tmp[$field];
                                break;
                            case 'default':
                                $duplicatedProduct[$field] = $defaultData[$field];
                                break;
                            case 'old': // Do nothing
                            default: break;
                        }
                    } else {
                        // Check with all records
                        if (is_array($new_fields) && in_array($field, $new_fields)) {
                            $duplicatedProduct[$field] = $tmp[$field];
                        } elseif (is_array($default_fields) && in_array($field, $default_fields)) {
                            $duplicatedProduct[$field] = $defaultData[$field];
                        } elseif (is_array($old_fields) && in_array($field, $old_fields)) {
                            // Do nothing
                            continue;
                        }
                    }
                }

                // Save data
                if (!$duplicatedProduct->save()) {
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
            } else {
                $wrongArray2 = [];
                $duplicatedArray2 = [];
                $newArray2 = [];
                $collection = Excel::toCollection(new ProductsImport, $request->file('import_file'));

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
        }

        $allTmpProducts = Tmp::query()
            ->where('collection', 'product')
            ->where('current_user', $currentUser->_id)
            ->where('random_key', $random_key)
            ->get();

        foreach ($allTmpProducts as $product) {
            $product->user = User::query()->find($product->user_id)['name'];
        }

        if (count($allTmpProducts) > 0) {
            return response()->json([$allTmpProducts, $random_key, 'product']);

        } else {
            return [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
        }
    }



//    public function importProduct2(Request $request)
//    {
//        $currentUser = $this->authLogin('');
//
//        $defaultData = [
//            'id' => 1,
//            'name' => 'Sản phẩm A',
//            'code' => 'SPA',
//            'price' => 1000000,
//            'image' => null,
//            'remain' => 1000,
//            'user_id' => [],
//            'status' => 'Đang hoạt động',
//            'created_at' => '2021-06-23T07:59:51.975000Z',
//            'updated_at' => '2021-06-12T05:09:50.169000Z'
//        ];
//        $allFields = ['name', 'code', 'price', 'remain', 'status', 'created_at', 'updated_at'];
//
//        // Check isset role
//        if (!$this->checkAuthorize($currentUser, 'product', 'import')) {
//            return [
//                'message' => 'Bạn không có quyền nhập dữ liệu công ty!',
//                'success' => false
//            ];
//        }
//
//        $random_key = Str::random(10);
//
//        // Check format of file
//        if ($request->file('import_file')->getClientOriginalExtension() != 'xlsx') {
//            return [
//                'message' => 'Sai loại file! Chỉ chấp nhận file đuôi .xlsx!',
//                'success' => false
//            ];
//        }
//
//        if ($request['step_import'] == 2) {
//            $collection = Excel::toCollection(new TmpImport, $request->file('import_file'));
//
//            // Check input data
//            if (!$collection || count($collection) < 1 || count($collection[0]) < 1) {
//                return [
//                    'message' => 'Dữ liệu không đúng định dạng hoặc không có dữ liệu!',
//                    'success' => false
//                ];
//            }
//
//            $allFieldLabels = Label::query()
//                ->where('collection', 'product')
//                ->first();
//
//            $headers = $collection[0][0];
//            $dataToShow = $collection[0]->slice(1)->take(5);
//
//            $view = view('import.review2')
//                ->with('headers', $headers)
//                ->with('allFields', $allFields)
//                ->with('dataToShow', $dataToShow)
//                ->with('allLabels', $allFieldLabels)
//                ->render();
//            return [$view, $request['step_import']];
//        }
//
//        if ($request['step_import'] == 3) {
//            $fields_in_db = $request['fields_in_db'];
//            $check_field_duplicate = $request['check_field_duplicate'] ?
//                array_keys($request['check_field_duplicate']) : [];
//            $check_field_require = $request['check_field_require'] ?
//                array_keys($request['check_field_require']) : [];
//            $fields = array_filter($fields_in_db, 'strlen');
//
//            // Check selected fields from step 2
//            if (count($fields) < 1) {
//                return [
//                    'message' => 'Chưa chọn trường để nhập dữ liệu!',
//                    'step' => $request['step_import'],
//                    'success' => false
//                ];
//            }
//
//            foreach ($check_field_duplicate as $key) {
//                if (!$fields_in_db[$key]) {
//                    return [
//                        'message' => 'Chưa chọn trường để nhập dữ liệu ở cột ' . ($key + 1) . '!',
//                        'step' => $request['step_import'],
//                        'success' => false
//                    ];
//                }
//            }
//            foreach ($check_field_require as $key) {
//                if (!$fields_in_db[$key]) {
//                    return [
//                        'message' => 'Chưa chọn trường để nhập dữ liệu ở cột ' . ($key + 1) . '!',
//                        'step' => $request['step_import'],
//                        'success' => false
//                    ];
//                }
//            }
//
//            // Create new tmp data
//            $collection = Excel::toCollection(new ProductsImport, $request->file('import_file'));
//
//            // Save data in excel to Tmp
//            $this->saveDataFromExcelToTmp('product', $collection[0], $random_key, $fields_in_db, $check_field_require);
//
//            $allTmpProducts = Tmp::getTmpData('product', $currentUser->_id, $random_key);
//
//            // Check duplicate for each field in duplicate_fields
//            foreach ($check_field_duplicate as $key => $duplicated_key) {
//                $field = $fields_in_db[$duplicated_key];
//
//                $tmpValueArray = [];    // All value of field, take from excel data
//                for ($i = 1; $i < count($collection[0]); $i++) {
//                    if (!in_array($collection[0][$i][$duplicated_key], $tmpValueArray)) {
//                        array_push($tmpValueArray, $collection[0][$i][$duplicated_key]);
//                    }
//                }
//
//                $duplicatedProducts = Product::query()
//                    ->whereIn($field, $tmpValueArray)
//                    ->get();
//
//                $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
//                foreach ($duplicatedProducts as $product) {
//                    if ($product[$field] && !in_array($product[$field], $tmpDuplicatedValueArray)) {
//                        array_push($tmpDuplicatedValueArray, $product[$field]);
//                    }
//                }
//
//                // Set value for field 'duplicated_fields'
//                foreach ($allTmpProducts as $product) {
//                    try {
//                        if (count($product['wrong_format']) > 0 || count($product['required_fields']) > 0) {
//                            continue;
//                        }
//                    } catch (Exception $e) {}
//
//                    if (in_array($product[$field], $tmpDuplicatedValueArray)) {
//                        $tmpArray = $product['duplicated_fields'];
//                        array_push($tmpArray, $field);
//                        $product['duplicated_fields'] = $tmpArray;
//                    }
//                }
//            }
//
//            // Set data for field 'duplicated_record'
//            foreach ($allTmpProducts as $product) {
//                if (count($product['duplicated_fields']) <= 0) {
//                    continue;
//                }
//
//                $field = $product['duplicated_fields'][0];
//
//                $tmpVar = Product::query()
//                    ->where($field, $product[$field])
//                    ->first();
//
//                foreach ($this->encryptedFields as $encryptedField) {
//                    $tmpVar[$encryptedField] = isset($tmpVar[$encryptedField]) ?
//                        Crypt::decrypt($tmpVar[$encryptedField]) : null;
//                }
//
//                $tmpVarEnd = [];
//                $tmpVarEnd['_id'] = $tmpVar['_id'];
//                foreach ($allFields as $field) {
//                    $tmpVarEnd[$field] = $tmpVar[$field];
//                }
//                $product['duplicated_record'] = $tmpVarEnd;
//            }
//
//
//            foreach ($allTmpProducts as $product) {
//                $product->save();
//            }
//
//            if (count($allTmpProducts) < 1) {
//                return [
//                    'message' => 'Không có dữ liệu hợp lệ!',
//                    'step' => $request['step_import'],
//                    'success' => false
//                ];
//            }
//
//
//            $allTmpProducts2 = Tmp::query()
//                ->where('collection', 'product')
//                ->where('current_user', $currentUser->_id)
//                ->where('random_key', $random_key)
//                ->paginate(10);
//
//            $view = view('import.review3')
//                ->with('allTmpData', $allTmpProducts2)
//                ->with('fields_in_db', $fields_in_db)
//                ->with('defaultData', $defaultData)
//                ->render();
//
//            return [$view, $request['step_import'], 'product', $random_key, $fields_in_db];
//
//        } elseif ($request['step_import'] == 4) {
//            $fields_in_db = $request['fields_in_db'];
//            $random_key = $request['import_form_random_key'];
//            $records = $request['records'];
//            $error = false;
//            $saved = false;
//            $default_fields = $request['default_fields'] ? explode(',', $request['default_fields']) : [];
//            $old_fields = $request['old_fields'] ? explode(',', $request['old_fields']) : [];
//            $new_fields = $request['new_fields'] ? explode('.', $request['new_fields']) : [];
//            $fields = array_filter($fields_in_db, 'strlen');
//            $allTmp = Tmp::query()
//                ->where('collection', 'product')
//                ->where('current_user', $currentUser->_id)
//                ->where('random_key', $random_key)
//                ->get();
//
//            // Loop with data
//            foreach ($allTmp as $tmp) {
//
//                // Check if wrong format => skip
//                if (count($tmp['wrong_format']) > 0) {
//                    continue;
//                }
//
//                // Check require
//                if (count($tmp['required_fields']) > 0) {
//                    continue;
//                }
//
//                // Get data from tmp
//                $newProductData = [];
//                foreach ($allFields as $field) {
//                    $newProductData[$field] = null;
//                }
//                foreach ($fields_in_db as $key => $field_db) {
//                    // Check null field
//                    if ($field_db != "" || $field_db) {
//                        $newProductData[$field_db] = $tmp[$field_db];
//                    }
//                }
//
//                // Check duplicate
//                if (count($tmp['duplicated_fields']) > 0) {
//                    // Have duplicate
//
//                    // Get duplicated record
//                    $duplicatedProduct = Product::query()
//                        ->where($tmp['duplicated_fields'][0], $tmp[$tmp['duplicated_fields'][0]])
//                        ->first();
//
//                    foreach ($fields as $field) {
//                        // Check each record
//                        if ($tmp['_id'] && isset($records[$tmp['_id']]) && $records[$tmp['_id']][$field]) {
//                            if ($records[$tmp['_id']][$field] == 'new') {
//                                $duplicatedProduct[$field] = $tmp[$field];
//                            } elseif ($records[$tmp['_id']][$field] == 'default') {
//                                $duplicatedProduct[$field] = $defaultData[$field];
//                            } elseif ($records[$tmp['_id']][$field] == 'old') {
//                                // Do nothing
//                                continue;
//                            }
//                        } else {
//                            // Check with all records
//                            if (is_array($new_fields) && in_array($field, $new_fields)) {
//                                $duplicatedProduct[$field] = $tmp[$field];
//                            } elseif (is_array($default_fields) && in_array($field, $default_fields)) {
//                                $duplicatedProduct[$field] = $defaultData[$field];
//                            } elseif (is_array($old_fields) && in_array($field, $old_fields)) {
//                                // Do nothing
//                                continue;
//                            }
//                        }
//                    }
//
//                    // Save data
//                    $newProductData['user_id'] = [$currentUser->_id];
//                    if (!$duplicatedProduct->save()) {
//                        $error = true;
//                        break;
//                    } else {
//                        $saved = true;
//                    }
//
//                } else {
//                    // Create new record
//                    $newProductData['user_id'] = [$currentUser->_id];
//                    // Check for field 'id'
//                    if (!in_array('id', $fields_in_db)) {
//                        $newProductData['id'] = Product::getLastProductId() + 1;
//                    }
//
//                    $newProduct = new Product($newProductData);
//                    if (!$newProduct->save()) {
//                        $error = true;
//                        break;
//                    } else {
//                        $saved = true;
//                    }
//                }
//
//            }
//
//            if ($error) {
//                return [
//                    'message' => 'Có lỗi khi lưu dữ liệu vào db!',
//                    'step' => $request['step_import'],
//                    'success' => false
//                ];
//            } elseif (!$saved) {
//                return [
//                    'message' => 'Lưu dữ liệu thất bại! Không có bản ghi hợp lệ.',
//                    'step' => $request['step_import'],
//                    'success' => false
//                ];
//
//            } else {
//                return [
//                    'message' => 'Nhập dữ liệu thành công!',
//                    'success' => true
//                ];
//
//            }
//        }
//
//        $allTmpProducts = Tmp::query()
//            ->where('collection', 'product')
//            ->where('current_user', $currentUser->_id)
//            ->where('random_key', $random_key)
//            ->get();
//
//        foreach ($allTmpProducts as $product) {
//            $product->user = User::query()->find($product->user_id)['name'];
//        }
//
//        if (count($allTmpProducts) > 0) {
//            return response()->json([$allTmpProducts, $random_key, 'product']);
//
//        } else {
//            return [
//                'message' => 'Không có dữ liệu!',
//                'success' => false
//            ];
//        }
//    }

//    public function importProduct(Request $request)
//    {
//        $currentUser = $this->authLogin('');
//
//        // Check isset role
//        if (!$this->checkAuthorize($currentUser, 'product', 'import')) {
//            return [
//                'message' => 'Bạn không có quyền nhập dữ liệu sản phẩm!',
//                'success' => false
//            ];
//        }
//
//        $allProducts = Product::all();
//        $random_key = Str::random(10);
//
//        if ($request->file('import_file')->getClientOriginalExtension() != 'xlsx') {
//            return [
//                'message' => 'Sai loại file! Chỉ chấp nhận file đuôi .xlsx!',
//                'success' => false
//            ];
//        }
//
//        $collection = Excel::toCollection(new ProductsImport, $request->file('import_file'));
//
//        // Check input file
//        if (!$collection || count($collection) < 1 || count($collection[0]) < 1) {
//            return [
//                'message' => 'Dữ liệu không đúng định dạng hoặc không có dữ liệu!',
//                'success' => false
//            ];
//        }
//        $keys = $collection[0][0]->keys();
//        $arrayKey = [
//            'id',
//            'name',
//            'code',
//            'price',
//            'image',
//            'remain',
//            'user',
//            'status',
//            'created_at',
//            'updated_at'
//        ];
//
//        if ($keys->intersect($arrayKey) != $keys) {
//            return [
//                'message' => 'Dữ liệu không đúng định dạng!',
//                'success' => false
//            ];
//        }
//
//        for ($i = 0; $i < count($collection[0]); $i++) {
//
//            // Check data
//            $user_id = User::query()
//                ->where('name', $collection[0][$i]['user'])
//                ->first()->_id;
//
//            if ($request['field_id']) {
//                foreach ($allProducts as $product) {
//                    if ($product->id != null && $product->id == $collection[0][$i]['id']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'id';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_name']) {
//                foreach ($allProducts as $product) {
//                    if ($product->name != null && $product->name == $collection[0][$i]['name']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'name';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_code']) {
//                foreach ($allProducts as $product) {
//                    if ($product->code != null && $product->code == $collection[0][$i]['code']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'code';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_price']) {
//                foreach ($allProducts as $product) {
//                    if ($product->price != null && $product->price == $collection[0][$i]['price']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'price';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_image']) {
//                foreach ($allProducts as $product) {
//                    if ($product->image != null && $product->image == $collection[0][$i]['image']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'image';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_remain']) {
//                foreach ($allProducts as $product) {
//                    if ($product->remain != null && $product->remain == $collection[0][$i]['remain']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'remain';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_user_id']) {
//                foreach ($allProducts as $product) {
//                    if ($product->user_id != null && $product->user_id == $collection[0][$i]['user_id']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'user_id';
//                        break;
//                    }
//                }
//            }
//
//            if ($request['field_status']) {
//                foreach ($allProducts as $product) {
//                    if ($product->status != null && $product->status == $collection[0][$i]['status']) {
//                        $collection[0][$i]['duplicate'] = true;
//                        $collection[0][$i]['duplicate_key'] = 'status';
//                        break;
//                    }
//                }
//            }
//
//            // when not duplicate -> save
//            $newProduct = new Tmp([
//                'collection' => 'product',
//                'id' => $collection[0][$i]['id'],
//                'name' => $collection[0][$i]['name'],
//                'code' => $collection[0][$i]['code'],
//                'price' => $collection[0][$i]['price'],
//                'image' => $collection[0][$i]['image'],
//                'remain' => $collection[0][$i]['remain'],
//                'user_id' => $user_id,
//                'status' => $collection[0][$i]['status'],
//                'current_user' => $currentUser->_id,
//                'random_key' => $random_key,
//                'duplicate' => isset($collection[0][$i]['duplicate']) ? $collection[0][$i]['duplicate'] : null,
//                'duplicate_key' => isset($collection[0][$i]['duplicate_key']) ? $collection[0][$i]['duplicate_key'] : null
//            ]);
//
//            $newProduct->save();
//        }
//
//        $allTmpProducts = Tmp::query()
//            ->where('collection', 'product')
//            ->where('current_user', $currentUser->_id)
//            ->where('random_key', $random_key)
//            ->get();
//
//        foreach ($allTmpProducts as $product) {
//            $product->user = User::query()
//                ->find($product->user_id)['name'];
//        }
//
//        if (count($allTmpProducts) > 0) {
//            return response()->json([$allTmpProducts, $random_key, 'product']);
//
//        } else {
//            return [
//                'message' => 'Không có dữ liệu!',
//                'success' => false
//            ];
//        }
//    }
}

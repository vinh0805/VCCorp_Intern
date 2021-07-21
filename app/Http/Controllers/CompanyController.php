<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Imports\CompaniesImport;
use App\Imports\TmpImport;
use App\Models\Company;
use App\Models\Label;
use App\Models\Role;
use App\Models\Tmp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    public function showViewCompanies()
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'company')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý công ty!");
        }

        $role = Role::query()->find($currentUser->role_id["company"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            $allCompanies = Company::query()
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $allCompanies = Company::query()
                ->where('user_id', 'all', [$currentUser->_id])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        $allUsers = User::query()->take(1000)->get();

        // Fix data
        // Set userList's name
        $userIdList = [];
        foreach ($allCompanies as $company) {
            if (is_array($company['user_id']) && count($company['user_id']) > 0) {
                foreach ($company['user_id'] as $userId) {
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
        foreach ($allCompanies as $company) {
            $company['userName'] = '';
            if (is_array($company['user_id']) && count($company['user_id']) > 0) {
                foreach ($company['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $company['userName'] = implode(', ', $tmpArray);
            $tmpArray = [];
        } // end set userList's name
        // Decrypt at company
        try {
            foreach ($allCompanies as $company) {
                $company['phone'] = isset($company['phone']) && $company['phone'] != '' ?
                    $company['phone'] = Crypt::decrypt($company['phone']['encrypted']) : null;
                $company['email'] = isset($company['email']) && $company['email'] != '' ?
                    $company['email'] = Crypt::decrypt($company['email']['encrypted']) : null;
            }
        } catch (Exception $exception) {
        }

        $allFieldLabels = Label::query()
            ->where('collection', 'company')
            ->first();

        return view('company.companies')
            ->with('allUsers', $allUsers)
            ->with('currentUser', $currentUser)
            ->with('allCompanies', $allCompanies)
            ->with('permissionList', $permissionList)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('title', 'Quản lý Công ty');
    }


    public function searchCompany(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkReadPermission($currentUser, 'company')) {
            return redirect('/permission-error')
                ->with('errorMessage', "Bạn không có quyền truy cập trang quản lý công ty!");
        }

        $search_field = isset($request['search_field']) ? $request['search_field'] : 'id';
        $search_value = $request['search_value'];

        $role = Role::query()->find($currentUser->role_id["company"]);

        $permissionList = $role['permission_list'];

        // Check permission -> get data
        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
            if ($search_field == 'id' || in_array($search_field, $this->encryptedFields)) {
                try {
                    $search_value = $search_field == 'id' ? $search_value + 0 : $search_value;
                } catch (Exception $e) {}
                $allCompanies = Company::query()
                    ->where($search_field, $search_value)
                    ->orWhere($search_field . '.hashed', md5($search_value))
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allCompanies = Company::query()
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
                    $allCompanies = Company::query()
                        ->where([
                            '$or' => [
                                ['$text' => ['$search' => $search_value]],
                                ['name' => ['$regex' => $search_value, '$options' => 'i']],
                                ['code' => ['$regex' => $search_value, '$options' => 'i']],
                                ['address' => ['$regex' => $search_value, '$options' => 'i']],
                                ['field' => ['$regex' => $search_value, '$options' => 'i']],
                                ['email.hashed' => md5($search_value)],
                                ['phone.hashed' => md5($search_value)],
                                ['created_at' => $time],
                                ['updated_at' => $time],
                                ['status' => $search_value]
                            ]
                        ])                        ->orderBy('id', 'desc')
                        ->paginate(10);
                } catch (Exception $e) {
                    $allCompanies = Company::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }

            } else {
                $allCompanies = Company::query()
                    ->where($search_field, 'regexp', '/'. $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        } else {
            if ($search_field == 'id' || in_array($search_field, $this->encryptedFields)) {
                try {
                    $search_value = $search_field == 'id' ? $search_value + 0 : $search_value;
                } catch (Exception $e) {}
                $allCompanies = Company::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->orWhere($search_field . '.hashed', md5($search_value))
                    ->where($search_field, $search_value)
                    ->orderBy('id', 'desc')
                    ->paginate(10);

            } elseif ($search_field == 'created_at' || $search_field == 'updated_at') {
                try {
                    $search_value = new Carbon($search_value);
                } catch (Exception $e) {}
                $allCompanies = Company::query()
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
                    $allCompanies = Company::query()
                        ->where('user_id', 'all', [$currentUser->_id])
                        ->where([
                            '$or' => [
                                ['$text' => ['$search' => $search_value]],
                                ['name' => ['$regex' => $search_value, '$options' => 'i']],
                                ['code' => ['$regex' => $search_value, '$options' => 'i']],
                                ['address' => ['$regex' => $search_value, '$options' => 'i']],
                                ['field' => ['$regex' => $search_value, '$options' => 'i']],
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
                    $allCompanies = Company::query()
                        ->where('id', '-1')
                        ->paginate(10);
                }

            } else {
                $allCompanies = Company::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->where($search_field, 'regexp', '/'. $search_value . '/i')
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
        }

        $allUsers = User::query()->take(1000)->get();

        // Fix data
        // Set userList's name
        $userIdList = [];
        foreach ($allCompanies as $company) {
            if (is_array($company['user_id']) && count($company['user_id']) > 0) {
                foreach ($company['user_id'] as $userId) {
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
        foreach ($allCompanies as $company) {
            $company['userName'] = '';
            if (is_array($company['user_id']) && count($company['user_id']) > 0) {
                foreach ($company['user_id'] as $userId) {
                    foreach ($userList as $user) {
                        if ($userId == $user['_id'] && !in_array($user['name'], $tmpArray)) {
                            array_push($tmpArray, $user['name']);
                        }
                    }
                }
            }
            $company['userName'] = implode(', ', $tmpArray);
            $tmpArray = [];
        } // end set userList's name
        // Decrypt at company
        try {
            foreach ($allCompanies as $company) {
                $company['phone'] = isset($company['phone']) && $company['phone'] != '' ?
                    $company['phone'] = Crypt::decrypt($company['phone']['encrypted']) : null;
                $company['email'] = isset($company['email']) && $company['email'] != '' ?
                    $company['email'] = Crypt::decrypt($company['email']['encrypted']) : null;
            }
        } catch (Exception $exception) {
        }

        $allFieldLabels = Label::query()
            ->where('collection', 'company')
            ->first();

        return view('company.companies')
            ->with('allUsers', $allUsers)
            ->with('currentUser', $currentUser)
            ->with('allCompanies', $allCompanies)
            ->with('permissionList', $permissionList)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('title', 'Quản lý Công ty')
            ->with('search_field', $search_field)
            ->with('search_value', $search_value);

    }


    public function createCompany(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'company', 'create')) {
            $data = [
                'message' => 'Bạn không có quyền tạo dữ liệu mới ở module Công ty!',
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
        if (!$this->validateVietnameseCharacters($request['field'])) {
            return response()->json([
                'message' => 'Trường lĩnh vực: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        } // end validate data


        // Confirm if have duplicated record
        if (!$request['confirm']) {
            if (isset($request['email'])) {
                $duplicatedEmailCompany = Company::query()
                    ->where('email.hashed', md5($request['email']))
                    ->first();
            }
            if ($request['phone']) {
                $duplicatedPhoneCompany = Company::query()
                    ->where('phone.hashed', md5($request['phone']))
                    ->first();
            }

            if (isset($duplicatedEmailCompany) && isset($duplicatedPhoneCompany)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' và số điện thoại '. $request['phone'] .' đã được sử dụng. Tiếp tục dùng email và số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedPhoneCompany)) {
                return response()->json([
                    'message' => 'Số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedEmailCompany)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' đã được sử dụng. Tiếp tục dùng email này?',
                    'confirm' => true
                ]);
            }
        }

        $newCompany = new Company([
            'id' => Company::getLastCompanyId() + 1,
            'name' => @$request['name'],
            'code' => @$request['code'],
            'address' => @$request['address'],
            'field' => @$request['field'],
            'email' => [
                'encrypted' => Crypt::encrypt(@$request['email']),
                'hashed' => md5(@$request['email'])
            ],
            'phone' => [
                'encrypted' => Crypt::encrypt(@$request['phone']),
                'hashed' => md5(@$request['phone'])
            ],
            'user_id' => isset($request['users']) ? $request['users'] : [],
            'status' => @$request['status'],
            'created_at' => Carbon::now()->format('Y-m-d H-i-s')
        ]);
        $newCompany['created_at'] = Carbon::now()->format('Y-m-d H-i-s');

        if ($newCompany->save()) {
            $data = [
                'message' => 'Tạo công ty mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Tạo công ty mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function deleteCompany($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'company', 'delete') &&
            !$this->checkAuthorize($currentUser, 'company', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module công ty!',
                'success' => false
            ];
            return response()->json($data);
        }


        $company = Company::query()->find($_id);
        if (!isset($_id) || !isset($company)) {
            $data = [
                'message' => 'Xóa thông tin công ty thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'company', 'delete') &&
            !$this->checkAuthorize($currentUser, 'company', 'delete_all') &&
            !in_array($currentUser->_id, $company['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa đối với công ty này!',
                'success' => false
            ]);
        }

        try {
            if ($company->delete()) {
                $data = [
                    'message' => 'Xóa công ty thành công!',
                    'success' => true
                ];

                return response()->json($data);
            }
        } catch (Exception $e) {
        }

        $data = [
            'message' => 'Xóa công ty thất bại! Dữ liệu không tồn tại.',
            'success' => false
        ];

        return response()->json($data);
    }


    public function deleteAllCompany(Request $request)
    {
        $currentUser = $this->authLogin('');
        $error = false;
        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'company', 'delete') &&
            !$this->checkAuthorize($currentUser, 'company', 'delete_all')) {
            $data = [
                'message' => 'Bạn không có quyền xóa đối với module công ty!',
                'success' => false
            ];
            return response()->json($data);
        }

        $companyList = Company::query()
            ->whereIn('_id', $request['idList'])
            ->get();

        foreach ($companyList as $company) {
            if (!isset($company)) {
                continue;
            }

            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'company', 'delete') &&
                !$this->checkAuthorize($currentUser, 'company', 'delete_all') &&
                !in_array($currentUser->_id, $company['user_id'])) {
                continue;
            }

            try {
                if (!$company->delete()) {
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


    public function saveCompany(Request $request, $_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'company', 'update') &&
            !$this->checkAuthorize($currentUser, 'company', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Công ty!',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset record
        $company = Company::query()->find($_id);
        if (!isset($_id) || !isset($company)) {
            $data = [
                'message' => 'Sửa thông tin công ty thất bại! Dữ liệu không tồn tại.',
                'success' => false
            ];
            return response()->json($data);
        }

        // Check isset role with record
        if ($this->checkAuthorize($currentUser, 'company', 'update') &&
            !$this->checkAuthorize($currentUser, 'company', 'update_all') &&
            !in_array($currentUser->_id, $company['user_id'])) {
            return response()->json([
                'message' => 'Bạn không có quyền sửa đối với công ty này!',
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
        if (!$this->validateVietnameseCharacters($request['field'])) {
            return response()->json([
                'message' => 'Trường lĩnh vực: không đúng định dạng dữ liệu!',
                'success' => false
            ]);
        }

        // Confirm if have duplicated record
        if (!$request['confirm']) {
            if (isset($request['email'])) {
                $duplicatedEmailCompany = Company::query()
                    ->where('email.hashed', md5($request['email']))
                    ->where('_id', '!=', $company['_id'])
                    ->first();
            }
            if ($request['phone']) {
                $duplicatedPhoneCompany = Company::query()
                    ->where('phone.hashed', md5($request['phone']))
                    ->where('_id', '!=', $company['_id'])
                    ->first();
            }

            if (isset($duplicatedEmailCompany) && isset($duplicatedPhoneCompany)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' và số điện thoại '. $request['phone'] .' đã được sử dụng. Tiếp tục dùng email và số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedPhoneCompany)) {
                return response()->json([
                    'message' => 'Số điện thoại ' . $request['phone'] . ' đã được sử dụng. Tiếp tục dùng số điện thoại này?',
                    'confirm' => true
                ]);
            }
            if (isset($duplicatedEmailCompany)) {
                return response()->json([
                    'message' => 'Email ' . $request['email'] . ' đã được sử dụng. Tiếp tục dùng email này?',
                    'confirm' => true
                ]);
            }
        }


        $company['name'] = $request['name'];
        $company['code'] = $request['code'];
        $company['address'] = $request['address'];
        $company['field'] = $request['field'];
        $company['email'] = [
            'encrypted' => Crypt::encrypt(@$request['email']),
            'hashed' => md5(@$request['email'])
        ];
        $company['phone'] = [
            'encrypted' => Crypt::encrypt(@$request['phone']),
            'hashed' => md5(@$request['phone'])
        ];
        $company['user_id'] = isset($request['users']) ? $request['users'] : [];

        $company['status'] = $request['status'];

        if ($company->save()) {
            $data = [
                'message' => 'Sửa thông tin công ty thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Sửa thông tin công ty thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function getDataToEditCompany($_id)
    {
        $currentUser = $this->authLogin('');

        // Check isset role with module
        if (!$this->checkAuthorize($currentUser, 'company', 'update') &&
            !$this->checkAuthorize($currentUser, 'company', 'update_all')) {
            $data = [
                'message' => 'Bạn không có quyền sửa dữ liệu ở module Công ty!',
                'success' => false
            ];
            return response()->json($data);
        }

        $editCompany = Company::query()->find($_id);

        if (isset($editCompany)) {
            // Check isset role with record
            if ($this->checkAuthorize($currentUser, 'company', 'update') &&
                !$this->checkAuthorize($currentUser, 'company', 'update_all') &&
                !in_array($currentUser->_id, $editCompany['user_id'])) {
                return response()->json([
                    'message' => 'Bạn không có quyền sửa đối với công ty này!',
                    'success' => false
                ]);
            }

            $editCompany['email'] = isset($editCompany['email']) && $editCompany['email'] != '' ?
                Crypt::decrypt($editCompany['email']['encrypted']) : null;
            $editCompany['phone'] = isset($editCompany['phone']) && $editCompany['phone'] != '' ?
                Crypt::decrypt($editCompany['phone']['encrypted']) : null;
            return response()->json($editCompany);
        } else {
            $data = [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function exportCompany(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'company', 'export') &&
            !$this->checkAuthorize($currentUser, 'company', 'export_all')) {
            return [
                'message' => 'Bạn không có quyền xuất dữ liệu ở module Công ty!',
                'success' => false
            ];
        }

        $file_name = @$request['name'] . '.xlsx';
        $fields = @$request['fields'];

        $allExportedCompanies = null;
        $option = @$request['export_option'];
        $option_number = $request['option_number'] + 1 - 1;
        $chosenRecords = explode(',', @$request['checked_list']);

        $role = Role::query()->find($currentUser->role_id["company"]);
        if ($option == 'choose') {  // export chosen records only
            $allExportedCompanies = Company::query()
                ->whereIn('_id', $chosenRecords)
                ->get($fields);
        } elseif ($option == 'all') {  // export all exportable records
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedCompanies = Company::query()
                    ->get($fields);
            } else {
                $allExportedCompanies = Company::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->get($fields);
            }
        } elseif ($option == 'input') {
            if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all']))) {
                $allExportedCompanies = Company::query()
                    ->take($option_number)
                    ->get($fields);
            } else {
                $allExportedCompanies = Company::query()
                    ->where('user_id', 'all', [$currentUser->_id])
                    ->take($option_number)
                    ->get($fields);
            }
        }

        // Unset _id field
        $allExportedCompanies->transform(function ($i) {
            unset($i['_id']);
            return $i;
        });


        // Fix data field user_id
        if (in_array('user_id', $fields)) {
            $userIdList = [];
            foreach ($allExportedCompanies as $company) {
                if (!is_array($company->user_id)) {
                    continue;
                }
                foreach ($company->user_id as $user_id) {
                    if (!in_array($user_id, $userIdList)) {
                        array_push($userIdList, $user_id);
                    }
                }
            }

            $userList = User::query()
                ->whereIn('_id', $userIdList)
                ->get();
            foreach ($allExportedCompanies as $company) {
                $tmpArray = [];
                foreach ($company->user_id as $user_id) {
                    if (!is_array($company->user_id)) {
                        continue;
                    }
                    foreach ($userList as $user) {
                        try {
                            $email = Crypt::decrypt($user->email);
                        } catch (Exception $e) {
                            $email = null;
                        }
                        if ($user_id == $user->_id && isset($user->email) &&
                            !in_array($email, $tmpArray)) {
                            array_push($tmpArray, $email);
                        }
                    }
                }
                $company->user_id = implode(', ', $tmpArray);
            } // End fix user_id
        }

        // Fix data encrypted fields
        foreach ($allExportedCompanies as $company) {
            foreach ($this->encryptedFields as $encryptedField) {
                if (isset($company[$encryptedField])) {
                    try {
                        $company[$encryptedField] = Crypt::decrypt($company[$encryptedField]['encrypted']);
                    } catch (Exception $e) {}
                }
            }
        }

        $dataArray = [];
        foreach ($allExportedCompanies as $company) {
            $created_at = $company['created_at']->format('Y-m-d H:i:s.u');
            $updated_at = $company['updated_at']->format('Y-m-d H:i:s.u');
            $company = $company->attributesToArray();
            $company['created_at'] = $created_at;
            $company['updated_at'] = $updated_at;
            array_push($dataArray, $company);
        }

        $header = [];
        $label = Label::query()
            ->where('collection', 'company')
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


    public function importCompany(Request $request)
    {
        $currentUser = $this->authLogin('');

        $defaultData = [
            'id' => 1,
            'name' => 'ABC',
            'code' => 'CT',
            'field' => 'Nhiều lĩnh vực',
            'address' => 'Ha Noi',
            'email' => 'ctabc@gmail.com',
            'phone' => '099 999 9999',
            'user_id' => [],
            'status' => 'Đang hoạt động',
            'created_at' => '2021-06-23T07:59:51.975000Z',
            'updated_at' => '2021-06-12T05:09:50.169000Z'
        ];
        $allFields = ['name', 'code', 'address', 'field', 'email', 'phone', 'status', 'created_at', 'updated_at'];

        // Check isset role
        if (!$this->checkAuthorize($currentUser, 'company', 'import')) {
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
                ->where('collection', 'company')
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
            $collection = Excel::toCollection(new CompaniesImport, $request->file('import_file'));

            // Save data in excel to Tmp
            $this->saveDataFromExcelToTmp('company', $collection[0], $random_key, $fields_in_db, $check_field_require);

            $allTmpCompanies = Tmp::getTmpData('company', $currentUser->_id, $random_key);

            // Check duplicate for each field in duplicate_fields
            foreach ($check_field_duplicate as $key => $duplicated_key) {
                $field = $fields_in_db[$duplicated_key];

                $tmpValueArray = [];    // All value of field, take from excel data
                for ($i = 1; $i < count($collection[0]); $i++) {
                    if (!in_array($collection[0][$i][$duplicated_key], $tmpValueArray)) {
                        array_push($tmpValueArray, $collection[0][$i][$duplicated_key]);
                    }
                }

                // $duplicatedCompanies: all duplicated records in db
                if (in_array($field, $this->encryptedFields)) {
                    $tmpValueArray = array_map(function ($value) {return md5($value);}, $tmpValueArray);
                    $duplicatedCompanies = Company::query()
                        ->where([$field. '.hashed' => ['$in' => $tmpValueArray]])
                        ->get();

                    $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
                    foreach ($duplicatedCompanies as $company) {
                        if ($company[$field . '.hashed'] && !in_array($company[$field . '.hashed'], $tmpDuplicatedValueArray)) {
                            array_push($tmpDuplicatedValueArray, $company[$field . '.hashed']);
                        }
                    }

                    // Set value for field 'duplicated_fields'
                    foreach ($allTmpCompanies as $company) {
                        try {
                            if (count($company['wrong_format']) > 0 || count($company['required_fields']) > 0) {
                                continue;
                            }
                        } catch (Exception $e) {}
                        if (in_array(md5($company[$field]), $tmpDuplicatedValueArray)) {
                            $tmpArray = $company['duplicated_fields'];
                            array_push($tmpArray, $field);
                            $company['duplicated_fields'] = $tmpArray;
                        }
                    }

                } else {
                    $duplicatedCompanies = Company::query()
                        ->whereIn($field, $tmpValueArray)
                        ->get();

                    $tmpDuplicatedValueArray = [];  // Duplicated values in data get from db
                    foreach ($duplicatedCompanies as $company) {
                        if ($company[$field] && !in_array($company[$field], $tmpDuplicatedValueArray)) {
                            array_push($tmpDuplicatedValueArray, $company[$field]);
                        }
                    }

                    // Set value for field 'duplicated_fields'
                    foreach ($allTmpCompanies as $company) {
                        if (in_array($company[$field], $tmpDuplicatedValueArray)) {
                            $tmpArray = $company['duplicated_fields'];
                            array_push($tmpArray, $field);
                            $company['duplicated_fields'] = $tmpArray;
                        }
                    }
                }
            }

            // Set data for field 'duplicated_record'
            foreach ($allTmpCompanies as $company) {
                if (count($company['duplicated_fields']) <= 0) {
                    continue;
                }

                $field = $company['duplicated_fields'][0];

                if (in_array($field, $this->encryptedFields)) {
                    // Get duplicated record + edit encrypted fields
                    $tmpVar = Company::query()
                        ->where($field . '.hashed', md5($company[$field]))
                        ->first();
                } else {
                    $tmpVar = Company::query()
                        ->where($field, $company[$field])
                        ->first();
                }
                try {
                    foreach ($this->encryptedFields as $encryptedField) {
                        $tmpVar[$encryptedField] = isset($tmpVar[$encryptedField]) ?
                            Crypt::decrypt($tmpVar[$encryptedField]['encrypted']) : null;
                    }
                } catch (Exception $e) {}

                $company['duplicated_record'] = $tmpVar->attributesToArray();
            }


            foreach ($allTmpCompanies as $company) {
                $company->save();
            }

            if (count($allTmpCompanies) < 1) {
                return [
                    'message' => 'Không có dữ liệu hợp lệ!',
                    'step' => $request['step_import'],
                    'success' => false
                ];
            }

            $allTmpCompanies2 = Tmp::query()
                ->where('collection', 'company')
                ->where('current_user', $currentUser->_id)
                ->where('random_key', $random_key)
                ->paginate(10);

            $view = view('import.review3')
                ->with('allTmpData', $allTmpCompanies2)
                ->with('fields_in_db', $fields_in_db)
                ->with('defaultData', $defaultData)
                ->render();

            return [$view, $request['step_import'], 'company', $random_key, $fields_in_db];

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
                ->where('collection', 'company')
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
                    $newCompanyData = [];
                    foreach ($allFields as $field) {
                        $newCompanyData[$field] = null;
                    }
                    foreach ($fields_in_db as $key => $field_db) {
                        // Check null field
                        if (!isset($field_db) || $field_db == '') {
                            continue;
                        }

                        $newCompanyData[$field_db] = in_array($field_db, $this->encryptedFields) ?
                            ['encrypted' => Crypt::encrypt($tmp[$field_db]), 'hashed' => md5($tmp[$field_db])] :
                            $tmp[$field_db];
                    }

                    // Check for field 'id'
                    if (!in_array('id', $fields_in_db)) {
                        $newCompanyData['id'] = Company::getLastCompanyId() + 1;
                    }

                    // Create new record
                    array_push($newArray, $tmp['excel_row']);
                    $newCompanyData['user_id'] = [$currentUser->_id];
                    $newCompany = new Company($newCompanyData);
                    if (!$newCompany->save()) {
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
                $duplicatedCompany = Company::query()
                    ->find($tmp['duplicated_record']['_id']);

                foreach ($fields as $field) {
                    // Check each record
                    if ($tmp['_id'] && isset($records[$tmp['_id']]) && $records[$tmp['_id']][$field]) {
                        switch ($records[$tmp['_id']][$field]) {
                            case 'new':
                                $duplicatedCompany[$field] = in_array($field, $this->encryptedFields) ?
                                    ['encrypted' => Crypt::encrypt($tmp[$field]), 'hashed' => md5($tmp[$field])] :
                                    $tmp[$field];
                                break;
                            case 'default':
                                $duplicatedCompany[$field] = in_array($field, $this->encryptedFields) ?
                                    ['encrypted' => Crypt::encrypt($defaultData[$field]), 'hashed' => md5($defaultData[$field])] :
                                    $defaultData[$field];
                                break;
                            case 'old': // Do nothing
                            default: break;
                        }
                    } else {
                        // Check with all records
                        if (is_array($new_fields) && in_array($field, $new_fields)) {
                            $duplicatedCompany[$field] = in_array($field, $this->encryptedFields) ?
                                ['encrypted' => Crypt::encrypt($tmp[$field]), 'hashed' => md5($tmp[$field])] :
                                $tmp[$field];
                        } elseif (is_array($default_fields) && in_array($field, $default_fields)) {
                            $duplicatedCompany[$field] = in_array($field, $this->encryptedFields) ?
                                ['encrypted' => Crypt::encrypt($defaultData[$field]), 'hashed' => md5($defaultData[$field])] :
                                $defaultData[$field];
                        } elseif (is_array($old_fields) && in_array($field, $old_fields)) {
                            // Do nothing
                            continue;
                        }
                    }
                }

                // Save data
                if (!$duplicatedCompany->save()) {
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
                $collection = Excel::toCollection(new CompaniesImport, $request->file('import_file'));

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

        $allTmpCompanies = Tmp::query()
            ->where('collection', 'company')
            ->where('current_user', $currentUser->_id)
            ->where('random_key', $random_key)
            ->get();

        foreach ($allTmpCompanies as $company) {
            $company->user = User::query()->find($company->user_id)['name'];
        }

        if (count($allTmpCompanies) > 0) {
            return response()->json([$allTmpCompanies, $random_key, 'company']);

        } else {
            return [
                'message' => 'Không có dữ liệu!',
                'success' => false
            ];
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function createNewUser(Request $request)
    {
        $data = $request->all();
        $checkEmail = User::query()
            ->where('email.hashed', md5($data['email']))
            ->first();
        $lastUser = User::query()
            ->orderBy('id', 'desc')->first();

        if (isset($checkEmail)) {
            return [
                'url' => null,
                'success' => false,
                'message' => 'Email đã được sử dụng!'
            ];
        }
        // Validate input data
        if (!$this->validateName($request['name'])) {
            return [
                'url' => null,
                'success' => false,
                'message' => 'Không đúng định dạng dữ liệu tên!'
            ];
        }
        if (!$this->validateEmail($request['email'])) {
            return [
                'url' => null,
                'success' => false,
                'message' => 'Không đúng định dạng dữ liệu email!'
            ];
        }

        $companyUserRole = Role::query()
            ->where('collection', 'company')
            ->where('name', 'user')
            ->first();
        $customerUserRole = Role::query()
            ->where('collection', 'customer')
            ->where('name', 'user')
            ->first();
        $orderUserRole = Role::query()
            ->where('collection', 'order')
            ->where('name', 'user')
            ->first();
        $productUserRole = Role::query()
            ->where('collection', 'product')
            ->where('name', 'user')
            ->first();

        if ($data['password'] == $data['confirm_password']) {
            $user = new User([
                'id' => $lastUser['id'] + 1,
                'name' => $data['name'],
                'email' => isset($data['email']) ? [
                    'encrypted' => Crypt::encrypt($data['email']),
                    'hashed' => md5($data['email'])
                ] : null,
                'password' => md5($data['password']),
                'super_admin' => 0,
                'role_id' => [
                    'company' => isset($companyUserRole) ? $companyUserRole['_id'] : null,
                    'customer' => isset($customerUserRole) ? $customerUserRole['_id'] : null,
                    'order' => isset($orderUserRole) ? $orderUserRole['_id'] : null,
                    'product' => isset($productUserRole) ? $productUserRole['_id'] : null,
                ]
            ]);

            if (!$user->save()) {
                return [
                    'url' => null,
                    'success' => false,
                    'message' => 'Có lỗi trong quá trình lưu dữ liệu! Vui lòng thực hiện lại.'
                ];
            }
            try {
                $user['email'] = Crypt::decrypt($user['email']['encrypted']);
            } catch (Exception $e) {
                $user['email'] = null;
            }
            Session::put('currentUser', $user);
            return [
                'url' => '/login',
                'success' => true,
                'message' => 'Đăng ký tài khoản mới thành công!'
            ];
        } else {
            return [
                'url' => null,
                'success' => false,
                'message' => 'Sai mật khẩu xác nhận!'
            ];
        }
    }


    public function showEditProfileView()
    {
        $currentUser = Session::get('currentUser');
        try {
            $currentUser['email'] = Crypt::decrypt($currentUser['email']['encrypted']);
            $currentUser['phone'] = Crypt::decrypt($currentUser['phone']['encrypted']);
        } catch (Exception $e) {
        }
        if (isset($currentUser)) {
            return view('home');
        } else return redirect('login');
    }


    public function saveUserInfo(Request $request)
    {
        $currentUser = $this->authLogin('');

        // Validate input data
        if (!$this->validateName($request['name'])) {
            return [
                'success' => false,
                'message' => "Không đúng định dạng dữ liệu trường Tên!"
            ];
        } elseif (!$this->validateEmail($request['email'])) {
            return [
                'success' => false,
                'message' => "Không đúng định dạng dữ liệu trường Email!"
            ];
        } elseif (!$this->validatePhone($request['phone'])) {
            return [
                'success' => false,
                'message' => "Không đúng định dạng dữ liệu trường Số điện thoại!"
            ];
        } elseif (!$this->validateImgFile($request->file('avatar'))) {
            return [
                'success' => false,
                'message' => "Không đúng định dạng dữ liệu trường Hình ảnh!"
            ];
        }

        $user = User::query()->find($currentUser->_id);
        if (isset($user)) {
            $user['name'] = $request['name'];
            $user['gender'] = $request['gender'];
            try {
                $user['phone'] = [
                    'encrypted' => Crypt::encrypt($request['phone']),
                    'hashed' => md5($request['phone'])
                ];
            } catch (Exception $e) {
                $user['phone'] = null;
            }
            $avatar = $request->file('avatar');
            if ($avatar) {
                $avatarName = $avatar->getClientOriginalName();
                $request['avatar']->storeAs('images', $avatarName, 'public');
                $user['avatar'] = $avatarName;
            }

            if ($user->save()) {
                $user['email'] = Crypt::decrypt($user['email']['encrypted']);
                $user['phone'] = Crypt::decrypt($user['phone']['encrypted']);
                Session::put('currentUser', $user);
                return [
                    'success' => true,
                    'message' => "Thay đổi thông tin thành công!"
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Thay đổi thông tin thất bại!"
                ];
            }
        }

        return [
            'success' => false,
            'message' => "Thay đổi thông tin thất bại! Không tìm thấy dữ liệu."
        ];
    }


    public function showViewUsersAdmin()
    {
        $currentUser = $this->authLogin('admin');

        $userList = User::query()
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($userList as $user) {
            $user->company_role = isset($user->role_id['company']) ?
                Role::query()->find($user->role_id['company']) : null;
            $user->customer_role = isset($user->role_id['customer']) ?
                Role::query()->find($user->role_id['customer']) : null;
            $user->order_role = isset($user->role_id['order']) ?
                Role::query()->find($user->role_id['order']) : null;
            $user->product_role = isset($user->role_id['product']) ?
                Role::query()->find($user->role_id['product']) : null;
            try {
                $user['email'] = isset($user['email']['encrypted']) ? Crypt::decrypt($user['email']['encrypted']) : null;
                $user['phone'] = isset($user['phone']['encrypted']) ? Crypt::decrypt($user['phone']['encrypted']) : null;
            } catch (Exception $e) {
            }
        }

        $allRoleCompanies = Role::query()->where('collection', 'company')->get();
        $allRoleCustomers = Role::query()->where('collection', 'customer')->get();
        $allRoleOrders = Role::query()->where('collection', 'order')->get();
        $allRoleProducts = Role::query()->where('collection', 'product')->get();
        $allFieldLabels = Label::query()
            ->where('collection', 'user')
            ->first();

        return view('admin/users')
            ->with('userList', $userList)
            ->with('allRoleCompanies', $allRoleCompanies)
            ->with('allRoleCustomers', $allRoleCustomers)
            ->with('allRoleOrders', $allRoleOrders)
            ->with('allRoleProducts', $allRoleProducts)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('currentUser', $currentUser);
    }


    public function createUserAdmin(Request $request)
    {
        $this->authLogin('admin');

        // Validate input data
        if (!$this->validateName($request['name']) ||
            !$this->validateEmail($request['email']) ||
            !$this->validatePhone($request['phone']) ||
            !$this->validateImgFile($request->file('avatar'))) {

            $data = [
                'message' => 'Không đúng định dạng dữ liệu!',
                'success' => false
            ];
            return response()->json($data);
        }

        $companyUserRole = Role::query()
            ->where('collection', 'company')
            ->where('name', 'user')
            ->first();
        $customerUserRole = Role::query()
            ->where('collection', 'customer')
            ->where('name', 'user')
            ->first();
        $orderUserRole = Role::query()
            ->where('collection', 'order')
            ->where('name', 'user')
            ->first();
        $productUserRole = Role::query()
            ->where('collection', 'product')
            ->where('name', 'user')
            ->first();

        // Check duplicated email
        $duplicatedUser = User::query()
            ->where('email.hashed', md5($request['email']))
            ->first();
        if (isset($duplicatedUser)) {
            return response()->json([
                'message' => 'Tạo người dùng mới thất bại! Email ' . $request['email'] . ' đã được sử dụng.',
                'success' => false
            ]);
        }

        $newUser = new User([
            'id' => @User::getLastUserId() + 1,
            'name' => @$request['name'],
            'email' => isset($request['email']) ? [
                'encrypted' => Crypt::encrypt($request['email']),
                'hashed' => md5($request['email'])
            ] : null,
            'gender' => @$request['gender'],
            'password' => @md5($request['password']),
            'phone' => isset($request['phone']) ? [
                'encrypted' => Crypt::encrypt($request['phone']),
                'hashed' => md5($request['phone'])
            ] : null,
            'super_admin' => @$request['super_admin'],
            'role_id' => [
                'company' => isset($request['role_company']) ? $request['role_company'] : $companyUserRole['_id'],
                'customer' => isset($request['role_customer']) ? $request['role_customer'] : $customerUserRole['_id'],
                'order' => isset($request['role_order']) ? $request['role_order'] : $orderUserRole['_id'],
                'product' => isset($request['role_product']) ? $request['role_product'] : $productUserRole['_id'],
            ]
        ]);

        $avatar = $request->file('avatar');
        if ($avatar) {
            $avatarName = $avatar->getClientOriginalName();
            $request['avatar']->storeAs('images', $avatarName, 'public');
            $newUser['avatar'] = $avatarName;
        }

        if ($newUser->save()) {
            $data = [
                'message' => 'Tạo người dùng mới thành công!',
                'success' => true
            ];
            return response()->json($data);

        } else {
            $data = [
                'message' => 'Tạo người dùng mới thất bại!',
                'success' => false
            ];
            return response()->json($data);
        }
    }


    public function searchUser(Request $request)
    {
        $currentUser = $this->authLogin('admin');

        $search_field = isset($request['search_field']) ? $request['search_field'] : 'id';
        $search_value = $request['search_value'];

        // Get data
        if ($search_field == 'id') {
            try {
                $search_value += 0;
            } catch (Exception $e) {}
            $allUsers = User::query()
                ->where($search_field, $search_value)
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else if (in_array($search_field, $this->encryptedFields)) {
            $allUsers = User::query()
                ->where($search_field . '.hashed', md5($search_value))
                ->orderBy('id', 'desc')
                ->paginate(10);
        } elseif ($search_field == null) {
            try {
                if (in_array($search_value, ['Super Admin', 'super admin'])) {
                    $admin = "1";
                } elseif (in_array($search_value, ['User', 'user'])) {
                    $admin = "0";
                } else {
                    $admin = "-1";
                }
                try {
                    $time = new Carbon($search_value);
                } catch (Exception $e) {
                    $time = null;
                }
                $allUsers = User::query()
                    ->where([
                        '$or' => [
                            ['$text' => ['$search' => $search_value]],
                            ['name' => ['$regex' => $search_value, '$options' => 'i']],
                            ['gender' => ['$regex' => $search_value, '$options' => 'i']],
                            ['email.hashed' => md5($search_value)],
                            ['phone.hashed' => md5($search_value)],
                            ['created_at' => $time],
                            ['updated_at' => $time]
                        ]
                    ])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            } catch (Exception $e) {
                $allUsers = User::query()
                    ->where('id', '-1')
                    ->paginate(10);
            }
        } else {
            $allUsers = User::query()
                ->where($search_field, 'regexp', '/' . $search_value . '/i')
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        // Fix data
        foreach ($allUsers as $user) {
            $user->company_role = isset($user->role_id['company']) ?
                Role::query()->find($user->role_id['company']) : null;
            $user->customer_role = isset($user->role_id['customer']) ?
                Role::query()->find($user->role_id['customer']) : null;
            $user->order_role = isset($user->role_id['order']) ?
                Role::query()->find($user->role_id['order']) : null;
            $user->product_role = isset($user->role_id['product']) ?
                Role::query()->find($user->role_id['product']) : null;
            try {
                $user['email'] = isset($user['email']['encrypted']) ? Crypt::decrypt($user['email']['encrypted']) : null;
                $user['phone'] = isset($user['phone']['encrypted']) ? Crypt::decrypt($user['phone']['encrypted']) : null;
            } catch (Exception $e) {
            }
        }

        $allRoleCompanies = Role::query()->where('collection', 'company')->get();
        $allRoleCustomers = Role::query()->where('collection', 'customer')->get();
        $allRoleOrders = Role::query()->where('collection', 'order')->get();
        $allRoleProducts = Role::query()->where('collection', 'product')->get();
        $allFieldLabels = Label::query()
            ->where('collection', 'user')
            ->first();

        return view('admin/users')
            ->with('userList', $allUsers)
            ->with('allRoleCompanies', $allRoleCompanies)
            ->with('allRoleCustomers', $allRoleCustomers)
            ->with('allRoleOrders', $allRoleOrders)
            ->with('allRoleProducts', $allRoleProducts)
            ->with('allFieldLabels', @$allFieldLabels['labels'])
            ->with('currentUser', $currentUser)
            ->with('search_field', $search_field)
            ->with('search_value', $search_value);
    }


    public function saveUserAdmin(Request $request, $_id)
    {
        $currentUser = $this->authLogin('admin');

        // Check isset $_id
        $user = User::query()->find($_id);
        if (!isset($_id) || !isset($user)) {
            Session::put('errorMessage', 'Sửa thông tin người dùng thất bại! Không tìm thấy dữ liệu.');
            return redirect('admin/users');
        }

        // Validate input data
        if (!$this->validateName($request['name']) ||
            !$this->validateEmail($request['email']) ||
            !$this->validatePhone($request['phone']) ||
            !$this->validateImgFile($request->file('avatar'))) {

            return redirect('admin/users')
                ->with('errorMessage', "Không đúng định dạng dữ liệu!");
        }

        // Check duplicated email
        $duplicatedUser = User::query()
            ->where('email.hashed', md5($request['email']))
            ->where('_id', '!=', $_id)
            ->first();
        if (isset($duplicatedUser)) {
            return response()->json([
                'message' => 'Tạo người dùng mới thất bại! Email ' . $request['email'] . ' đã được sử dụng.',
                'success' => false
            ]);
        }


        $user['name'] = $request['name'];
        $user['gender'] = $request['gender'];
        $user['email'] = $user['email'];
        if (isset($request['password']) && $request['password']) {
            $user['password'] = md5($request['password']);
        }
        $user['phone'] = isset($request['phone']) ? [
            'encrypted' => Crypt::encrypt($request['phone']),
            'hashed' => md5($request['phone'])
        ] : null;
        $user['super_admin'] = $request['super_admin'];
        $user['role_id'] = [
            'company' => $request['role_company'],
            'customer' => $request['role_customer'],
            'order' => $request['role_order'],
            'product' => $request['role_product'],
        ];
        $avatar = $request->file('avatar');
        if ($avatar) {
            $avatarName = $avatar->getClientOriginalName();
            $request['avatar']->storeAs('images', $avatarName, 'public');
            $user['avatar'] = $avatarName;
        }

        if ($user->save()) {
            if ($user['_id'] == $currentUser->_id) {
                Session::put('currentUser', $user);
            }
            return [
                'message' => 'Sửa thông tin người dùng thành công!',
                'success' => true
            ];
        } else {
            return [
                'message' => 'Sửa thông tin người dùng thất bại!',
                'success' => false
            ];
        }
    }


    public function deleteUserAdmin($_id)
    {
        $this->authLogin('admin');

        $user = User::query()->find($_id);
        try {
            if (isset($user) && $user->delete()) {
                $data = [
                    'message' => 'Xóa người dùng thành công!',
                    'success' => true
                ];
                return response()->json($data);

            }
        } catch (Exception $e) {

        }

        $data = [
            'message' => 'Xóa người dùng thất bại!',
            'success' => false
        ];

        return response()->json($data);

    }


    public function deleteAllUserAdmin(Request $request)
    {
        $this->authLogin('admin');
        $error = false;

        $userList = User::query()
            ->whereIn('_id', $request['idList'])
            ->get();

        foreach ($userList as $user) {
            if (!isset($user)) {
                continue;
            }

            try {
                if (!$user->delete()) {
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


    public function getDataToEditUserAdmin($_id)
    {
        $this->authLogin('admin');

        $editUser = User::query()->find($_id);

        if (isset($editUser)) {
            try {
                $editUser['email'] = Crypt::decrypt($editUser['email']['encrypted']);
            } catch (Exception $e) {
                $editUser['email'] = null;
            }
            try {
                $editUser['phone'] = Crypt::decrypt($editUser['phone']['encrypted']);
            } catch (Exception $e) {
                $editUser['phone'] = null;
            }
            return response()->json($editUser);
        } else {
            return null;
        }
    }
}

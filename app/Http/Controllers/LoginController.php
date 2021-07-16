<?php

namespace App\Http\Controllers;

use App\Models\Tmp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function login()
    {
        return view('login.login');
    }

    public function loginConfirm(Request $request)
    {
        $user = User::query()
            ->where('email.hashed', md5($request['email']))
            ->first();

        if (isset($user)) {
            if ($user['password'] == md5($request['password'])) {
                $user['email'] = isset($user['email']) ? Crypt::decrypt($user['email']['encrypted']) : null;
                $user['phone'] = isset($user['phone']) ? Crypt::decrypt($user['phone']['encrypted']) : null;
                Session::put('currentUser', $user);
                return [
                    'success' => true,
                    'url' => 'home'
                ];
            } else {
                return ['message' => 'Sai mật khẩu!'];
            }
        } else {
            return ['message' => 'Email không tồn tại hoặc nhập sai email!'];
        }
    }

    public function logout()
    {
        $currentUser = $this->authLogin('');

        $this->deleteRedundantData($currentUser->_id, null);
        Session::flush();

        return redirect('login');
    }

    public function signup()
    {
        return view('login.signup');
    }


    public function deleteRedundantData($_id, $key)
    {
        $currentUser = $this->authLogin('');

        if ($currentUser->_id == $_id && $key) {
            Tmp::query()->where('current_user', $_id)->where('random_key', $key)->delete();
        } elseif($currentUser->_id == $_id && !$key) {
            Tmp::query()->where('current_user', $_id)->delete();
        }
    }

}

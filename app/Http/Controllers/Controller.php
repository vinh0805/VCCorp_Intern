<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tmp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $currentYear = 2021;
    public $encryptedFields = ['phone', 'email'];

    // Auth
    public function checkUser()
    {
        return Session::get('currentUser') ? Session::get('currentUser') : redirect('login')->send();
    }

    public function checkAdmin()
    {
        $currentUser = Session::get('currentUser');

        if (!$currentUser) {
            return redirect('login')->send();
        } else if (!$currentUser->super_admin) {
            return redirect('permission-error')
                ->with('errorMessage', 'Chỉ có admin mới có quyền truy cập trang này!')
                ->send();
        } else return $currentUser;
    }

    public function authLogin($isAdmin)
    {
        if ($isAdmin == 'admin') {
            return $this->checkAdmin();
        } else {
            return $this->checkUser();
        }
    }


    // Validate
    public function validateName($name)
    {
        if ($name) {
            if (strlen($name) > 50) {
                return false;
            }
            return preg_match("/^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i", $name);
        } else {
            // name is required
            return false;
        }
    }

    public function validateVietnameseCharacters($text)
    {
        if ($text) {
            return preg_match("/^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+)$/i", $text);
        } else {
            return true;
        }
    }

    public function validateAddress($text)
    {
        if ($text) {
            return preg_match("/^([a-zA-Z0-9ÀÁÂÃÈÉÊẾÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêếìíòóôõùúăđĩũơƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỂưạảấầẩẫậắằẳẵặẹẻẽềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ \-,.\/\s]+)$/i", $text);
        } else {
            return true;
        }
    }

    public function validateBirth($birth)
    {
        if (!$birth) {
            return true;
        }

        return preg_match("/^(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)\d\d$/", $birth);

    }

    public function validateEmail($email)
    {
        if ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        } else {
            return true;
        }
    }

    public function validatePhone($phone)
    {
        if (!isset($phone) || $phone == '') {
            return true;
        }

        $phone = strval($phone);

        if (strlen($phone) < 8 || strlen($phone) > 14) {
            return false;
        }
        return preg_match("/^([0]|[8][4]|[+][8][4])[1-9 ][0-9 ]*$/", $phone);
//        return preg_match("/^[0-9 \-+.]*$/", $phone);
//        return preg_match("/^[0][1-9]{2} [0-9]{3} [0-9]{4}$/", $phone);
    }

    public function validateTax($tax) {
        if (isset($tax)) {
            if (is_numeric($tax)) {
                if ($tax >= 0 || $tax <= 1000) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function validatePrice($price) {
        if (isset($price)) {
            if (is_numeric($price)) {
                if ($price < 1000) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validateNumber($n) {
        if (isset($n)) {
            if (is_numeric($n)) {
                if ($n < 1) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function validateImgFile($file)
    {
        if (!isset($file)) {
            return true;
        }
        if (filesize($file) > 2097152) {
            return false;
        }
        return in_array($file->getClientOriginalExtension(), ['jpg', 'png']);
    }

    public function validateUserId($user_id)
    {
        if (!isset($user_id)) {
            return false;
        }

        if (!is_array($user_id)) {
            return false;
        }

        foreach ($user_id as $_id) {
            if (!is_string($_id) || strlen($_id) != 24 ||  !preg_match("/^([a-z0-9]+)$/i", $_id)) {
                return false;
            }
        }

        return true;
    }

    public function validateDate($date)
    {
        if (!isset($date)) {
            return true;
        }

        return preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/(19|20)\d\d$/", $date);
    }

    public function validateStatus($status) {
        return in_array($status, ['Đang hoạt động', 'Không hoạt động', 'Có sẵn', 'Không có sẵn', null]);
    }

    public function validateGender($gender) {
        if (!isset($gender)) {
            return true;
        }
        return in_array($gender, ['Nam', 'Nữ', 'Khác']);
    }

    public function validateISODate($date)
    {
        if (!isset($date)) {
            return true;
        }
        return preg_match('^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$^', $date);
    }

    public function checkAuthorize($currentUser, $collection, $permission)
    {
        // Check isset role
        if (!isset($currentUser->role_id) || !isset($currentUser->role_id[$collection])) {
            return false;
        }

        $role = Role::query()->find($currentUser->role_id[$collection]);

        if (!isset($role) || !in_array($permission, $role['permission_list'])) {
            return false;
        }

        return true;
    }

    public function checkReadPermission($currentUser, $collection)
    {
        // Check isset role

        if (!isset($currentUser->role_id) || !isset($currentUser->role_id[$collection])) {
            return false;
        }

        $role = Role::query()->find($currentUser['role_id'][$collection]);

        if (!$role || $role['permission_list'] == [] || $role['permission_list'] == null) {
            return false;
        }

        return true;

    }

    public function checkReadAllPermission($currentUser, $collection)
    {
        // Check isset role
        if (!isset($currentUser->role_id) || !isset($currentUser->role_id[$collection])) {
            return false;
        }

        $role = Role::query()->find($currentUser->role_id[$collection]);

        if (!isset($role) || $role['permission_list'] == [] || $role['permission_list'] == null) {
            return false;
        }

        if (count(array_intersect($role['permission_list'], ['read_all', 'update_all', 'delete_all', 'export_all'])) > 0) {
            return true;
        }

        return false;
    }

    public function checkFormatData($field, $value)
    {
        if (!isset($field)) {
            return false;
        }

        switch ($field) {
            case 'id' :
            case 'remain':
                return $this->validateNumber($value);
            case 'name':
                return $this->validateName($value);
            case 'code':
                return true;
            case 'field':
            case 'job':
                return $this->validateVietnameseCharacters($value);
            case 'address':
                return $this->validateAddress($value);
            case 'email':
                return $this->validateEmail($value);
            case 'phone':
                return $this->validatePhone($value);
            case 'user_id':
                return $this->validateUserId($value);
            case 'status':
                return $this->validateStatus($value);
            case 'created_at':
            case 'updated_at':
                return $this->validateISODate($value);
            case 'birth':
                return $this->validateDate($value);
            case 'gender':
                return $this->validateGender($value);
            case 'price':
                return $this->validatePrice($value);

            default: return false;
        }
    }


    // Import
    public function saveDataFromExcelToTmp($collection, $excelData, $random_key, $fields_in_db, $check_field_require)
    {
        $currentUser = $this->authLogin('');

        for ($i = 1; $i < count($excelData); $i++) {

            // create tmp data
            if ($collection == 'company') {
                $newTmp = new Tmp([
                    'collection' => 'company',
                    'excel_row' => $i + 1,
                    'id' => "1",
                    'name' => null,
                    'code' => null,
                    'field' => null,
                    'address' => null,
                    'email' => null,
                    'phone' => null,
                    'user_id' => [],
                    'status' => null,
                    'current_user' => $currentUser->_id,
                    'random_key' => $random_key,
                    'duplicated_fields' => [],
                    'duplicated_record' => null,
                    'required_fields' => [],
                    'wrong_format' => []
                ]);
            } elseif ($collection == 'customer') {
                $newTmp = new Tmp([
                    'collection' => 'customer',
                    'excel_row' => $i + 1,
                    'id' => "1",
                    'birth' => null,
                    'gender' => null,
                    'job' => null,
                    'address' => null,
                    'email' => null,
                    'phone' => null,
                    'user_id' => [],
                    'status' => null,
                    'current_user' => $currentUser->_id,
                    'random_key' => $random_key,
                    'duplicated_fields' => [],
                    'duplicated_record' => null,
                    'required_fields' => [],
                    'wrong_format' => []
                ]);
            } elseif ($collection == 'product') {
                $newTmp = new Tmp([
                    'collection' => 'product',
                    'excel_row' => $i + 1,
                    'id' => "1",
                    'name' => null,
                    'code' => null,
                    'price' => null,
                    'image' => null,
                    'remain' => null,
                    'user_id' => [],
                    'status' => null,
                    'current_user' => $currentUser->_id,
                    'random_key' => $random_key,
                    'duplicated_fields' => [],
                    'duplicated_record' => null,
                    'required_fields' => [],
                    'wrong_format' => []
                ]);
            } else {
                return false;
            }

            // Set data from request for tmp data
            $tmpArray = [];
            foreach ($fields_in_db as $key => $field) {
                if (!isset($field)) {
                    continue;
                }
                // Check data's format
                if ($this->checkFormatData($field, $excelData[$i][$key])) {
                    $newTmp[$field] = $excelData[$i][$key];

                } elseif (!in_array($field, $newTmp['wrong_format'])) {
                    array_push($tmpArray, $field);
                }
            }
            $newTmp['wrong_format'] = $tmpArray;
            $tmpArray = [];


            // Check required fields
            foreach ($check_field_require as $required_key) { // $required_key: 1, 2
                $field = $fields_in_db[$required_key];  // id, name

                if (!$excelData[$i][$required_key] && !in_array($field, $newTmp['required_fields'])) {
                    array_push($tmpArray, $field);
                }
            }
            $newTmp['required_fields'] = $tmpArray;
            $tmpArray = [];

            $newTmp['duplicated_fields'] = [];

            $newTmp->save();
        }
    }
}

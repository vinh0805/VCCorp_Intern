<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = ['id', 'name', 'email', 'gender', 'password', 'phone', 'avatar', 'super_admin', 'role_id'];


    public static function getLastUserId()
    {
        $lastUser = User::query()
            ->orderBy('id', 'desc')
            ->first();
        return isset($lastUser) && isset($lastUser->id) ? $lastUser->id : 0;
    }

    public static function getAllDecryptedData($encryptedFields)
    {
        $allUsers = User::query()
            ->get($encryptedFields);

        foreach ($allUsers as $user) {
            foreach ($encryptedFields as $field) {
                // Try to decrypt data
                try {
                    $user[$field] = Crypt::decrypt($user[$field]);
                } catch (\Exception $e) {

                }
            }
        }

        return $allUsers;
    }
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function permissionRole()
    {
        return $this->hasMany(PermissionRole::class);
    }
}

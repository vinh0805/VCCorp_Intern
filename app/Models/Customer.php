<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Mongodb\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'customers';

    protected $fillable = ['id', 'name', 'birth', 'gender', 'job', 'address', 'email', 'phone', 'company_id', 'user_id', 'status'];

    public static function getLastCustomerId()
    {
        $lastCustomer = Customer::query()->orderBy('id', 'desc')->first();
        return isset($lastCustomer) && isset($lastCustomer->id) ? $lastCustomer->id : 0;
    }

    public static function getAllCustomerOfUser($userId)
    {
        return Customer::query()->where('user_id', $userId)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getAllDecryptedData($encryptedFields)
    {
        $allEndcryptedData = Customer::query()
            ->get($encryptedFields);

        foreach ($allEndcryptedData as $customer) {
            foreach ($encryptedFields as $field) {
                // Try to decrypt data
                try {
                    $customer[$field] = Crypt::decrypt($customer[$field]);
                } catch (Exception $e) {

                }
            }
        }

        return $allEndcryptedData;
    }

}

<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Jenssegers\Mongodb\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'companies';

    protected $fillable = ['id', 'name', 'code', 'field', 'address', 'email', 'phone', 'user_id', 'status'];

    public static function getAllCompaniesOfUser($id)
    {
        return Company::query()->where('user_id', $id)->get();
    }

    public static function getCompanyIdByName($name)
    {
        $company = Company::query()->where('name', $name)->first();
        return isset($company) ? $company->_id : null;
    }

    public static function getLastCompanyId()
    {
        $lastCompany = Company::query()->orderBy('id', 'desc')->first();
        return isset($lastCompany) && isset($lastCompany->id) ? $lastCompany->id : 0;
    }

    public static function getAllDecryptedData($encryptedFields)
    {
        $allDecryptedCompanies = Company::query()
            ->get($encryptedFields);

        foreach ($allDecryptedCompanies as $company) {
            foreach ($encryptedFields as $field) {
                // Try to decrypt data
                try {
                    $company[$field] = Crypt::decrypt($company[$field]);
                } catch (\Exception $e) {

                }
            }
        }

        return $allDecryptedCompanies;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function permissionRole()
    {
        return $this->hasMany(PermissionRole::class);
    }


}

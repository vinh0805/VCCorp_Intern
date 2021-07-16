<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class PermissionRole extends Model
{
    public $timestamps = false;

    protected $connection = 'mongodb';
    protected $collection = 'permission_role';

    protected $fillable = ['user_id', 'record_id', 'permission_id', 'collection'];

    public static function getAllCompanies()
    {
        return PermissionRole::all()->each(
            function ($item) {
                $company = Company::find($item->record_id);
                if (isset($company)) {
                    $tmpData['company'] = $company;
                    $tmpData['user'] = User::find($item->user_id);
                    $tmpData['permission'] = Permission::find($item->permission_id);
                    return $tmpData;
                }
            }
        );
    }

    public static function getAllCompaniesWithReadPermission($user, $limit, $start)
    {
//        $read = Permission::where('name', '=', 'read')->first();
//
//        return self::getAllCompanies()->where('permission', '=', $read)
//            ->where('user', $user);

        $permission_id = Permission::where('name', 'read')->first()->_id;

        return $tmp = PermissionRole::where('user_id', $user->_id)
            ->where('collection', 'company')
            ->where('permission_id', $permission_id)
//            ->skip($start)
//            ->paginate($limit);
            ->get();

//        $company_id_list = [];
//        foreach ($tmp as $item) {
//            array_push($company_id_list ,$item->record_id);
//        }
//
//        return Company::whereIn('_id', $company_id_list)->get();
    }

    public static function getAllCustomers()
    {
        return PermissionRole::all()->map(
            function ($item) {
                $customer = Customer::find($item->record_id);
                if (isset($customer)) {
                    $tmpData['customer'] = $customer;
                    $tmpData['user'] = User::find($item->user_id);
                    $tmpData['permission'] = Permission::find($item->permission_id);
                    return $tmpData;
                }
            }
        );
    }

    public static function getAllCustomersWithReadPermission($user)
    {
        $read = Permission::where('name', '=', 'read')->first();

        return self::getAllCustomers()->where('permission', '=', $read)
            ->where('user', '=', $user);
    }

    public static function getAllOrders()
    {
        return PermissionRole::all()->map(
            function ($item) {
                $order = Order::find($item->record_id);
                if (isset($order)) {
                    $tmpData['order'] = $order;
                    $tmpData['user'] = User::find($item->user_id);
                    $tmpData['permission'] = Permission::find($item->permission_id);
                    return $tmpData;
                }
            }
        );

    }

    public static function getAllOrdersWithReadPermission($user)
    {
        $read = Permission::where('name', '=', 'read')->first();

        return self::getAllOrders()->where('permission', '=', $read)
            ->where('user', $user);
    }

    public static function getAllProducts()
    {
        return PermissionRole::all()->map(
            function ($item) {
                $product = Product::find($item->record_id);
                if (isset($product)) {
                    $tmpData['product'] = $product;
                    $tmpData['user'] = User::find($item->user_id);
                    $tmpData['permission'] = Permission::find($item->permission_id);
                    return $tmpData;
                }
            }
        );
    }

    public static function getAllProductsWithReadPermission($user)
    {
        $read = Permission::where('name', '=', 'read')->first();

        return self::getAllProducts()->where('permission', '=', $read)
            ->where('user', $user);
    }

    public static function getDataToCheckPermission($user_id, $record_id, $permission)
    {
        return PermissionRole::where('user_id', $user_id)
            ->where('record_id', $record_id)
            ->where('permission_id', Permission::where('name', $permission)->first()->_id)
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'record_id');
    }

}

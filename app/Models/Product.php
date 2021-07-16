<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = ['id', 'name', 'code', 'price', 'image', 'remain', 'user_id', 'status'];

    public static function getAllProductsOfUser($_id)
    {
        return Product::query()->where('user_id', $_id)->get();
    }

    public static function getLastProductId()
    {
        $lastProduct = Product::query()->orderBy('id', 'desc')->first();
        return isset($lastProduct) && isset($lastProduct->id) ? $lastProduct->id : 0;
    }

    public static function getAvailableProducts()
    {
        return Product::query()->where('status', 'Có sẵn')->get();
    }

    public static function getAvailableProductsOfUser($_id)
    {
        return Product::query()->where('status', 'Có sẵn')->where('user_id', $_id)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissionRole()
    {
        return $this->hasMany(PermissionRole::class);
    }

}

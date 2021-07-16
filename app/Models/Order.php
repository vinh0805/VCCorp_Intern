<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $guarded = [];
//    protected $fillable = ['id', 'customer_id', 'company_id', 'products', 'price', 'tax', 'total_price', 'time', 'address', 'user_id', 'status'];

    public static function getAllOrdersOfUser($id)
    {
        return Order::query()->where('user_id', $id)->get();
    }

    public static function getLastOrderId()
    {
        $lastOrder = Order::query()->orderBy('id', 'desc')->first();
        return isset($lastOrder) && isset($lastOrder->id) ? $lastOrder->id : 0;
    }
}

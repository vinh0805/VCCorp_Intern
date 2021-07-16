<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Tmp extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tmp';

    protected $guarded = [];

    public static function getTmpData($collection, $user_id, $random_key)
    {
        return Tmp::query()
            ->where('collection', $collection)
            ->where('current_user', $user_id)
            ->where('random_key', $random_key)
            ->get();
    }
}

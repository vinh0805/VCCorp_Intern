<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    protected $fillable = ['id', 'name', 'collection', 'permission_list'];

    public static function getLastRoleId()
    {
        $lastRole = Role::query()->orderBy('id', 'desc')->first();
        return isset($lastRole) && isset($lastRole->id) ? $lastRole->id : 0;
    }

}

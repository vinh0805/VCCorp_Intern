<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;

    protected $connection = 'mongodb';
    protected $collection = 'permission';

    protected $fillable = ['name'];

    public function permission()
    {
        return $this->hasMany(PermissionRole::class);
    }
}

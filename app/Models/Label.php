<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Label extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'labels';
    protected $guarded = [];

}

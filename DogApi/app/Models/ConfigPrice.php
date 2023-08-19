<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigPrice extends Model
{
    protected $table = 'config_price';

    protected $fillable = [
        'price'
    ];
}

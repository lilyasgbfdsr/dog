<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateTime extends Model
{
    protected $fillable = [
        'sort',
        'start_time',
        'end_time'
    ];
}

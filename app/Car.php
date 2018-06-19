<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'board',
        'model',
        'manufacturer',
        'color',
        'year_manufacture',
        'capacity',
        'user_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}

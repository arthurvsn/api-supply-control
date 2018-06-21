<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suplly extends Model
{
    protected $fillable = [
        'liters',
        'amount',
        'type',
        'date_supply',
        'car_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}

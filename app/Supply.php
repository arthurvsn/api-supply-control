<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'liters',
        'amount',
        'type',
        'fuel_price',
        'date_supply',
        'car_id',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}

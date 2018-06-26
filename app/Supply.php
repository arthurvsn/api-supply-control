<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public function getExpenses($dateStart, $dateEnd, $carID)
    {
        $valueAmount = DB::table('supplies')
            ->select(DB::raw('SUM(amount) as valueAmount'))
            ->whereBetween('date_supply', [$dateStart, $dateEnd])
            ->where('car_id', $carID)
            ->first();
        
        return $valueAmount;
    }
}

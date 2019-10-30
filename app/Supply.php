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
        'car_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Table cars relationship with supplies
     */
    public function cars()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Table cars relationship with supplies
     */
    public function typeFuels()
    {
        return $this->belongsTo(TypeFuel::class);
    }

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

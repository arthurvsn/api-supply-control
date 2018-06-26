<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    /**
     * Get all cars by user ID on database
     * @param int $userID
     * @return object $cars
     */
    public function getAllCarsByUser($userID)
    {
        $cars = DB::table('cars')
            ->select('id', 'board', 'model', 'manufacturer', 'color', 'year_manufacture', 'capacity')
            ->where('user_id', '=', $userID)
            ->get();

        return $cars;
    }
}

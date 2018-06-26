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
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Table user relationship with cars
     */
    public function users()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Table supplies relationship with car
     */
    public function supplies()
    {
        return $this->hasMany('App\Supply');
    }
}

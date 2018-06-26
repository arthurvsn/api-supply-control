<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Phone extends Model
{
    protected $fillable = [
        'country_code',
        'number',
        'user_id',
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Table user relationship with phones
     */
    public function users()
    {
        return $this->belongsTo('App\User');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Address extends Model
{
    protected $fillable = [
        'street',
        'city',
        'state',
        'zip_code',
        'country',
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
     * Table user relationship with addresses
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}

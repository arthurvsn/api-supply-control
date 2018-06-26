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
        'user_id',
    ];

    protected $hidden = [
        'user_id',
        'updated_at',
        'deleted_at'
    ];

    public function users()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * Get information address o user
     * @param int $userId
     * @return object $address
     */
    public function getAddressUser($userId)
    {
        $address = DB::table('addresses')
            ->select('street', 'city', 'state', 'zip_code','country')
            ->where('user_id', '=', $userId)
            ->get();
        
        return $address;
    }
}

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
        'updated_at',
        'deleted_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get number phones according id user
     * @param int $userId
     * @return object $phones
     */
    public function getPhoneUser($userId)
    {
        $phones = DB::table('phones')
            ->select('country_code', 'number')
            ->where('user_id', '=', $userId)
            ->get();
        
        return $phones;
    }
}

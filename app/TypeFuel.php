<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeFuel extends Model
{
    protected $fillable = [
        'type'
    ];

    /**
     * Table supplies relationship with type fuel
     */
    public function supplies()
    {
        return $this->hasMany('App\Supply');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeFuel extends Model
{
    protected $fillable = [
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Table supplies relationship with type fuel
     */
    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}

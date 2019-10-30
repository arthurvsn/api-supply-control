<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'username',
        'email',
        'profile_picture',
        'password', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'updated_at',
        'deleted_at'
    ];

    /**
     * Table addresses relationship with user
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Table phones relationship with user
     */
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Table cars relationship with user
     */
    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}

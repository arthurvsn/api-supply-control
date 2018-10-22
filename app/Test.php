<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use YourAppRocks\EloquentUuid\Traits\HasUuid;

class Test extends Model
{
    use HasUuid;

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $fillable = [
        'name'
    ];
}

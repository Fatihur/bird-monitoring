<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = [
        'relay',
        'buzzer',
        'all_off',
        'pir',
        'acknowledged',
    ];
}

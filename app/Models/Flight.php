<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'airline',
        'from_city',
        'to_city',
        'departure_time',
        'arrival_time',
        'duration',
        'price',
        'class',
    ];
}

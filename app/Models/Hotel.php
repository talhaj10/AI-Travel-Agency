<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'city',
        'price_per_night',
        'rating',
        'amenities',
        'image',
    ];
}

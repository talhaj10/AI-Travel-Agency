<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reference_id',
        'total_price',
        'booking_date',
        'status',
    ];
}

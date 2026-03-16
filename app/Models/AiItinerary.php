<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiItinerary extends Model
{
    protected $fillable = [
        'user_id',
        'destination',
        'days',
        'budget',
        'interests',
        'travel_type',
        'generated_plan',
    ];
}

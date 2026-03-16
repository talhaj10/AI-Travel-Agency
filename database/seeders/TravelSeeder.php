<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mock Flights
        \App\Models\Flight::create([
            'airline' => 'IndiGo',
            'from_city' => 'Delhi',
            'to_city' => 'Goa',
            'departure_time' => now()->addDays(2)->setTime(10, 30),
            'arrival_time' => now()->addDays(2)->setTime(13, 00),
            'duration' => '2h 30m',
            'price' => 4500.00,
            'class' => 'Economy'
        ]);

        \App\Models\Flight::create([
            'airline' => 'Air India',
            'from_city' => 'Delhi',
            'to_city' => 'Goa',
            'departure_time' => now()->addDays(2)->setTime(15, 00),
            'arrival_time' => now()->addDays(2)->setTime(17, 45),
            'duration' => '2h 45m',
            'price' => 5200.00,
            'class' => 'Economy'
        ]);

        // Mock Hotels
        \App\Models\Hotel::create([
            'name' => 'Taj Exotica Resort',
            'city' => 'Goa',
            'price_per_night' => 12000.00,
            'rating' => 4.8,
            'amenities' => 'Pool, Spa, Beach Access, Free WiFi',
            'image' => 'hotel_taj.png'
        ]);

        \App\Models\Hotel::create([
            'name' => 'Novotel Goa Resort',
            'city' => 'Goa',
            'price_per_night' => 8500.00,
            'rating' => 4.5,
            'amenities' => 'Pool, Gym, Free Breakfast',
            'image' => 'hotel_novotel.png'
        ]);
    }
}

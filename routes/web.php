<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TripPlannerController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Unified Trip Planner (main flow)
Route::post('/trip/plan', [TripPlannerController::class, 'plan'])->name('trip.plan');
Route::post('/trip/save-itinerary', [TripPlannerController::class, 'saveItinerary'])->name('trip.saveItinerary');
Route::get('/api/airports/search', [TripPlannerController::class, 'searchAirports'])->name('api.airports.search');

// Legacy Search Routes (kept for direct access)
Route::get('/search/flights', [TravelController::class, 'searchFlights'])->name('search.flights');
Route::get('/search/hotels', [TravelController::class, 'searchHotels'])->name('search.hotels');

// AI Itinerary Routes
Route::get('/ai-planner', [ItineraryController::class, 'index'])->name('ai.planner');
Route::post('/ai-planner/generate', [ItineraryController::class, 'generate'])->name('ai.generate');
Route::get('/ai-planner/{itinerary}', [ItineraryController::class, 'show'])->name('ai.show');
Route::post('/ai-planner/{itinerary}/regenerate', [ItineraryController::class, 'regenerate'])->name('ai.regenerate');

// Booking Routes
Route::post('/book', [BookingController::class, 'store'])->name('book.store');
Route::get('/checkout/{booking}', [BookingController::class, 'checkout'])->name('checkout');
Route::post('/payment/process', [BookingController::class, 'processPayment'])->name('payment.process');
Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// Admin Routes (Placeholder for now)
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', function () {
        return "Admin Dashboard";
    });
});

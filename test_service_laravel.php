<?php

use App\Services\SearchApiService;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(SearchApiService::class);
echo "Testing SearchApiService...\n";

$flights = $service->fetchFlights('BOM', 'DEL', '2026-03-25');

echo "Found " . $flights->count() . " flights.\n";
foreach ($flights->take(1) as $index => $flight) {
    echo "Flight " . ($index + 1) . ": " . $flight->airline . " (" . $flight->flight_number . ")\n";
    echo "  From: " . $flight->from_city . " (" . $flight->from_code . ")\n";
    echo "  To: " . $flight->to_city . " (" . $flight->to_code . ")\n";
    echo "  Departure: " . $flight->departure_time . "\n";
    echo "  Arrival: " . $flight->arrival_time . "\n";
    echo "  Price: " . $flight->price . " INR\n";
    echo "  Price P/P: " . $flight->price_per_person . " INR\n";
    echo "  Stops: " . $flight->stops . "\n";
    echo "  Airplane: " . $flight->airplane . "\n";
    echo "  Legroom: " . $flight->legroom . "\n";
    echo "  Trip Type: " . $flight->trip_type . "\n";
}

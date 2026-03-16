<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Mocking minimal Laravel environment for standalone test if needed, 
// but easier to just use standard PHP with curl or copy the logic.

$apiKey = 'ec274f8308f518ac6807e26cdf8ada9b87eee644ba564ef8ef642562a7cc2412';
$from = 'STV'; // Surat
$to = 'COK';   // Kochi (Kerala)
$date = '2026-03-20';

echo "Testing SerpAPI for Flights from $from to $to on $date...\n";

$params = [
    'engine' => 'google_flights',
    'departure_id' => $from,
    'arrival_id' => $to,
    'outbound_date' => $date,
    'currency' => 'INR',
    'hl' => 'en',
    'gl' => 'in',
    'api_key' => $apiKey,
    'type' => '2', // One way
    'adults' => '1',
];

$url = 'https://serpapi.com/search.json?' . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$json = json_decode($response, true);

if (isset($json['error'])) {
    echo "Error: " . $json['error'] . "\n";
} else {
    $count = 0;
    if (isset($json['best_flights'])) {
        $count += count($json['best_flights']);
        echo "Found " . count($json['best_flights']) . " 'best_flights'.\n";
        foreach ($json['best_flights'] as $flight) {
            $airline = $flight['flights'][0]['airline'] ?? 'Unknown';
            $price = $flight['price'] ?? 'N/A';
            echo " - $airline: ₹$price\n";
        }
    }
    if (isset($json['other_flights'])) {
        $count += count($json['other_flights']);
        echo "Found " . count($json['other_flights']) . " 'other_flights'.\n";
    }

    if ($count == 0) {
        echo "No flights found in response.\n";
        echo "Keys in response: " . implode(', ', array_keys($json)) . "\n";
    }
}

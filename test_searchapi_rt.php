<?php
$apiKey = 'QaRH42bJ2NrAeh6YWBfBjcgy';

$params = [
    'engine' => 'google_flights',
    'departure_id' => 'BOM',
    'arrival_id' => 'DEL',
    'outbound_date' => '2026-03-25',
    'return_date' => '2026-03-30', // Round trip
    'api_key' => $apiKey,
    'flight_type' => 'round_trip',
    'currency' => 'INR',
    'gl' => 'in',
    'hl' => 'en'
];

$url = "https://www.searchapi.io/api/v1/search?" . http_build_query($params);
echo "URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
file_put_contents('e:/xampp/htdocs/Travel Guide/api_res_rt.json', $response);
echo "Written to api_res_rt.json\n";
?>
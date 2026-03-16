<?php
$apiKey = 'QaRH42bJ2NrAeh6YWBfBjcgy';

$params = [
    'engine' => 'google_flights',
    'departure_id' => 'BOM',
    'arrival_id' => 'DEL',
    'outbound_date' => '2026-03-25',
    'api_key' => $apiKey
];

$url = "https://www.searchapi.io/api/v1/search?" . http_build_query($params);
echo "URL: $url\n";

$response = file_get_contents($url);
echo "Response: $response\n";
?>
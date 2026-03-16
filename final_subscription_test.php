<?php
$apiKey = '407e538479msh510cbd21930ed33p1e24d7jsn4395e6d73cc';
$apiHost = 'sky-scrapper.p.rapidapi.com';

function call($url, $host, $key)
{
    echo "Calling: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "X-RapidAPI-Key: $key",
        "X-RapidAPI-Host: $host"
    ));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "HTTP Code: $httpCode\n";
    echo "Response: " . $response . "\n\n";
}

echo "--- TEST 1: AIRPORTS ---\n";
call("https://$apiHost/flights/airports?query=Mumbai", $apiHost, $apiKey);

echo "--- TEST 2: SEARCH V1 ---\n";
$params = http_build_query(['originSkyId' => 'BOM', 'destinationSkyId' => 'DEL', 'date' => '2026-03-15']);
call("https://$apiHost/v1/flights/searchFlights?$params", $apiHost, $apiKey);

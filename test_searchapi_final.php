<?php
$apiKey = 'QaRH42bJ2NrAeh6YWBfBjcgy';

function testSearchApi($from, $to, $date)
{
    global $apiKey;

    echo "Testing SearchApi.io: $from -> $to on $date\n";

    $params = [
        'engine' => 'google_flights',
        'departure_id' => $from,
        'arrival_id' => $to,
        'outbound_date' => $date,
        'currency' => 'INR',
        'hl' => 'en',
        'gl' => 'in',
        'api_key' => $apiKey,
        'type' => 'one_way',
        'adults' => 1
    ];

    $url = "https://www.searchapi.io/api/v1/search?" . http_build_query($params);

    echo "URL: $url\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";
    $json = json_decode($response, true);

    if (isset($json['error'])) {
        echo "Error: " . $json['error'] . "\n";
    } elseif (isset($json['best_flights'])) {
        echo "Success! Found " . count($json['best_flights']) . " best flights.\n";
        $first = $json['best_flights'][0];
        echo "Example: " . ($first['flights'][0]['airline'] ?? 'N/A') . " - INR " . ($first['price'] ?? '0') . "\n";
    } else {
        echo "Response Structure: " . substr($response, 0, 500) . "...\n";
    }
    echo "-----------------------------------\n";
}

testSearchApi('BOM', 'DEL', '2026-03-25');

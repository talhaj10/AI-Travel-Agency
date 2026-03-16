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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL errors if any on Windows

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "CURL Error: $error\n";
    }

    echo "Response: " . $response . "\n";

    $json = json_decode($response, true);
    if (isset($json['error'])) {
        echo "API Error: " . ($json['error']['message'] ?? json_encode($json['error'])) . "\n";
    }
}

testSearchApi('BOM', 'DEL', '2026-03-25');

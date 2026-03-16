<?php
require 'vendor/autoload.php';
use Illuminate\Support\Facades\Http;

// Mocking Laravel environment for a standalone script
function env($key, $default = null) {
    if ($key === 'GEMINI_KEY') return 'AIzaSyA8KUCkFOMcvvNgLUsWopvjr7bPM0Zp1Dc';
    return $default;
}

$apiKey = env('GEMINI_KEY');
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$data = [
    "contents" => [[
        "parts" => [["text" => "Hello, tell me a short joke."]]
    ]]
];

try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status Code: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
$apiKey = 'AIzaSyA8KUCkFOMcvvNgLUsWopvjr7bPM0Zp1Dc';
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
file_put_contents('gemini_models.json', $response);
echo "Done\n";

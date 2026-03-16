<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestAmadeus extends Command
{
    protected $signature = 'test:amadeus';
    protected $description = 'Test Amadeus API Connectivity';

    public function handle()
    {
        $clientId = env('AMADEUS_CLIENT_ID');
        $clientSecret = env('AMADEUS_CLIENT_SECRET');

        $this->info("Checking Amadeus Credentials...");
        $this->line("Client ID: " . ($clientId ? 'SET' : 'MISSING'));
        $this->line("Client Secret: " . ($clientSecret ? 'SET' : 'MISSING'));

        if (!$clientId || !$clientSecret) {
            $this->error("Amadeus credentials are missing in .env");
            return;
        }

        try {
            $this->info("Requesting OAuth2 Token...");
            $response = Http::asForm()->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if ($response->successful()) {
                $token = $response->json()['access_token'];
                $this->success("Successfully obtained Amadeus token!");

                $this->info("Testing Flight Search (DEL to BOM)...");
                $flightResponse = Http::withToken($token)
                    ->get('https://test.api.amadeus.com/v2/shopping/flight-offers', [
                        'originLocationCode' => 'DEL',
                        'destinationLocationCode' => 'BOM',
                        'departureDate' => now()->addDays(14)->format('Y-m-d'),
                        'adults' => 1,
                        'max' => 1,
                        'currencyCode' => 'INR'
                    ]);

                if ($flightResponse->successful()) {
                    $this->success("Flight Search Working! Found " . count($flightResponse->json()['data'] ?? []) . " offer(s).");
                } else {
                    $this->error("Flight Search Failed: " . $flightResponse->status());
                    $this->line($flightResponse->body());
                }
            } else {
                $this->error("Token Request Failed: " . $response->status());
                $this->line($response->body());
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }

    private function success($msg)
    {
        $this->line("<info>SUCCESS:</info> $msg");
    }
}

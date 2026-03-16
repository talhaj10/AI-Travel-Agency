<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchApiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('SEARCHAPI_KEY');
    }

    /**
     * Fetch flights via SearchApi.io (Google Flights Engine)
     */
    public function fetchFlights(string $from, string $to, string $date, int $travelers = 1, int $tripType = 2, ?string $returnDate = null, ?int $maxBudget = null): \Illuminate\Support\Collection
    {
        $flights = collect();

        if (!$this->apiKey) {
            Log::warning('SearchApi key missing in SearchApiService');
            return $flights;
        }

        try {
            $params = [
                'engine' => 'google_flights',
                'departure_id' => $from,
                'arrival_id' => $to,
                'outbound_date' => $date,
                'currency' => 'INR',
                'hl' => 'en',
                'gl' => 'in',
                'api_key' => $this->apiKey,
                'adults' => $travelers,
                'flight_type' => ($tripType == 1) ? 'round_trip' : 'one_way'
            ];

            if ($tripType == 1 && $returnDate) {
                $params['return_date'] = $returnDate;
            }

            $response = Http::timeout(20)->get('https://www.searchapi.io/api/v1/search', $params);

            if ($response->failed()) {
                Log::error("SearchApi Request Failed: " . $response->body());
                return $flights;
            }

            $json = $response->json();

            if (isset($json['error'])) {
                Log::error("SearchApi Error: " . ($json['error']['message'] ?? json_encode($json['error'])));
                return $flights;
            }

            foreach (['best_flights', 'other_flights'] as $section) {
                if (!isset($json[$section]))
                    continue;

                foreach ($json[$section] as $itinerary) {
                    $price = $itinerary['price'] ?? 0;
                    if ($maxBudget && $price > $maxBudget)
                        continue;

                    if (empty($itinerary['flights']))
                        continue;

                    $firstFlight = $itinerary['flights'][0];
                    $lastFlight = end($itinerary['flights']);

                    $flights->push((object) [
                        'id' => $flights->count() + 1,
                        'airline' => $firstFlight['airline'] ?? 'Airline',
                        'airline_logo' => $firstFlight['airline_logo'] ?? '',
                        'flight_number' => $firstFlight['flight_number'] ?? 'N/A',
                        'from_city' => $firstFlight['departure_airport']['name'] ?? $from,
                        'from_code' => $firstFlight['departure_airport']['id'] ?? $from,
                        'to_city' => $lastFlight['arrival_airport']['name'] ?? $to,
                        'to_code' => $lastFlight['arrival_airport']['id'] ?? $to,
                        'departure_time' => ($firstFlight['departure_airport']['date'] ?? '') . ' ' . ($firstFlight['departure_airport']['time'] ?? ''),
                        'arrival_time' => ($lastFlight['arrival_airport']['date'] ?? '') . ' ' . ($lastFlight['arrival_airport']['time'] ?? ''),
                        'duration' => $this->formatDuration($itinerary['total_duration'] ?? 0),
                        'price' => (float) $price,
                        'price_per_person' => $travelers > 0 ? round($price / $travelers) : $price,
                        'stops' => (count($itinerary['flights']) - 1) <= 0 ? 'Non-stop' : ((count($itinerary['flights']) - 1) . ' stop(s)'),
                        'travel_class' => $firstFlight['travel_class'] ?? 'Economy',
                        'airplane' => $firstFlight['airplane'] ?? 'N/A',
                        'legroom' => $this->extractLegroom($firstFlight),
                        'trip_type' => $tripType,
                        'return_leg' => null,
                    ]);
                }
            }

            Log::info("Found " . $flights->count() . " flights via SearchApi.io");
        } catch (\Exception $e) {
            Log::error("SearchApiService Exception: " . $e->getMessage());
        }

        return $flights->sortBy('price')->values();
    }

    /**
     * Format minutes into human-readable duration
     */
    protected function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . 'h ' . $mins . 'm';
    }

    /**
     * Extract legroom from extensions
     */
    protected function extractLegroom(array $flight): string
    {
        if (isset($flight['detected_extensions']['legroom_short'])) {
            return $flight['detected_extensions']['legroom_short'];
        }

        foreach ($flight['extensions'] ?? [] as $ext) {
            if (str_contains(strtolower($ext), 'legroom')) {
                return $ext;
            }
        }

        return 'N/A';
    }
}

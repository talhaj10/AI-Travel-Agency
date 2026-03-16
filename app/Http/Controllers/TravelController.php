<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\SearchApiService;

class TravelController extends Controller
{
    protected $searchApiService;

    public function __construct(SearchApiService $searchApiService)
    {
        $this->searchApiService = $searchApiService;
    }
    /**
     * Search flights using SerpAPI Google Flights engine
     */
    public function searchFlights(Request $request)
    {
        $from = $request->input('from', '');
        $to = $request->input('to', '');
        $date = $request->input('date', now()->addDays(7)->format('Y-m-d'));
        $apiKey = env('RAPIDAPI_KEY');
        $apiHost = env('RAPIDAPI_HOST');
        $flights = collect();

        if ($apiKey && $apiHost && $from && $to) {
            try {
                $fromCode = $this->getAirportCode($from);
                $toCode = $this->getAirportCode($to);

                // Use RapidAPI for flights
                $response = Http::withHeaders([
                    'X-RapidAPI-Host' => $apiHost,
                    'X-RapidAPI-Key' => $apiKey,
                ])->get("https://{$apiHost}/flights/search-one-way", [
                            'fromEntityId' => $fromCode,
                            'toEntityId' => $toCode,
                            'departDate' => $date,
                            'adults' => 1,
                            'currency' => 'INR',
                            'market' => 'IN',
                            'locale' => 'en-GB'
                        ]);

                $json = $response->json();
                Log::info("RapidAPI Flights Status: " . $response->status());

                if (isset($json['data']['itineraries'])) {
                    foreach ($json['data']['itineraries'] as $item) {
                        $leg = $item['legs'][0];
                        $price = $item['price']['raw'] ?? 0;

                        $flights->push((object) [
                            'id' => $flights->count() + 1,
                            'airline' => $leg['carriers']['marketing'][0]['name'] ?? 'Airline',
                            'airline_logo' => $leg['carriers']['marketing'][0]['logoUrl'] ?? '',
                            'flight_number' => $leg['segments'][0]['flightNumber'] ?? 'N/A',
                            'from_city' => $from,
                            'from_code' => $fromCode,
                            'to_city' => $to,
                            'to_code' => $toCode,
                            'departure_time' => $leg['departure'],
                            'arrival_time' => $leg['arrival'],
                            'duration' => $this->formatDuration($leg['durationInMinutes'] ?? 0),
                            'price' => (float) $price,
                            'stops' => ($leg['stopCount'] ?? 0) == 0 ? 'Non-stop' : ($leg['stopCount'] . ' stop(s)'),
                            'travel_class' => 'Economy',
                            'airplane' => 'Boeing 737',
                            'legroom' => '30 in',
                        ]);
                    }
                }

                Log::info("Found " . $flights->count() . " flights via RapidAPI");

            } catch (\Exception $e) {
                Log::error("RapidAPI Flights Exception: " . $e->getMessage());
            }
        }

        // Fallback to SearchApi.io if RapidAPI fails or key missing
        if ($flights->isEmpty() && env('SEARCHAPI_KEY') && $from && $to) {
            try {
                Log::info("RapidAPI failed or empty, trying SearchApi.io for flights");
                $fromCode = $this->getAirportCode($from);
                $toCode = $this->getAirportCode($to);

                $searchApiFlights = $this->searchApiService->fetchFlights($fromCode, $toCode, $date);

                foreach ($searchApiFlights as $flight) {
                    $flights->push((object) [
                        'id' => $flights->count() + 1,
                        'airline' => $flight->airline,
                        'airline_logo' => $flight->airline_logo,
                        'flight_number' => $flight->flight_number,
                        'from_city' => $flight->from_city,
                        'from_code' => $flight->from_code,
                        'to_city' => $flight->to_city,
                        'to_code' => $flight->to_code,
                        'departure_time' => $flight->departure_time,
                        'arrival_time' => $flight->arrival_time,
                        'duration' => $flight->duration,
                        'price' => $flight->price,
                        'price_per_person' => $flight->price_per_person,
                        'stops' => $flight->stops,
                        'travel_class' => $flight->travel_class,
                        'airplane' => $flight->airplane,
                        'legroom' => $flight->legroom,
                        'trip_type' => $flight->trip_type,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("SearchApi Fallback Flights Exception: " . $e->getMessage());
            }
        }

        // Fallback to SerpAPI if everything above fails
        if ($flights->isEmpty() && env('SERPAPI_KEY') && $from && $to) {
            try {
                Log::info("RapidAPI failed or empty, trying SerpAPI for flights");
                $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                    'engine' => 'google_flights',
                    'departure_id' => $this->getAirportCode($from),
                    'arrival_id' => $this->getAirportCode($to),
                    'outbound_date' => $date,
                    'currency' => 'INR',
                    'api_key' => env('SERPAPI_KEY'),
                    'type' => '2',
                ]);
                $json = $response->json();
                foreach (['best_flights', 'other_flights'] as $sect) {
                    if (isset($json[$sect])) {
                        foreach ($json[$sect] as $group) {
                            foreach ($group['flights'] ?? [] as $segment) {
                                $flights->push((object) [
                                    'id' => $flights->count() + 1,
                                    'airline' => $segment['airline'] ?? 'Unknown Airline',
                                    'airline_logo' => $segment['airline_logo'] ?? '',
                                    'flight_number' => $segment['flight_number'] ?? '',
                                    'from_city' => $segment['departure_airport']['name'] ?? $from,
                                    'from_code' => $segment['departure_airport']['id'] ?? '',
                                    'to_city' => $segment['arrival_airport']['name'] ?? $to,
                                    'to_code' => $segment['arrival_airport']['id'] ?? '',
                                    'departure_time' => $segment['departure_airport']['time'] ?? '',
                                    'arrival_time' => $segment['arrival_airport']['time'] ?? '',
                                    'duration' => isset($segment['duration']) ? $this->formatDuration($segment['duration']) : 'N/A',
                                    'price' => $group['price'] ?? 0,
                                    'stops' => count($group['flights'] ?? []) > 1 ? (count($group['flights']) - 1) . ' stop(s)' : 'Non-stop',
                                    'travel_class' => $segment['travel_class'] ?? 'Economy',
                                    'airplane' => $segment['airplane'] ?? '',
                                    'legroom' => $segment['legroom'] ?? '',
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("SerpAPI Fallback Flights Exception: " . $e->getMessage());
            }
        }

        $searchFrom = $from;
        $searchTo = $to;
        $searchDate = $date;

        return view('search.flights', compact('flights', 'searchFrom', 'searchTo', 'searchDate'));
    }

    /**
     * Search hotels using SerpAPI Google Hotels engine
     */
    public function searchHotels(Request $request)
    {
        $city = $request->input('city', '');
        $budget = $request->input('budget', null);
        $checkin = $request->input('checkin', now()->addDays(7)->format('Y-m-d'));
        $checkout = $request->input('checkout', now()->addDays(8)->format('Y-m-d'));
        $apiKey = env('SERPAPI_KEY');

        $hotels = collect();

        if ($apiKey && $city) {
            try {
                $checkinObj = \Carbon\Carbon::parse($checkin);
                $checkoutObj = \Carbon\Carbon::parse($checkout);
                if ($checkoutObj->lte($checkinObj)) {
                    $checkoutObj = $checkinObj->copy()->addDay();
                    $checkout = $checkoutObj->format('Y-m-d');
                }

                $params = [
                    'engine' => 'google_hotels',
                    'q' => $city,
                    'check_in_date' => $checkin,
                    'check_out_date' => $checkout,
                    'currency' => 'INR',
                    'api_key' => $apiKey,
                    'adults' => 1,
                    'gl' => 'in',
                    'hl' => 'en'
                ];

                // Set budget filter with buffer
                if ($budget) {
                    $params['max_price'] = round($budget * 1.3);
                }

                $response = Http::timeout(30)->get('https://serpapi.com/search.json', $params);

                $json = $response->json();
                Log::info("SerpAPI Hotels Status: " . $response->status());

                if (isset($json['properties'])) {
                    foreach ($json['properties'] as $property) {
                        $price = 0;
                        if (isset($property['rate_per_night']['extracted_lowest'])) {
                            $price = $property['rate_per_night']['extracted_lowest'];
                        } elseif (isset($property['total_rate']['extracted_lowest'])) {
                            $price = $property['total_rate']['extracted_lowest'];
                        } elseif (isset($property['rate_per_night']['lowest'])) {
                            $price = (int) preg_replace('/[^0-9]/', '', $property['rate_per_night']['lowest']);
                        }

                        // Skip if over budget
                        if ($budget && $price > $budget) {
                            continue;
                        }

                        $amenities = [];
                        if (isset($property['amenities'])) {
                            $amenities = array_slice($property['amenities'], 0, 6);
                        }

                        $hotels->push((object) [
                            'id' => $hotels->count() + 1,
                            'name' => $property['name'] ?? 'Unknown Hotel',
                            'city' => $city,
                            'rating' => $property['overall_rating'] ?? 0,
                            'reviews' => $property['reviews'] ?? 0,
                            'price_per_night' => $price,
                            'amenities' => implode(', ', $amenities),
                            'type' => $property['type'] ?? 'Hotel',
                            'description' => $property['description'] ?? '',
                            'thumbnail' => $property['images'][0]['thumbnail'] ?? ($property['images'][0]['original_image'] ?? ''),
                            'check_in_time' => $property['check_in_time'] ?? '',
                            'check_out_time' => $property['check_out_time'] ?? '',
                            'link' => $property['link'] ?? '#',
                            'nearby' => $property['nearby_places'][0]['name'] ?? '',
                        ]);
                    }
                }

                Log::info("Found " . $hotels->count() . " hotels via SerpAPI");

            } catch (\Exception $e) {
                Log::error("SerpAPI Hotels Exception: " . $e->getMessage());
            }
        }

        $searchCity = $city;
        $searchBudget = $budget;

        return view('search.hotels', compact('hotels', 'searchCity', 'searchBudget'));
    }

    /**
     * Map city names to IATA airport codes
     */
    private function getAirportCode(string $input): string
    {
        $input = strtolower(trim($input));

        $codes = [
            // Indian Cities & States
            'delhi' => 'DEL',
            'new delhi' => 'DEL',
            'mumbai' => 'BOM',
            'bombay' => 'BOM',
            'bangalore' => 'BLR',
            'bengaluru' => 'BLR',
            'hyderabad' => 'HYD',
            'chennai' => 'MAA',
            'kolkata' => 'CCU',
            'calcutta' => 'CCU',
            'goa' => 'GOI',
            'jaipur' => 'JAI',
            'surat' => 'STV',
            'ahmedabad' => 'AMD',
            'pune' => 'PNQ',
            'lucknow' => 'LKO',
            'kochi' => 'COK',
            'kerala' => 'COK',
            'trivandrum' => 'TRV',
            'kozhikode' => 'CCJ',
            'guwahati' => 'GAU',
            'varanasi' => 'VNS',
            'chandigarh' => 'IXC',
            'indore' => 'IDR',
            'patna' => 'PAT',
            'bhopal' => 'BHO',
            'srinagar' => 'SXR',
            'udaipur' => 'UDR',
            'amritsar' => 'ATQ',
            'vizag' => 'VTZ',
            'nagpur' => 'NAG',
            'coimbatore' => 'CJB',
            'ranchi' => 'IXR',
            'raipur' => 'RPR',
            'jodhpur' => 'JDH',
            'madurai' => 'IXM',

            // Major Countries (Hubs)
            'usa' => 'JFK',
            'united states' => 'JFK',
            'america' => 'JFK',
            'uk' => 'LHR',
            'united kingdom' => 'LHR',
            'england' => 'LHR',
            'japan' => 'NRT',
            'france' => 'CDG',
            'germany' => 'FRA',
            'canada' => 'YYZ',
            'australia' => 'SYD',
            'singapore' => 'SIN',
            'thailand' => 'BKK',
            'uae' => 'DXB',
            'dubai' => 'DXB',
            'qatar' => 'DOH',
            'saudi arabia' => 'RUH',
            'malaysia' => 'KUL',
            'indonesia' => 'DPS',
            'bali' => 'DPS',
            'vietnam' => 'SGN',
            'italy' => 'FCO',
            'spain' => 'MAD',
            'netherlands' => 'AMS',
            'switzerland' => 'ZRH',
            'turkey' => 'IST',
            'south africa' => 'JNB',
            'egypt' => 'CAI',
            'sri lanka' => 'CMB',
            'maldives' => 'MLE',
            'nepal' => 'KTM',
            'russia' => 'SVO',
            'brazil' => 'GRU',
            'mexico' => 'MEX',
            'china' => 'PEK',
            'south korea' => 'ICN',
            'hong kong' => 'HKG',
            'new zealand' => 'AKL',
            'philippines' => 'MNL',
            'greece' => 'ATH',
            'portugal' => 'LIS',
            'austria' => 'VIE',
            'belgium' => 'BRU',
            'denmark' => 'CPH',
            'sweden' => 'ARN',
            'norway' => 'OSL',
            'finland' => 'HEL',
            'ireland' => 'DUB',
            'israel' => 'TLV',
            'jordan' => 'AMM',
            'kuwait' => 'KWI',
            'oman' => 'MCT',

            // Popular Global Cities
            'london' => 'LHR',
            'new york' => 'JFK',
            'paris' => 'CDG',
            'tokyo' => 'NRT',
            'bangkok' => 'BKK',
            'singapore city' => 'SIN',
            'kuala lumpur' => 'KUL',
            'sydney city' => 'SYD',
            'melbourne' => 'MEL',
            'toronto city' => 'YYZ',
            'vancouver' => 'YVR',
            'san francisco' => 'SFO',
            'los angeles' => 'LAX',
            'chicago' => 'ORD',
            'miami' => 'MIA',
            'dubai city' => 'DXB',
            'abu dhabi' => 'AUH',
            'istanbul city' => 'IST',
            'rome' => 'FCO',
            'milan' => 'MXP',
            'madrid city' => 'MAD',
            'barcelona' => 'BCN',
            'amsterdam city' => 'AMS',
            'berlin' => 'BER',
            'munich' => 'MUC',
            'zurich city' => 'ZRH',
            'geneva' => 'GVA',
            'vienna' => 'VIE',
            'prague' => 'PRG',
            'budapest' => 'BUD',
            'moscow' => 'SVO',
            'shanghai' => 'PVG',
            'beijing' => 'PEK',
            'seoul' => 'ICN',
            'manila' => 'MNL',
            'ho chi minh city' => 'SGN',
            'hanoi' => 'HAN',
            'phuket' => 'HKT',
            'koh samui' => 'USM',
            'maldives islands' => 'MLE',
        ];

        if (isset($codes[$input])) {
            return $codes[$input];
        }

        if (preg_match('/^[a-z]{3}$/', $input)) {
            return strtoupper($input);
        }
        if (preg_match('/\(([a-z]{3})\)/', $input, $matches)) {
            return strtoupper($matches[1]);
        }

        // Try RapidAPI lookup before falling back
        $rapidCode = $this->getAirportCodeRapid($input);
        if ($rapidCode) {
            return $rapidCode;
        }

        return strtoupper(substr($input, 0, 3));
    }

    /**
     * Fallback to RapidAPI for airport code lookup
     */
    private function getAirportCodeRapid(string $query): ?string
    {
        $apiKey = env('RAPIDAPI_KEY');
        $apiHost = env('RAPIDAPI_HOST');

        if (!$apiKey || !$apiHost)
            return null;

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => $apiHost,
                'X-RapidAPI-Key' => $apiKey,
            ])->get("https://{$apiHost}/flights/airports", [
                        'query' => $query
                    ]);

            $json = $response->json();
            if (isset($json['data'][0]['iataCode'])) {
                return $json['data'][0]['iataCode'];
            }
        } catch (\Exception $e) {
            Log::error("RapidAPI Airport Lookup Exception: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Format minutes into human-readable duration
     */
    private function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . 'h ' . $mins . 'm';
    }
}

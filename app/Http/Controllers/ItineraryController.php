<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiItinerary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItineraryController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'days' => 'required|integer|min:1|max:14',
            'budget' => 'required|numeric',
        ]);

        $destination = $request->destination;
        $days = $request->days;
        $budget = $request->budget;

        // Step 1: Scrape real data from Google via SerpAPI
        $attractions = $this->searchSerpApi("top tourist attractions things to do in {$destination}");
        $restaurants = $this->searchSerpApi("best restaurants to eat in {$destination}");
        $hotels = $this->searchSerpApi("best budget hotels to stay in {$destination}");

        // Step 2: Try AI to organize the scraped data into a narrative itinerary
        $plan = $this->tryAiItinerary($destination, $days, $budget, $attractions, $restaurants, $hotels);

        // Step 3: If AI fails, build itinerary programmatically from scraped data
        if (!$plan) {
            $plan = $this->buildItineraryFromData($destination, $days, $budget, $attractions, $restaurants, $hotels);
        }

        $itinerary = AiItinerary::create([
            'user_id' => 1,
            'destination' => $destination,
            'days' => $days,
            'budget' => $budget,
            'interests' => 'General',
            'travel_type' => 'Leisure',
            'generated_plan' => $plan,
        ]);

        return redirect()->route('ai.show', $itinerary->id);
    }

    /**
     * Regenerate a new itinerary for the same destination/days/budget
     */
    public function regenerate(AiItinerary $itinerary)
    {
        $destination = $itinerary->destination;
        $days = $itinerary->days;
        $budget = $itinerary->budget;

        // Fetch fresh data from SerpAPI
        $attractions = $this->searchSerpApi("top tourist attractions things to do in {$destination}");
        $restaurants = $this->searchSerpApi("best restaurants to eat in {$destination}");
        $hotels = $this->searchSerpApi("best budget hotels to stay in {$destination}");

        // Use a slightly higher temperature variation by shuffling the data
        shuffle($attractions);
        shuffle($restaurants);
        shuffle($hotels);

        // Try AI with the freshly shuffled data
        $plan = $this->tryAiItinerary($destination, $days, $budget, $attractions, $restaurants, $hotels);

        // Fallback to programmatic if AI fails
        if (!$plan) {
            $plan = $this->buildItineraryFromData($destination, $days, $budget, $attractions, $restaurants, $hotels);
        }

        // Create a new itinerary record
        $newItinerary = AiItinerary::create([
            'user_id' => $itinerary->user_id,
            'destination' => $destination,
            'days' => $days,
            'budget' => $budget,
            'interests' => $itinerary->interests ?? 'General',
            'travel_type' => $itinerary->travel_type ?? 'Leisure',
            'generated_plan' => $plan,
        ]);

        return redirect()->route('ai.show', $newItinerary->id)
            ->with('success', 'Fresh itinerary generated! Here\'s a new plan for your trip.');
    }

    /**
     * Search Google via SerpAPI and extract useful place data
     */
    private function searchSerpApi(string $query): array
    {
        $apiKey = env('SERPAPI_KEY');
        if (!$apiKey) {
            Log::warning('SerpAPI key not found');
            return [];
        }

        try {
            $response = Http::timeout(30)->get('https://serpapi.com/search.json', [
                'q' => $query,
                'api_key' => $apiKey,
                'engine' => 'google',
                'hl' => 'en',
                'gl' => 'in',
                'num' => 10,
            ]);

            $json = $response->json();
            Log::info("SerpAPI search for: {$query} — Status: " . $response->status());

            $results = [];

            // Extract from local_results (Google Maps places)
            if (isset($json['local_results']['places'])) {
                foreach ($json['local_results']['places'] as $place) {
                    $results[] = [
                        'name' => $place['title'] ?? 'Unknown',
                        'rating' => $place['rating'] ?? null,
                        'reviews' => $place['reviews'] ?? null,
                        'address' => $place['address'] ?? '',
                        'type' => $place['type'] ?? '',
                        'description' => $place['description'] ?? '',
                        'price' => $place['price'] ?? '',
                        'hours' => $place['hours'] ?? '',
                        'thumbnail' => $place['thumbnail'] ?? '',
                    ];
                }
            }

            // Extract from top_sights if available
            if (isset($json['top_sights']['sights'])) {
                foreach ($json['top_sights']['sights'] as $sight) {
                    $results[] = [
                        'name' => $sight['title'] ?? 'Unknown',
                        'rating' => $sight['rating'] ?? null,
                        'reviews' => $sight['reviews'] ?? null,
                        'address' => $sight['address'] ?? '',
                        'type' => 'Attraction',
                        'description' => $sight['description'] ?? '',
                        'price' => '',
                        'hours' => '',
                        'thumbnail' => $sight['thumbnail'] ?? '',
                    ];
                }
            }

            // If no local results, extract from organic results
            if (empty($results) && isset($json['organic_results'])) {
                foreach (array_slice($json['organic_results'], 0, 8) as $organic) {
                    // Parse snippets for place names
                    $results[] = [
                        'name' => $organic['title'] ?? '',
                        'rating' => null,
                        'reviews' => null,
                        'address' => '',
                        'type' => '',
                        'description' => $organic['snippet'] ?? '',
                        'price' => '',
                        'hours' => '',
                        'thumbnail' => $organic['thumbnail'] ?? '',
                    ];
                }
            }

            // Also check knowledge_graph
            if (isset($json['knowledge_graph'])) {
                $kg = $json['knowledge_graph'];
                if (isset($kg['local_results'])) {
                    foreach ($kg['local_results'] as $local) {
                        $results[] = [
                            'name' => $local['title'] ?? '',
                            'rating' => $local['rating'] ?? null,
                            'reviews' => $local['reviews'] ?? null,
                            'address' => $local['address'] ?? '',
                            'type' => $local['type'] ?? '',
                            'description' => '',
                            'price' => '',
                            'hours' => '',
                            'thumbnail' => '',
                        ];
                    }
                }
            }

            Log::info("SerpAPI found " . count($results) . " results for: {$query}");
            return $results;

        } catch (\Exception $e) {
            Log::error("SerpAPI Exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Try Hugging Face AI to create a narrative itinerary using real scraped data
     */
    private function tryAiItinerary($destination, $days, $budget, $attractions, $restaurants, $hotels): ?string
    {
        $hfToken = env('HF_API_TOKEN');
        if (!$hfToken || empty($attractions)) {
            return null;
        }

        // Build a data summary for the AI
        $attractionNames = collect($attractions)->pluck('name')->filter()->take(10)->implode(', ');
        $restaurantNames = collect($restaurants)->pluck('name')->filter()->take(8)->implode(', ');
        $hotelNames = collect($hotels)->pluck('name')->filter()->take(5)->implode(', ');

        $dataPrompt = "Create a HIGHLY DETAILED {$days}-day travel itinerary for {$destination} using ONLY these REAL places. Total budget for food and activities is ₹{$budget}.

REAL ATTRACTIONS: {$attractionNames}
REAL RESTAURANTS: {$restaurantNames}
REAL HOTELS: {$hotelNames}

MANDATORY STRUCTURE FOR EVERY DAY:

1. **Sections**: Break each day into ## Day [X], then ### 🌅 Morning, ### ☀️ Afternoon, and ### 🌙 Evening.
2. **Meals**: Suggest a UNIQUE restaurant for EVERY meal (Breakfast, Lunch, Dinner). No repeats.
   - Format: \"🍽️ **[Meal] at [Restaurant Name]** ([Location]) | 🕒 [Hours] | ₹[Cost]\"
3. **Transport**: For every major move between places, include:
   - \"🚗 **Transport**: [Mode] | [Time] | ₹[Cost]\"
4. **Places to Visit**: Include at least 2-3 specific sightseeing spots or attractions from the list provided for each day, integrated into the morning, afternoon, and evening.
   - Format: \"🎯 **[Place Name]**: [Description] | ₹[Entry Fee]\"
5. **Day 1**: Must include arrival and transfer from airport.
6. **Last Day**: Must include transfer back to airport.

Format as clean Markdown with emojis. Ensure a logical flow where transport moves are suggested between meals and places. Keep total costs within ₹{$budget}. End with ## 💡 Pro Tips.";

        $models = [
            'mistralai/Mistral-7B-Instruct-v0.3',
            'Qwen/Qwen2.5-3B-Instruct',
        ];

        foreach ($models as $model) {
            try {
                $response = Http::timeout(120)->withHeaders([
                    'Authorization' => 'Bearer ' . $hfToken,
                    'Content-Type' => 'application/json',
                ])->post("https://router.huggingface.co/hf-inference/models/{$model}/v1/chat/completions", [
                            'model' => $model,
                            'messages' => [
                                ['role' => 'system', 'content' => 'You are a travel planner. Create itineraries using ONLY the real places provided. Use Markdown with emojis.'],
                                ['role' => 'user', 'content' => $dataPrompt],
                            ],
                            'max_tokens' => 3000,
                            'temperature' => 0.7,
                        ]);

                $json = $response->json();
                Log::info("HF [{$model}] Status: " . $response->status());

                if ($response->successful() && isset($json['choices'][0]['message']['content'])) {
                    $plan = $json['choices'][0]['message']['content'];
                    if (strlen($plan) > 200) {
                        Log::info("AI itinerary generated via HF [{$model}]");
                        return $plan;
                    }
                }
                Log::warning("HF [{$model}] insufficient response");
            } catch (\Exception $e) {
                Log::error("HF [{$model}] Exception: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Build a structured itinerary from scraped data (no AI needed)
     */
    private function buildItineraryFromData($destination, $days, $budget, $attractions, $restaurants, $hotels): string
    {
        $dailyBudget = round($budget / $days);
        $accommodationBudget = round($dailyBudget * 0.35);
        $foodBudget = round($dailyBudget * 0.30);
        $activityBudget = round($dailyBudget * 0.20);
        $transportBudget = round($dailyBudget * 0.15);

        $plan = "## 🗺️ {$days}-Day Travel Itinerary for {$destination}\n\n";
        $plan .= "**💰 Total Budget:** ₹" . number_format($budget) . " | **📅 Duration:** {$days} days | **💵 Per Day:** ₹" . number_format($dailyBudget) . "\n\n";
        $plan .= "---\n\n";

        // Budget breakdown
        $plan .= "### 💰 Daily Budget Breakdown\n";
        $plan .= "| Category | Amount |\n|---|---|\n";
        $plan .= "| 🏨 Accommodation | ₹" . number_format($accommodationBudget) . "/night |\n";
        $plan .= "| 🍽️ Food & Dining | ₹" . number_format($foodBudget) . "/day |\n";
        $plan .= "| 🎯 Activities & Entry Fees | ₹" . number_format($activityBudget) . "/day |\n";
        $plan .= "| 🚗 Transport | ₹" . number_format($transportBudget) . "/day |\n\n";
        $plan .= "---\n\n";

        // Hotel recommendation
        if (!empty($hotels)) {
            $plan .= "### 🏨 Recommended Accommodation\n";
            foreach (array_slice($hotels, 0, 3) as $hotel) {
                $rating = $hotel['rating'] ? "⭐ {$hotel['rating']}" : '';
                $reviews = $hotel['reviews'] ? "({$hotel['reviews']} reviews)" : '';
                $price = $hotel['price'] ? " — {$hotel['price']}" : " — ~₹" . number_format($accommodationBudget) . "/night";
                $plan .= "- **{$hotel['name']}** {$rating} {$reviews}{$price}\n";
                if ($hotel['address']) {
                    $plan .= "  📍 {$hotel['address']}\n";
                }
            }
            $plan .= "\n---\n\n";
        }

        // Distribute attractions and restaurants across days
        $attractionIndex = 0;
        $restaurantIndex = 0;
        $totalAttractions = count($attractions);
        $totalRestaurants = count($restaurants);

        $morningTimes = ['8:00 AM', '8:30 AM', '9:00 AM'];
        $afternoonTimes = ['12:30 PM', '1:00 PM', '1:30 PM'];
        $eveningTimes = ['5:30 PM', '6:00 PM', '6:30 PM'];
        $dinnerTimes = ['7:30 PM', '8:00 PM', '8:30 PM'];

        for ($day = 1; $day <= $days; $day++) {
            $plan .= "## 📅 Day {$day}\n\n";

            // Transport Tip (Start of day)
            if ($day == 1) {
                $plan .= "🚗 **Arrival & Transfer**: Take a prepaid taxi or Uber from the airport to your hotel | ⏱️ 45-60 mins | 💵 Estimated: ₹800\n\n";
            } else {
                $plan .= "🚗 **Morning Move**: Local auto-rickshaw or cab to your first destination | ⏱️ 15-20 mins | 💵 Estimated: ₹150\n\n";
            }

            // Morning
            $morningTime = $morningTimes[($day - 1) % 3];
            $plan .= "### 🌅 Morning ({$morningTime} - 12:00 PM)\n";

            if ($totalRestaurants > 0) {
                $breakfast = $restaurants[$restaurantIndex % $totalRestaurants];
                $bRating = $breakfast['rating'] ? " ⭐ {$breakfast['rating']}" : '';
                $plan .= "- **☕ Breakfast** at **{$breakfast['name']}**{$bRating}\n";
                if ($breakfast['address'])
                    $plan .= "  📍 {$breakfast['address']}\n";
                if ($breakfast['type'])
                    $plan .= "  🍽️ {$breakfast['type']}\n";
                $plan .= "  💵 Estimated: ₹" . number_format(round($foodBudget * 0.25)) . "\n\n";
                $restaurantIndex++;
            }

            if ($totalAttractions > 0) {
                $morningAttraction = $attractions[$attractionIndex % $totalAttractions];
                $aRating = $morningAttraction['rating'] ? " ⭐ {$morningAttraction['rating']}" : '';
                $aReviews = $morningAttraction['reviews'] ? " ({$morningAttraction['reviews']} reviews)" : '';
                $plan .= "- **🎯 Morning Activity:** Visit **{$morningAttraction['name']}**{$aRating}{$aReviews}\n";
                if ($morningAttraction['address'])
                    $plan .= "  📍 {$morningAttraction['address']}\n";
                if ($morningAttraction['description'])
                    $plan .= "  ℹ️ {$morningAttraction['description']}\n";
                if ($morningAttraction['hours'])
                    $plan .= "  🕐 {$morningAttraction['hours']}\n";
                $plan .= "  ⏱️ Recommended: 2-3 hours\n";
                $plan .= "  💵 Entry: ₹" . number_format(round($activityBudget * 0.4)) . " (approx)\n\n";
                $attractionIndex++;
            }

            $plan .= "🚗 **Afternoon Move**: Short cab ride or walk to lunch | ⏱️ 10 mins | 💵 Estimated: ₹100\n\n";

            // Afternoon
            $afternoonTime = $afternoonTimes[($day - 1) % 3];
            $plan .= "### ☀️ Afternoon ({$afternoonTime} - 5:00 PM)\n";

            if ($totalRestaurants > 0) {
                $lunch = $restaurants[$restaurantIndex % $totalRestaurants];
                $lRating = $lunch['rating'] ? " ⭐ {$lunch['rating']}" : '';
                $plan .= "- **🍽️ Lunch** at **{$lunch['name']}**{$lRating}\n";
                if ($lunch['address'])
                    $plan .= "  📍 {$lunch['address']}\n";
                if ($lunch['type'])
                    $plan .= "  🍽️ {$lunch['type']}\n";
                $plan .= "  💵 Estimated: ₹" . number_format(round($foodBudget * 0.35)) . "\n\n";
                $restaurantIndex++;
            }

            if ($totalAttractions > 0) {
                $afternoonAttraction = $attractions[$attractionIndex % $totalAttractions];
                $aRating = $afternoonAttraction['rating'] ? " ⭐ {$afternoonAttraction['rating']}" : '';
                $aReviews = $afternoonAttraction['reviews'] ? " ({$afternoonAttraction['reviews']} reviews)" : '';
                $plan .= "- **🏛️ Afternoon Exploration:** Visit **{$afternoonAttraction['name']}**{$aRating}{$aReviews}\n";
                if ($afternoonAttraction['address'])
                    $plan .= "  📍 {$afternoonAttraction['address']}\n";
                if ($afternoonAttraction['description'])
                    $plan .= "  ℹ️ {$afternoonAttraction['description']}\n";
                $plan .= "  ⏱️ Recommended: 2-3 hours\n";
                $plan .= "  💵 Entry: ₹" . number_format(round($activityBudget * 0.3)) . " (approx)\n\n";
                $attractionIndex++;
            }

            $plan .= "🚗 **Evening Move**: Head to a scenic spot or local market | ⏱️ 20 mins | 💵 Estimated: ₹200\n\n";

            // Evening
            $eveningTime = $eveningTimes[($day - 1) % 3];
            $dinnerTime = $dinnerTimes[($day - 1) % 3];
            $plan .= "### 🌙 Evening ({$eveningTime} - 10:00 PM)\n";

            if ($totalAttractions > 0) {
                $eveningAttraction = $attractions[$attractionIndex % $totalAttractions];
                $aRating = $eveningAttraction['rating'] ? " ⭐ {$eveningAttraction['rating']}" : '';
                $plan .= "- **🌆 Evening Activity:** Explore **{$eveningAttraction['name']}**{$aRating}\n";
                if ($eveningAttraction['address'])
                    $plan .= "  📍 {$eveningAttraction['address']}\n";
                $plan .= "  ⏱️ Recommended: 1-2 hours\n\n";
                $attractionIndex++;
            }

            if ($totalRestaurants > 0) {
                $dinner = $restaurants[$restaurantIndex % $totalRestaurants];
                $dRating = $dinner['rating'] ? " ⭐ {$dinner['rating']}" : '';
                $plan .= "- **🍷 Dinner ({$dinnerTime})** at **{$dinner['name']}**{$dRating}\n";
                if ($dinner['address'])
                    $plan .= "  📍 {$dinner['address']}\n";
                if ($dinner['type'])
                    $plan .= "  🍽️ {$dinner['type']}\n";
                $plan .= "  💵 Estimated: ₹" . number_format(round($foodBudget * 0.40)) . "\n\n";
                $restaurantIndex++;
            }

            $plan .= "**🚗 Day {$day} Transport Budget:** ₹" . number_format($transportBudget) . " (use local auto/cab/bus)\n\n";
            $plan .= "---\n\n";
        }

        // Travel tips
        $plan .= "## 💡 Pro Tips for {$destination}\n\n";
        $plan .= "- 🕐 **Start early** — Most attractions are less crowded before 10 AM\n";
        $plan .= "- 💧 **Stay hydrated** — Carry a water bottle, especially during summer months\n";
        $plan .= "- 📱 **Download offline maps** — Helps navigate without data connection\n";
        $plan .= "- 💵 **Carry cash** — Smaller shops and street vendors may not accept UPI/cards\n";
        $plan .= "- 🤝 **Bargain politely** — Negotiate at local markets for better prices\n";
        $plan .= "- 📸 **Photography** — Ask permission before photographing people or religious sites\n";
        $plan .= "- 🚗 **Transport** — Pre-book cabs for airport/station transfers for better rates\n";
        $plan .= "- 🍽️ **Eat local** — Street food is often the best and cheapest way to experience local cuisine\n\n";

        $plan .= "---\n\n";
        $plan .= "*🔄 This itinerary was generated using real data from Google Search. All places, restaurants, and hotels listed are real locations in {$destination}.*\n";

        return $plan;
    }

    public function show(AiItinerary $itinerary)
    {
        return view('itinerary.show', compact('itinerary'));
    }
}

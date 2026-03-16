<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Your complete trip plan to {{ $destination }} - flights, hotels, and AI-generated itinerary by Tourex.">
    <title>Your Trip to {{ $destination }} - Tourex</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://js.puter.com/v2/"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-soft font-sans antialiased text-primary">
    <!-- Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">tourex</a>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-400">Plan Your Trip</span>
                <a href="{{ route('home') }}"
                    class="px-5 py-2 border border-gray-200 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all">
                    New Trip
                </a>
            </div>
        </div>
    </header>

    <!-- Trip Summary Header -->
    <section class="bg-primary text-white py-12">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <p class="text-white/50 text-xs font-bold uppercase tracking-[0.3em] mb-2">Your Journey to
                        {{ $destination }}</p>
                    <h1 class="text-4xl font-black mb-2">{{ $from }} <span class="text-white/30">→</span>
                        {{ $to }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/70 text-sm">
                        <span class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            {{ \Carbon\Carbon::parse($departureDate)->format('M d') }} -
                            {{ \Carbon\Carbon::parse($returnDate)->format('M d, Y') }}
                        </span>
                        <span class="text-white/30">•</span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            {{ $days }} Days
                        </span>
                        <span class="text-white/30">•</span>
                        <span class="flex items-center gap-2">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            {{ $travelers }} Traveler{{ $travelers > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 text-center min-w-[120px]">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/40 mb-1">Total Budget</p>
                    <p class="text-2xl font-black">₹{{ number_format($budget) }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Step Progress Bar -->
    <nav class="bg-white border-b border-gray-100 sticky top-[65px] z-40">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex items-center gap-0">
                <button onclick="goToStep(1)" id="step-tab-1"
                    class="step-tab flex-1 flex items-center justify-center gap-2 py-4 font-bold text-sm border-b-3 transition-all cursor-pointer border-blue-500 text-blue-600">
                    <span class="step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-blue-500 text-white">1</span>
                    <span class="hidden sm:inline">Select Flight</span>
                </button>
                <button onclick="goToStep(2)" id="step-tab-2"
                    class="step-tab flex-1 flex items-center justify-center gap-2 py-4 font-bold text-sm border-b-3 transition-all cursor-pointer border-transparent text-gray-400">
                    <span class="step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-gray-200 text-gray-500">2</span>
                    <span class="hidden sm:inline">Select Hotel</span>
                </button>
                <button onclick="goToStep(3)" id="step-tab-3"
                    class="step-tab flex-1 flex items-center justify-center gap-2 py-4 font-bold text-sm border-b-3 transition-all cursor-pointer border-transparent text-gray-400">
                    <span class="step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-gray-200 text-gray-500">3</span>
                    <span class="hidden sm:inline">Itinerary</span>
                </button>
                <button onclick="goToStep(4)" id="step-tab-4"
                    class="step-tab flex-1 flex items-center justify-center gap-2 py-4 font-bold text-sm border-b-3 transition-all cursor-pointer border-transparent text-gray-400">
                    <span class="step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-gray-200 text-gray-500">4</span>
                    <span class="hidden sm:inline">Summary</span>
                </button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 py-10">

        <!-- ============ STEP 1: FLIGHTS ============ -->
        <section id="step-1" class="step-section">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-blue-50 p-3 rounded-2xl">
                    <i data-lucide="plane" class="w-6 h-6 text-blue-500"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black">Select Your Flight</h2>
                    <p class="text-gray-400 text-sm">{{ $from }} → {{ $destination }} ·
                        {{ \Carbon\Carbon::parse($departureDate)->format('M d, Y') }}
                        · Budget: ₹{{ number_format($flightBudget) }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($flights->take(6) as $flight)
                    <div class="flight-card bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all border-2 border-gray-50 cursor-pointer group"
                         data-flight-id="{{ $flight->id }}"
                         data-flight-airline="{{ $flight->airline }}"
                         data-flight-number="{{ $flight->flight_number }}"
                         data-flight-price="{{ $flight->price }}"
                         data-flight-price-pp="{{ $flight->price_per_person }}"
                         onclick="selectFlight(this)">
                        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
                            <!-- Airline Info -->
                            <div class="flex items-center gap-4 min-w-[180px]">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden border border-gray-100">
                                    @if($flight->airline_logo)
                                        <img src="{{ $flight->airline_logo }}" alt="{{ $flight->airline }}" class="w-10 h-10 object-contain" referrerpolicy="no-referrer">
                                    @else
                                        <i data-lucide="plane" class="w-6 h-6 text-primary"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h3 class="font-bold text-sm">{{ $flight->airline }}</h3>
                                        @if(($flight->trip_type ?? 2) == 1)
                                            <span class="bg-primary/10 text-primary text-[8px] font-black uppercase px-2 py-0.5 rounded-full">Round Trip</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 text-[10px] font-bold">{{ $flight->flight_number }} · {{ $flight->airplane ?? 'Economy' }}</p>
                                </div>
                            </div>

                            <!-- Flight Routes -->
                            <div class="flex-1 w-full space-y-4">
                                <!-- Outbound -->
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-left w-12">
                                        <span class="text-[8px] font-black uppercase text-gray-300 block mb-1">DEP</span>
                                        <p class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}</p>
                                        <p class="text-[10px] font-bold text-gray-400">{{ $flight->from_code }}</p>
                                    </div>
                                    <div class="flex-1 flex flex-col items-center gap-1">
                                        <span class="text-[9px] font-bold text-gray-300 lowercase italic">{{ $flight->duration }}</span>
                                        <div class="relative w-full h-[2px] bg-gray-100 flex items-center justify-center">
                                            <div class="absolute inset-0 flex justify-between items-center px-1">
                                                <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                                <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                            </div>
                                            <i data-lucide="plane" class="w-3 h-3 text-primary relative bg-white px-0.5 rotate-90"></i>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">{{ $flight->stops }}</span>
                                    </div>
                                    <div class="text-right w-12">
                                        <span class="text-[8px] font-black uppercase text-gray-300 block mb-1">ARR</span>
                                        <p class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}</p>
                                        <p class="text-[10px] font-bold text-gray-400">{{ $flight->to_code }}</p>
                                    </div>
                                </div>

                                <!-- Return Leg -->
                                @if(isset($flight->return_leg) && $flight->return_leg)
                                    <div class="pt-3 border-t border-gray-50 flex items-center justify-between gap-4">
                                        <div class="text-left w-12 text-blue-500/60">
                                            <p class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($flight->return_leg['departure_time'])->format('H:i') }}</p>
                                            <p class="text-[10px] font-bold uppercase tracking-tight">{{ $flight->to_code }}</p>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <span class="text-[9px] font-bold text-gray-300 lowercase italic">{{ $flight->return_leg['duration'] }}</span>
                                            <div class="relative w-full h-[2px] bg-gray-50 flex items-center justify-center opacity-60">
                                                <i data-lucide="plane" class="w-3 h-3 text-gray-300 relative bg-white px-0.5 -rotate-90"></i>
                                            </div>
                                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-tighter">{{ $flight->return_leg['stops'] }}</span>
                                        </div>
                                        <div class="text-right w-12 text-blue-500/60">
                                            <p class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($flight->return_leg['arrival_time'])->format('H:i') }}</p>
                                            <p class="text-[10px] font-bold uppercase tracking-tight">{{ $flight->from_code }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Pricing -->
                            <div class="text-right min-w-[150px] lg:border-l lg:border-gray-50 lg:pl-6">
                                <p class="text-2xl font-black text-primary">₹{{ number_format($flight->price_per_person) }} <span class="text-[9px] text-gray-400 font-medium">/ person</span></p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-3 italic">Total: ₹{{ number_format($flight->price) }}</p>
                                <div class="select-badge hidden bg-emerald-500 text-white py-1.5 px-4 rounded-xl text-xs font-black text-center uppercase tracking-wide">
                                    ✓ Selected
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-16 text-center space-y-4 border border-gray-50">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto">
                            <i data-lucide="plane" class="w-8 h-8 text-blue-300"></i>
                        </div>
                        <h3 class="text-xl font-bold">No flights found</h3>
                        <p class="text-gray-400 max-w-xs mx-auto text-sm">We couldn't find flights for this route.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end mt-8">
                <button onclick="goToStep(2)" id="btn-step1-next"
                    class="bg-gray-200 text-gray-400 px-8 py-3 rounded-2xl font-bold text-sm cursor-not-allowed transition-all flex items-center gap-2"
                    disabled>
                    Continue to Hotels
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </section>

        <!-- ============ STEP 2: HOTELS ============ -->
        <section id="step-2" class="step-section hidden">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-emerald-50 p-3 rounded-2xl">
                    <i data-lucide="building-2" class="w-6 h-6 text-emerald-500"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black">Select Your Hotel</h2>
                    <p class="text-gray-400 text-sm">{{ $destination }} ·
                        {{ \Carbon\Carbon::parse($departureDate)->format('M d') }} -
                        {{ \Carbon\Carbon::parse($returnDate)->format('M d') }} ·
                        {{ $days }} night{{ $days > 1 ? 's' : '' }}
                        · Budget: ₹{{ number_format($hotelBudget) }}
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($hotels->take(6) as $hotel)
                    <div class="hotel-card bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all border-2 border-gray-50 cursor-pointer group"
                         data-hotel-id="{{ $hotel->id }}"
                         data-hotel-name="{{ $hotel->name }}"
                         data-hotel-ppn="{{ $hotel->price_per_night }}"
                         data-hotel-nights="{{ $days }}"
                         onclick="selectHotel(this)">
                        <div class="relative h-48 bg-linear-to-br from-emerald-100 to-teal-50">
                            @if($hotel->thumbnail)
                                <img src="{{ $hotel->thumbnail }}" alt="{{ $hotel->name }}"
                                    referrerpolicy="no-referrer"
                                    crossorigin="anonymous"
                                    loading="lazy"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="absolute inset-0 bg-linear-to-br from-emerald-100 to-teal-50 items-center justify-center" style="display:none;">
                                    <i data-lucide="building-2" class="w-12 h-12 text-emerald-300"></i>
                                </div>
                            @else
                                <div class="absolute inset-0 bg-linear-to-br from-emerald-100 to-teal-50 flex items-center justify-center">
                                    <i data-lucide="building-2" class="w-12 h-12 text-emerald-300"></i>
                                </div>
                            @endif

                            @if($hotel->rating)
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                                    <i data-lucide="star" class="w-3 h-3 text-orange-400 fill-orange-400"></i>
                                    <span class="text-xs font-bold">{{ $hotel->rating }}</span>
                                    @if($hotel->reviews)
                                        <span class="text-[9px] text-gray-400">({{ number_format($hotel->reviews) }})</span>
                                    @endif
                                </div>
                            @endif

                            @if(isset($hotel->type) && $hotel->type)
                                <div class="absolute bottom-3 left-3 bg-black/50 backdrop-blur-sm text-white px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                                    {{ $hotel->type }}
                                </div>
                            @endif

                            <div class="hotel-selected-badge absolute top-3 right-3 hidden bg-emerald-500 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide shadow-lg">
                                ✓ Selected
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="text-base font-bold mb-1 leading-tight">{{ $hotel->name }}</h3>
                            <p class="text-gray-400 text-xs flex items-center gap-1 mb-2">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                {{ $hotel->city }}
                                @if($hotel->nearby) · Near {{ $hotel->nearby }} @endif
                            </p>

                            @if($hotel->amenities)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice(explode(',', $hotel->amenities), 0, 3) as $amenity)
                                        <span class="bg-gray-50 text-gray-500 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border border-gray-100">
                                            {{ trim($amenity) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Room Selector -->
                            <div class="bg-gray-50 rounded-xl p-3 mb-3" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Rooms</span>
                                    <div class="flex items-center gap-2">
                                        <button onclick="changeRooms({{ $hotel->id }}, -1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-sm font-bold hover:bg-gray-100 transition-all">−</button>
                                        <span id="rooms-{{ $hotel->id }}" class="text-sm font-black w-6 text-center">1</span>
                                        <button onclick="changeRooms({{ $hotel->id }}, 1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-sm font-bold hover:bg-gray-100 transition-all">+</button>
                                    </div>
                                </div>
                                <div class="text-[10px] text-gray-400 font-medium">
                                    <span id="rooms-{{ $hotel->id }}-count">1</span> room × {{ $days }} night{{ $days > 1 ? 's' : '' }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Per night</p>
                                    <p class="text-sm font-black">
                                        @if($hotel->price_per_night > 0)
                                            ₹{{ number_format($hotel->price_per_night) }}
                                        @else
                                            <span class="text-gray-400 text-sm">On request</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Total</p>
                                    <p class="text-lg font-black text-emerald-600" id="hotel-total-{{ $hotel->id }}">
                                        ₹{{ number_format($hotel->price_per_night * $days) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-16 text-center space-y-4 border border-gray-50">
                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto">
                            <i data-lucide="building-2" class="w-8 h-8 text-emerald-300"></i>
                        </div>
                        <h3 class="text-xl font-bold">No hotels found</h3>
                        <p class="text-gray-400 max-w-xs mx-auto text-sm">Try broadening your budget or dates.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex justify-between mt-8">
                <button onclick="goToStep(1)" class="px-8 py-3 rounded-2xl font-bold text-sm border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                </button>
                <button onclick="goToStep(3)" id="btn-step2-next"
                    class="bg-gray-200 text-gray-400 px-8 py-3 rounded-2xl font-bold text-sm cursor-not-allowed transition-all flex items-center gap-2"
                    disabled>
                    Continue to Itinerary*u*
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </div>
        </section>

        <!-- ============ STEP 3: ITINERARY ============ -->
        <section id="step-3" class="step-section hidden">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-amber-50 p-3 rounded-2xl">
                    <i data-lucide="sparkles" class="w-6 h-6 text-amber-500"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black">Review Your Itinerary</h2>
                    <p class="text-gray-400 text-sm">Your personalized {{ $days }}-day plan for {{ $destination }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-50">
                <div class="bg-linear-to-r from-amber-500 to-orange-500 p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-1">{{ $days }}-Day {{ $destination }} Itinerary</h3>
                            <p class="opacity-70 text-sm">Real places & restaurants curated by AI</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-0.5">Powered by</p>
                            <p class="font-bold flex items-center gap-1.5 text-sm">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                Tourex AI
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-10">
                    <!-- Loading State -->
                    <div id="itinerary-loading" class="flex flex-col items-center justify-center py-16 space-y-6">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-full border-4 border-amber-100 border-t-amber-500 animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i data-lucide="sparkles" class="w-8 h-8 text-amber-500 animate-pulse"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Crafting Your Perfect Itinerary</h3>
                            <p class="text-gray-400 text-sm max-w-md">Puter AI is creating a personalized day-by-day plan for your {{ $destination }} trip...</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-300 font-medium">
                            <div class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></div>
                            <span id="ai-status-text">Connecting to AI...</span>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="itinerary-error" style="display:none" class="flex flex-col items-center justify-center py-16 space-y-4">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800" id="error-title">Generation Failed</h3>
                        <p class="text-gray-400 text-sm max-w-md text-center" id="error-message">Something went wrong. Please try again.</p>
                        <!-- Puter Sign-in Button (shown when auth is required) -->
                        <button id="btn-puter-signin" onclick="puterSignInAndRetry()" style="display:none"
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-200">
                            <i data-lucide="log-in" class="w-4 h-4"></i> Sign in with Puter to Generate AI Itinerary
                        </button>
                        <button onclick="generateItinerary()" id="btn-retry-itinerary" class="bg-amber-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-amber-600 transition-all flex items-center gap-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Retry
                        </button>
                    </div>

                    <!-- Generated Content -->
                    <div id="itinerary-content" style="display:none" class="prose prose-lg prose-slate max-w-none"></div>
                </div>
            </div>

            <div class="flex justify-between mt-8">
                <button onclick="goToStep(2)" class="px-8 py-3 rounded-2xl font-bold text-sm border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                </button>
                <button onclick="approveItinerary()" id="btn-approve"
                    class="bg-amber-500 text-white px-8 py-3 rounded-2xl font-bold text-sm hover:bg-amber-600 transition-all flex items-center gap-2 shadow-lg shadow-amber-200">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Approve & Continue
                </button>
            </div>
        </section>

        <!-- ============ STEP 4: SUMMARY & PAYMENT ============ -->
        <section id="step-4" class="step-section hidden">
            <div class="flex items-center gap-4 mb-6">
                <div class="bg-purple-50 p-3 rounded-2xl">
                    <i data-lucide="receipt" class="w-6 h-6 text-purple-500"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black">Trip Summary</h2>
                    <p class="text-gray-400 text-sm">Review your selections and proceed to payment</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Summary Cards -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Selected Flight -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-blue-50 p-2 rounded-xl"><i data-lucide="plane" class="w-5 h-5 text-blue-500"></i></div>
                            <h3 class="font-bold text-lg">Flight</h3>
                            <button onclick="goToStep(1)" class="ml-auto text-xs font-bold text-blue-500 hover:underline">Change</button>
                        </div>
                        <div id="summary-flight" class="text-gray-500 text-sm">No flight selected</div>
                    </div>

                    <!-- Selected Hotel -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-emerald-50 p-2 rounded-xl"><i data-lucide="building-2" class="w-5 h-5 text-emerald-500"></i></div>
                            <h3 class="font-bold text-lg">Hotel</h3>
                            <button onclick="goToStep(2)" class="ml-auto text-xs font-bold text-emerald-500 hover:underline">Change</button>
                        </div>
                        <div id="summary-hotel" class="text-gray-500 text-sm">No hotel selected</div>
                    </div>

                    <!-- Itinerary -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-amber-50 p-2 rounded-xl"><i data-lucide="map" class="w-5 h-5 text-amber-500"></i></div>
                            <h3 class="font-bold text-lg">AI Itinerary</h3>
                            <span class="ml-auto bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase px-3 py-1 rounded-full">✓ Approved</span>
                        </div>
                        <p class="text-gray-500 text-sm">{{ $days }}-day personalized plan for {{ $destination }}</p>
                    </div>
                </div>

                <!-- Price Breakdown & Pay -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-50 sticky top-[140px]">
                        <h3 class="font-bold text-lg mb-5">Price Breakdown</h3>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i data-lucide="plane" class="w-3.5 h-3.5 text-blue-400"></i> Flight</span>
                                <span class="font-bold" id="price-flight">₹0</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i data-lucide="building-2" class="w-3.5 h-3.5 text-emerald-400"></i> Hotel</span>
                                <span class="font-bold" id="price-hotel">₹0</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i data-lucide="map" class="w-3.5 h-3.5 text-amber-400"></i> Activities</span>
                                <span class="font-bold">₹{{ number_format($activityBudget) }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-lg">Total</span>
                                <span class="font-black text-2xl text-primary" id="price-total">₹0</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-[10px] text-gray-400 uppercase tracking-wide">Budget remaining</span>
                                <span class="text-sm font-bold" id="budget-remaining">₹{{ number_format($budget) }}</span>
                            </div>
                        </div>

                        <form action="{{ route('book.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="flight" id="booking-type">
                            <input type="hidden" name="reference_id" value="0" id="booking-ref">
                            <input type="hidden" name="total_price" value="0" id="booking-price">
                            <button type="submit" id="btn-pay"
                                class="w-full bg-primary text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-primary/10 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:shadow-none"
                                disabled>
                                Proceed to Payment
                            </button>
                        </form>

                        <p class="text-[10px] text-gray-400 text-center mt-3">Secure demo payment gateway</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-start mt-8">
                <button onclick="goToStep(3)" class="px-8 py-3 rounded-2xl font-bold text-sm border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                </button>
            </div>
        </section>

    </main>

    <footer class="bg-primary text-white/40 py-8 px-8 text-center text-sm mt-12">
        <p>© 2026 Tourex Travel Guide. Built with Laravel and Tailwind CSS.</p>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // === State ===
        let currentStep = 1;
        let selectedFlight = null;
        let selectedHotel = null;
        let hotelRooms = {};   // hotelId -> room count
        let itineraryApproved = false;

        const DAYS = {{ $days }};
        const BUDGET = {{ $budget }};
        const ACTIVITY_BUDGET = {{ $activityBudget }};
        const DESTINATION = @json($destination);
        const TRAVELERS = {{ $travelers }};
        const ITINERARY_ID = {{ $itinerary->id }};
        const DEPARTURE_CITY = @json($from);
        const ARRIVAL_AIRPORT = @json($to);
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        let itineraryGenerated = false;

        // === Step Navigation ===
        function goToStep(step) {
            // Validate progression
            if (step > 1 && !selectedFlight) return;
            if (step > 2 && !selectedHotel) return;
            if (step > 3 && !itineraryApproved) return;

            currentStep = step;
            document.querySelectorAll('.step-section').forEach(s => s.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');
            updateStepTabs();
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Trigger Puter.js AI generation when entering Step 3
            if (step === 3 && !itineraryGenerated) {
                generateItinerary();
            }
            if (step === 4) updateSummary();
            lucide.createIcons();
        }

        function updateStepTabs() {
            for (let i = 1; i <= 4; i++) {
                const tab = document.getElementById('step-tab-' + i);
                const num = tab.querySelector('.step-num');
                if (i === currentStep) {
                    tab.className = tab.className.replace(/border-transparent|text-gray-400/g, '').trim();
                    tab.classList.add('border-blue-500', 'text-blue-600');
                    num.className = 'step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-blue-500 text-white';
                } else if (i < currentStep || (i === 1 && selectedFlight) || (i === 2 && selectedHotel) || (i === 3 && itineraryApproved)) {
                    tab.className = tab.className.replace(/border-blue-500|text-blue-600|border-transparent|text-gray-400/g, '').trim();
                    tab.classList.add('border-emerald-500', 'text-emerald-600');
                    num.className = 'step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-emerald-500 text-white';
                } else {
                    tab.className = tab.className.replace(/border-blue-500|text-blue-600|border-emerald-500|text-emerald-600/g, '').trim();
                    tab.classList.add('border-transparent', 'text-gray-400');
                    num.className = 'step-num w-7 h-7 rounded-full flex items-center justify-center text-xs font-black bg-gray-200 text-gray-500';
                }
            }
        }

        // === Flight Selection ===
        function selectFlight(el) {
            document.querySelectorAll('.flight-card').forEach(c => {
                c.classList.remove('border-blue-500', 'bg-blue-50/30');
                c.classList.add('border-gray-50');
                c.querySelector('.select-badge').classList.add('hidden');
            });

            el.classList.remove('border-gray-50');
            el.classList.add('border-blue-500', 'bg-blue-50/30');
            el.querySelector('.select-badge').classList.remove('hidden');

            selectedFlight = {
                id: el.dataset.flightId,
                airline: el.dataset.flightAirline,
                number: el.dataset.flightNumber,
                price: parseFloat(el.dataset.flightPrice),
                pricePP: parseFloat(el.dataset.flightPricePp),
            };

            const btn = document.getElementById('btn-step1-next');
            btn.disabled = false;
            btn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-primary', 'text-white', 'hover:bg-black', 'shadow-lg');
        }

        // === Hotel Selection ===
        function selectHotel(el) {
            document.querySelectorAll('.hotel-card').forEach(c => {
                c.classList.remove('border-emerald-500');
                c.classList.add('border-gray-50');
                c.querySelector('.hotel-selected-badge').classList.add('hidden');
            });

            el.classList.remove('border-gray-50');
            el.classList.add('border-emerald-500');
            el.querySelector('.hotel-selected-badge').classList.remove('hidden');

            const hotelId = el.dataset.hotelId;
            const ppn = parseFloat(el.dataset.hotelPpn);
            const rooms = hotelRooms[hotelId] || 1;

            selectedHotel = {
                id: hotelId,
                name: el.dataset.hotelName,
                ppn: ppn,
                rooms: rooms,
                total: ppn * DAYS * rooms,
            };

            const btn = document.getElementById('btn-step2-next');
            btn.disabled = false;
            btn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            btn.classList.add('bg-primary', 'text-white', 'hover:bg-black', 'shadow-lg');
        }

        // === Room Count ===
        function changeRooms(hotelId, delta) {
            const current = hotelRooms[hotelId] || 1;
            const newVal = Math.max(1, Math.min(10, current + delta));
            hotelRooms[hotelId] = newVal;

            document.getElementById('rooms-' + hotelId).textContent = newVal;
            document.getElementById('rooms-' + hotelId + '-count').textContent = newVal;

            // Recalculate total
            const card = document.querySelector(`.hotel-card[data-hotel-id="${hotelId}"]`);
            const ppn = parseFloat(card.dataset.hotelPpn);
            const total = ppn * DAYS * newVal;
            document.getElementById('hotel-total-' + hotelId).textContent = '₹' + total.toLocaleString('en-IN');

            // If this hotel is selected, update selection
            if (selectedHotel && selectedHotel.id == hotelId) {
                selectedHotel.rooms = newVal;
                selectedHotel.total = total;
            }
        }

        // === Itinerary Approval ===
        function approveItinerary() {
            itineraryApproved = true;
            goToStep(4);
        }

        // === Summary ===
        function updateSummary() {
            // Flight
            if (selectedFlight) {
                document.getElementById('summary-flight').innerHTML = `
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-primary text-base">${selectedFlight.airline}</p>
                            <p class="text-xs text-gray-400">${selectedFlight.number}</p>
                        </div>
                        <p class="font-black text-primary text-lg">₹${selectedFlight.price.toLocaleString('en-IN')}</p>
                    </div>
                `;
                document.getElementById('price-flight').textContent = '₹' + selectedFlight.price.toLocaleString('en-IN');
            }

            // Hotel
            if (selectedHotel) {
                document.getElementById('summary-hotel').innerHTML = `
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-primary text-base">${selectedHotel.name}</p>
                            <p class="text-xs text-gray-400">${selectedHotel.rooms} room${selectedHotel.rooms > 1 ? 's' : ''} × ${DAYS} night${DAYS > 1 ? 's' : ''} @ ₹${selectedHotel.ppn.toLocaleString('en-IN')}/night</p>
                        </div>
                        <p class="font-black text-emerald-600 text-lg">₹${selectedHotel.total.toLocaleString('en-IN')}</p>
                    </div>
                `;
                document.getElementById('price-hotel').textContent = '₹' + selectedHotel.total.toLocaleString('en-IN');
            }

            // Total
            const flightCost = selectedFlight ? selectedFlight.price : 0;
            const hotelCost = selectedHotel ? selectedHotel.total : 0;
            const total = flightCost + hotelCost + ACTIVITY_BUDGET;
            const remaining = BUDGET - total;

            document.getElementById('price-total').textContent = '₹' + total.toLocaleString('en-IN');
            const remainEl = document.getElementById('budget-remaining');
            remainEl.textContent = (remaining >= 0 ? '₹' : '-₹') + Math.abs(remaining).toLocaleString('en-IN');
            remainEl.classList.toggle('text-red-500', remaining < 0);
            remainEl.classList.toggle('text-emerald-600', remaining >= 0);

            // Booking form
            document.getElementById('booking-ref').value = selectedFlight ? selectedFlight.id : 0;
            document.getElementById('booking-price').value = total;

            // Enable pay button
            const payBtn = document.getElementById('btn-pay');
            payBtn.disabled = false;
        }

        // === Puter.js AI Itinerary Generation ===
        let puterAuthenticated = false;

        function buildItineraryPrompt() {
            const flightInfo = selectedFlight
                ? `Flying ${selectedFlight.airline} (${selectedFlight.number}), costing ₹${selectedFlight.price.toLocaleString('en-IN')}`
                : 'Flight details pending';
            const hotelInfo = selectedHotel
                ? `Staying at ${selectedHotel.name} (₹${selectedHotel.ppn.toLocaleString('en-IN')}/night)`
                : 'Hotel details pending';

            return `Create a highly detailed ${DAYS}-day travel itinerary for ${DESTINATION}.

Trip Context:
- Travelers: ${TRAVELERS}
- Total Food & Activity Budget: ₹${ACTIVITY_BUDGET.toLocaleString('en-IN')}
- ${flightInfo}
- ${hotelInfo}
- From: ${DEPARTURE_CITY} (${ARRIVAL_AIRPORT} airport) to ${DESTINATION}

MANDATORY STRUCTURE & DETAILS (VERY IMPORTANT):

1. **Daily Layout**: Every day must be clearly broken into:
   - ### 🌅 Morning (Early Morning - 12:00 PM)
   - ### ☀️ Afternoon (12:00 PM - 5:00 PM)
   - ### 🌙 Evening (5:00 PM - Late Night)

2. **Meals**: You MUST include a specific restaurant suggestion for EVERY meal (Breakfast, Lunch, Dinner). No repeats!
   - Format: "🍽️ **[Meal] at [Restaurant Name]** ([Specific Location/Landmark]) | 🕒 [Hours] | ₹[Cost per person]"

3. **Transport**: For every major move (e.g., Airport to Hotel, Hotel to Attraction), specify:
   - "🚗 **How to Travel**: [Mode e.g. Uber/Auto/Metro] | [Time e.g. 30 mins] | ₹[Est. Cost]"

4. **Places to Visit**: Include at least 2-3 specific sightseeing spots or attractions per day.
   - Format: "🎯 **[Place Name]**: [What to see/do there] | 🕒 [Recommended Duration] | ₹[Entry fee if any]"

5. **Day 1 specifics**: Start with arrival at ${ARRIVAL_AIRPORT} airport and clear transport instructions to ${DESTINATION}.
6. **Last Day specifics**: Include transport instructions back to ${ARRIVAL_AIRPORT} airport.

Format as clean Markdown with emojis. Ensure a logical flow where transport moves are suggested between meals and places. Keep the total food and activity costs within ₹${ACTIVITY_BUDGET.toLocaleString('en-IN')}. End with a "## 💡 Pro Tips" section.`;
        }

        function showAuthError(loadingEl, errorEl) {
            loadingEl.style.display = 'none';
            errorEl.style.display = 'flex';
            document.getElementById('error-title').textContent = 'Sign in Required';
            document.getElementById('error-message').textContent = 'Puter.js requires you to sign in with a free Puter account to use AI features. Click below to sign in — it only takes a few seconds!';
            document.getElementById('btn-puter-signin').style.display = 'flex';
            document.getElementById('btn-retry-itinerary').style.display = 'none';
            lucide.createIcons();
        }

        function showGeneralError(loadingEl, errorEl, message) {
            loadingEl.style.display = 'none';
            errorEl.style.display = 'flex';
            document.getElementById('error-title').textContent = 'Generation Failed';
            document.getElementById('error-message').textContent = message || 'Something went wrong. Please try again.';
            document.getElementById('btn-puter-signin').style.display = 'none';
            document.getElementById('btn-retry-itinerary').style.display = 'flex';
            lucide.createIcons();
        }

        // Sign in with Puter and retry (must be called from user click for popup to work)
        async function puterSignInAndRetry() {
            try {
                const signinBtn = document.getElementById('btn-puter-signin');
                signinBtn.textContent = 'Signing in...';
                signinBtn.disabled = true;

                await puter.auth.signIn();
                puterAuthenticated = true;
                console.log('Puter sign-in successful');

                // Retry generation after successful sign-in
                generateItinerary();
            } catch (error) {
                console.error('Puter sign-in failed:', error);
                const signinBtn = document.getElementById('btn-puter-signin');
                signinBtn.textContent = 'Sign in with Puter to Generate AI Itinerary';
                signinBtn.disabled = false;

                const loadingEl = document.getElementById('itinerary-loading');
                const errorEl = document.getElementById('itinerary-error');
                showGeneralError(loadingEl, errorEl, 'Sign-in was cancelled. Please sign in to generate your AI itinerary.');
            }
        }

        async function generateItinerary() {
            const loadingEl = document.getElementById('itinerary-loading');
            const errorEl = document.getElementById('itinerary-error');
            const contentEl = document.getElementById('itinerary-content');
            const statusText = document.getElementById('ai-status-text');
            const approveBtn = document.getElementById('btn-approve');

            // Show loading, hide others
            loadingEl.style.display = 'flex';
            errorEl.style.display = 'none';
            contentEl.style.display = 'none';
            approveBtn.disabled = true;
            approveBtn.classList.add('opacity-50', 'cursor-not-allowed');

            const prompt = buildItineraryPrompt();
            statusText.textContent = 'Generating your itinerary...';

            // Set a timeout of 90 seconds
            const timeoutId = setTimeout(() => {
                console.warn('Puter.js AI timed out after 90s');
                showGeneralError(loadingEl, errorEl, 'AI generation timed out. Please retry — it usually works on the second attempt.');
            }, 90000);

            try {
                // Check if Puter.js SDK is loaded
                if (typeof puter === 'undefined' || !puter.ai) {
                    throw new Error('Puter.js SDK not loaded');
                }

                statusText.textContent = 'Connecting to Puter AI...';

                const response = await puter.ai.chat(prompt);

                clearTimeout(timeoutId);

                const plan = typeof response === 'string' ? response : (response?.message?.content || response?.toString() || '');

                if (!plan || plan.length < 100) {
                    throw new Error('AI response was too short or empty');
                }

                // Render the markdown
                contentEl.innerHTML = marked.parse(plan);
                itineraryGenerated = true;

                // Show content, hide loading
                loadingEl.style.display = 'none';
                contentEl.style.display = 'block';

                // Enable approve button
                approveBtn.disabled = false;
                approveBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Re-initialize Lucide icons
                lucide.createIcons();

                // Silently save to backend
                saveToBackend(plan);

            } catch (error) {
                clearTimeout(timeoutId);
                console.error('Puter.js AI error:', error);

                // Check if this is an authentication error (401 / whoami failure)
                const errorMsg = error?.message || error?.toString() || '';
                const isAuthError = errorMsg.includes('401') ||
                                   errorMsg.includes('auth') ||
                                   errorMsg.includes('sign') ||
                                   errorMsg.includes('Unauthorized') ||
                                   errorMsg.includes('whoami') ||
                                   errorMsg.includes('not logged in');

                if (isAuthError && !puterAuthenticated) {
                    // Show the sign-in prompt instead of fallback
                    showAuthError(loadingEl, errorEl);
                } else {
                    // For other errors, show general error with retry
                    showGeneralError(loadingEl, errorEl, 'AI generation failed. You can retry or use the basic itinerary below.');
                }
            }
        }



        // Save generated itinerary to backend
        async function saveToBackend(plan) {
            try {
                await fetch('/trip/save-itinerary', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        itinerary_id: ITINERARY_ID,
                        generated_plan: plan,
                    }),
                });
            } catch (e) {
                console.warn('Failed to save itinerary to backend:', e);
            }
        }
    </script>

    <style>
        .prose h2 {
            font-weight: 800;
            color: #1A1A1A;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .prose h3 {
            font-weight: 700;
            color: #1A1A1A;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }

        .prose p {
            color: #4A4A4A;
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose li {
            color: #4A4A4A;
            margin-bottom: 0.4rem;
        }

        .prose strong {
            color: #1A1A1A;
            font-weight: 700;
        }

        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .prose th,
        .prose td {
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .prose th {
            background: #f9fafb;
            font-weight: 700;
        }

        html {
            scroll-behavior: smooth;
        }

        .step-section {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>

</html>
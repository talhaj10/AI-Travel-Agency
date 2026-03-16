<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tourex - Find your next adventure</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-white text-text-body">
    <div class="relative min-h-screen">
        <!-- Hero Background -->
        <div class="absolute inset-0 z-0 h-[90vh]">
            <img src="{{ asset('images/hero_bg.png') }}" alt="Hero Background"
                class="w-full h-full object-cover rounded-b-[4rem]">
            <div class="absolute inset-0 bg-black/30 rounded-b-[4rem]"></div>
        </div>

        <!-- Header / Navbar -->
        <header class="relative z-10 flex items-center justify-between px-8 py-6 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <span class="text-white text-3xl font-bold tracking-tight">tourex</span>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-white/90 font-medium">
                <a href="#" class="hover:text-white transition-colors">Home</a>
                <a href="#" class="hover:text-white transition-colors">Tour</a>
                <a href="#" class="hover:text-white transition-colors">About Us</a>
                <a href="#" class="hover:text-white transition-colors">Contact Us</a>
            </nav>

            <div class="flex items-center gap-4">
                <button
                    class="px-6 py-2.5 rounded-full border border-white/30 text-white font-medium hover:bg-white hover:text-primary transition-all">
                    Sign In
                </button>
            </div>
        </header>

        <!-- Hero Content -->
        <main class="relative z-10 max-w-7xl mx-auto px-8 pt-16 pb-40">
            <!-- Centered Hero Text -->
            <div class="text-center mb-16">
                <h1 class="text-white text-6xl md:text-8xl font-bold leading-[1.1] mb-6 tracking-tight">
                    Let's find your<br>
                    <span class="text-white">next adventure</span>
                </h1>
                <p class="text-white/80 text-xl max-w-lg mx-auto leading-relaxed font-light">
                    When an unknown printer took a gallery offer type area year anytype of make special moment
                </p>
            </div>

            <div class="flex justify-center">
                <!-- Unified Trip Planner Widget -->
                <div class="bg-white p-10 rounded-[3rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] w-full max-w-xl">
                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-8">
                        <div class="bg-primary/5 p-3 rounded-2xl">
                            <i data-lucide="sparkles" class="w-6 h-6 text-primary"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-primary">Plan Your Entire Trip</h3>
                            <p class="text-gray-400 text-xs">Flights + Hotels + AI Itinerary</p>
                        </div>
                    </div>

                    <form action="{{ route('trip.plan') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Step 1: Location to Visit -->
                        <div>
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Where
                                do you want to go?</label>
                            <div class="relative">
                                <i data-lucide="map"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input type="text" name="destination" id="main_destination" placeholder="Paris, France"
                                    required
                                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary placeholder:text-gray-300">
                            </div>
                        </div>

                        <!-- Step 2: Flight Origins -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Flights
                                    From</label>
                                <div class="relative">
                                    <i data-lucide="plane-takeoff"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="text" name="from" placeholder="Delhi (DEL)" required
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary placeholder:text-gray-300">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Flights
                                    To <span class="text-[8px] lowercase opacity-50 font-medium tracking-normal">(Major
                                        Airport)</span></label>
                                <div class="relative">
                                    <i data-lucide="plane-landing"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="text" name="to" id="flights_to" placeholder="Paris (CDG)" required
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary placeholder:text-gray-300">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 & 4: Trip Type & Dates -->
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Trip
                                    Type</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="trip_type" value="2" checked class="peer sr-only">
                                        <div
                                            class="bg-gray-50 border border-gray-100 rounded-xl py-3 text-center text-xs font-bold text-gray-400 peer-checked:bg-primary/5 peer-checked:border-primary peer-checked:text-primary transition-all">
                                            Single Trip
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="trip_type" value="1" class="peer sr-only">
                                        <div
                                            class="bg-gray-50 border border-gray-100 rounded-xl py-3 text-center text-xs font-bold text-gray-400 peer-checked:bg-primary/5 peer-checked:border-primary peer-checked:text-primary transition-all">
                                            Round Trip
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Departure
                                        Date</label>
                                    <div class="relative">
                                        <i data-lucide="calendar"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input type="date" name="departure_date"
                                            value="{{ now()->addDays(7)->format('Y-m-d') }}" required
                                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary">
                                    </div>
                                </div>
                                <div id="return_date_container" class="hidden">
                                    <label
                                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Return
                                        Date</label>
                                    <div class="relative">
                                        <i data-lucide="calendar-check"
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input type="date" name="return_date"
                                            value="{{ now()->addDays(14)->format('Y-m-d') }}"
                                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 & 6: Budget & Travelers -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Budget
                                    (₹)</label>
                                <div class="relative">
                                    <i data-lucide="indian-rupee"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="number" name="budget" placeholder="50000" required min="1000"
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary placeholder:text-gray-300">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-2">Travelers</label>
                                <div class="relative">
                                    <i data-lucide="users"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="number" name="travelers" placeholder="1" value="1" required min="1"
                                        max="10"
                                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 pl-12 pr-4 focus:ring-4 focus:ring-primary/5 focus:bg-white transition-all font-semibold text-sm text-primary">
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="plan-trip-btn"
                            class="w-full bg-primary text-white py-5 rounded-2xl font-bold text-lg hover:bg-black transition-all shadow-2xl shadow-primary/30 mt-2 active:scale-[0.98] flex items-center justify-center gap-3">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                            Plan My Trip
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Intro Text Section -->
    <section class="py-20 max-w-7xl mx-auto px-8">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <h2 class="text-5xl md:text-6xl font-bold text-primary leading-tight flex-1">
                Take the step and explore
                <span class="inline-flex items-center align-middle mx-2">
                    <img src="{{ asset('images/dest_kauai_177.png') }}"
                        class="w-32 h-14 object-cover rounded-full shadow-lg">
                </span>
                the world
                <span class="inline-flex items-center align-middle mx-2">
                    <img src="{{ asset('images/dest_flores_177.png') }}"
                        class="w-32 h-14 object-cover rounded-full shadow-lg">
                </span>
                waiting for you.
            </h2>
            <div class="flex-1 space-y-6">
                <p class="text-gray-500 text-lg leading-relaxed max-w-lg">
                    Traveling change you. You see new places, meet new people, and become a new version of yourself.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats and Image Section -->
    <section class="bg-gray-50 py-32 rounded-[4rem]">
        <div class="max-w-7xl mx-auto px-8 grid lg:grid-cols-2 gap-20 items-center">
            <div class="rounded-[3rem] overflow-hidden shadow-2xl">
                <img src="{{ asset('images/dest_raja_ampat_177.png') }}" alt="Traveler"
                    class="w-full h-[600px] object-cover">
            </div>
            <div class="space-y-12">
                <div class="space-y-6">
                    <h2 class="text-5xl font-bold text-primary leading-tight">Traveling brings you closer to new places,
                        people, and memories</h2>
                    <p class="text-gray-500 text-lg leading-relaxed">Traveling change you. You see new places, meet new
                        people, and become a new version of yourself.</p>
                </div>

                <div class="grid grid-cols-2 gap-12">
                    <div class="space-y-2">
                        <div class="flex items-end gap-2">
                            <h3 class="text-6xl font-black text-primary">97%</h3>
                            <span class="text-gray-400 font-bold mb-2">Customer Satisfaction</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-end gap-2">
                            <h3 class="text-6xl font-black text-primary">85+</h3>
                            <span class="text-gray-400 font-bold mb-2">Popular Destinations</span>
                        </div>
                    </div>
                    <div class="space-y-2 col-span-2">
                        <div class="flex items-end gap-2">
                            <h3 class="text-6xl font-black text-primary">245+</h3>
                            <span class="text-gray-400 font-bold mb-2">Experienced Guide</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Our Destination Section -->
    <section class="py-32 max-w-7xl mx-auto px-8">
        <div class="flex justify-between items-end mb-16">
            <h2 class="text-5xl font-bold text-primary">Experience Our Destination</h2>
            <div class="flex gap-4">
                <button
                    class="p-4 border border-gray-200 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all">
                    <i data-lucide="chevron-left" class="w-6 h-6"></i>
                </button>
                <button
                    class="p-4 border border-gray-200 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all">
                    <i data-lucide="chevron-right" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group relative rounded-[2.5rem] overflow-hidden h-[450px]">
                <img src="{{ asset('images/dest_kauai_177.png') }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h4 class="text-white text-2xl font-bold mb-1">Mountains Kauai</h4>
                    <p class="text-white/70 flex items-center gap-2"><i data-lucide="map-pin" class="w-3 h-3"></i> Maui,
                        USA</p>
                </div>
            </div>

            <div class="group relative rounded-[2.5rem] overflow-hidden h-[450px]">
                <img src="{{ asset('images/dest_bromo_small_177.png') }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h4
                        class="text-white text-2xl font-bold mb-1 text-primary-extra bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl inline-block">
                        Mountain Bromo</h4>
                    <p class="text-white/70 flex items-center gap-2 mt-2"><i data-lucide="map-pin" class="w-3 h-3"></i>
                        Indonesia</p>
                </div>
            </div>

            <div class="group relative rounded-[2.5rem] overflow-hidden h-[450px]">
                <img src="{{ asset('images/dest_flores_177.png') }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h4 class="text-white text-2xl font-bold mb-1">Flores archipelago</h4>
                    <p class="text-white/70 flex items-center gap-2"><i data-lucide="map-pin" class="w-3 h-3"></i>
                        Portugal</p>
                </div>
            </div>

            <div class="group relative rounded-[2.5rem] overflow-hidden h-[450px]">
                <img src="{{ asset('images/dest_raja_ampat_177.png') }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h4 class="text-white text-2xl font-bold mb-1">Raja Ampat</h4>
                    <p class="text-white/70 flex items-center gap-2"><i data-lucide="map-pin" class="w-3 h-3"></i>
                        Indonesia</p>
                </div>
            </div>

            <div class="group relative rounded-[2.5rem] overflow-hidden h-[450px]">
                <img src="{{ asset('images/dest_fuji_177.png') }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h4 class="text-white text-2xl font-bold mb-1">Mountain Fuji</h4>
                    <p class="text-white/70 flex items-center gap-2"><i data-lucide="map-pin" class="w-3 h-3"></i>
                        Indonesia</p>
                </div>
            </div>

            <div class="flex flex-col gap-8">
                <div class="flex-1 bg-primary rounded-[2.5rem] p-10 text-white space-y-4">
                    <h4 class="text-3xl font-bold">Explore More</h4>
                    <p class="opacity-70">Find more amazing destinations and plan your next journey with us.</p>
                    <button class="bg-white text-primary p-4 rounded-full mt-4 self-start">
                        <i data-lucide="arrow-right" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-primary text-white py-32 rounded-t-[4rem]">
        <div class="max-w-7xl mx-auto px-8 grid md:grid-cols-3 gap-20">
            <div class="space-y-6">
                <div class="bg-white/10 w-20 h-20 rounded-full flex items-center justify-center">
                    <i data-lucide="smile" class="w-10 h-10"></i>
                </div>
                <h4 class="text-2xl font-bold">Ease of travel</h4>
                <p class="text-white/60 leading-relaxed">Professional local guide your will eat with hidden paradise
                    places</p>
            </div>
            <div class="space-y-6">
                <div class="bg-white/10 w-20 h-20 rounded-full flex items-center justify-center">
                    <i data-lucide="users" class="w-10 h-10"></i>
                </div>
                <h4 class="text-2xl font-bold">Local Staff Help</h4>
                <p class="text-white/60 leading-relaxed">Professional local guide your will eat with hidden paradise
                    places</p>
            </div>
            <div class="space-y-6">
                <div class="bg-white/10 w-20 h-20 rounded-full flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-10 h-10"></i>
                </div>
                <h4 class="text-2xl font-bold">Safe and reliable</h4>
                <p class="text-white/60 leading-relaxed">Professional local guide your will eat with hidden paradise
                    places</p>
            </div>
        </div>
    </section>

    <footer class="bg-primary text-white/40 py-12 px-8 border-t border-white/5 text-center">
        <p>© 2026 Tourex Travel Guide. Built with Laravel and Tailwind CSS.</p>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Toggle Return Date Visibility
        const tripTypeRadios = document.querySelectorAll('input[name="trip_type"]');
        const returnDateContainer = document.getElementById('return_date_container');
        const returnDateInput = document.querySelector('input[name="return_date"]');

        tripTypeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === '1') { // Round Trip
                    returnDateContainer.classList.remove('hidden');
                    returnDateInput.required = true;
                } else { // Single Trip
                    returnDateContainer.classList.add('hidden');
                    returnDateInput.required = false;
                }
            });
        });

        // Loading state for Plan My Trip button
        document.querySelector('form[action*="trip/plan"]')?.addEventListener('submit', function () {
            const btn = document.getElementById('plan-trip-btn');
            btn.innerHTML = '<svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Planning your trip...';
            btn.disabled = true;
            btn.classList.add('opacity-70');
        });

        // Airport Autocomplete
        const setupAutocomplete = (inputId, resultsId) => {
            const input = document.querySelector(`input[name="${inputId}"]`);
            if (!input) return;

            // Create results container if it doesn't exist
            let results = document.getElementById(resultsId);
            if (!results) {
                results = document.createElement('div');
                results.id = resultsId;
                results.className = 'absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden hidden transform origin-top transition-all duration-200';
                input.parentNode.appendChild(results);
            }

            let debounceTimer;
            input.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    results.classList.add('hidden');
                    return;
                }

                debounceTimer = setTimeout(async () => {
                    try {
                        const response = await fetch(`/api/airports/search?q=${encodeURIComponent(query)}`);
                        const data = await response.json();

                        if (data.length > 0) {
                            results.innerHTML = data.map(item => `
                                <div class="px-5 py-3 hover:bg-primary/5 cursor-pointer transition-colors flex items-center justify-between group" 
                                     onclick="selectAirport('${inputId}', '${item.display}')">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-primary group-hover:text-black">${item.name}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">${item.code} Airport</span>
                                    </div>
                                    <i data-lucide="plane" class="w-4 h-4 text-gray-200 group-hover:text-primary transition-colors"></i>
                                </div>
                            `).join('');
                            results.classList.remove('hidden');
                            lucide.createIcons();
                        } else {
                            results.classList.add('hidden');
                        }
                    } catch (error) {
                        console.error('Error fetching airports:', error);
                    }
                }, 300);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !results.contains(e.target)) {
                    results.classList.add('hidden');
                }
            });
        };

        window.selectAirport = (inputId, value) => {
            const input = document.querySelector(`input[name="${inputId}"]`);
            input.value = value;
            const resultsId = inputId === 'from' ? 'from-results' : 'to-results';
            document.getElementById(resultsId).classList.add('hidden');
        };

        setupAutocomplete('from', 'from-results');
        setupAutocomplete('to', 'to-results');
    </script>
</body>

</html>
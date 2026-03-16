<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Results - Tourex</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="referrer" content="no-referrer">
</head>

<body class="bg-background-soft font-sans antialiased text-primary">
    <!-- Mini Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">tourex</a>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-500">Flight Search Results</span>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-8 py-12">
        <div class="mb-10">
            <h1 class="text-4xl font-bold mb-2">Available Flights</h1>
            <p class="text-gray-500">
                @if(isset($searchFrom) && isset($searchTo))
                    {{ $searchFrom }} → {{ $searchTo }}
                    @if(isset($searchDate)) · {{ \Carbon\Carbon::parse($searchDate)->format('M d, Y') }} @endif
                    ·
                @endif
                We found {{ $flights->count() }} flights for your journey
            </p>
        </div>

        <div class="space-y-6">
            @forelse($flights as $flight)
                <div class="bg-white rounded-4xl p-8 shadow-sm hover:shadow-xl transition-shadow border border-gray-50">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                        <!-- Airline & Type -->
                        <div class="flex items-center gap-6 min-w-[200px]">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center overflow-hidden">
                                @if($flight->airline_logo)
                                    <img src="{{ $flight->airline_logo }}" alt="{{ $flight->airline }}" class="w-12 h-12 object-contain" referrerpolicy="no-referrer">
                                @else
                                    <i data-lucide="plane" class="w-8 h-8 text-primary"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-xl">{{ $flight->airline }}</h3>
                                <p class="text-gray-400 text-sm">{{ $flight->flight_number }} · {{ $flight->travel_class }}</p>
                                @if($flight->airplane)
                                    <p class="text-gray-300 text-xs">{{ $flight->airplane }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Route & Time -->
                        <div class="flex-1 flex items-center justify-center gap-12 text-center">
                            <div>
                                <p class="text-2xl font-black">
                                    {{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}</p>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mt-1">
                                    {{ $flight->from_code ?: $flight->from_city }}</p>
                            </div>

                            <div class="flex flex-col items-center gap-2 flex-1 max-w-[150px]">
                                <span class="text-xs font-bold text-gray-300">{{ $flight->duration }}</span>
                                <div class="relative w-full h-[2px] bg-gray-100">
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-gray-200">
                                    </div>
                                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-gray-200">
                                    </div>
                                    <i data-lucide="plane"
                                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 bg-white px-1"></i>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $flight->stops }}</span>
                            </div>

                            <div>
                                <p class="text-2xl font-black">
                                    {{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}</p>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mt-1">
                                    {{ $flight->to_code ?: $flight->to_city }}</p>
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="text-right min-w-[150px]">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Price</p>
                            <p class="text-3xl font-black text-primary mb-1">₹{{ number_format($flight->price) }}</p>
                            @if($flight->legroom)
                                <p class="text-xs text-gray-400 mb-3">{{ $flight->legroom }} legroom</p>
                            @else
                                <p class="mb-3"></p>
                            @endif
                            <form action="{{ route('book.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="flight">
                                <input type="hidden" name="reference_id" value="{{ $flight->id }}">
                                <input type="hidden" name="total_price" value="{{ $flight->price }}">
                                <button type="submit"
                                    class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-black transition-all shadow-lg shadow-primary/10">
                                    Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-[2.5rem] p-20 text-center space-y-6 shadow-sm">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                        <i data-lucide="search-x" class="w-12 h-12 text-gray-300"></i>
                    </div>
                    <h2 class="text-2xl font-bold">No flights found</h2>
                    <p class="text-gray-400 max-w-xs mx-auto">We couldn't find any flights matching your criteria. Try
                        different cities or dates.</p>
                    <a href="{{ route('home') }}"
                        class="inline-block text-primary font-bold border-b-2 border-primary pb-1">Back to search</a>
                </div>
            @endforelse
        </div>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>

</html>
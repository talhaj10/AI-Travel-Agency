<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Results - Tourex</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-soft font-sans antialiased text-primary">
    <!-- Mini Navbar -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">tourex</a>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-500">Hotel Search Results</span>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-8 py-12">
        <div class="mb-10">
            <h1 class="text-4xl font-bold mb-2">Top Rated Hotels</h1>
            <p class="text-gray-500">
                @if(isset($searchCity))
                    Hotels in {{ $searchCity }}
                    @if(isset($searchBudget) && $searchBudget) · Budget up to ₹{{ number_format($searchBudget) }}/night
                    @endif
                    ·
                @endif
                We found {{ $hotels->count() }} hotels
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($hotels as $hotel)
                <div
                    class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all group border border-gray-50">
                    <div class="relative h-64">
                        @if($hotel->thumbnail)
                            <img src="{{ $hotel->thumbnail }}" alt="{{ $hotel->name }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                                <i data-lucide="building-2" class="w-16 h-16 text-gray-300"></i>
                            </div>
                        @endif

                        @if($hotel->rating)
                            <div
                                class="absolute top-6 left-6 bg-white/90 backdrop-blur-sm px-4 py-1.5 rounded-full flex items-center gap-2 shadow-sm">
                                <i data-lucide="star" class="w-4 h-4 text-orange-400 fill-orange-400"></i>
                                <span class="text-sm font-bold">{{ $hotel->rating }}</span>
                                @if($hotel->reviews)
                                    <span class="text-xs text-gray-400">({{ $hotel->reviews }})</span>
                                @endif
                            </div>
                        @endif

                        @if($hotel->type && $hotel->type !== 'Hotel')
                            <div
                                class="absolute top-6 right-6 bg-primary/90 text-white text-xs font-bold px-3 py-1 rounded-full">
                                {{ $hotel->type }}
                            </div>
                        @endif
                    </div>

                    <div class="p-8">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold mb-1 group-hover:text-primary transition-colors leading-tight">
                                {{ $hotel->name }}
                            </h3>
                            <p class="text-gray-400 text-sm flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                {{ $hotel->city }}
                                @if($hotel->nearby)
                                    · Near {{ $hotel->nearby }}
                                @endif
                            </p>
                        </div>

                        @if($hotel->description)
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $hotel->description }}</p>
                        @endif

                        @if($hotel->amenities)
                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach(explode(',', $hotel->amenities) as $amenity)
                                    <span
                                        class="bg-gray-50 text-gray-500 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg border border-gray-100">
                                        {{ trim($amenity) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Per night</p>
                                <p class="text-2xl font-black">
                                    @if($hotel->price_per_night > 0)
                                        ₹{{ number_format($hotel->price_per_night) }}
                                    @else
                                        <span class="text-gray-400 text-base">Price on request</span>
                                    @endif
                                </p>
                            </div>
                            <form action="{{ route('book.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="hotel">
                                <input type="hidden" name="reference_id" value="{{ $hotel->id }}">
                                <input type="hidden" name="total_price" value="{{ $hotel->price_per_night }}">
                                <button type="submit"
                                    class="bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-black transition-all shadow-lg active:scale-95">
                                    Book Room
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-[2.5rem] p-20 text-center space-y-6 shadow-sm">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                        <i data-lucide="building" class="w-12 h-12 text-gray-300"></i>
                    </div>
                    <h2 class="text-2xl font-bold">No hotels found</h2>
                    <p class="text-gray-400 max-w-xs mx-auto">Try broadening your search criteria or check back later.</p>
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - Tourex</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased text-primary">
    <main class="min-h-screen flex items-center justify-center py-12 px-8">
        <div class="max-w-4xl w-full grid md:grid-cols-2 gap-8">
            <!-- Order Summary -->
            <div class="bg-primary text-white p-12 rounded-[3rem] shadow-2xl">
                <h2 class="text-3xl font-bold mb-8">Trip Summary</h2>
                <div class="space-y-6 mb-12">
                    <div class="flex justify-between items-center py-4 border-b border-white/10">
                        <span class="opacity-60">Type</span>
                        <span class="font-bold uppercase tracking-widest text-sm">{{ $booking->type }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-white/10">
                        <span class="opacity-60">Booking ID</span>
                        <span class="font-bold">#TRX-{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-8">
                        <span class="text-xl font-medium">Total Amount</span>
                        <span class="text-4xl font-black">₹{{ number_format($booking->total_price) }}</span>
                    </div>
                </div>
                <div class="bg-white/10 p-6 rounded-2xl border border-white/10 text-sm leading-relaxed">
                    <p class="opacity-70">Your payment is secured by Tourex Demo Gateway. Success rate is 80% for
                        simulation purposes.</p>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white p-12 rounded-[3rem] shadow-xl border border-gray-100">
                <h2 class="text-3xl font-bold mb-8 text-primary">Secure Payment</h2>

                @if(session('error'))
                    <div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6 text-sm font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('payment.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                    <div class="space-y-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">Payment
                            Method</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="relative border-2 border-gray-100 rounded-2xl p-4 cursor-pointer hover:border-primary transition-all has-checked:border-primary has-checked:bg-gray-50">
                                <input type="radio" name="payment_method" value="card" checked class="hidden">
                                <i data-lucide="credit-card" class="w-6 h-6 mb-2"></i>
                                <span class="font-bold text-sm block">Card</span>
                            </label>
                            <label
                                class="relative border-2 border-gray-100 rounded-2xl p-4 cursor-pointer hover:border-primary transition-all has-checked:border-primary has-checked:bg-gray-50">
                                <input type="radio" name="payment_method" value="upi" class="hidden">
                                <i data-lucide="smartphone" class="w-6 h-6 mb-2"></i>
                                <span class="font-bold text-sm block">UPI</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">Card
                            Details (Demo)</label>
                        <input type="text" placeholder="XXXX XXXX XXXX 4242"
                            class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-primary/5 transition-all font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">Expiry</label>
                            <input type="text" placeholder="MM/YY"
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-primary/5 transition-all font-semibold">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-2">CVC</label>
                            <input type="password" placeholder="***"
                                class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-4 px-6 focus:ring-4 focus:ring-primary/5 transition-all font-semibold">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white py-5 rounded-2xl font-bold text-lg hover:bg-black transition-all shadow-xl shadow-primary/20 mt-4 active:scale-95">
                        Pay ₹{{ number_format($booking->total_price) }}
                    </button>

                    <p class="text-center text-xs text-gray-400">
                        <i data-lucide="shield-check" class="w-3 h-3 inline mr-1"></i>
                        Encrypted with 256-bit SSL
                    </p>
                </form>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed! - Tourex</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased text-primary">
    <main class="min-h-screen flex items-center justify-center p-8">
        <div
            class="max-w-xl w-full bg-white rounded-[3.5rem] shadow-2xl overflow-hidden border border-gray-100 text-center">
            <div class="bg-primary p-16 text-white text-center">
                <div
                    class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce">
                    <i data-lucide="check" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-4xl font-bold mb-4">Trip Confirmed!</h1>
                <p class="opacity-70">Pack your bags, the world is waiting.</p>
            </div>

            <div class="p-12 space-y-10">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-4 border-b border-gray-50">
                        <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Booking
                            Reference</span>
                        <span class="font-bold">#TRX-{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-gray-50">
                        <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Transaction
                            ID</span>
                        <span class="font-bold text-sm">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-gray-50">
                        <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Amount Paid</span>
                        <span class="font-bold text-xl">₹{{ number_format($payment->amount) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-gray-50">
                        <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Date</span>
                        <span
                            class="font-bold">{{ \Carbon\Carbon::parse($payment->payment_date)->format('D, d M Y') }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <a href="{{ route('home') }}"
                        class="w-full bg-primary text-white py-5 rounded-4xl font-bold text-lg hover:bg-black transition-all shadow-xl shadow-primary/20">
                        Go to Dashboard
                    </a>
                    <button
                        class="w-full bg-gray-50 text-gray-400 py-5 rounded-4xl font-bold text-lg hover:bg-gray-100 transition-all border border-gray-100 flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        Download Receipt
                    </button>
                </div>

                <p class="text-xs text-gray-400 italic">A confirmation email has been sent to your registered address.
                </p>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>

</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:flight,hotel',
            'reference_id' => 'required|integer',
            'total_price' => 'required|numeric',
        ]);

        $booking = Booking::create([
            'user_id' => 1, // Placeholder
            'type' => $request->type,
            'reference_id' => $request->reference_id,
            'total_price' => $request->total_price,
            'booking_date' => now(),
            'status' => 'pending',
        ]);

        return redirect()->route('checkout', $booking->id);
    }

    public function checkout(Booking $booking)
    {
        return view('checkout.payment', compact('booking'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Simulation logic: 80% Success, 20% failure
        $isSuccess = rand(1, 100) <= 80;

        if ($isSuccess) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => 1,
                'amount' => $booking->total_price,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'TXN-' . Str::upper(Str::random(10)),
                'payment_status' => 'success',
                'payment_date' => now(),
            ]);

            $booking->update(['status' => 'confirmed']);

            return redirect()->route('booking.confirmation', $booking->id)->with('success', 'Payment successful! Your trip is confirmed.');
        } else {
            return back()->with('error', 'Payment failed. Please try again or use a different card.');
        }
    }

    public function confirmation(Booking $booking)
    {
        $payment = Payment::where('booking_id', $booking->id)->first();
        return view('checkout.confirmation', compact('booking', 'payment'));
    }
}

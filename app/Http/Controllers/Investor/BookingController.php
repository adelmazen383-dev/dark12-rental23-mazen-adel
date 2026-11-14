<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $investor = auth()->user()->investor;
        $apartmentIds = $investor->apartments()->pluck('id');

        $query = Booking::with('apartment')
            ->whereIn('apartment_id', $apartmentIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);

        return view('investor.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $investor = auth()->user()->investor;
        if ($booking->apartment->investor_id !== $investor->id) {
            abort(403);
        }

        $booking->load(['apartment', 'receipts']);
        return view('investor.bookings.show', compact('booking'));
    }
}

<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, InspectionRequest};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;

        if (!$investor) {
            return redirect()->route('investor.register')->with('warning', 'يرجى إكمال ملف المستثمر أولاً.');
        }

        $apartmentsCount = $investor->apartments()->count();
        $apartmentIds = $investor->apartments()->pluck('id');

        $activeBookingsCount = Booking::whereIn('apartment_id', $apartmentIds)
            ->where('status', 'confirmed')
            ->where('checkout_date', '>=', now())
            ->count();

        $recentBookings = Booking::with('apartment')
            ->whereIn('apartment_id', $apartmentIds)
            ->latest()
            ->take(5)
            ->get();

        $recentInspections = InspectionRequest::with('apartment')
            ->whereIn('apartment_id', $apartmentIds)
            ->latest()
            ->take(5)
            ->get();

        $wallet = $investor->wallet;
        $subscription = $investor->activeSubscription;

        return view('investor.dashboard', compact(
            'investor',
            'apartmentsCount',
            'activeBookingsCount',
            'recentBookings',
            'recentInspections',
            'wallet',
            'subscription'
        ));
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Setting};
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        // Enforce all business visibility rules:
        // 1. Not rented (no active booking or rental contract today)
        // 2. Marked available & active
        // 3. Investor approved & active subscription, OR admin approved override
        $query = Apartment::visibleToClients()->with(['media', 'investor']);

        // Filter by floor
        if ($request->filled('floor')) {
            $query->where('floor_number', $request->floor);
        }

        // Filter by apartment number
        if ($request->filled('apartment_number')) {
            $query->where('apartment_number', 'like', '%' . $request->apartment_number . '%');
        }

        // Filter by rooms
        if ($request->filled('rooms')) {
            $query->where('rooms_count', '>=', $request->rooms);
        }

        // Filter by view
        if ($request->filled('view')) {
            $query->where('view_type', 'like', '%' . $request->view . '%');
        }

        $apartments = $query->orderByDesc('is_featured_on_homepage')
            ->orderBy('homepage_order')
            ->orderBy('floor_number')
            ->orderBy('apartment_number')
            ->paginate(12);

        // Get available floors for filter dropdown from visible apartments only
        $floors = Apartment::visibleToClients()
            ->select('floor_number')
            ->distinct()
            ->orderBy('floor_number')
            ->pluck('floor_number');

        $siteName = Setting::getValue('site_name', 'أجنحة البرج الفندقية');

        return view('client.apartments.index', compact('apartments', 'floors', 'siteName'));
    }

    public function show(Apartment $apartment)
    {
        if (!$apartment->isAvailableForClients()) {
            $reason = 'هذه الشقة غير متاحة حالياً للحجز.';
            if ($apartment->isCurrentlyRented()) {
                $reason = 'هذه الشقة مؤجرة حالياً وغير متاحة للحجز.';
            } elseif (!$apartment->is_available || !$apartment->is_active) {
                $reason = 'هذه الشقة غير متاحة حالياً.';
            } elseif ($apartment->investor && !$apartment->investor->isAllowedToDisplayApartments()) {
                if ($apartment->investor->wallet && $apartment->investor->wallet->balance <= 0) {
                    $reason = 'هذه الشقة غير متاحة حالياً بانتظار شحن محفظة المستثمر.';
                } else {
                    $reason = 'هذه الشقة غير متاحة حالياً بانتظار اعتماد الإدارة أو تجديد الاشتراك.';
                }
            }

            return redirect()->route('client.apartments.index')
                ->with('error', $reason);
        }

        $apartment->load(['media', 'investor']);
        $images = $apartment->images;
        $videos = $apartment->videos;

        return view('client.apartments.show', compact('apartment', 'images', 'videos'));
    }
}

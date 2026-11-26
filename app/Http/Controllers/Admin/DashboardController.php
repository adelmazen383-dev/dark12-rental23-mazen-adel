<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, BookingReceipt, Investor, InspectionRequest, NotificationLog, Setting, Subscription, Wallet, WalletTransaction};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_apartments' => Apartment::count(),
            'active_apartments' => Apartment::where('is_active', true)->where('is_available', true)->count(),
            'pending_investors' => Investor::where('status', 'pending')->count(),
            'total_investors' => Investor::count(),
            'pending_receipts' => BookingReceipt::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_bookings_revenue' => Booking::where('status', 'confirmed')->sum('total_amount'),
            'total_booking_fees' => WalletTransaction::where('type', 'deduction')->sum('amount'),
            'total_subscriptions' => Subscription::where('status', 'active')->sum('amount'),
            'total_wallets_balance' => Wallet::sum('balance'),
        ];

        $pendingReceipts = BookingReceipt::with(['booking.apartment.investor'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['apartment.investor'])
            ->latest()
            ->take(8)
            ->get();

        $pendingInvestors = Investor::where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingReceipts', 'recentBookings', 'pendingInvestors'));
    }

    public function inspections(Request $request)
    {
        $query = InspectionRequest::with(['apartment.investor'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $inspections = $query->paginate(15);

        return view('admin.inspections.index', compact('inspections'));
    }

    public function scheduleInspection(Request $request, InspectionRequest $inspection)
    {
        $request->validate([
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $inspection->update([
            'status' => 'scheduled',
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
        ]);

        $inspection->loadMissing('apartment');
        \App\Services\WebPushService::sendToClientByPhone(
            $inspection->customer_phone,
            "📅 تم تحديد موعد معاينة شقة {$inspection->apartment?->apartment_number}",
            "الموعد: " . \Carbon\Carbon::parse($request->scheduled_at)->format('Y-m-d H:i') . ($request->notes ? " | {$request->notes}" : ''),
            route('client.apartments.show', $inspection->apartment_id),
            'client-inspection-' . $inspection->id
        );

        return back()->with('success', 'تم تحديد موعد المعاينة بنجاح.');
    }

    public function completeInspection(InspectionRequest $inspection)
    {
        $inspection->update(['status' => 'completed']);

        $inspection->loadMissing('apartment');
        \App\Services\WebPushService::sendToClientByPhone(
            $inspection->customer_phone,
            "✅ اكتملت معاينة شقة {$inspection->apartment?->apartment_number}",
            "يمكنك الآن إتمام حجز الشقة إلكترونياً عبر منصة دارك.",
            route('client.apartments.show', $inspection->apartment_id),
            'client-inspection-done-' . $inspection->id
        );

        return back()->with('success', 'تم تحديث حالة المعاينة إلى مكتملة.');
    }

    public function notifications()
    {
        $notifications = NotificationLog::latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Apartment, Investor, Conversation, Setting};
use App\Services\{AuditService, NotificationService, WalletService};
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    /**
     * Reception & Security Gatekeeper Dashboard
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Expected / Today's Bookings
        $todayArrivals = Booking::with(['apartment.investor', 'checkedInByUser'])
            ->whereDate('checkin_date', '<=', $today)
            ->whereDate('checkout_date', '>=', $today)
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderByRaw('reception_checked_in_at IS NULL DESC')
            ->latest()
            ->paginate(15, ['*'], 'arrivals_page');

        // 2. Currently In-House Guests (checked in, not checked out yet)
        $inHouseCount = Booking::whereNotNull('reception_checked_in_at')
            ->whereNull('reception_checked_out_at')
            ->where('status', 'confirmed')
            ->count();

        // 3. Bypass Alerts: Conversations with bypass attempts or survey discrepancies
        $bypassAlerts = Conversation::with(['apartment', 'investor'])
            ->where(function ($q) {
                $q->where('has_bypass_attempt', true)
                  ->orWhere('deal_status', 'caught_offline')
                  ->orWhere(function ($sq) {
                      $sq->where('client_survey_response', 'deal_done')
                         ->where('investor_survey_response', 'no_deal');
                  });
            })
            ->latest('last_message_at')
            ->limit(10)
            ->get();

        // 4. Quick Lookup Results if search is performed
        $searchResult = null;
        $searchChats = null;
        if ($request->filled('q')) {
            $q = trim($request->q);
            $searchResult = Booking::with(['apartment.investor', 'checkedInByUser'])
                ->where('reference_number', $q)
                ->orWhere('reception_code', $q)
                ->orWhere('customer_phone', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->latest()
                ->first();

            // Also search for recent open conversations with this phone/guest
            $searchChats = Conversation::with(['apartment.investor'])
                ->where('customer_phone', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%")
                ->latest('last_message_at')
                ->limit(5)
                ->get();
        }

        $allApartments = Apartment::with('investor')->orderBy('apartment_number')->get();
        $bookingFee = (float) Setting::getValue('booking_fee', 50);
        $penaltyFee = (float) Setting::getValue('unregistered_deal_penalty_fee', 100);

        return view('admin.reception.index', compact(
            'todayArrivals',
            'inHouseCount',
            'bypassAlerts',
            'searchResult',
            'searchChats',
            'allApartments',
            'bookingFee',
            'penaltyFee'
        ));
    }

    /**
     * Handover key and check in guest using secret reception code
     */
    public function checkInByCode(Request $request)
    {
        $request->validate([
            'reception_code' => 'required|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $code = trim($request->reception_code);
        $booking = Booking::with('apartment.investor')
            ->where('reception_code', $code)
            ->orWhere('reference_number', $code)
            ->first();

        if (!$booking) {
            return back()->with('error', 'كود الدخول أو الرقم المرجعي غير صحيح أو غير مسجل في النظام.');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'هذا الحجز ملغي في النظام، لا يمكن تسليم المفتاح.');
        }

        if ($booking->isReceptionCheckedIn()) {
            return back()->with('info', "النزيل مسجل دخوله مسبقاً بتاريخ {$booking->reception_checked_in_at->format('Y-m-d H:i')}.");
        }

        $booking->update([
            'reception_checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
            'reception_notes' => $request->notes ?? $booking->reception_notes,
            'status' => 'confirmed',
        ]);

        AuditService::log('reception_key_delivered_checkin', $booking, null, [
            'reception_code' => $booking->reception_code,
            'customer_name' => $booking->customer_name,
            'apartment_id' => $booking->apartment_id,
        ]);

        // WhatsApp notification to investor
        if ($booking->apartment->investor && $booking->apartment->investor->whatsapp) {
            NotificationService::sendWhatsApp(
                $booking->apartment->investor->whatsapp,
                "🔑 إشعار استلام مفتاح ودخول نزيل:\nوصل النزيل {$booking->customer_name} إلى استقبال البرج وتم تسليمه كارت الدخول لشقة {$booking->apartment->apartment_number}.\nنتمنى له إقامة سعيدة.",
                'investor',
                'booking',
                $booking->id
            );
        }

        return back()->with('success', "تم التحقق وتسجيل دخول النزيل {$booking->customer_name} وتسليمه مفتاح شقة {$booking->apartment->apartment_number} بنجاح.");
    }

    /**
     * Guest Checkout at Reception
     */
    public function checkOut(Request $request, Booking $booking)
    {
        $booking->update([
            'reception_checked_out_at' => now(),
            'status' => 'completed',
        ]);

        AuditService::log('reception_guest_checkout', $booking, null, [
            'customer_name' => $booking->customer_name,
            'apartment' => $booking->apartment->apartment_number,
        ]);

        return back()->with('success', "تم تسجيل خروج النزيل واستلام الكارت بنجاح. أصبحت شقة {$booking->apartment->apartment_number} جاهزة للتنظيف وإعادة الإتاحة.");
    }

    /**
     * Anti-Bypass Enforcement:
     * When reception/security detects a guest entering without an official booking (circumvention deal),
     * register it on the spot, deduct commission + penalty fee from investor's wallet.
     */
    public function recordUnregisteredDeal(Request $request, WalletService $walletService)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'nights_count' => 'required|integer|min:1',
            'daily_rate' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $apartment = Apartment::with('investor')->findOrFail($validated['apartment_id']);
        $investor = $apartment->investor;

        $totalAmount = $validated['daily_rate'] * $validated['nights_count'];
        $bookingFee = (float) Setting::getValue('booking_fee', 50);
        $penaltyFee = (float) Setting::getValue('unregistered_deal_penalty_fee', 100);
        $totalDeduction = $bookingFee + $penaltyFee;

        // Create official booking record flagged as unregistered bypass
        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'checkin_date' => now()->toDateString(),
            'checkout_date' => now()->addDays($validated['nights_count'])->toDateString(),
            'nights_count' => $validated['nights_count'],
            'daily_rate' => $validated['daily_rate'],
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
            'reception_checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
            'is_unregistered_deal' => true,
            'reception_notes' => "صفقة مباشرة مرصودة بالاستقبال: " . ($validated['notes'] ?? 'تم ضبط النزيل بالاستقبال دون حجز مسجل بالمنصة'),
        ]);

        // Link with any existing conversation between this client and investor
        $conversation = Conversation::where('apartment_id', $apartment->id)
            ->where('customer_phone', $validated['customer_phone'])
            ->first();

        if ($conversation) {
            $conversation->update([
                'booking_id' => $booking->id,
                'deal_status' => 'caught_offline',
                'has_bypass_attempt' => true,
                'bypass_attempt_reason' => 'تم ضبط النزيل بالاستقبال وإثبات الصفقة بعد إغلاق المحادثة دون حجز رسمي',
            ]);
        }

        // Deduct booking fee + penalty from investor's wallet
        if ($investor && $investor->wallet) {
            $walletService->deposit(
                $investor->wallet,
                -$totalDeduction,
                "عمولة تأجير + غرامة تأجير خارجي دون حجز رسمي (حجز #{$booking->reference_number} - شقة {$apartment->apartment_number})",
                auth()->id()
            );
        }

        AuditService::log('unregistered_deal_enforced', $booking, null, [
            'investor_id' => $investor?->id,
            'total_deduction' => $totalDeduction,
            'apartment' => $apartment->apartment_number,
        ]);

        // Notify investor via WhatsApp
        if ($investor && $investor->whatsapp) {
            NotificationService::sendWhatsApp(
                $investor->whatsapp,
                "🚨 ضبط تأجير وإثبات صفقة مباشرة:\nتم رصد النزيل ({$booking->customer_name}) في استقبال البرج متوجهاً لشقتك ({$apartment->apartment_number}) دون تسجيل حجز رسمي بالمنصة.\nتم قيد عمولة المنصة ({$bookingFee} ر.س) + غرامة الالتفاف ({$penaltyFee} ر.س) على محفظتك لضمان حقوق التشغيل والأمن الفندقي.",
                'investor',
                'booking',
                $booking->id
            );
        }

        return back()->with('success', "تم إثبات الصفقة المباشرة بنجاح، وتسجيل دخول النزيل بشقة {$apartment->apartment_number}، وخصم العمولة والغرامة ({$totalDeduction} ر.س) من محفظة المستثمر.");
    }
}

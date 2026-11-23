<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, BookingReceipt, Setting};
use App\Services\{AuditService, NotificationService, WalletService};
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['apartment.investor', 'receipts']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reference')) {
            $query->where('reference_number', 'like', '%' . $request->reference . '%');
        }
        if ($request->filled('apartment_number')) {
            $query->whereHas('apartment', function ($q) use ($request) {
                $q->where('apartment_number', 'like', '%' . $request->apartment_number . '%');
            });
        }
        if ($request->filled('customer')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->customer . '%');
            });
        }

        $bookings = $query->latest()->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['apartment.investor', 'apartment.media', 'receipts.verifiedByUser', 'confirmedByUser']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking, WalletService $walletService)
    {
        if ($booking->status === 'confirmed') {
            return back()->with('info', 'هذا الحجز مؤكد بالفعل.');
        }

        $investor = $booking->apartment->investor;
        $wallet = $investor->wallet;

        $bookingFee = (float) Setting::getValue('booking_fee', 150);

        // Deduct booking fee from investor's wallet
        if ($wallet && $bookingFee > 0) {
            try {
                $walletService->deductBookingFee($wallet, $booking, auth()->id());

                // Check low balance alert
                $threshold = (float) Setting::getValue('low_balance_threshold', 100);
                if ($wallet->fresh()->balance <= $threshold) {
                    NotificationService::sendWhatsApp(
                        $investor->whatsapp,
                        "⚠️ تنبيه انخفاض الرصيد: رصيد محفظتك الحالي هو {$wallet->balance} ريال، وهو أقل من الحد المسموح. يرجى الشحن لضمان استمرار استقبال الحجوزات.",
                        'investor',
                        'wallet',
                        $wallet->id
                    );
                }
            } catch (\Exception $e) {
                // If wallet balance insufficient, admin can still confirm but with a warning logged
            }
        }

        // Mark receipts as verified
        $booking->receipts()->where('status', 'pending')->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        AuditService::log('booking_confirmed', $booking, null, ['confirmed_by' => auth()->id()]);

        // Send WhatsApp confirmation to both customer and investor
        NotificationService::notifyBookingConfirmed($booking);

        return back()->with('success', 'تم اعتماد الحجز وخصم الرسوم وإرسال إشعارات التأكيد بنجاح.');
    }

    public function verifyReceipt(Booking $booking, BookingReceipt $receipt)
    {
        $receipt->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        AuditService::log('receipt_verified', $receipt);

        return back()->with('success', 'تم التحقق من صحة الإيصال.');
    }

    public function rejectReceipt(Request $request, Booking $booking, BookingReceipt $receipt)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $receipt->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $booking->update(['status' => 'pending_payment']);

        AuditService::log('receipt_rejected', $receipt, null, ['reason' => $request->rejection_reason]);

        NotificationService::sendWhatsApp(
            $booking->customer_phone,
            "⚠️ إشعار بخصوص حجزك رقم {$booking->reference_number}:\nتعذر التحقق من إيصال التحويل المرفق للأسباب التالية:\n{$request->rejection_reason}\nيرجى إعادة رفع إيصال صحيح عبر رابط الحجز.",
            'customer',
            'booking',
            $booking->id
        );

        return back()->with('success', 'تم رفض الإيصال وإشعار العميل.');
    }

    public function cancel(Request $request, Booking $booking, WalletService $walletService)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $wasConfirmed = ($booking->status === 'confirmed');

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // If the booking was confirmed, refund the booking fee to the investor's wallet
        if ($wasConfirmed) {
            $wallet = $booking->apartment->investor?->wallet;
            if ($wallet) {
                $walletService->refundBookingFee($wallet, $booking, auth()->id());
            }
        }

        AuditService::log('booking_cancelled', $booking, null, ['reason' => $request->cancellation_reason]);

        NotificationService::sendWhatsApp(
            $booking->customer_phone,
            "تم إلغاء حجزك رقم {$booking->reference_number}.\nسبب الإلغاء: {$request->cancellation_reason}",
            'customer',
            'booking',
            $booking->id
        );

        return back()->with('success', 'تم إلغاء الحجز وإعادة رسوم الحجز إلى محفظة المستثمر بنجاح.');
    }

    public function complete(Booking $booking)
    {
        $booking->update(['status' => 'completed']);
        AuditService::log('booking_completed', $booking);
        return back()->with('success', 'تم تحديث حالة الحجز إلى مكتمل.');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, BookingReceipt, PlatformBankAccount};
use App\Services\{AuditService, NotificationService};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'notes' => 'nullable|string',
        ], [
            'customer_name.required' => 'اسم العميل مطلوب',
            'customer_phone.required' => 'رقم الجوال مطلوب',
            'checkin_date.required' => 'تاريخ الدخول مطلوب',
            'checkin_date.after_or_equal' => 'تاريخ الدخول يجب أن يكون اليوم أو تاريخاً مستقبلياً',
            'checkout_date.required' => 'تاريخ الخروج مطلوب',
            'checkout_date.after' => 'تاريخ الخروج يجب أن يكون بعد تاريخ الدخول',
        ]);

        $checkin = Carbon::parse($validated['checkin_date']);
        $checkout = Carbon::parse($validated['checkout_date']);

        if (!$apartment->is_active || !$apartment->is_available) {
            return back()->withInput()->with('error', 'عذراً، هذه الشقة غير متاحة حالياً للحجز.');
        }

        if ($apartment->investor && !$apartment->investor->isAllowedToDisplayApartments() && !$apartment->admin_approved_override) {
            return back()->withInput()->with('error', 'عذراً، لا يمكن استقبال حجوزات جديدة لهذه الشقة في الوقت الحالي.');
        }

        // Check availability for dates (including bookings and rental contracts)
        if ($apartment->isBookedForDates($checkin, $checkout)) {
            return back()->withInput()->with('error', 'عذراً، الشقة محجوزة أو مؤجرة في هذه الفترة المحددة. يرجى اختيار تواريخ أخرى.');
        }

        $nights = $checkin->diffInDays($checkout);
        if ($nights < 1) $nights = 1;

        $dailyRate = $apartment->daily_price;
        $insurance = $apartment->insurance_amount;
        $extraFees = $apartment->extra_fees;
        $totalAmount = ($dailyRate * $nights) + $insurance + $extraFees;

        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'checkin_date' => $checkin->toDateString(),
            'checkout_date' => $checkout->toDateString(),
            'nights_count' => $nights,
            'daily_rate' => $dailyRate,
            'insurance_amount' => $insurance,
            'extra_fees' => $extraFees,
            'total_amount' => $totalAmount,
            'status' => 'pending_payment',
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditService::log('booking_created', $booking, null, [
            'reference' => $booking->reference_number,
            'customer' => $booking->customer_name,
            'total' => $totalAmount
        ]);

        return redirect()->route('client.booking.payment', $booking->reference_number)
            ->with('success', 'تم تسجيل اختيار الشقة وتفاصيل الحجز بنجاح. يرجى استكمال التحويل البنكي ورفع الإيصال.');
    }

    public function payment(Booking $booking)
    {
        $booking->load(['apartment.investor']);
        $bankAccounts = PlatformBankAccount::active()->get();

        return view('client.booking.payment', compact('booking', 'bankAccounts'));
    }

    public function uploadReceipt(Request $request, Booking $booking)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ], [
            'receipt.required' => 'يرجى إرفاق إيصال التحويل البنكي',
            'receipt.mimes' => 'يجب أن يكون الإيصال صورة (JPG, PNG) أو ملف PDF',
            'receipt.max' => 'الحد الأقصى لحجم الملف هو 10 ميجابايت',
        ]);

        $path = $request->file('receipt')->store('receipts', 'public');

        DB::transaction(function () use ($booking, $path) {
            BookingReceipt::create([
                'booking_id' => $booking->id,
                'file_path' => $path,
                'status' => 'pending',
            ]);

            $booking->update([
                'status' => 'pending_verification',
            ]);

            AuditService::log('receipt_uploaded', $booking, null, ['file' => $path]);
        });

        // Send notifications via WhatsApp
        NotificationService::notifyReceiptUploaded($booking);

        return redirect()->route('client.booking.confirmation', $booking->reference_number)
            ->with('success', 'تم رفع إيصال التحويل بنجاح! الطلب الآن بانتظار التحقق من الإدارة وسيتم إشعارك فور التأكيد.');
    }

    public function confirmation(Booking $booking)
    {
        $booking->load(['apartment', 'receipts']);
        return view('client.booking.confirmation', compact('booking'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['apartment.investor', 'receipts']);
        return view('client.booking.show', compact('booking'));
    }
}

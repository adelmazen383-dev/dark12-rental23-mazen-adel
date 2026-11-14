<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, Conversation, Message, Setting};
use App\Services\{AuditService, ChatSecurityService, NotificationService, WalletService};
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Start or resume a conversation from client side
     */
    public function start(Request $request, Apartment $apartment)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'initial_message' => 'nullable|string|max:1000',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        // Find existing open conversation for this apartment & phone, or create new
        $conversation = Conversation::where('apartment_id', $apartment->id)
            ->where('customer_phone', $validated['customer_phone'])
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'apartment_id' => $apartment->id,
                'investor_id' => $apartment->investor_id,
                'booking_id' => $validated['booking_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'subject' => "محادثة حول شقة {$apartment->apartment_number} (الدور {$apartment->floor_number})",
                'last_message_at' => now(),
                'status' => 'open',
                'deal_status' => 'negotiating',
            ]);
        }

        // Add initial message if provided
        if (!empty($validated['initial_message'])) {
            $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['initial_message'], 'customer');

            $conversation->messages()->create([
                'sender_type' => 'customer',
                'sender_name' => $conversation->customer_name,
                'message' => $guardResult['cleaned_message'],
                'is_read_by_customer' => true,
                'is_read_by_investor' => false,
                'is_read_by_admin' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // Notify Investor via WhatsApp
            if ($apartment->investor && $apartment->investor->whatsapp) {
                NotificationService::sendWhatsApp(
                    $apartment->investor->whatsapp,
                    "💬 استفسار ومحادثة جديدة من نزيل حول شقتك {$apartment->apartment_number}:\nالاسم: {$conversation->customer_name}\nالهاتف: {$conversation->customer_phone}\nالرسالة: {$guardResult['cleaned_message']}\nيمكنك الرد وتقديم عرض حجز رسمي له من لوحة المستثمر.",
                    'investor',
                    'chat',
                    $conversation->id
                );
            }
        }

        return redirect()->route('client.chat.show', $conversation->customer_token);
    }

    /**
     * Show chat room for customer
     */
    public function show($token)
    {
        $conversation = Conversation::where('customer_token', $token)
            ->with(['apartment.investor', 'messages', 'booking'])
            ->firstOrFail();

        // Mark incoming messages as read by customer
        $conversation->messages()
            ->whereIn('sender_type', ['investor', 'admin'])
            ->where('is_read_by_customer', false)
            ->update(['is_read_by_customer' => true]);

        return view('client.chat.show', compact('conversation'));
    }

    /**
     * Send message from customer
     */
    public function sendMessage(Request $request, $token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $guardResult = ChatSecurityService::inspectAndGuard($conversation, $validated['message'], 'customer');

        $msg = $conversation->messages()->create([
            'sender_type' => 'customer',
            'sender_name' => $conversation->customer_name,
            'message' => $guardResult['cleaned_message'],
            'is_read_by_customer' => true,
            'is_read_by_investor' => false,
            'is_read_by_admin' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'flagged' => $guardResult['flagged'],
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->format('H:i'),
                ]
            ]);
        }

        return back()->with('success', 'تم إرسال رسالتك');
    }

    /**
     * Polling messages endpoint for customer
     */
    public function getMessages($token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        // Mark incoming messages as read
        $conversation->messages()
            ->whereIn('sender_type', ['investor', 'admin'])
            ->where('is_read_by_customer', false)
            ->update(['is_read_by_customer' => true]);

        $messages = $conversation->messages()->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'message' => $m->message,
                'created_at' => $m->created_at->format('H:i'),
                'is_me' => $m->sender_type === 'customer',
            ];
        });

        return response()->json([
            'status' => $conversation->status,
            'deal_status' => $conversation->deal_status,
            'offer_price' => $conversation->offer_price,
            'messages' => $messages,
        ]);
    }

    /**
     * Client accepts official deal offer from investor:
     * Confirms the booking, generates Reception Key Code, and triggers wallet fee deduction.
     */
    public function acceptOffer(Request $request, $token, WalletService $walletService)
    {
        $conversation = Conversation::where('customer_token', $token)
            ->with(['apartment.investor'])
            ->firstOrFail();

        if ($conversation->deal_status !== 'offer_sent' || !$conversation->offer_price) {
            return back()->with('error', 'لا يوجد عرض رسمي متاح للقبول حالياً.');
        }

        $apartment = $conversation->apartment;
        $investor = $apartment->investor;

        $checkin = $conversation->offer_checkin_date ?? now()->toDateString();
        $checkout = $conversation->offer_checkout_date ?? now()->addDay()->toDateString();
        $nights = max(1, Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout)));
        $totalAmount = (float) $conversation->offer_price;
        $dailyRate = $totalAmount / $nights;

        // Create official confirmed booking
        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'customer_name' => $conversation->customer_name,
            'customer_phone' => $conversation->customer_phone,
            'checkin_date' => $checkin,
            'checkout_date' => $checkout,
            'nights_count' => $nights,
            'daily_rate' => $dailyRate,
            'insurance_amount' => 500, // refundable security deposit
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'notes' => 'حجز رسمي معتمد عبر التفاوض المباشر بالشات',
        ]);

        // Link to conversation
        $conversation->update([
            'booking_id' => $booking->id,
            'deal_status' => 'agreed',
            'client_survey_response' => 'deal_done',
            'investor_survey_response' => 'deal_done',
        ]);

        // Deduct platform booking fee from investor wallet
        $bookingFee = (float) Setting::getValue('booking_fee', 50);
        if ($investor && $investor->wallet && $bookingFee > 0) {
            $walletService->deposit(
                $investor->wallet,
                -$bookingFee,
                "رسوم إتمام حجز من الشات (#{$booking->reference_number}) - كود استلام المفتاح: {$booking->reception_code}",
                auth()->id()
            );
        }

        // Post confirmation in chat
        $conversation->messages()->create([
            'sender_type' => 'admin',
            'sender_name' => 'نظام الحجز الفندقي',
            'message' => "🎉 مبروك! تم إتمام الحجز وتثبيته رسمياً برقم: {$booking->reference_number}\n🔑 كود استلام المفتاح من الاستقبال: [ {$booking->reception_code} ]\nيرجى إبراز هذا الكود عند الوصول لبوابة البرج لاستلام كارت الدخول والمفتاح.",
            'is_read_by_customer' => true,
            'is_read_by_investor' => false,
            'is_read_by_admin' => true,
        ]);

        AuditService::log('deal_offer_accepted_by_client', $booking, null, [
            'conversation_id' => $conversation->id,
            'reception_code' => $booking->reception_code,
            'total_amount' => $totalAmount,
        ]);

        // WhatsApp notification to investor
        if ($investor && $investor->whatsapp) {
            NotificationService::sendWhatsApp(
                $investor->whatsapp,
                "✅ تم قبول عرضك وتأكيد الحجز بنجاح!\nالنزيل: {$booking->customer_name}\nرقم الحجز: {$booking->reference_number}\nالمبلغ الإجمالي: {$booking->total_amount} ريال\nتم إصدار كود تسليم المفتاح ({$booking->reception_code}) للاستقبال وخصم رسوم الخدمة من محفظتك.",
                'investor',
                'booking',
                $booking->id
            );
        }

        return redirect()->route('client.booking.confirmation', $booking->reference_number)
            ->with('success', 'تم تأكيد حجزك بنجاح! احتفظ بكود استلام المفتاح لإبرازه بالاستقبال.');
    }

    /**
     * Submit client deal closure survey
     */
    public function submitSurvey(Request $request, $token)
    {
        $conversation = Conversation::where('customer_token', $token)->firstOrFail();

        $validated = $request->validate([
            'response' => 'required|in:deal_done,no_deal',
            'notes' => 'nullable|string|max:500',
        ]);

        $conversation->update([
            'client_survey_response' => $validated['response'],
        ]);

        // Check if there is discrepancy with investor survey
        if ($validated['response'] === 'deal_done' && $conversation->investor_survey_response === 'no_deal') {
            $conversation->update([
                'has_bypass_attempt' => true,
                'deal_status' => 'caught_offline',
                'bypass_attempt_reason' => 'تضارب استبيان إتمام الصفقة: النزيل أكد الاتفاق بينما أنكر المالك الاتفاق للتهرب من العمولة',
            ]);

            AuditService::log('deal_discrepancy_alert', $conversation, null, [
                'client' => 'deal_done',
                'investor' => 'no_deal',
            ]);
        }

        return back()->with('success', 'شكراً لمشاركتنا تقييم حالة التفاوض.');
    }
}

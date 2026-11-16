<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{Apartment, Booking, Conversation, InspectionRequest, Message};
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

    /**
     * Polling endpoint for phone notifications and live desktop alerts
     */
    public function unreadAlerts(Request $request)
    {
        $investor = auth()->user()->investor;
        if (!$investor) {
            return response()->json(['unread_messages_count' => 0, 'unread_bookings_count' => 0]);
        }

        $apartmentIds = $investor->apartments()->pluck('id');
        $conversationIds = Conversation::whereIn('apartment_id', $apartmentIds)->pluck('id');

        // Unread customer/admin messages
        $unreadMessagesQuery = Message::with('conversation.apartment')
            ->whereIn('conversation_id', $conversationIds)
            ->whereIn('sender_type', ['customer', 'admin'])
            ->where('is_read_by_investor', false);

        $unreadMessagesCount = $unreadMessagesQuery->count();
        $latestMessageObj = (clone $unreadMessagesQuery)->latest()->first();

        $latestMessage = null;
        if ($latestMessageObj) {
            $latestMessage = [
                'id' => $latestMessageObj->id,
                'sender_name' => $latestMessageObj->sender_name ?: 'نزيل',
                'snippet' => \Illuminate\Support\Str::limit($latestMessageObj->message, 80),
                'apartment_number' => $latestMessageObj->conversation?->apartment?->apartment_number ?? '',
                'url' => route('investor.chats.show', $latestMessageObj->conversation_id),
                'created_at' => $latestMessageObj->created_at->diffForHumans(),
            ];
        }

        // Recent bookings in last 24 hours
        $recentBookingsQuery = Booking::with('apartment')
            ->whereIn('apartment_id', $apartmentIds)
            ->where('created_at', '>=', now()->subHours(24))
            ->whereIn('status', ['pending_payment', 'confirmed']);

        $recentBookingsCount = $recentBookingsQuery->count();
        $latestBookingObj = (clone $recentBookingsQuery)->latest()->first();

        $latestBooking = null;
        if ($latestBookingObj) {
            $latestBooking = [
                'id' => $latestBookingObj->id,
                'reference' => $latestBookingObj->reference_number,
                'customer_name' => $latestBookingObj->customer_name,
                'total_amount' => number_format($latestBookingObj->total_amount, 0),
                'apartment_number' => $latestBookingObj->apartment?->apartment_number ?? '',
                'status' => $latestBookingObj->status === 'confirmed' ? 'مؤكد' : 'بانتظار الدفع',
                'url' => route('investor.bookings.show', $latestBookingObj->id),
            ];
        }

        return response()->json([
            'unread_messages_count' => $unreadMessagesCount,
            'recent_bookings_count' => $recentBookingsCount,
            'total_alerts' => $unreadMessagesCount + $recentBookingsCount,
            'latest_message' => $latestMessage,
            'latest_booking' => $latestBooking,
        ]);
    }

    /**
     * Get VAPID Public Key for client push registration
     */
    public function getVapidKey()
    {
        return response()->json([
            'publicKey' => \App\Services\WebPushService::getPublicKey(),
        ]);
    }

    /**
     * Store Web Push subscription from browser
     */
    public function subscribePush(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $sub = \App\Services\WebPushService::saveSubscription(
            auth()->user(),
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth']
        );

        return response()->json(['success' => true, 'id' => $sub->id]);
    }

    /**
     * Send instant test push notification
     */
    public function testPush(Request $request)
    {
        $investor = auth()->user()->investor;
        if (!$investor) {
            return response()->json(['success' => false, 'message' => 'حساب مستثمر غير متوفر'], 400);
        }

        $sent = \App\Services\WebPushService::sendToInvestor(
            $investor,
            '🔔 تجربة إشعار دارك (الموقع مغلق)',
            'مبروك! نظام الإشعارات في الخلفية شغال بنجاح وصوت التنبيه فعال حتى والموقع مغلق.',
            route('investor.chats.index')
        );

        return response()->json([
            'success' => true,
            'sent_count' => $sent,
            'message' => $sent > 0 ? 'تم إرسال الإشعار الفوري بنجاح إلى هاتفك!' : 'لم يتم العثور على أجهزة مشتركة، يرجى تفعيل الإشعارات أولاً.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Return VAPID public key for client-side Web Push subscription.
     */
    public function vapidKey()
    {
        return response()->json([
            'publicKey' => WebPushService::getPublicKey(),
        ]);
    }

    /**
     * Subscribe a client browser/device (iPhone PWA, Android, Desktop) to instant push notifications.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:2000',
            'keys.p256dh' => 'required|string|max:500',
            'keys.auth' => 'required|string|max:500',
            'contentEncoding' => 'nullable|string|max:30',
            'conversation_token' => 'nullable|string|max:100',
            'booking_reference' => 'nullable|string|max:50',
            'device_label' => 'nullable|string|max:100',
        ]);

        $conversationToken = null;
        $bookingReference = null;
        $customerPhone = null;

        if (!empty($validated['conversation_token'])) {
            $conversation = Conversation::where('customer_token', $validated['conversation_token'])->first();
            if ($conversation) {
                $conversationToken = $conversation->customer_token;
                $customerPhone = $conversation->customer_phone;
                if ($conversation->booking) {
                    $bookingReference = $conversation->booking->reference_number;
                }
            }
        }

        if (!empty($validated['booking_reference'])) {
            $booking = Booking::where('reference_number', $validated['booking_reference'])->first();
            if ($booking) {
                $bookingReference = $booking->reference_number;
                $customerPhone = $customerPhone ?: $booking->customer_phone;
            }
        }

        $sub = WebPushService::saveClientSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? 'aes128gcm',
            $conversationToken,
            $bookingReference,
            $customerPhone,
            $validated['device_label'] ?? null
        );

        return response()->json([
            'success' => true,
            'id' => $sub->id,
        ]);
    }

    /**
     * Send an instant or delayed test notification to the client's subscribed endpoint.
     */
    public function testPush(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:2000',
            'url' => 'nullable|string|max:500',
        ]);

        $sub = PushSubscription::where('endpoint', $validated['endpoint'])->first();
        if (!$sub) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الجهاز غير مسجل بعد، يرجى الضغط على «تفعيل الإشعارات» أولاً.',
            ], 404);
        }

        $targetUrl = !empty($validated['url']) && str_starts_with($validated['url'], url('/'))
            ? $validated['url']
            : route('client.apartments.index');

        $sent = WebPushService::sendToEndpoint(
            $validated['endpoint'],
            '🔔 تم تفعيل إشعارات منصة دارك بنجاح!',
            'سيصلك إشعار فوري على هذا الجهاز بمجرد رد المالك أو تأكيد حجزك حتى لو أغلقت الموقع.',
            $targetUrl,
            'client-test-' . time()
        );

        return response()->json([
            'success' => $sent > 0,
            'sent_count' => $sent,
            'message' => $sent > 0
                ? 'تم إرسال إشعار تجريبي إلى جهازك بنجاح!'
                : 'تعذر إرسال الإشعار التجريبي، يرجى التأكد من السماح بالإشعارات وإعادة المحاولة.',
        ]);
    }

    /**
     * Rotate an endpoint from Service Worker pushsubscriptionchange without requiring auth.
     */
    public function rotate(Request $request)
    {
        $validated = $request->validate([
            'old_endpoint' => 'nullable|string|max:2000',
            'endpoint' => 'required|string|max:2000',
            'keys.p256dh' => 'required|string|max:500',
            'keys.auth' => 'required|string|max:500',
            'contentEncoding' => 'nullable|string|max:30',
        ]);

        $rotated = WebPushService::rotateSubscription(
            $validated['old_endpoint'] ?? null,
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? 'aes128gcm'
        );

        return response()->json(['success' => $rotated]);
    }

    /**
     * Lightweight polling endpoint for live booking status updates on client confirmation/details page.
     */
    public function bookingStatus(Booking $booking)
    {
        return response()->json([
            'reference_number' => $booking->reference_number,
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'reception_code' => $booking->status === 'confirmed' ? $booking->reception_code : null,
            'updated_at' => $booking->updated_at?->toIso8601String(),
        ]);
    }
}

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
            ->whereIn('status', ['pending_payment', 'pending_verification', 'confirmed']);

        $recentBookingsCount = $recentBookingsQuery->count();
        $latestBookingObj = (clone $recentBookingsQuery)->latest()->first();

        $latestBooking = null;
        if ($latestBookingObj) {
            $statusLabel = match ($latestBookingObj->status) {
                'confirmed' => 'مؤكد',
                'pending_verification' => 'بانتظار مراجعة الإيصال',
                default => 'بانتظار الدفع',
            };
            $latestBooking = [
                'id' => $latestBookingObj->id,
                'reference' => $latestBookingObj->reference_number,
                'customer_name' => $latestBookingObj->customer_name,
                'total_amount' => number_format($latestBookingObj->total_amount, 0),
                'apartment_number' => $latestBookingObj->apartment?->apartment_number ?? '',
                'status' => $statusLabel,
                'url' => route('investor.bookings.show', $latestBookingObj->id),
            ];
        }

        // Pending inspection requests in last 24 hours
        $recentInspectionsQuery = InspectionRequest::with('apartment')
            ->whereIn('apartment_id', $apartmentIds)
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'pending');

        $recentInspectionsCount = $recentInspectionsQuery->count();
        $latestInspectionObj = (clone $recentInspectionsQuery)->latest()->first();
        $latestInspection = null;
        if ($latestInspectionObj) {
            $latestInspection = [
                'id' => $latestInspectionObj->id,
                'customer_name' => $latestInspectionObj->customer_name,
                'customer_phone' => $latestInspectionObj->customer_phone,
                'apartment_number' => $latestInspectionObj->apartment?->apartment_number ?? '',
                'url' => route('investor.dashboard'),
            ];
        }

        $pushSubscriptionsCount = \App\Models\PushSubscription::where('investor_id', $investor->id)->count();

        return response()->json([
            'unread_messages_count' => $unreadMessagesCount,
            'recent_bookings_count' => $recentBookingsCount,
            'unread_bookings_count' => $recentBookingsCount,
            'recent_inspections_count' => $recentInspectionsCount,
            'total_alerts' => $unreadMessagesCount + $recentBookingsCount + $recentInspectionsCount,
            'latest_message' => $latestMessage,
            'latest_booking' => $latestBooking,
            'latest_inspection' => $latestInspection,
            'push_subscriptions_count' => $pushSubscriptionsCount,
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
            'endpoint' => 'required|string|max:2000',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'contentEncoding' => 'nullable|string|max:30',
            'device_label' => 'nullable|string|max:100',
        ]);

        $sub = \App\Services\WebPushService::saveSubscription(
            auth()->user(),
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? 'aes128gcm',
            $validated['device_label'] ?? null
        );

        $subscriptionsCount = \App\Models\PushSubscription::where('investor_id', auth()->user()->investor?->id)->count();

        return response()->json([
            'success' => true,
            'id' => $sub->id,
            'subscriptions_count' => $subscriptionsCount,
        ]);
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

        $subscriptionsCount = \App\Models\PushSubscription::where(function ($q) use ($investor) {
            $q->where('investor_id', $investor->id);
            if ($investor->user_id) {
                $q->orWhere('user_id', $investor->user_id);
            }
        })->count();

        $delay = max(0, min(60, (int) $request->input('delay', 0)));
        if ($delay > 0) {
            if (!app()->environment('testing')) {
                $artisan = base_path('artisan');
                $phpBinary = null;

                if (PHP_SAPI === 'cli' && PHP_BINARY) {
                    $phpBinary = PHP_BINARY;
                } else {
                    foreach (['/usr/bin/php8.4', '/usr/bin/php8.3', '/usr/bin/php8.2', '/usr/bin/php', '/usr/local/bin/php', PHP_BINDIR . DIRECTORY_SEPARATOR . 'php', PHP_BINDIR . DIRECTORY_SEPARATOR . 'php.exe'] as $candidate) {
                        if ($candidate && @file_exists($candidate)) {
                            $phpBinary = $candidate;
                            break;
                        }
                    }
                }

                $dispatchedBg = false;
                if ($phpBinary && function_exists('exec')) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        @pclose(@popen(sprintf(
                            'start /B "" %s %s push:test %d --delay=%d > NUL 2>&1',
                            escapeshellarg($phpBinary),
                            escapeshellarg($artisan),
                            $investor->id,
                            $delay
                        ), 'r'));
                        $dispatchedBg = true;
                    } else {
                        $cmd = sprintf(
                            'nohup %s %s push:test %d --delay=%d > /dev/null 2>&1 &',
                            escapeshellarg($phpBinary),
                            escapeshellarg($artisan),
                            $investor->id,
                            $delay
                        );
                        @exec($cmd);
                        $dispatchedBg = true;
                    }
                }

                if (!$dispatchedBg && function_exists('fastcgi_finish_request')) {
                    ignore_user_abort(true);
                    $response = response()->json([
                        'success' => true,
                        'delayed' => true,
                        'delay_seconds' => $delay,
                        'subscriptions_count' => $subscriptionsCount,
                        'message' => "تمت جدولة الإشعار! أغلق الموقع والمتصفح أو اقفل الشاشة الآن تماماً، وسيصلك التنبيه خلال {$delay} ثوانٍ 🚀",
                    ]);
                    $response->send();
                    fastcgi_finish_request();
                    sleep($delay);
                    \App\Services\WebPushService::sendToInvestor(
                        $investor,
                        '🔔 إشعار من دارك (وصلك والموقع مغلق)',
                        'مبروك! تم استلام الإشعار بنجاح في الخلفية أثناء إغلاق الموقع.',
                        route('investor.bookings.index'),
                        'test-bg-' . time()
                    );
                    exit;
                }
            }

            return response()->json([
                'success' => true,
                'delayed' => true,
                'delay_seconds' => $delay,
                'subscriptions_count' => $subscriptionsCount,
                'message' => "تمت جدولة الإشعار! أغلق الموقع والمتصفح أو اقفل الشاشة الآن تماماً، وسيصلك التنبيه خلال {$delay} ثوانٍ 🚀",
            ]);
        }

        $sent = \App\Services\WebPushService::sendToInvestor(
            $investor,
            '🔔 تجربة إشعار دارك (يعمل والموقع مغلق)',
            'مبروك! نظام الإشعارات الفورية شغال بنجاح — ستصلك طلبات الحجز ورسائل العملاء فوراً حتى لو المتصفح مغلق.',
            route('investor.bookings.index'),
            'test-push-' . time()
        );

        return response()->json([
            'success' => $sent > 0,
            'sent_count' => $sent,
            'subscriptions_count' => $subscriptionsCount,
            'message' => $sent > 0
                ? "تم إرسال الإشعار الفوري بنجاح إلى {$sent} جهاز!"
                : ($subscriptionsCount > 0
                    ? 'تم العثور على جهاز مسجل لكن تعذر الإرسال إليه (قد يكون الاشتراك منتهي، أعد تفعيل الإشعارات).'
                    : 'لم يتم تسجيل هذا الجهاز بعد، يرجى الضغط على «تفعيل الإشعارات» والسماح بها.'),
        ]);
    }
}

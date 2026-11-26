<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Investor;
use App\Models\PushSubscription;
use App\Models\Setting;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private const DEFAULT_PUBLIC_KEY = 'BI3Jn8jTTDzGD6KxXYSQto7xKD2Aw9HZW1BUTJvvUVyUvoDxmRO6U9QshSo1_OFvjJAD_EF2dcdv85K80B8bq1U';
    private const DEFAULT_PRIVATE_KEY = 'SsMiYjafV9nyjHxHn38PLrVUFgb0fN-gakoTTQ-8Otc';

    protected static bool $schemaChecked = false;

    public static function getPublicKey(): string
    {
        return config('services.webpush.public_key')
            ?: Setting::getValue('vapid_public_key', self::DEFAULT_PUBLIC_KEY);
    }

    public static function getPrivateKey(): string
    {
        return config('services.webpush.private_key')
            ?: Setting::getValue('vapid_private_key', self::DEFAULT_PRIVATE_KEY);
    }

    /**
     * VAPID subject must be a mailto: address or HTTPS origin (no path, never http:// or localhost)
     * for strict Apple APNs (iOS Safari) / FCM / Mozilla Autopush compatibility.
     */
    public static function getSubject(): string
    {
        $configured = trim((string) config('services.webpush.subject', ''));
        if ($configured !== '') {
            if (str_starts_with($configured, 'mailto:')) {
                return $configured;
            }
            if (str_starts_with($configured, 'https://')) {
                $parts = parse_url($configured);
                if (!empty($parts['host']) && !in_array($parts['host'], ['localhost', '127.0.0.1'], true)) {
                    return 'https://' . $parts['host'];
                }
            }
        }

        $parts = parse_url((string) config('app.url', 'https://thiqa.me'));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme === 'https' && $host !== '' && !in_array($host, ['localhost', '127.0.0.1'], true)) {
            return 'https://' . $host;
        }

        if ($host !== '' && !in_array($host, ['localhost', '127.0.0.1'], true)) {
            return 'mailto:notifications@' . $host;
        }

        return 'mailto:notifications@thiqa.me';
    }

    /**
     * Auto-heal missing client/device columns if code is deployed before running migrations.
     */
    public static function ensureSchemaColumns(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        try {
            if (!Schema::hasTable('push_subscriptions')) {
                return;
            }
            if (Schema::hasColumn('push_subscriptions', 'subscriber_type')) {
                return;
            }

            Schema::table('push_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('push_subscriptions', 'subscriber_type')) {
                    $table->string('subscriber_type', 20)->default('investor')->after('investor_id')->index();
                }
                if (!Schema::hasColumn('push_subscriptions', 'customer_phone')) {
                    $table->string('customer_phone', 30)->nullable()->after('subscriber_type')->index();
                }
                if (!Schema::hasColumn('push_subscriptions', 'conversation_token')) {
                    $table->string('conversation_token', 100)->nullable()->after('customer_phone')->index();
                }
                if (!Schema::hasColumn('push_subscriptions', 'booking_reference')) {
                    $table->string('booking_reference', 50)->nullable()->after('conversation_token')->index();
                }
                if (!Schema::hasColumn('push_subscriptions', 'device_label')) {
                    $table->string('device_label', 100)->nullable()->after('content_encoding');
                }
                if (!Schema::hasColumn('push_subscriptions', 'last_notified_at')) {
                    $table->timestamp('last_notified_at')->nullable()->after('device_label');
                }
            });
        } catch (\Throwable $e) {
            Log::debug('PushSubscription schema check skipped: ' . $e->getMessage());
        }
    }

    /**
     * Normalize Saudi/international phone variants for reliable matching.
     *
     * @return array<int, string>
     */
    public static function phoneVariants(?string $phone): array
    {
        $raw = trim((string) $phone);
        if ($raw === '') {
            return [];
        }

        $digits = preg_replace('/[^\d]/', '', $raw);
        if ($digits === '') {
            return [$raw];
        }

        $variants = [$raw, $digits];
        if (str_starts_with($digits, '00966')) {
            $core = substr($digits, 5);
        } elseif (str_starts_with($digits, '966')) {
            $core = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $core = substr($digits, 1);
        } else {
            $core = $digits;
        }

        if ($core !== '') {
            $variants[] = $core;
            $variants[] = '0' . $core;
            $variants[] = '966' . $core;
            $variants[] = '+966' . $core;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    /**
     * Store or update an authenticated user's (investor or admin) push subscription.
     */
    public static function saveSubscription(
        User $user,
        string $endpoint,
        ?string $publicKey,
        ?string $authToken,
        string $encoding = 'aes128gcm',
        ?string $deviceLabel = null
    ): PushSubscription {
        self::ensureSchemaColumns();

        $investorId = $user->investor?->id;
        $type = $user->isAdmin() ? 'admin' : 'investor';

        $data = [
            'user_id' => $user->id,
            'investor_id' => $investorId,
            'public_key' => $publicKey,
            'auth_token' => $authToken,
            'content_encoding' => $encoding ?: 'aes128gcm',
        ];

        if (Schema::hasColumn('push_subscriptions', 'subscriber_type')) {
            $data['subscriber_type'] = $type;
            if ($deviceLabel) {
                $data['device_label'] = mb_substr($deviceLabel, 0, 100);
            }
        }

        return PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            $data
        );
    }

    /**
     * Store or update a guest client's push subscription linked to a booking, conversation, or phone.
     */
    public static function saveClientSubscription(
        string $endpoint,
        ?string $publicKey,
        ?string $authToken,
        string $encoding = 'aes128gcm',
        ?string $conversationToken = null,
        ?string $bookingReference = null,
        ?string $customerPhone = null,
        ?string $deviceLabel = null
    ): PushSubscription {
        self::ensureSchemaColumns();

        $existing = PushSubscription::where('endpoint', $endpoint)->first();

        $data = [
            'public_key' => $publicKey,
            'auth_token' => $authToken,
            'content_encoding' => $encoding ?: 'aes128gcm',
        ];

        if (!$existing) {
            $data['subscriber_type'] = 'client';
        }

        if ($conversationToken) {
            $data['conversation_token'] = $conversationToken;
        }
        if ($bookingReference) {
            $data['booking_reference'] = $bookingReference;
        }
        if ($customerPhone) {
            $data['customer_phone'] = $customerPhone;
        }
        if ($deviceLabel) {
            $data['device_label'] = mb_substr($deviceLabel, 0, 100);
        }

        return PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            $data
        );
    }

    /**
     * Seamlessly rotate an expired/rotated endpoint from Service Worker pushsubscriptionchange.
     */
    public static function rotateSubscription(
        ?string $oldEndpoint,
        string $newEndpoint,
        ?string $publicKey,
        ?string $authToken,
        string $encoding = 'aes128gcm'
    ): bool {
        self::ensureSchemaColumns();

        if ($oldEndpoint) {
            $existing = PushSubscription::where('endpoint', $oldEndpoint)->first();
            if ($existing) {
                $existing->update([
                    'endpoint' => $newEndpoint,
                    'public_key' => $publicKey,
                    'auth_token' => $authToken,
                    'content_encoding' => $encoding ?: 'aes128gcm',
                ]);
                return true;
            }
        }

        $current = PushSubscription::where('endpoint', $newEndpoint)->first();
        if ($current) {
            $current->update([
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'content_encoding' => $encoding ?: 'aes128gcm',
            ]);
            return true;
        }

        return false;
    }

    /**
     * Send Web Push notification to all subscriptions belonging to an investor.
     */
    public static function sendToInvestor(?Investor $investor, string $title, string $body, string $url, ?string $tag = null): int
    {
        if (!$investor) {
            return 0;
        }

        $subscriptions = PushSubscription::where(function ($query) use ($investor) {
            $query->where('investor_id', $investor->id);
            if ($investor->user_id) {
                $query->orWhere('user_id', $investor->user_id);
            }
        })->get()->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send Web Push notification to all subscriptions belonging to a specific user.
     */
    public static function sendToUser(?User $user, string $title, string $body, string $url, ?string $tag = null): int
    {
        if (!$user) {
            return 0;
        }

        $subscriptions = PushSubscription::where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if ($user->investor) {
                $q->orWhere('investor_id', $user->investor->id);
            }
        })->get()->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send Web Push notification to all subscribed administrators.
     */
    public static function sendToAdmins(string $title, string $body, string $url, ?string $tag = null): int
    {
        self::ensureSchemaColumns();

        $adminIds = User::where('role', 'admin')->where('is_active', true)->pluck('id');
        if ($adminIds->isEmpty()) {
            return 0;
        }

        $subscriptions = PushSubscription::whereIn('user_id', $adminIds)
            ->get()
            ->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send instant Web Push notification to a guest client subscribed to a conversation.
     */
    public static function sendToClientByConversation(?Conversation $conversation, string $title, string $body, string $url, ?string $tag = null): int
    {
        if (!$conversation) {
            return 0;
        }

        self::ensureSchemaColumns();
        if (!Schema::hasColumn('push_subscriptions', 'conversation_token')) {
            return 0;
        }

        $phoneVariants = self::phoneVariants($conversation->customer_phone);
        $bookingRef = $conversation->booking?->reference_number;

        $subscriptions = PushSubscription::where(function ($q) use ($conversation, $phoneVariants, $bookingRef) {
            $q->where('conversation_token', $conversation->customer_token);
            if ($bookingRef) {
                $q->orWhere('booking_reference', $bookingRef);
            }
            if (!empty($phoneVariants)) {
                $q->orWhereIn('customer_phone', $phoneVariants);
            }
        })->get()->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send instant Web Push notification to a guest client subscribed to a booking.
     */
    public static function sendToClientByBooking(?Booking $booking, string $title, string $body, string $url, ?string $tag = null): int
    {
        if (!$booking) {
            return 0;
        }

        self::ensureSchemaColumns();
        if (!Schema::hasColumn('push_subscriptions', 'booking_reference')) {
            return 0;
        }

        $phoneVariants = self::phoneVariants($booking->customer_phone);

        $subscriptions = PushSubscription::where(function ($q) use ($booking, $phoneVariants) {
            $q->where('booking_reference', $booking->reference_number);
            if (!empty($phoneVariants)) {
                $q->orWhereIn('customer_phone', $phoneVariants);
            }
        })->get()->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send instant Web Push notification to a guest client by phone number.
     */
    public static function sendToClientByPhone(?string $phone, string $title, string $body, string $url, ?string $tag = null): int
    {
        $phoneVariants = self::phoneVariants($phone);
        if (empty($phoneVariants)) {
            return 0;
        }

        self::ensureSchemaColumns();
        if (!Schema::hasColumn('push_subscriptions', 'customer_phone')) {
            return 0;
        }

        $subscriptions = PushSubscription::whereIn('customer_phone', $phoneVariants)
            ->get()
            ->unique('endpoint');

        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Send a direct test push notification to a single subscription endpoint.
     */
    public static function sendToEndpoint(string $endpoint, string $title, string $body, string $url, ?string $tag = null): int
    {
        $subscriptions = PushSubscription::where('endpoint', $endpoint)->get();
        return self::dispatch($subscriptions, $title, $body, $url, $tag);
    }

    /**
     * Encrypt and push a notification payload to a collection of PushSubscription records.
     */
    protected static function dispatch(Collection $subscriptions, string $title, string $body, string $url, ?string $tag = null): int
    {
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $auth = [
            'VAPID' => [
                'subject' => self::getSubject(),
                'publicKey' => self::getPublicKey(),
                'privateKey' => self::getPrivateKey(),
            ],
        ];

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => asset('images/icon-192.png'),
            'badge' => asset('images/favicon-96.png'),
            'tag' => $tag ?: 'alert-' . time(),
            'timestamp' => time() * 1000,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $sentCount = 0;

        try {
            $httpClient = class_exists(GuzzleClient::class)
                ? new GuzzleClient(['timeout' => 8, 'connect_timeout' => 5])
                : null;

            $webPush = new WebPush($auth, [
                'TTL' => 86400, // Keep deliverable for 24h if phone is temporarily offline
                'urgency' => 'high',
                'batchSize' => 100,
            ], $httpClient);

            $webPush->setDefaultOptions([
                'urgency' => 'high',
                'TTL' => 86400,
                'batchSize' => 100,
            ]);
            $webPush->setReuseVAPIDHeaders(true);

            foreach ($subscriptions as $sub) {
                if (!$sub->public_key || !$sub->auth_token) {
                    continue;
                }

                $webPushSub = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                ]);

                $webPush->queueNotification($webPushSub, $payload);
            }

            $succeededEndpoints = [];
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                    $succeededEndpoints[] = $endpoint;
                } else {
                    $statusCode = $report->getResponse()?->getStatusCode();
                    $responseBody = '';
                    try {
                        $responseBody = (string) $report->getResponse()?->getBody();
                    } catch (\Throwable) {}

                    if (in_array($statusCode, [404, 410], true)) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    } else {
                        Log::warning('WebPush delivery failed', [
                            'status' => $statusCode,
                            'reason' => $report->getReason(),
                            'body' => mb_substr($responseBody, 0, 300),
                            'endpoint' => substr($endpoint, 0, 100),
                        ]);
                    }
                }
            }

            if (!empty($succeededEndpoints) && Schema::hasColumn('push_subscriptions', 'last_notified_at')) {
                PushSubscription::whereIn('endpoint', $succeededEndpoints)
                    ->update(['last_notified_at' => now()]);
            }
        } catch (\Throwable $e) {
            Log::error('WebPush notification error: ' . $e->getMessage());
        }

        return $sentCount;
    }
}

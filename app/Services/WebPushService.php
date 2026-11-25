<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\PushSubscription;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private const DEFAULT_PUBLIC_KEY = 'BI3Jn8jTTDzGD6KxXYSQto7xKD2Aw9HZW1BUTJvvUVyUvoDxmRO6U9QshSo1_OFvjJAD_EF2dcdv85K80B8bq1U';
    private const DEFAULT_PRIVATE_KEY = 'SsMiYjafV9nyjHxHn38PLrVUFgb0fN-gakoTTQ-8Otc';

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
     * VAPID subject must be a mailto: address or HTTPS origin (no path) for Safari/Chrome/Firefox compatibility.
     */
    public static function getSubject(): string
    {
        $configured = config('services.webpush.subject');
        if ($configured) {
            return $configured;
        }

        $parts = parse_url((string) config('app.url', 'https://thiqa.me'));
        if (!empty($parts['scheme']) && !empty($parts['host'])) {
            return $parts['scheme'] . '://' . $parts['host'];
        }

        return 'mailto:notifications@thiqa.me';
    }

    /**
     * Store or update a push subscription.
     */
    public static function saveSubscription(User $user, string $endpoint, ?string $publicKey, ?string $authToken, string $encoding = 'aes128gcm'): PushSubscription
    {
        $investorId = $user->investor?->id;

        return PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'user_id' => $user->id,
                'investor_id' => $investorId,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'content_encoding' => $encoding ?: 'aes128gcm',
            ]
        );
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

        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->get()
            ->unique('endpoint');

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
            'badge' => asset('images/icon-192.png'),
            'tag' => $tag ?: 'alert-' . time(),
            'timestamp' => time() * 1000,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $sentCount = 0;

        try {
            $webPush = new WebPush($auth, [
                'TTL' => 86400, // Keep deliverable for 24h if investor's phone is temporarily offline
                'urgency' => 'high',
            ]);

            $webPush->setDefaultOptions([
                'urgency' => 'high',
                'TTL' => 86400,
            ]);

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

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    $statusCode = $report->getResponse()?->getStatusCode();
                    if (in_array($statusCode, [404, 410], true)) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    } else {
                        Log::warning('WebPush delivery failed', [
                            'status' => $statusCode,
                            'reason' => $report->getReason(),
                            'endpoint' => substr($endpoint, 0, 100),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WebPush notification error: ' . $e->getMessage());
        }

        return $sentCount;
    }
}

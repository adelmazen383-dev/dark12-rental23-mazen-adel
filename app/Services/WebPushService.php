<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\PushSubscription;
use App\Models\Setting;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public static function getPublicKey(): string
    {
        return env('VAPID_PUBLIC_KEY') ?: Setting::getValue(
            'vapid_public_key',
            'BI3Jn8jTTDzGD6KxXYSQto7xKD2Aw9HZW1BUTJvvUVyUvoDxmRO6U9QshSo1_OFvjJAD_EF2dcdv85K80B8bq1U'
        );
    }

    public static function getPrivateKey(): string
    {
        return env('VAPID_PRIVATE_KEY') ?: Setting::getValue(
            'vapid_private_key',
            'SsMiYjafV9nyjHxHn38PLrVUFgb0fN-gakoTTQ-8Otc'
        );
    }

    public static function getSubject(): string
    {
        return config('app.url', 'https://tower-rental.vercel.app');
    }

    /**
     * Store or update a push subscription
     */
    public static function saveSubscription($user, string $endpoint, ?string $publicKey, ?string $authToken, string $encoding = 'aes128gcm'): PushSubscription
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
     * Send Web Push notification to all subscriptions for an investor
     */
    public static function sendToInvestor(Investor $investor, string $title, string $body, string $url, ?string $tag = null): int
    {
        $subscriptions = PushSubscription::where('investor_id', $investor->id)->get();
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
            'tag' => $tag ?: 'alert-' . time(),
            'timestamp' => time() * 1000,
        ], JSON_UNESCAPED_UNICODE);

        $sentCount = 0;

        try {
            $webPush = new WebPush($auth);
            $webPush->setDefaultOptions([
                'urgency' => 'high',
                'TTL' => 86400, // 24 hours
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
                    // Subscription expired, gone, or unsubscribed
                    if (in_array($report->getResponse()?->getStatusCode(), [404, 410])) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('WebPush notification error: ' . $e->getMessage());
        }

        return $sentCount;
    }
}

<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Investor;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_investor_can_subscribe_and_sync_device_push_endpoint(): void
    {
        $investorUser = User::where('email', 'investor@tower.com')->firstOrFail();
        $investor = $investorUser->investor;

        $payload = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-device-123',
            'keys' => [
                'p256dh' => 'BOrI9f7d_test_p256dh_key_value_padding_here_123456',
                'auth' => 'test_auth_token_1234',
            ],
            'contentEncoding' => 'aes128gcm',
        ];

        $res = $this->actingAs($investorUser)->postJson('/investor/api/push-subscribe', $payload);
        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'subscriptions_count' => 1,
            ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $investorUser->id,
            'investor_id' => $investor->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-device-123',
        ]);

        // Re-subscribing with the same endpoint updates rather than duplicating
        $res2 = $this->actingAs($investorUser)->postJson('/investor/api/push-subscribe', $payload);
        $res2->assertStatus(200)->assertJson(['subscriptions_count' => 1]);
        $this->assertEquals(1, PushSubscription::where('investor_id', $investor->id)->count());

        // Test delayed push endpoint
        $delayRes = $this->actingAs($investorUser)->postJson('/investor/api/test-push', ['delay' => 5]);
        $delayRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'delayed' => true,
                'delay_seconds' => 5,
            ]);
    }

    public function test_investor_dashboard_renders_webpush_subscription_wiring(): void
    {
        $investorUser = User::where('email', 'investor@tower.com')->firstOrFail();

        $res = $this->actingAs($investorUser)->get('/investor');
        $res->assertStatus(200);
        $res->assertSee('push-subscribe');
        $res->assertSee('vapid-key');
        $res->assertSee('test-push');
        $res->assertSee('sw.js');
        $res->assertSee('icon-192.png');
    }

    public function test_client_booking_and_inspection_notify_investor_and_appear_in_alerts(): void
    {
        $investorUser = User::where('email', 'investor@tower.com')->firstOrFail();
        $investor = Investor::where('user_id', $investorUser->id)->firstOrFail();
        $apartment = Apartment::where('investor_id', $investor->id)
            ->where('is_active', true)
            ->where('is_available', true)
            ->firstOrFail();

        // Ensure dates are far enough in future not to collide with seeded bookings
        $checkin = now()->addDays(40)->toDateString();
        $checkout = now()->addDays(42)->toDateString();

        $bookRes = $this->post("/apartments/{$apartment->id}/book", [
            'customer_name' => 'أحمد العميل',
            'customer_phone' => '0598765432',
            'checkin_date' => $checkin,
            'checkout_date' => $checkout,
        ]);

        $bookRes->assertRedirect();
        $this->assertDatabaseHas('notification_logs', [
            'recipient_type' => 'investor',
            'related_type' => 'booking',
        ]);

        $inspectRes = $this->post("/apartments/{$apartment->id}/inspect", [
            'customer_name' => 'خالد للمعاينة',
            'customer_phone' => '0588887777',
            'notes' => 'معاينة مسائية',
        ]);
        $inspectRes->assertRedirect();
        $this->assertDatabaseHas('notification_logs', [
            'recipient_type' => 'investor',
            'related_type' => 'inspection',
        ]);

        $alertsRes = $this->actingAs($investorUser)->getJson('/investor/api/unread-alerts');
        $alertsRes->assertStatus(200)
            ->assertJsonStructure([
                'unread_messages_count',
                'recent_bookings_count',
                'unread_bookings_count',
                'recent_inspections_count',
                'push_subscriptions_count',
                'latest_booking',
                'latest_inspection',
            ]);
        $this->assertGreaterThanOrEqual(1, $alertsRes->json('unread_bookings_count'));
        $this->assertGreaterThanOrEqual(1, $alertsRes->json('recent_inspections_count'));
    }
}

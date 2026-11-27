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

    public function test_client_can_subscribe_to_push_for_conversation_and_booking_and_rotate(): void
    {
        $apt = Apartment::firstOrFail();

        // Client starts chat
        $this->post("/apartments/{$apt->id}/chat/start", [
            'customer_name' => 'فيصل العميل',
            'customer_phone' => '0511223344',
            'initial_message' => 'هل الشقة متوفرة؟',
        ])->assertRedirect();

        $conv = \App\Models\Conversation::where('customer_phone', '0511223344')->firstOrFail();

        // Client creates booking
        $booking = \App\Models\Booking::create([
            'apartment_id' => $apt->id,
            'customer_name' => 'فيصل العميل',
            'customer_phone' => '0511223344',
            'checkin_date' => now()->addDays(50)->toDateString(),
            'checkout_date' => now()->addDays(52)->toDateString(),
            'nights_count' => 2,
            'daily_rate' => $apt->daily_price,
            'total_amount' => $apt->daily_price * 2,
            'status' => 'pending_verification',
        ]);

        $subRes = $this->postJson('/client/api/push-subscribe', [
            'endpoint' => 'https://web.push.apple.com/QTEST_IPHONE_ENDPOINT',
            'keys' => [
                'p256dh' => 'BOrI9f7d_test_p256dh_key_value_padding_here_123456',
                'auth' => 'test_auth_token_1234',
            ],
            'conversation_token' => $conv->customer_token,
            'booking_reference' => $booking->reference_number,
            'device_label' => 'iPhone/iPad PWA',
        ]);

        $subRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscriber_type' => 'client',
            'conversation_token' => $conv->customer_token,
            'booking_reference' => $booking->reference_number,
            'customer_phone' => '0511223344',
            'device_label' => 'iPhone/iPad PWA',
        ]);

        // Rotate endpoint seamlessly via SW endpoint
        $rotRes = $this->postJson('/api/push-rotate', [
            'old_endpoint' => 'https://web.push.apple.com/QTEST_IPHONE_ENDPOINT',
            'endpoint' => 'https://web.push.apple.com/QTEST_IPHONE_ENDPOINT_V2',
            'keys' => [
                'p256dh' => 'BOrI9f7d_test_p256dh_key_value_padding_here_654321',
                'auth' => 'test_auth_token_5678',
            ],
        ]);
        $rotRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://web.push.apple.com/QTEST_IPHONE_ENDPOINT_V2',
            'booking_reference' => $booking->reference_number,
        ]);

        // Live booking status hides reception code until confirmed
        $statusPending = $this->getJson("/client/api/booking-status/{$booking->reference_number}");
        $statusPending->assertStatus(200)->assertJson([
            'status' => 'pending_verification',
            'reception_code' => null,
        ]);

        $booking->update(['status' => 'confirmed']);
        $statusConfirmed = $this->getJson("/client/api/booking-status/{$booking->reference_number}");
        $statusConfirmed->assertStatus(200)->assertJson([
            'status' => 'confirmed',
            'reception_code' => $booking->reception_code,
        ]);
    }

    public function test_apple_apns_vapid_subject_and_seo_icons_and_manifests(): void
    {
        $subject = \App\Services\WebPushService::getSubject();
        $this->assertTrue(
            str_starts_with($subject, 'https://') || str_starts_with($subject, 'mailto:'),
            "APNs requires https:// or mailto: subject, got: {$subject}"
        );
        $this->assertStringNotContainsString('http://', $subject);

        $this->get('/manifest-client.json')->assertStatus(200)->assertJson([
            'short_name' => 'منصة دارك',
            'display' => 'standalone',
        ]);

        $this->get('/sitemap.xml')->assertStatus(200)->assertSee('image:image');

        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('og:site_name');
        $home->assertSee('favicon-48.png');
        $home->assertSee('apple-touch-icon.png');
        $home->assertSee('FAQPage');

        $this->assertGreaterThan(1000, filesize(public_path('favicon.ico')));
        $this->assertFileExists(public_path('images/favicon-48.png'));
        $this->assertFileExists(public_path('images/apple-touch-icon.png'));
    }

    public function test_admin_can_configure_smtp_settings_and_password_is_encrypted(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $res = $this->actingAs($admin)->post('/admin/settings', [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_username' => 'noreply@darak.sa',
            'mail_password' => 'super-secret-app-pass',
            'mail_from_address' => 'noreply@darak.sa',
        ]);
        $res->assertRedirect();

        $storedPass = \App\Models\Setting::getValue('mail_password');
        $this->assertNotEmpty($storedPass);
        $this->assertNotEquals('super-secret-app-pass', $storedPass);
        $this->assertEquals('super-secret-app-pass', \Illuminate\Support\Facades\Crypt::decryptString($storedPass));
    }

    public function test_forgot_password_accepts_case_insensitive_email_and_sends_otp(): void
    {
        $investorUser = User::where('email', 'investor@tower.com')->firstOrFail();

        $res = $this->post('/password/send-otp', [
            'email' => '  INVESTOR@TOWER.COM ',
        ]);
        $res->assertRedirect();
        $res->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'investor@tower.com',
        ]);
    }

    public function test_admin_smtp_ajax_save_normalizes_port_587_and_strips_gmail_password_spaces(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $res = $this->actingAs($admin)->postJson('/admin/settings', [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'ssl',
            'mail_username' => 'test@gmail.com',
            'mail_password' => 'abcd efgh ijkl mnop',
            'mail_from_address' => 'test@gmail.com',
        ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure(['success', 'message', 'csrf_token', 'mail_configured']);

        $this->assertEquals('tls', \App\Models\Setting::getValue('mail_encryption'));
        $storedPass = \App\Models\Setting::getValue('mail_password');
        $this->assertEquals('abcdefghijklmnop', \Illuminate\Support\Facades\Crypt::decryptString($storedPass));
    }
}

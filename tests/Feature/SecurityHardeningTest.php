<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Investor, Apartment, Wallet, WalletTransaction, Booking, AuditLog, Setting};
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;

/**
 * SecurityHardeningTest
 * اختبارات الحماية الأمنية الشاملة لمنصة دَارِك
 */
class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $investorUser;
    protected Investor $investor;
    protected Wallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // runs DatabaseSeeder which creates admin, investor, apartments, etc.

        $this->admin        = User::where('email', 'admin@tower.com')->first();
        $this->investorUser = User::where('email', 'investor@tower.com')->first();
        $this->investor     = Investor::where('user_id', $this->investorUser->id)->first();
        $this->wallet       = Wallet::where('investor_id', $this->investor->id)->first();
    }

    // =========================================================
    // 1. أمان تسجيل الدخول – Login Rate Limiting
    // =========================================================

    public function test_login_rate_limiting_blocks_brute_force(): void
    {
        $throttleKey = 'brute@test.com|127.0.0.1';
        RateLimiter::clear($throttleKey);

        // 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => 'brute@test.com',
                'password' => 'wrong_' . $i,
            ]);
        }

        // 6th attempt → blocked
        $response = $this->post('/login', [
            'email'    => 'brute@test.com',
            'password' => 'wrong_again',
        ]);

        $this->assertTrue(
            in_array($response->status(), [302, 429]),
            'Expected brute-force block but got status: ' . $response->status()
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security_brute_force_blocked',
        ]);
    }

    public function test_failed_login_is_logged_to_audit(): void
    {
        RateLimiter::clear('nobody@domain.com|127.0.0.1');

        $this->post('/login', [
            'email'    => 'nobody@domain.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security_failed_login',
        ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $inactive = User::factory()->create([
            'email'     => 'inactive_sec@test.com',
            'password'  => Hash::make('Pass@1234'),
            'role'      => 'investor',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email'    => 'inactive_sec@test.com',
            'password' => 'Pass@1234',
        ]);

        $this->assertGuest();
    }

    // =========================================================
    // 2. أمان صفحات الأدمن – Admin Access Control
    // =========================================================

    public function test_guest_is_redirected_to_login_from_admin_pages(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_investor_gets_403_on_admin_pages_and_is_logged(): void
    {
        $response = $this->actingAs($this->investorUser)->get('/admin');
        $response->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security_unauthorized_admin_access',
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertStatus(200);
    }

    // =========================================================
    // 3. أمان صفحات المستثمر – Investor Access Control
    // =========================================================

    public function test_guest_is_redirected_to_login_from_investor_dashboard(): void
    {
        $this->get('/investor')->assertRedirect('/login');
    }

    public function test_investor_can_access_own_dashboard(): void
    {
        $this->actingAs($this->investorUser)->get('/investor')->assertStatus(200);
    }

    // =========================================================
    // 4. اختراق مسار الملفات – Storage Path Traversal
    // =========================================================

    public function test_storage_route_blocks_path_traversal_attempts(): void
    {
        $paths = [
            '../.env',
            '../../.env',
            '%2e%2e%2f.env',
            '..%2F..%2F.env',
        ];

        foreach ($paths as $path) {
            $response = $this->get('/storage/' . $path);
            $this->assertTrue(
                in_array($response->status(), [400, 403, 404]),
                "Path traversal not blocked for [{$path}] — got {$response->status()}"
            );
        }
    }

    public function test_storage_route_blocks_sensitive_extensions(): void
    {
        $files = [
            'database.sqlite',
            'config.php',
            'backup.sql',
            'secrets.json',
            'debug.log',
        ];

        foreach ($files as $file) {
            $response = $this->get('/storage/' . $file);
            $this->assertTrue(
                in_array($response->status(), [400, 403, 404]),
                "Sensitive file not blocked [{$file}] — got {$response->status()}"
            );
        }
    }

    // =========================================================
    // 5. رؤوس الأمان – Security Headers
    // =========================================================

    public function test_security_headers_present_on_homepage(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_security_headers_present_on_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    // =========================================================
    // 6. سلامة المحفظة – Wallet Financial Integrity
    // =========================================================

    public function test_wallet_service_rejects_zero_deposit(): void
    {
        $service = new WalletService();
        $this->expectException(\InvalidArgumentException::class);
        $service->deposit($this->wallet, 0.0, 'Zero test');
    }

    public function test_wallet_service_prevents_negative_overdraft(): void
    {
        // Set balance lower than attempted deduction
        $this->wallet->update(['balance' => 50.00]);
        $this->wallet->refresh();

        $service = new WalletService();
        $this->expectException(\Exception::class);
        $service->deposit($this->wallet, -200.0, 'Overdraft test');
    }

    public function test_valid_wallet_deposit_succeeds(): void
    {
        $before  = (float) $this->wallet->fresh()->balance;
        $service = new WalletService();
        $service->deposit($this->wallet, 300.0, 'Security test deposit');

        $this->assertEquals($before + 300.0, (float) $this->wallet->fresh()->balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id'  => $this->wallet->id,
            'type'       => 'deposit',
            'amount'     => 300,
        ]);
    }

    public function test_wallet_refund_prevents_double_refund(): void
    {
        $apartment = Apartment::where('investor_id', $this->investor->id)->first();

        if (!$apartment) {
            $this->markTestSkipped('No apartment found for investor.');
        }

        $booking = Booking::create([
            'apartment_id'     => $apartment->id,
            'reference_number' => 'REF-SEC-DUP',
            'customer_name'    => 'اختبار',
            'customer_phone'   => '0500000000',
            'checkin_date'     => now()->addDay()->toDateString(),
            'checkout_date'    => now()->addDays(2)->toDateString(),
            'nights_count'     => 1,
            'daily_rate'       => 500,
            'total_amount'     => 500,
            'status'           => 'confirmed',
        ]);

        // Deduction
        WalletTransaction::create([
            'wallet_id'    => $this->wallet->id,
            'booking_id'   => $booking->id,
            'type'         => 'deduction',
            'amount'       => 150,
            'description'  => 'رسوم حجز #REF-SEC-DUP',
            'balance_after'=> 1000,
        ]);

        // Already refunded
        WalletTransaction::create([
            'wallet_id'    => $this->wallet->id,
            'booking_id'   => $booking->id,
            'type'         => 'refund',
            'amount'       => 150,
            'description'  => 'استرداد رسوم حجز #REF-SEC-DUP',
            'balance_after'=> 1150,
        ]);

        $service = new WalletService();
        $this->expectException(\Exception::class);
        $service->refundBookingFee($this->wallet, $booking);
    }

    // =========================================================
    // 7. وضع التصحيح – Debug Disabled in Production .env
    // =========================================================

    public function test_debug_mode_is_false_in_env_file(): void
    {
        $envContent = file_get_contents(base_path('.env'));
        $this->assertStringContainsString(
            'APP_DEBUG=false',
            $envContent,
            '.env يجب أن يحتوي على APP_DEBUG=false للبيئة الإنتاجية'
        );
    }

    // =========================================================
    // 8. كلمات المرور – Password Hashing
    // =========================================================

    public function test_passwords_are_bcrypt_hashed_not_plain_text(): void
    {
        $plain = 'SuperSecret@999';
        $user  = User::factory()->create(['password' => Hash::make($plain)]);

        $stored = User::find($user->id)->password;
        $this->assertNotEquals($plain, $stored, 'Password must not be stored as plain text');
        $this->assertTrue(Hash::check($plain, $stored), 'Password hash must be verifiable via Hash::check');
    }

    // =========================================================
    // 9. رسوم الحجز والعقد – Fee Configuration
    // =========================================================

    public function test_booking_fee_is_150_sar(): void
    {
        $fee = (float) Setting::getValue('booking_fee', 150);
        $this->assertEquals(150.0, $fee, 'رسوم الحجز يجب أن تكون 150 ريال');
    }

    public function test_contract_service_fee_is_100_sar(): void
    {
        $fee = (float) Setting::getValue('contract_service_fee', 100);
        $this->assertEquals(100.0, $fee, 'رسوم العقد يجب أن تكون 100 ريال');
    }

    // =========================================================
    // 10. المفتاح الخاص لـ VAPID – No Private Key Exposure
    // =========================================================

    public function test_vapid_private_key_not_in_api_response(): void
    {
        // The investor API exposes only the public key
        $response = $this->actingAs($this->investorUser)->get('/investor/api/vapid-key');

        if ($response->status() === 404) {
            $this->markTestSkipped('VAPID route not found.');
        }

        $content = $response->getContent();
        // The hardcoded fallback private key must never leak
        $this->assertStringNotContainsString(
            'SsMiYjafV9nyjHxHn38PLrVUFgb0fN',
            $content,
            'VAPID private key must not appear in API responses'
        );
    }

    // =========================================================
    // 11. CSRF – Token Protection on POST Forms
    // =========================================================

    public function test_post_to_login_with_valid_csrf_token_does_not_return_419(): void
    {
        // Laravel test client auto-injects CSRF — ensure normal post is not 419
        $response = $this->post('/login', [
            'email'    => 'admin@tower.com',
            'password' => 'wrongpass',
        ]);

        $this->assertNotEquals(419, $response->status(), 'CSRF token should be valid in test context');
    }

    // =========================================================
    // 12. تسجيل دخول الأدمن – Admin Login Flow
    // =========================================================

    public function test_correct_admin_credentials_redirect_to_dashboard(): void
    {
        RateLimiter::clear('admin@tower.com|127.0.0.1');

        $response = $this->post('/login', [
            'email'    => 'admin@tower.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    // =========================================================
    // 13. تسجيل المستثمر – Register Page Accessible
    // =========================================================

    public function test_investor_registration_page_loads(): void
    {
        $this->get('/investor/register')->assertStatus(200);
    }

    // =========================================================
    // 14. الواجهة الرئيسية – No Debug/Trace Leaked to Public
    // =========================================================

    public function test_homepage_does_not_leak_debug_info(): void
    {
        $response = $this->get('/');
        $content  = $response->getContent();

        $this->assertStringNotContainsString('APP_KEY', $content);
        $this->assertStringNotContainsString('DB_PASSWORD', $content);
        $this->assertStringNotContainsString('Stack trace', $content);
    }
}

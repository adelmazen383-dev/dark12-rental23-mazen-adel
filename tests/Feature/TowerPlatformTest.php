<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Investor, Apartment, RentalContract, Invoice, Conversation, Message, Setting};
use Illuminate\Foundation\Testing\RefreshDatabase;

class TowerPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_client_can_view_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('منصة دَارِك');
    }

    public function test_client_can_view_apartment_details(): void
    {
        $response = $this->get('/apartments/1');
        $response->assertStatus(200);
        $response->assertSee('تأكيد اختيار الشقة');
        $response->assertSee('محادثة صاحب الشقة');
    }

    public function test_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_can_view_investor_registration(): void
    {
        $response = $this->get('/investor/register');
        $response->assertStatus(200);
        $response->assertSee('تسجيل مستثمر وشقة جديدة');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('لوحة المتابعة المركزية');
    }

    public function test_investor_can_access_dashboard(): void
    {
        $investorUser = User::where('role', 'investor')->where('email', 'investor@tower.com')->first();
        $response = $this->actingAs($investorUser)->get('/investor');
        $response->assertStatus(200);
        $response->assertSee('لوحة تحكم المستثمر');
    }

    public function test_admin_secret_homepage_feature_toggle(): void
    {
        $admin = User::where('role', 'admin')->first();
        $apt = Apartment::first();

        $initialStatus = $apt->is_featured_on_homepage;

        $response = $this->actingAs($admin)->post("/admin/apartments/{$apt->id}/toggle-featured");
        $response->assertRedirect();

        $apt->refresh();
        $this->assertEquals(!$initialStatus, $apt->is_featured_on_homepage);

        // Verify investor portal does NOT show secret homepage controls
        $investorUser = User::where('role', 'investor')->where('email', 'investor@tower.com')->first();
        $invResp = $this->actingAs($investorUser)->get("/investor/apartments/{$apt->id}/edit");
        $invResp->assertStatus(200);
        $invResp->assertDontSee('الصفحة الأولى (سري)');
    }

    public function test_contracts_section_and_wallet_deduction(): void
    {
        $admin = User::where('role', 'admin')->first();
        $investorUser = User::where('role', 'investor')->where('email', 'investor@tower.com')->first();
        $investor = $investorUser->investor;

        // Ensure wallet has balance
        $investor->wallet->update(['balance' => 1000]);

        // Investor can view contracts index & create
        $resp = $this->actingAs($investorUser)->get('/investor/contracts');
        $resp->assertStatus(200);
        $resp->assertSee('عقود الإيجار الخاصة بي');

        // Request a contract as investor
        $storeResp = $this->actingAs($investorUser)->post('/investor/contracts', [
            'unit_number' => '501',
            'floor_number' => 5,
            'tenant_name' => 'سعد المنصور',
            'tenant_national_id' => '1099887766',
            'tenant_phone' => '0599998888',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'rent_amount' => 45000,
            'insurance_amount' => 3000,
            'payment_type' => 'monthly',
        ]);
        $storeResp->assertRedirect('/investor/contracts');

        // Check contract created and wallet deducted
        $contract = RentalContract::where('tenant_name', 'سعد المنصور')->first();
        $this->assertNotNull($contract);
        $this->assertEquals(150, $contract->service_fee); // Admin-set fee

        $investor->wallet->refresh();
        $this->assertEquals(850, $investor->wallet->balance);

        // Admin can view contract and print it
        $adminResp = $this->actingAs($admin)->get("/admin/contracts/{$contract->id}");
        $adminResp->assertStatus(200);
        $adminResp->assertSee($contract->contract_number);

        $printResp = $this->actingAs($admin)->get("/admin/contracts/{$contract->id}/print");
        $printResp->assertStatus(200);
        $printResp->assertSee('عقد إيجار');
        $this->assertEquals($investor->full_name, $contract->lessor_name);
        $this->assertEquals($investor->id_number, $contract->lessor_national_id);
    }

    public function test_invoices_section(): void
    {
        $admin = User::where('role', 'admin')->first();
        $investorUser = User::where('role', 'investor')->where('email', 'investor@tower.com')->first();
        $investor = $investorUser->investor;
        $investor->wallet->update(['balance' => 500]);

        // Investor creates invoice
        $storeResp = $this->actingAs($investorUser)->post('/investor/invoices', [
            'client_name' => 'شركة الأفق للاستشارات',
            'client_phone' => '0511223344',
            'issue_date' => now()->toDateString(),
            'items_desc' => ['إيجار قاعة اجتماعات برج'],
            'items_qty' => [1],
            'items_price' => [1200],
        ]);
        $storeResp->assertRedirect('/investor/invoices');

        $invoice = Invoice::where('client_name', 'شركة الأفق للاستشارات')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(50, $invoice->service_fee);

        // Wallet was deducted 50 SAR fee
        $investor->wallet->refresh();
        $this->assertEquals(450, $investor->wallet->balance);

        // Admin can view invoice
        $adminResp = $this->actingAs($admin)->get("/admin/invoices/{$invoice->id}");
        $adminResp->assertStatus(200);
        $adminResp->assertSee($invoice->invoice_number);

        // Print preview
        $printResp = $this->actingAs($admin)->get("/admin/invoices/{$invoice->id}/print");
        $printResp->assertStatus(200);
        $printResp->assertSee('فاتورة ضريبية');
    }

    public function test_chat_between_client_investor_and_admin_oversight(): void
    {
        $apt = Apartment::first();
        $admin = User::where('role', 'admin')->first();
        $investorUser = $apt->investor->user;

        // 1. Client starts chat
        $startResp = $this->post("/apartments/{$apt->id}/chat/start", [
            'customer_name' => 'طارق الأحمد',
            'customer_phone' => '0555123456',
            'initial_message' => 'السلام عليكم، هل الشقة مطلة على البحر؟',
        ]);

        $conversation = Conversation::where('customer_phone', '0555123456')->first();
        $this->assertNotNull($conversation);
        $startResp->assertRedirect("/chat/{$conversation->customer_token}");

        // 2. Client can view chat room and send another message
        $chatResp = $this->get("/chat/{$conversation->customer_token}");
        $chatResp->assertStatus(200);
        $chatResp->assertSee('طارق الأحمد');

        $sendResp = $this->postJson("/chat/{$conversation->customer_token}/send", [
            'message' => 'وهل السعر قابل للتفاوض؟',
        ]);
        $sendResp->assertStatus(200);

        // 3. Investor views chat and replies
        $invIndexResp = $this->actingAs($investorUser)->get('/investor/chats');
        $invIndexResp->assertStatus(200);
        $invIndexResp->assertSee('طارق الأحمد');

        $invReplyResp = $this->actingAs($investorUser)->postJson("/investor/chats/{$conversation->id}/send", [
            'message' => 'أهلاً وسهلاً أخي طارق، نعم الإطلالة بحرية كاملة، والسعر ممتاز.',
        ]);
        $invReplyResp->assertStatus(200);

        // 4. Admin oversight: Admin views all chats across the tower
        $adminIndexResp = $this->actingAs($admin)->get('/admin/chats');
        $adminIndexResp->assertStatus(200);
        $adminIndexResp->assertSee('طارق الأحمد');

        // Admin views the conversation transcript
        $adminShowResp = $this->actingAs($admin)->get("/admin/chats/{$conversation->id}");
        $adminShowResp->assertStatus(200);
        $adminShowResp->assertSee('أهلاً وسهلاً أخي طارق');

        // Admin intervenes and sends administrative notice
        $adminMsgResp = $this->actingAs($admin)->postJson("/admin/chats/{$conversation->id}/send", [
            'message' => 'تنويه إدارة البرج: يمكنكم معاينة الشقة فوراً عبر الاستقبال.',
        ]);
        $adminMsgResp->assertStatus(200);

        // 5. Verify polling endpoint returns all 4 messages
        $pollResp = $this->getJson("/chat/{$conversation->customer_token}/messages");
        $pollResp->assertStatus(200);
        $messages = $pollResp->json('messages');
        $this->assertCount(4, $messages);
    }

    public function test_rented_or_unavailable_apartment_does_not_show_to_clients(): void
    {
        $apt = Apartment::first();
        $apt->update(['is_available' => true, 'is_active' => true]);

        // Initially visible
        $resp = $this->get('/');
        $resp->assertStatus(200);
        $resp->assertSee("شقة {$apt->apartment_number}");

        // Case 1: When apartment becomes unavailable manually
        $apt->update(['is_available' => false]);
        $resp = $this->get('/');
        $resp->assertDontSee("شقة {$apt->apartment_number}");

        $showResp = $this->get("/apartments/{$apt->id}");
        $showResp->assertRedirect('/');

        // Re-enable
        $apt->update(['is_available' => true]);

        // Case 2: When apartment is rented out via confirmed booking covering today
        $booking = \App\Models\Booking::create([
            'apartment_id' => $apt->id,
            'customer_name' => 'نزيل حالي',
            'customer_phone' => '0500112233',
            'checkin_date' => now()->subDay()->toDateString(),
            'checkout_date' => now()->addDays(2)->toDateString(),
            'nights_count' => 3,
            'daily_rate' => $apt->daily_price,
            'insurance_amount' => $apt->insurance_amount,
            'extra_fees' => 0,
            'total_amount' => $apt->daily_price * 3,
            'status' => 'confirmed',
        ]);

        $this->assertTrue($apt->isCurrentlyRented());

        // Must NOT show on client homepage
        $respRented = $this->get('/');
        $respRented->assertDontSee("شقة {$apt->apartment_number}");

        // Show page must block access with rented notice
        $showRentedResp = $this->get("/apartments/{$apt->id}");
        $showRentedResp->assertRedirect('/');
        $showRentedResp->assertSessionHas('error');

        // Cancel the booking - should reappear
        $booking->update(['status' => 'cancelled']);
        $this->assertFalse($apt->isCurrentlyRented());
        $respBack = $this->get('/');
        $respBack->assertSee("شقة {$apt->apartment_number}");
    }

    public function test_suspended_or_expired_subscription_investor_apartments_hide_unless_admin_approved(): void
    {
        $admin = User::where('role', 'admin')->first();
        $apt = Apartment::first();
        $investor = $apt->investor;

        // Ensure active subscription is expired
        $investor->subscriptions()->update(['status' => 'expired', 'end_date' => now()->subDay()->toDateString()]);
        $investor->update(['admin_approved_override' => false]);
        $apt->update(['admin_approved_override' => false, 'is_available' => true, 'is_active' => true]);

        $this->assertFalse($investor->hasActiveSubscription());
        $this->assertFalse($investor->isAllowedToDisplayApartments());

        // Apartment must NOT appear to clients
        $resp = $this->get('/');
        $resp->assertDontSee("شقة {$apt->apartment_number}");

        $showResp = $this->get("/apartments/{$apt->id}");
        $showResp->assertRedirect('/');

        // ADMIN OVERRIDE 1: Admin grants approval to the investor!
        $toggleResp = $this->actingAs($admin)->post("/admin/investors/{$investor->id}/toggle-admin-approval");
        $toggleResp->assertRedirect();

        $investor->refresh();
        $this->assertTrue($investor->admin_approved_override);
        $this->assertTrue($investor->isAllowedToDisplayApartments());

        // Now apartment MUST appear because Admin approved it!
        $respApproved = $this->get('/');
        $respApproved->assertSee("شقة {$apt->apartment_number}");

        // Revoke investor approval
        $this->actingAs($admin)->post("/admin/investors/{$investor->id}/toggle-admin-approval");
        $investor->refresh();
        $this->assertFalse($investor->admin_approved_override);

        // Case: Investor is suspended
        $investor->update(['status' => 'suspended']);
        $respSuspended = $this->get('/');
        $respSuspended->assertDontSee("شقة {$apt->apartment_number}");

        // ADMIN OVERRIDE 2: Admin grants approval specifically to this apartment
        $this->actingAs($admin)->post("/admin/apartments/{$apt->id}/toggle-admin-approval");
        $apt->refresh();
        $this->assertTrue($apt->admin_approved_override);

        // Apartment MUST appear because Admin directly approved this apartment!
        $respAptApproved = $this->get('/');
        $respAptApproved->assertSee("شقة {$apt->apartment_number}");
    }

    public function test_investor_registration_clears_active_session_for_fresh_signup(): void
    {
        $investorUser = User::where('role', 'investor')->first();
        $this->actingAs($investorUser);

        $response = $this->get('/investor/register');
        $response->assertStatus(200);
        $response->assertSee('تسجيل مستثمر وشقة جديدة');
        $this->assertGuest();
    }

    public function test_forgot_password_workflow_and_verification(): void
    {
        $investorUser = User::where('role', 'investor')->first();

        // 1. View forgot password page
        $pageResp = $this->get('/password/forgot');
        $pageResp->assertStatus(200);
        $pageResp->assertSee('استعادة كلمة المرور');

        // 2. Request OTP email
        $otpResp = $this->post('/password/send-otp', [
            'email' => $investorUser->email,
        ]);
        $otpResp->assertRedirect();
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $investorUser->email,
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $investorUser->email)
            ->first();
        $this->assertNotNull($record);
        $correctOtp = $record->token;

        // 3. Failed verification with wrong OTP
        $failResp = $this->post('/password/verify-otp', [
            'email' => $investorUser->email,
            'otp' => '000000',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $failResp->assertSessionHasErrors('otp');

        // 4. Successful verification with correct OTP
        $successResp = $this->post('/password/verify-otp', [
            'email' => $investorUser->email,
            'otp' => $correctOtp,
            'password' => 'BrandNewSecret2026!',
            'password_confirmation' => 'BrandNewSecret2026!',
        ]);
        $successResp->assertRedirect('/login');
        $successResp->assertSessionHas('success');

        // 5. Verify login with new password
        $loginResp = $this->post('/login', [
            'email' => $investorUser->email,
            'password' => 'BrandNewSecret2026!',
        ]);
        $loginResp->assertRedirect('/investor');
        $this->assertAuthenticatedAs($investorUser);
    }

    public function test_admin_can_reset_investor_password(): void
    {
        $admin = User::where('role', 'admin')->first();
        $investorUser = User::where('role', 'investor')->first();
        $investor = $investorUser->investor;

        $resp = $this->actingAs($admin)->post("/admin/investors/{$investor->id}/reset-password", [
            'new_password' => 'AdminSetPassword2026!',
        ]);
        $resp->assertRedirect();
        $resp->assertSessionHas('success');

        // Logout admin and login as investor with admin-set password
        $this->post('/logout');

        $loginResp = $this->post('/login', [
            'email' => $investorUser->email,
            'password' => 'AdminSetPassword2026!',
        ]);
        $loginResp->assertRedirect('/investor');
        $this->assertAuthenticatedAs($investorUser);
    }
}

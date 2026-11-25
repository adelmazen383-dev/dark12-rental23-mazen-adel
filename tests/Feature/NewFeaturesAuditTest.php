<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test Client Chat Portal (/my-chats)
     */
    public function test_client_my_chats_portal(): void
    {
        // 1. Visit /my-chats without phone -> should show clean phone entry screen
        $res = $this->get('/my-chats');
        $res->assertStatus(200);
        $res->assertSee('محادثاتي مع المُلّاك');
        $res->assertSee('أدخل رقم جوالك');

        // 2. Lookup conversations with phone
        $res2 = $this->post('/my-chats/lookup', [
            'phone' => '0599999999',
        ]);
        $res2->assertRedirect('/my-chats?phone=0599999999');

        // 3. View with query param
        $res3 = $this->get('/my-chats?phone=0599999999');
        $res3->assertStatus(200);
    }

    /**
     * Test Storage and Fallback SVGs
     */
    public function test_storage_routes_and_fallbacks(): void
    {
        // 1. Investor ID document route returns 200 SVG
        $resId = $this->get('/storage/investors/ids/sample_id.png');
        $resId->assertStatus(200);
        $this->assertStringContainsString('image/svg+xml', $resId->headers->get('Content-Type'));

        // 2. Investor License document route returns 200 SVG
        $resLic = $this->get('/storage/investors/licenses/sample_lic.png');
        $resLic->assertStatus(200);
        $this->assertStringContainsString('image/svg+xml', $resLic->headers->get('Content-Type'));

        // 3. Apartment images route returns 200 SVG
        $resApt = $this->get('/storage/apartments/images/sample_apt.jpg');
        $resApt->assertStatus(200);
        $this->assertStringContainsString('image/svg+xml', $resApt->headers->get('Content-Type'));

        // 4. Booking receipts route returns 200 SVG
        $resRec = $this->get('/storage/receipts/non_existent.jpg');
        $resRec->assertStatus(200);
        $this->assertStringContainsString('image/svg+xml', $resRec->headers->get('Content-Type'));

        // 5. Existing physical file returns actual file
        $resReal = $this->get('/storage/receipts/demo_receipt.txt');
        $resReal->assertStatus(200);
    }

    /**
     * Test Service Worker & Manifest availability
     */
    public function test_pwa_and_push_assets(): void
    {
        $swRes = $this->get('/sw.js');
        $swRes->assertStatus(200);
        $this->assertStringContainsString('dark-sw', $swRes->getContent());

        $manifestRes = $this->get('/manifest.json');
        $manifestRes->assertStatus(200);
    }

    /**
     * Test Investor Unread Alerts API
     */
    public function test_investor_unread_alerts_api(): void
    {
        $investorUser = User::where('role', 'investor')->first();

        // Acting as investor
        $res = $this->actingAs($investorUser)->get('/investor/api/unread-alerts');
        $res->assertStatus(200);
        $res->assertJsonStructure([
            'unread_messages_count',
            'recent_bookings_count',
            'total_alerts',
        ]);

        // VAPID public key endpoint
        $vapidRes = $this->actingAs($investorUser)->get('/investor/api/vapid-key');
        $vapidRes->assertStatus(200);
        $vapidRes->assertJsonStructure(['publicKey']);
    }

    /**
     * Test Remember-Me Sets Long-Lived Cookies
     */
    public function test_remember_me_sets_cookies(): void
    {
        $investorUser = User::where('role', 'investor')->first();

        $res = $this->post('/login', [
            'email' => $investorUser->email,
            'password' => 'investor123', // seeded default
            'remember' => '1',
        ]);

        $res->assertRedirect('/investor');
        $res->assertCookie('dark_remember_email');
        $res->assertCookie('dark_remember_token');
    }

    /**
     * Test Admin Investors Pending Filter
     */
    public function test_admin_investors_view(): void
    {
        $adminUser = User::where('role', 'admin')->first();

        $res = $this->actingAs($adminUser)->get('/admin/investors');
        $res->assertStatus(200);
        $res->assertSee('المستثمرون والتراخيص');

        $pendingRes = $this->actingAs($adminUser)->get('/admin/investors?status=pending');
        $pendingRes->assertStatus(200);
    }

    /**
     * Test Admin Investor Document View Endpoint (prevents 404 on investor docs)
     */
    public function test_admin_investor_document_view_endpoint(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $investor = \App\Models\Investor::first();

        // Guest cannot access
        $guestRes = $this->get("/admin/investors/{$investor->id}/documents/id_image");
        $guestRes->assertRedirect('/login');

        // Admin can view ID document with 200 OK
        $adminIdRes = $this->actingAs($adminUser)->get("/admin/investors/{$investor->id}/documents/id_image");
        $adminIdRes->assertStatus(200);

        // Admin can view license document with 200 OK
        $adminLicRes = $this->actingAs($adminUser)->get("/admin/investors/{$investor->id}/documents/license_image");
        $adminLicRes->assertStatus(200);
    }
}

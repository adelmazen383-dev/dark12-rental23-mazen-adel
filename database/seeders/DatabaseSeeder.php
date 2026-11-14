<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Investor, Apartment, ApartmentMedia, Booking, BookingReceipt, Wallet, WalletTransaction, Subscription, Setting, PlatformBankAccount};
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'أجنحة البرج الفندقية', 'description' => 'اسم المنصة'],
            ['key' => 'monthly_subscription', 'value' => '500', 'description' => 'قيمة الاشتراك الشهري بالريال'],
            ['key' => 'booking_fee', 'value' => '50', 'description' => 'رسوم المنصة لكل حجز مؤكد بالريال'],
            ['key' => 'contract_service_fee', 'value' => '150', 'description' => 'رسوم إصدار وتوثيق عقد إيجار رسمي بالريال'],
            ['key' => 'invoice_service_fee', 'value' => '50', 'description' => 'رسوم إصدار فاتورة ضريبية رسمية بالريال'],
            ['key' => 'admin_whatsapp', 'value' => '0555555555', 'description' => 'رقم واتساب الإدارة'],
            ['key' => 'low_balance_threshold', 'value' => '100', 'description' => 'حد التنبيه لانخفاض الرصيد'],
            ['key' => 'subscription_reminder_days', 'value' => '7', 'description' => 'أيام التنبيه قبل انتهاء الاشتراك'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Approved Platform Bank Accounts
        PlatformBankAccount::updateOrCreate(
            ['iban' => 'SA0380000000608010167519'],
            [
                'bank_name' => 'مصرف الراجحي',
                'account_name' => 'مؤسسة أجنحة البرج للتأجير الفندقي',
                'is_active' => true,
            ]
        );

        PlatformBankAccount::updateOrCreate(
            ['iban' => 'SA4410000002010000987654'],
            [
                'bank_name' => 'البنك الأهلي السعودي (SNB)',
                'account_name' => 'مؤسسة أجنحة البرج للتأجير الفندقي',
                'is_active' => true,
            ]
        );

        // 3. Admin Account
        $admin = User::updateOrCreate(
            ['email' => 'admin@tower.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '0555555555',
            ]
        );

        // 4. Approved Investor Account
        $investorUser = User::updateOrCreate(
            ['email' => 'investor@tower.com'],
            [
                'name' => 'عبدالله بن محمد المنصور',
                'password' => Hash::make('investor123'),
                'role' => 'investor',
                'is_active' => true,
                'phone' => '0512345678',
            ]
        );

        $investor = Investor::updateOrCreate(
            ['user_id' => $investorUser->id],
            [
                'full_name' => 'عبدالله بن محمد المنصور',
                'id_number' => '1088765432',
                'id_image' => null,
                'phone' => '0512345678',
                'whatsapp' => '0512345678',
                'bank_name' => 'مصرف الراجحي',
                'bank_account_name' => 'عبدالله بن محمد المنصور',
                'iban' => 'SA9280000201608010555432',
                'license_number' => 'TL-2024-9988',
                'license_name' => 'مؤسسة المنصور للضيافة الفندقية',
                'license_image' => null,
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        // Investor Wallet with balance
        $wallet = Wallet::updateOrCreate(
            ['investor_id' => $investor->id],
            ['balance' => 1500.00]
        );

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 1500.00,
            'description' => 'إيداع رصيد افتتاحي عبر حوالة بنكية معتمدة',
            'balance_after' => 1500.00,
            'created_by' => $admin->id,
        ]);

        // Investor Active Subscription
        Subscription::updateOrCreate(
            ['investor_id' => $investor->id],
            [
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(28)->toDateString(),
                'amount' => 500.00,
                'status' => 'active',
                'paid_at' => now(),
                'payment_reference' => 'SUB-REF-001',
            ]
        );

        // 5. Sample Apartments in Different Floors
        $apt1 = Apartment::updateOrCreate(
            ['apartment_number' => '301', 'floor_number' => 3],
            [
                'investor_id' => $investor->id,
                'description' => 'جناح فندقي فاخر بإطلالة بانورامية هادئة على المدينة، مجهز بأثاث عصري ومطبخ متكامل ودخول ذكي بالرمز السري.',
                'rooms_count' => 2,
                'living_rooms_count' => 1,
                'bathrooms_count' => 2,
                'view_type' => 'بانورامية على المدينة',
                'features' => 'واي فاي فائق السرعة، دخول ذكي، شاشة 65 بوصة 4K، مطبخ مجهز، موقف خاص تحت الأرض، آلة قهوة إسبريسو',
                'daily_price' => 550.00,
                'insurance_amount' => 300.00,
                'extra_fees' => 50.00,
                'extra_fees_description' => 'رسوم نظافة وتعقيم',
                'checkin_time' => '14:00',
                'checkout_time' => '12:00',
                'is_available' => true,
                'is_active' => true,
            ]
        );

        $apt2 = Apartment::updateOrCreate(
            ['apartment_number' => '504', 'floor_number' => 5],
            [
                'investor_id' => $investor->id,
                'description' => 'شقة عائلية رحبة في الدور الخامس تتميز بالهدوء التام والخصوصية العالية، مناسبة للعائلات ورجال الأعمال.',
                'rooms_count' => 3,
                'living_rooms_count' => 1,
                'bathrooms_count' => 2,
                'view_type' => 'واجهة البرج الرئيسية',
                'features' => 'إنترنت ألياف بصرية، دخول ذكي، غسالة ملابس ومجفف، مواقف سيارات خاصة، قريبة من المصاعد الرئيسية',
                'daily_price' => 750.00,
                'insurance_amount' => 500.00,
                'extra_fees' => 0.00,
                'checkin_time' => '15:00',
                'checkout_time' => '12:00',
                'is_available' => true,
                'is_active' => true,
                'is_featured_on_homepage' => true,
                'homepage_order' => 1,
            ]
        );

        $apt3 = Apartment::updateOrCreate(
            ['apartment_number' => '802', 'floor_number' => 8],
            [
                'investor_id' => $investor->id,
                'description' => 'استوديو كبار الشخصيات VIP بإطلالة علوية ساحرة، تصميم فندقي راقي وديكورات مريحة وتجهيزات ترفيهية كاملة.',
                'rooms_count' => 1,
                'living_rooms_count' => 1,
                'bathrooms_count' => 1,
                'view_type' => 'إطلالة علوية على أفق المدينة',
                'features' => 'خدمة غرف متاحة، دخول برمز QR، شاشة سينمائية ذكية، جاكوزي خاص، بار قهوة متكامل',
                'daily_price' => 450.00,
                'insurance_amount' => 300.00,
                'extra_fees' => 30.00,
                'checkin_time' => '14:00',
                'checkout_time' => '12:00',
                'is_available' => true,
                'is_active' => true,
            ]
        );

        // 6. Sample Pending Booking with Uploaded Receipt for Testing
        $booking = Booking::updateOrCreate(
            ['reference_number' => 'BK-DEMO88'],
            [
                'apartment_id' => $apt2->id,
                'customer_name' => 'سعود بن فهد التميمي',
                'customer_phone' => '0567890123',
                'checkin_date' => now()->addDays(2)->toDateString(),
                'checkout_date' => now()->addDays(4)->toDateString(),
                'nights_count' => 2,
                'daily_rate' => 750.00,
                'insurance_amount' => 500.00,
                'extra_fees' => 0.00,
                'total_amount' => 2000.00,
                'status' => 'pending_verification',
                'notes' => 'يرجى تجهيز سرير إضافي للأطفال إن أمكن.',
            ]
        );

        BookingReceipt::create([
            'booking_id' => $booking->id,
            'file_path' => 'receipts/demo_receipt.txt',
            'status' => 'pending',
        ]);

        // 7. Sample Rental Contract (Independent platform revenue stream)
        $contract = \App\Models\RentalContract::updateOrCreate(
            ['contract_number' => 'CNT-2026-0001'],
            [
                'investor_id' => $investor->id,
                'apartment_id' => $apt2->id,
                'unit_number' => '504',
                'floor_number' => 5,
                'tenant_name' => 'سعود بن فهد التميمي',
                'tenant_national_id' => '1099887766',
                'tenant_phone' => '0567890123',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'rent_amount' => 18000.00,
                'insurance_amount' => 2000.00,
                'payment_type' => 'monthly',
                'service_fee' => 150.00,
                'status' => 'active',
                'special_terms' => 'يلتزم المستأجر بالمحافظة على الهدوء في البرج وعدم التدخين داخل الغرف وسداد الإيجار في اليوم الأول من كل شهر ميلادي.',
                'issued_at' => now(),
                'issued_by' => $admin->id,
            ]
        );

        // 8. Sample Official Tax Invoice
        \App\Models\Invoice::updateOrCreate(
            ['invoice_number' => 'INV-2026-0001'],
            [
                'investor_id' => $investor->id,
                'booking_id' => $booking->id,
                'rental_contract_id' => $contract->id,
                'client_name' => 'سعود بن فهد التميمي',
                'client_phone' => '0567890123',
                'client_tax_number' => '300000000000003',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => 2000.00,
                'tax_amount' => 0.00,
                'total_amount' => 2000.00,
                'service_fee' => 50.00,
                'status' => 'issued',
                'items' => [
                    ['description' => 'إيجار جناح فندقي فاخر رقم 504 - الدور 5 (ليلتين)', 'quantity' => 2, 'unit_price' => 750.00, 'total' => 1500.00],
                    ['description' => 'مبلغ تأمين مسترد للوحدة', 'quantity' => 1, 'unit_price' => 500.00, 'total' => 500.00],
                ],
                'notes' => 'شكراً لاختياركم أجنحة البرج. تأمين الوحدة مسترد عند فحص الخروج.',
            ]
        );

        // 9. Sample Conversation & Messages between Tenant and Landlord
        $conversation = \App\Models\Conversation::updateOrCreate(
            ['customer_phone' => '0567890123', 'apartment_id' => $apt2->id],
            [
                'booking_id' => $booking->id,
                'investor_id' => $investor->id,
                'customer_name' => 'سعود بن فهد التميمي',
                'customer_token' => 'demo-chat-token-123456789',
                'subject' => 'استفسار بخصوص تسجيل الدخول المبكر وسرير إضافي',
                'last_message_at' => now(),
                'status' => 'open',
            ]
        );

        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'sender_name' => 'سعود التميمي',
            'message' => 'السلام عليكم، هل يمكن تسجيل الدخول مبكراً الساعة 1 ظهراً وتجهيز سرير أطفال إضافي؟',
            'is_read_by_customer' => true,
            'is_read_by_investor' => true,
            'is_read_by_admin' => true,
        ]);

        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'investor',
            'sender_id' => $investorUser->id,
            'sender_name' => 'عبدالله المنصور (صاحب الشقة)',
            'message' => 'وعليكم السلام ورحمة الله، أهلاً بك أخي سعود! بالتأكيد الشقة جاهزة وسنقوم بتجهيز سرير الأطفال لك بكل سرور.',
            'is_read_by_customer' => true,
            'is_read_by_investor' => true,
            'is_read_by_admin' => true,
        ]);

        // 10. A Pending Investor for Testing Admin Approval Workflow
        $pendingUser = User::updateOrCreate(
            ['email' => 'pending@tower.com'],
            [
                'name' => 'سلطان القحطاني',
                'password' => Hash::make('investor123'),
                'role' => 'investor',
                'is_active' => true,
                'phone' => '0599887766',
            ]
        );

        Investor::updateOrCreate(
            ['user_id' => $pendingUser->id],
            [
                'full_name' => 'سلطان بن خالد القحطاني',
                'id_number' => '1022334455',
                'phone' => '0599887766',
                'whatsapp' => '0599887766',
                'bank_name' => 'البنك الأهلي السعودي',
                'bank_account_name' => 'سلطان القحطاني',
                'iban' => 'SA4410000002010000112233',
                'license_number' => 'TL-2024-5544',
                'license_name' => 'أجنحة القحطاني السياحية',
                'status' => 'pending',
            ]
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\{Setting, PlatformBankAccount};
use App\Services\{AuditService, MailConfigService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $bankAccounts = PlatformBankAccount::all();

        $mailConfigured = !empty($settings['mail_host']) && ($settings['mail_mailer'] ?? '') === 'smtp'
            || (config('mail.default') === 'smtp' && config('mail.mailers.smtp.host') !== '127.0.0.1');

        return view('admin.settings.index', compact('settings', 'bankAccounts', 'mailConfigured'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:100',
            'monthly_subscription' => 'nullable|numeric|min:0|max:1000000',
            'booking_fee' => 'nullable|numeric|min:0|max:100000',
            'contract_service_fee' => 'nullable|numeric|min:0|max:100000',
            'invoice_service_fee' => 'nullable|numeric|min:0|max:100000',
            'admin_whatsapp' => 'nullable|string|max:20',
            'low_balance_threshold' => 'nullable|numeric|min:0|max:100000',
            'subscription_reminder_days' => 'nullable|integer|min:1|max:365',
            // Mail / SMTP settings
            'mail_mailer' => 'nullable|in:smtp,log',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $keys = [
            'site_name',
            'monthly_subscription',
            'booking_fee',
            'contract_service_fee',
            'invoice_service_fee',
            'admin_whatsapp',
            'low_balance_threshold',
            'subscription_reminder_days',
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_from_address',
            'mail_from_name',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key));
            }
        }

        // Encrypt SMTP password only if explicitly provided (keep existing if blank)
        if ($request->filled('mail_password')) {
            Setting::setValue('mail_password', Crypt::encryptString($request->input('mail_password')), 'كلمة مرور خادم البريد SMTP');
        }

        AuditService::log('settings_updated', null, null, $request->only($keys));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الإعدادات بنجاح.',
                'csrf_token' => csrf_token(),
            ]);
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }

    /**
     * Send a test email using current SMTP settings so admin can verify OTP delivery immediately.
     */
    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:255',
        ], [
            'test_email.required' => 'يرجى إدخال البريد الإلكتروني المستهدف للاختبار.',
            'test_email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
        ]);

        MailConfigService::apply();

        if (!MailConfigService::isRealDeliveryConfigured()) {
            $msg = 'خدمة البريد (SMTP) غير مفعلة بعد! يرجى إدخال بيانات خادم SMTP وحفظ التعديلات أولاً ثم الضغط على اختبار الإرسال.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        try {
            $sampleOtp = (string) random_int(100000, 999999);
            Mail::to($request->input('test_email'))->send(new PasswordResetOtpMail($sampleOtp, auth()->user()->name ?? 'المدير'));

            $msg = "تم إرسال رسالة اختبار بنجاح إلى ({$request->input('test_email')})! تحقق من صندوق الوارد أو مجلد Spam.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg, 'csrf_token' => csrf_token()]);
            }
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $msg = 'فشل الاتصال بخادم البريد (SMTP): ' . $e->getMessage();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return back()->with('error', $msg);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
        ]);

        $account = PlatformBankAccount::create($validated);
        AuditService::log('bank_account_created', $account);

        return back()->with('success', 'تمت إضافة الحساب البنكي بنجاح.');
    }

    public function destroy(PlatformBankAccount $bankAccount)
    {
        $bankAccount->delete();
        AuditService::log('bank_account_deleted', null, $bankAccount->toArray(), null);

        return back()->with('success', 'تم حذف الحساب البنكي.');
    }
}

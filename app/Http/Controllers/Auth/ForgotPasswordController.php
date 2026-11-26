<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Services\AuditService;
use App\Services\MailConfigService;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show initial form to request OTP email
     */
    public function showResetForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP code to user's registered email (with strict delivery verification + push fallback)
     */
    public function sendOtp(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.exists' => 'البريد الإلكتروني المدخل غير مسجل لدينا في النظام.',
        ]);

        // Security: Rate limiting (max 3 OTP requests per 10 minutes)
        $throttleKey = 'send-otp:' . Str::transliterate(Str::lower($validated['email']) . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AuditService::log('security_otp_rate_limited', null, null, [
                'email' => $validated['email'],
                'ip' => $request->ip(),
                'lockout_seconds' => $seconds,
            ]);

            return back()->withInput()->withErrors([
                'email' => "تم تجاوز الحد الأقصى لمحاولات إرسال رمز التحقق. يرجى الانتظار {$seconds} ثانية قبل المحاولة مرة أخرى.",
            ]);
        }
        RateLimiter::hit($throttleKey, 600);

        $user = User::whereRaw('LOWER(email) = ?', [Str::lower($validated['email'])])->firstOrFail();

        // Generate cryptographically secure 6-digit numeric OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        // Apply Admin-configured SMTP settings if present
        MailConfigService::apply();

        $mailSent = false;
        $mailError = null;

        if (app()->environment('testing') || config('mail.default') === 'array') {
            try {
                Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
                $mailSent = true;
            } catch (\Throwable $e) {
                $mailError = $e->getMessage();
            }
        } elseif (MailConfigService::isRealDeliveryConfigured()) {
            try {
                Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
                $mailSent = true;
            } catch (\Throwable $e) {
                $mailError = $e->getMessage();
                Log::error("Password reset OTP mail failed for {$user->email}: " . $mailError);
                AuditService::log('password_reset_mail_failed', $user, null, [
                    'email' => $user->email,
                    'error' => mb_substr($mailError, 0, 300),
                ]);
            }
        } else {
            $mailError = 'SMTP_NOT_CONFIGURED';
            Log::warning("Password reset OTP requested for {$user->email} while MAIL_MAILER=" . config('mail.default') . " (no real SMTP configured).");
        }

        // If email could NOT be sent, try delivering OTP securely to the user's own registered Web Push device(s)
        $pushDelivered = 0;
        if (!$mailSent && !app()->environment('testing')) {
            $pushDelivered = WebPushService::sendToUser(
                $user,
                "🔐 رمز التحقق لإعادة تعيين كلمة المرور: {$otp}",
                "الرمز صالح لمدة 15 دقيقة فقط. لا تشاركه مع أي شخص.",
                route('password.verify', ['email' => $user->email]),
                'password-otp-' . $user->id
            );
        }

        if (!$mailSent && $pushDelivered === 0 && !(config('app.debug') && config('mail.default') === 'log')) {
            // Do not keep an undeliverable token or burn user rate-limit attempts on server mail error
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            RateLimiter::clear($throttleKey);

            $errorText = $mailError === 'SMTP_NOT_CONFIGURED'
                ? 'تعذر إرسال رمز التحقق لأن خدمة البريد الإلكتروني (SMTP) غير مفعلة على السيرفر بعد. يرجى ضبط إعدادات البريد الإلكتروني من لوحة تحكم الإدارة (إعدادات النظام ← إعدادات البريد SMTP)، أو التواصل مع إدارة المنصة لإعادة تعيين كلمة المرور.'
                : 'تعذر إرسال البريد الإلكتروني في الوقت الحالي بسبب خطأ في خادم البريد (SMTP). يرجى التحقق من إعدادات البريد في لوحة الإدارة أو التواصل مع الدعم.';

            return back()->withInput()->withErrors(['email' => $errorText]);
        }

        session(['reset_email' => $user->email]);

        if ($mailSent) {
            $msg = "تم إرسال رمز التحقق (OTP) المكون من 6 أرقام إلى بريدك الإلكتروني ({$user->email}). يرجى مراجعة صندوق الوارد أو مجلد الرسائل غير المرغوب فيها (Spam).";
        } elseif ($pushDelivered > 0) {
            $msg = "خدمة البريد غير مفعلة حالياً، ولكن تم إرسال رمز التحقق (OTP) مباشرة عبر إشعارات التطبيق إلى جهازك المسجل!";
        } else {
            $msg = "تم إنشاء رمز التحقق (OTP) لحسابك ({$user->email}).";
            if (config('app.debug') && config('mail.default') === 'log') {
                session()->flash('demo_otp', $otp);
            }
        }

        return redirect()->route('password.verify', ['email' => $user->email])
            ->with('success', $msg);
    }

    /**
     * Show form to verify OTP and set new password
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email') ?? session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->with('warning', 'يرجى إدخال بريدك الإلكتروني أولاً لاستلام رمز التحقق.');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify OTP and update password
     */
    public function verifyOtpAndReset(Request $request)
    {
        if ($request->has('email')) {
            $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.exists' => 'البريد الإلكتروني غير مسجل في النظام.',
            'otp.required' => 'يرجى إدخال رمز التحقق OTP المكون من 6 أرقام.',
            'otp.size' => 'رمز التحقق يجب أن يتكون من 6 أرقام بالضبط.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        // Security: Rate limiting OTP verification attempts (max 5 attempts per 10 minutes)
        $throttleKey = 'verify-otp:' . Str::transliterate(Str::lower($validated['email']) . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AuditService::log('security_brute_force_blocked', null, null, [
                'type' => 'otp_verify_lockout',
                'email' => $validated['email'],
                'ip' => $request->ip(),
                'lockout_seconds' => $seconds,
            ]);

            return back()->withInput()->withErrors([
                'otp' => "تم حظر إدخال رمز التحقق مؤقتاً لتكرار المحاولات الخاطئة. يرجى الانتظار {$seconds} ثانية والمحاولة مجدداً.",
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(email) = ?', [Str::lower($validated['email'])])
            ->first();

        if (!$record) {
            RateLimiter::hit($throttleKey, 600);
            return back()->withInput()->withErrors([
                'otp' => 'لم يتم العثور على رمز تحقق نشط لهذا البريد. يرجى طلب رمز جديد.',
            ]);
        }

        // Check if OTP expired (15 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withInput()->withErrors([
                'otp' => 'انتهت صلاحية رمز التحقق (صالح لمدة 15 دقيقة فقط). يرجى طلب رمز جديد.',
            ]);
        }

        // Timing-attack safe comparison
        if (!hash_equals(trim((string) $record->token), trim((string) $validated['otp']))) {
            RateLimiter::hit($throttleKey, 600);
            AuditService::log('security_invalid_otp_attempt', null, null, [
                'email' => $validated['email'],
                'ip' => $request->ip(),
                'attempts_left' => RateLimiter::retriesLeft($throttleKey, 5),
            ]);

            return back()->withInput()->withErrors([
                'otp' => 'رمز التحقق (OTP) غير صحيح. يرجى مراجعة بريدك الإلكتروني وإدخال الرمز الصحيح.',
            ]);
        }

        // Clear throttle on success
        RateLimiter::clear($throttleKey);

        // Update User Password securely
        $user = User::whereRaw('LOWER(email) = ?', [Str::lower($validated['email'])])->firstOrFail();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Delete used token immediately
        DB::table('password_reset_tokens')->whereRaw('LOWER(email) = ?', [Str::lower($validated['email'])])->delete();
        session()->forget('reset_email');

        AuditService::log('password_reset_via_email_otp', $user, null, [
            'email' => $user->email,
            'name' => $user->name,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('login')
            ->with('success', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول باستخدام كلمة المرور الجديدة.');
    }
}

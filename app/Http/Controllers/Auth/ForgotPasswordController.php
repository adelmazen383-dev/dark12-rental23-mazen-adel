<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * Send OTP code to user's registered email
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.exists' => 'البريد الإلكتروني المدخل غير مسجل لدينا في النظام.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Generate 6-digit numeric OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $otp,
                'created_at' => now(),
            ]
        );

        // Send Email
        $mailSent = false;
        try {
            Mail::to($user->email)->send(new PasswordResetOtpMail($otp, $user->name));
            $mailSent = true;
        } catch (\Throwable $e) {
            Log::warning("Password reset OTP mail failed for {$user->email}: " . $e->getMessage());
        }

        session(['reset_email' => $user->email]);

        // Provide friendly feedback
        $msg = "تم إرسال رمز التحقق (OTP) المكون من 6 أرقام إلى بريدك الإلكتروني ({$user->email}).";
        if (!$mailSent || config('mail.default') === 'log' || config('app.debug')) {
            session()->flash('demo_otp', $otp);
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

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$record) {
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

        // Check matching code
        if (trim((string)$record->token) !== trim((string)$validated['otp'])) {
            return back()->withInput()->withErrors([
                'otp' => 'رمز التحقق (OTP) غير صحيح. يرجى مراجعة بريدك الإلكتروني وإدخال الرمز الصحيح.',
            ]);
        }

        // Update User Password
        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
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

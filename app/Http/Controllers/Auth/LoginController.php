<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Auto-login if remember cookie exists
        if (!Auth::check() && $rememberedEmail = Cookie::get('dark_remember_email')) {
            $rememberedToken = Cookie::get('dark_remember_token');
            if ($rememberedEmail && $rememberedToken) {
                $user = \App\Models\User::where('email', $rememberedEmail)->first();
                if ($user && hash_equals((string)$user->remember_token, (string)$rememberedToken) && $user->is_active) {
                    Auth::login($user, true);
                }
            }
        }

        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('investor.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        // Security: Rate limiting key (per email + IP)
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        // Max 5 attempts per 60 seconds
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            AuditService::log('security_brute_force_blocked', null, null, [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'lockout_seconds' => $seconds,
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors([
                'email' => "لقد تجاوزت عدد محاولات الدخول المسموح بها لأسباب أمنية. يرجى الانتظار {$seconds} ثانية والمحاولة مجدداً.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'تم تعطيل هذا الحساب. يرجى التواصل مع الإدارة.']);
            }

            // Log successful login
            AuditService::log('user_login_success', $user, null, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // If "remember me" ticked, store long-lived cookies (1 year)
            // Reuse the remember_token already created by Auth::attempt($credentials, true)
            // so Laravel's native remember_web_* recaller cookie is NOT invalidated!
            if ($remember) {
                $token = $user->getRememberToken();
                if (empty($token)) {
                    $token = Str::random(60);
                    $user->forceFill(['remember_token' => $token])->save();
                }

                $minutesInYear = 525600;
                $isSecure = $request->secure() || config('app.env') === 'production';
                Cookie::queue('dark_remember_email', $user->email, $minutesInYear, '/', null, $isSecure, true, false, 'Lax');
                Cookie::queue('dark_remember_token', $token, $minutesInYear, '/', null, $isSecure, true, false, 'Lax');
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->isInvestor()) {
                $investor = $user->investor;
                if ($investor && $investor->status === 'suspended') {
                    return redirect()->route('investor.dashboard')->with('warning', 'حسابك موقوف مؤقتاً: ' . ($investor->suspension_reason ?? ''));
                }
                return redirect()->intended(route('investor.dashboard'));
            }

            return redirect('/');
        }

        // Record failed attempt
        RateLimiter::hit($throttleKey, 60);

        AuditService::log('security_failed_login', null, null, [
            'attempted_email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'attempts_left' => RateLimiter::retriesLeft($throttleKey, 5),
        ]);

        return back()->withErrors([
            'email' => 'بيانات الاعتماد المدخلة غير متطابقة مع سجلاتنا.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear remember-me cookies
        Cookie::queue(Cookie::forget('dark_remember_email'));
        Cookie::queue(Cookie::forget('dark_remember_token'));

        return redirect()->route('login');
    }
}

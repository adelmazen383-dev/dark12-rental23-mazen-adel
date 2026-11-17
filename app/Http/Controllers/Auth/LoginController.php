<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

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
            'password' => 'required',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'تم تعطيل هذا الحساب. يرجى التواصل مع الإدارة.']);
            }

            // If "remember me" ticked, store long-lived cookies (1 year) for Vercel serverless compat
            if ($remember) {
                // Generate & store a remember token in DB
                $token = \Illuminate\Support\Str::random(60);
                $user->forceFill(['remember_token' => $token])->save();

                $minutesInYear = 525600;
                Cookie::queue('dark_remember_email', $user->email, $minutesInYear, '/', null, true, true, false, 'None');
                Cookie::queue('dark_remember_token', $token, $minutesInYear, '/', null, true, true, false, 'None');
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

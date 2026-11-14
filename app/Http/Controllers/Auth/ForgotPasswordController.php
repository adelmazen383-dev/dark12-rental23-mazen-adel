<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Investor};
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showResetForm()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_data' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.exists' => 'البريد الإلكتروني المدخل غير مسجل لدينا في النظام.',
            'verification_data.required' => 'رقم الهوية الوطنية أو رقم الجوال مطلوب للتحقق من هويتك.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Verify ownership via Investor data (ID number or phone) or admin check
        $verified = false;
        if ($user->isInvestor() && $user->investor) {
            $investor = $user->investor;
            $input = trim($validated['verification_data']);
            if ($input === $investor->id_number || $input === $investor->phone || $input === $user->phone) {
                $verified = true;
            }
        } elseif ($user->isAdmin()) {
            // Admin can verify with phone
            if (trim($validated['verification_data']) === $user->phone) {
                $verified = true;
            }
        }

        if (!$verified) {
            return back()->withInput()->withErrors([
                'verification_data' => 'بيانات التحقق (رقم الهوية الوطنية أو رقم الجوال المسجل) غير متطابقة مع هذا الحساب لضمان الأمان. يرجى التأكد أو التواصل مع الإدارة.',
            ]);
        }

        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();

        AuditService::log('password_reset_self_service', $user, null, ['email' => $user->email, 'name' => $user->name]);

        return redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول باستخدام كلمة المرور الجديدة.');
    }
}

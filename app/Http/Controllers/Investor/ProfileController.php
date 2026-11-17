<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $investor = auth()->user()->investor;
        return view('investor.profile.edit', compact('investor'));
    }

    public function update(Request $request)
    {
        $investor = auth()->user()->investor;

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'bank_name' => 'required|string|max:100',
            'bank_account_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
        ]);

        $old = $investor->toArray();
        $investor->update($validated);

        // Also update user's phone
        auth()->user()->update(['phone' => $validated['phone']]);

        AuditService::log('investor_profile_updated', $investor, $old, $investor->toArray());

        return back()->with('success', 'تم تحديث بيانات الملف الشخصي والحساب البنكي بنجاح.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        AuditService::log('investor_password_changed_from_profile', $user, null, ['user_id' => $user->id]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}

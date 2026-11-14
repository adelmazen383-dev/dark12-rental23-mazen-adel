<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;

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
}

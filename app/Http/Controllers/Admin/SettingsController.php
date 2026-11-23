<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Setting, PlatformBankAccount};
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $bankAccounts = PlatformBankAccount::all();

        return view('admin.settings.index', compact('settings', 'bankAccounts'));
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
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->input($key));
            }
        }

        AuditService::log('settings_updated', null, null, $request->only($keys));

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
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

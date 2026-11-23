<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{RentalContract, Apartment, Setting};
use App\Services\{AuditService, NotificationService, WalletService};
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $contracts = $investor->rentalContracts()->with(['apartment', 'issuer'])->latest()->paginate(15);
        $serviceFee = (float) Setting::getValue('contract_service_fee', 100);

        return view('investor.contracts.index', compact('investor', 'contracts', 'serviceFee'));
    }

    public function create()
    {
        $investor = auth()->user()->investor;
        $apartments = $investor->apartments;
        $serviceFee = (float) Setting::getValue('contract_service_fee', 100);
        $wallet = $investor->wallet;

        return view('investor.contracts.create', compact('investor', 'apartments', 'serviceFee', 'wallet'));
    }

    public function store(Request $request, WalletService $walletService)
    {
        $investor = auth()->user()->investor;
        $serviceFee = (float) Setting::getValue('contract_service_fee', 100);

        $validated = $request->validate([
            'apartment_id' => 'nullable|exists:apartments,id',
            'unit_number' => 'required|string|max:20',
            'floor_number' => 'required|integer',
            'tenant_name' => 'required|string|max:255',
            'tenant_national_id' => 'required|string|max:20',
            'tenant_phone' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'insurance_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:daily,monthly,quarterly,semi_annual,annual',
            'special_terms' => 'nullable|string',
        ]);

        // Security: Prevent IDOR - ensure apartment belongs to current investor
        if (!empty($validated['apartment_id'])) {
            $ownsApartment = Apartment::where('id', $validated['apartment_id'])
                ->where('investor_id', $investor->id)
                ->exists();
            if (!$ownsApartment) {
                abort(403, 'الشقة المحددة غير صحيحة أو لا تتبع لحسابك.');
            }
        }

        // Check wallet balance
        $wallet = $investor->wallet;
        if ($serviceFee > 0 && (!$wallet || $wallet->balance < $serviceFee)) {
            return back()->withInput()->with('error', "عذراً، رصيد محفظتك الحالي (" . number_format($wallet?->balance ?? 0, 2) . " ر.س) لا يكفي لسداد رسوم إصدار العقد ({$serviceFee} ر.س). يرجى شحن المحفظة أولاً.");
        }

        $validated['investor_id'] = $investor->id;
        $validated['lessor_name'] = $investor->full_name;
        $validated['lessor_national_id'] = $investor->id_number;
        $validated['service_fee'] = $serviceFee;
        $validated['status'] = 'pending'; // Requires admin review or direct activation

        $contract = RentalContract::create($validated);

        // Deduct service fee from wallet
        if ($serviceFee > 0 && $wallet) {
            $walletService->deposit(
                $wallet,
                -$serviceFee,
                "رسوم طلب إصدار عقد إيجار رسمي #{$contract->contract_number}",
                auth()->id()
            );
        }

        AuditService::log('investor_requested_contract', $contract);

        // Notify Admin via WhatsApp
        $adminPhone = Setting::getValue('admin_whatsapp', '0500000000');
        NotificationService::sendWhatsApp(
            $adminPhone,
            "📑 طلب إصدار عقد إيجار جديد من المستثمر: {$investor->full_name}\nرقم العقد: {$contract->contract_number}\nالمستأجر: {$contract->tenant_name}\nالوحدة: {$contract->unit_number}\nتم خصم رسوم الخدمة ({$serviceFee} ريال) من محفظة المستثمر.",
            'admin',
            'contract',
            $contract->id
        );

        return redirect()->route('investor.contracts.index')
            ->with('success', "تم إرسال طلب إصدار وتوثيق العقد رقم {$contract->contract_number} بنجاح، وخُصمت رسوم الخدمة من محفظتك.");
    }

    public function show(RentalContract $contract)
    {
        $investor = auth()->user()->investor;
        if ($contract->investor_id !== $investor->id) {
            abort(403);
        }

        $contract->load(['apartment', 'invoices']);
        return view('investor.contracts.show', compact('contract'));
    }

    public function print(RentalContract $contract)
    {
        $investor = auth()->user()->investor;
        if ($contract->investor_id !== $investor->id) {
            abort(403);
        }

        $contract->load(['investor', 'apartment', 'issuer']);
        $siteName = Setting::getValue('site_name', 'أجنحة البرج الفندقية');
        return view('contracts.print', compact('contract', 'siteName'));
    }
}

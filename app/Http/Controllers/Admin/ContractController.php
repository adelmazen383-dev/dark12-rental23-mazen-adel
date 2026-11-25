<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{RentalContract, Apartment, Investor, Setting};
use App\Services\{AuditService, NotificationService, WalletService};
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = RentalContract::with(['investor', 'apartment', 'issuer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('contract_number', 'like', "%{$s}%")
                  ->orWhere('tenant_name', 'like', "%{$s}%")
                  ->orWhere('tenant_phone', 'like', "%{$s}%")
                  ->orWhere('unit_number', 'like', "%{$s}%");
            });
        }

        $contracts = $query->paginate(15);
        $totalFees = RentalContract::where('status', 'active')->sum('service_fee');

        return view('admin.contracts.index', compact('contracts', 'totalFees'));
    }

    public function create()
    {
        $investors = Investor::where('status', 'approved')->with('apartments')->get();
        $serviceFee = (float) Setting::getValue('contract_service_fee', 100);

        return view('admin.contracts.create', compact('investors', 'serviceFee'));
    }

    public function store(Request $request, WalletService $walletService)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
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
            'service_fee' => 'required|numeric|min:0',
            'special_terms' => 'nullable|string',
            'deduct_from_wallet' => 'nullable|boolean',
        ]);

        $validated['status'] = 'active';
        $validated['issued_at'] = now();
        $validated['issued_by'] = auth()->id();

        $contract = RentalContract::create($validated);

        // Deduct service fee from investor wallet if requested
        if ($request->boolean('deduct_from_wallet') && $contract->service_fee > 0) {
            $investor = Investor::find($validated['investor_id']);
            if ($investor && $investor->wallet && $investor->wallet->balance >= $contract->service_fee) {
                $walletService->deposit(
                    $investor->wallet,
                    -$contract->service_fee,
                    "رسوم إصدار وتوثيق عقد إيجار رسمي #{$contract->contract_number}",
                    auth()->id()
                );
            }
        }

        AuditService::log('rental_contract_created', $contract, null, $contract->toArray());

        $contractInvestor = Investor::find($validated['investor_id']);
        if ($contractInvestor) {
            \App\Services\WebPushService::sendToInvestor(
                $contractInvestor,
                "📝 تم إصدار عقد إيجار رسمي لشقة {$contract->unit_number}",
                "العقد #{$contract->contract_number} | المستأجر: {$contract->tenant_name} | قيمة الإيجار: " . number_format((float) $contract->rent_amount, 0) . " ريال",
                route('investor.contracts.show', $contract->id),
                'contract-' . $contract->id
            );
        }

        return redirect()->route('admin.contracts.show', $contract->id)
            ->with('success', "تم إصدار وتوثيق عقد الإيجار رقم {$contract->contract_number} بنجاح.");
    }

    public function show(RentalContract $contract)
    {
        $contract->load(['investor.user', 'apartment', 'issuer', 'invoices']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function print(RentalContract $contract)
    {
        $contract->load(['investor', 'apartment', 'issuer']);
        $siteName = Setting::getValue('site_name', 'أجنحة البرج الفندقية');
        return view('contracts.print', compact('contract', 'siteName'));
    }

    public function updateStatus(Request $request, RentalContract $contract)
    {
        $request->validate(['status' => 'required|in:pending,active,completed,cancelled']);

        $contract->update(['status' => $request->status]);
        AuditService::log('rental_contract_status_updated', $contract, null, ['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة العقد بنجاح.');
    }
}

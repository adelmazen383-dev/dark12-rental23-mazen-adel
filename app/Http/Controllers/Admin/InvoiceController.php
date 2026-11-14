<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Invoice, Investor, Booking, RentalContract, Setting};
use App\Services\{AuditService, WalletService};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['investor', 'contract', 'booking'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhere('client_name', 'like', "%{$s}%")
                  ->orWhere('client_phone', 'like', "%{$s}%");
            });
        }

        $invoices = $query->paginate(15);
        $totalFees = Invoice::sum('service_fee');

        return view('admin.invoices.index', compact('invoices', 'totalFees'));
    }

    public function create()
    {
        $investors = Investor::where('status', 'approved')->get();
        $contracts = RentalContract::where('status', 'active')->get();
        $serviceFee = (float) Setting::getValue('invoice_service_fee', 50);
        $targetInvestor = request('investor_id') ? Investor::find(request('investor_id')) : null;

        return view('admin.invoices.create', compact('investors', 'contracts', 'serviceFee', 'targetInvestor'));
    }

    public function store(Request $request, WalletService $walletService)
    {
        $validated = $request->validate([
            'investor_id' => 'nullable|exists:investors,id',
            'rental_contract_id' => 'nullable|exists:rental_contracts,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_tax_number' => 'nullable|string|max:50',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'service_fee' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items_desc' => 'required|array|min:1',
            'items_desc.*' => 'required|string',
            'items_qty' => 'required|array',
            'items_qty.*' => 'required|numeric|min:1',
            'items_price' => 'required|array',
            'items_price.*' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'deduct_from_wallet' => 'nullable|boolean',
        ]);

        $items = [];
        $subtotal = 0;
        foreach ($validated['items_desc'] as $i => $desc) {
            $qty = (float) ($validated['items_qty'][$i] ?? 1);
            $price = (float) ($validated['items_price'][$i] ?? 0);
            $total = $qty * $price;
            $subtotal += $total;

            $items[] = [
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => $total,
            ];
        }

        $taxRate = (float) ($request->input('tax_rate', 0));
        $taxAmount = ($subtotal * $taxRate) / 100;
        $totalAmount = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'investor_id' => $validated['investor_id'] ?? null,
            'rental_contract_id' => $validated['rental_contract_id'] ?? null,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'client_tax_number' => $validated['client_tax_number'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'service_fee' => $validated['service_fee'],
            'status' => 'issued',
            'items' => $items,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Deduct service fee from investor wallet if requested
        if ($request->boolean('deduct_from_wallet') && $invoice->service_fee > 0 && $invoice->investor_id) {
            $investor = Investor::find($invoice->investor_id);
            if ($investor && $investor->wallet && $investor->wallet->balance >= $invoice->service_fee) {
                $walletService->deposit(
                    $investor->wallet,
                    -$invoice->service_fee,
                    "رسوم إصدار فاتورة رسمية #{$invoice->invoice_number}",
                    auth()->id()
                );
            }
        }

        AuditService::log('invoice_created', $invoice, null, $invoice->toArray());

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', "تم إنشاء الفاتورة رقم {$invoice->invoice_number} بنجاح.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['investor', 'contract', 'booking']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['investor', 'contract', 'booking']);
        $siteName = Setting::getValue('site_name', 'أجنحة البرج الفندقية');
        return view('invoices.print', compact('invoice', 'siteName'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,issued,paid,cancelled']);
        $invoice->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الفاتورة بنجاح.');
    }
}

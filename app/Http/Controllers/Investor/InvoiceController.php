<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\{Invoice, RentalContract, Setting};
use App\Services\{AuditService, NotificationService, WalletService};
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $investor = auth()->user()->investor;
        $invoices = $investor->invoices()->with(['contract', 'booking'])->latest()->paginate(15);
        $serviceFee = (float) Setting::getValue('invoice_service_fee', 50);

        return view('investor.invoices.index', compact('investor', 'invoices', 'serviceFee'));
    }

    public function create()
    {
        $investor = auth()->user()->investor;
        $contracts = $investor->rentalContracts()->where('status', 'active')->get();
        $serviceFee = (float) Setting::getValue('invoice_service_fee', 50);
        $wallet = $investor->wallet;

        return view('investor.invoices.create', compact('investor', 'contracts', 'serviceFee', 'wallet'));
    }

    public function store(Request $request, WalletService $walletService)
    {
        $investor = auth()->user()->investor;
        $serviceFee = (float) Setting::getValue('invoice_service_fee', 50);

        $validated = $request->validate([
            'rental_contract_id' => 'nullable|exists:rental_contracts,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_tax_number' => 'nullable|string|max:50',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items_desc' => 'required|array|min:1',
            'items_desc.*' => 'required|string',
            'items_qty' => 'required|array',
            'items_qty.*' => 'required|numeric|min:1',
            'items_price' => 'required|array',
            'items_price.*' => 'required|numeric|min:0',
        ]);

        $wallet = $investor->wallet;
        if ($serviceFee > 0 && (!$wallet || $wallet->balance < $serviceFee)) {
            return back()->withInput()->with('error', "عذراً، رصيد محفظتك الحالي (" . number_format($wallet?->balance ?? 0, 2) . " ر.س) لا يكفي لسداد رسوم إصدار الفاتورة ({$serviceFee} ر.س). يرجى شحن المحفظة أولاً.");
        }

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

        $invoice = Invoice::create([
            'investor_id' => $investor->id,
            'rental_contract_id' => $validated['rental_contract_id'] ?? null,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'client_tax_number' => $validated['client_tax_number'] ?? null,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'] ?? null,
            'subtotal' => $subtotal,
            'tax_amount' => 0.00,
            'total_amount' => $subtotal,
            'service_fee' => $serviceFee,
            'status' => 'issued',
            'items' => $items,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($serviceFee > 0 && $wallet) {
            $walletService->deposit(
                $wallet,
                -$serviceFee,
                "رسوم إصدار فاتورة رسمية معتمدة #{$invoice->invoice_number}",
                auth()->id()
            );
        }

        AuditService::log('investor_issued_invoice', $invoice);

        return redirect()->route('investor.invoices.index')
            ->with('success', "تم إصدار الفاتورة رقم {$invoice->invoice_number} بنجاح، وخُصمت رسوم الخدمة من محفظتك.");
    }

    public function show(Invoice $invoice)
    {
        $investor = auth()->user()->investor;
        if ($invoice->investor_id !== $investor->id) {
            abort(403);
        }

        $invoice->load(['contract', 'booking']);
        return view('investor.invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $investor = auth()->user()->investor;
        if ($invoice->investor_id !== $investor->id) {
            abort(403);
        }

        $invoice->load(['investor', 'contract', 'booking']);
        $siteName = Setting::getValue('site_name', 'أجنحة البرج الفندقية');
        return view('invoices.print', compact('invoice', 'siteName'));
    }
}

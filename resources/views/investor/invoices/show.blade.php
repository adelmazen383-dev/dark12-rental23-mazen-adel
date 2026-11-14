@extends('layouts.investor')

@section('title', 'تفاصيل الفاتورة ' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 font-mono">{{ $invoice->invoice_number }}</h1>
                @if($invoice->status === 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مسددة</span>
                @elseif($invoice->status === 'issued')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">صادرة</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">ملغاة</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">تاريخ الإصدار: {{ $invoice->issue_date->format('Y-m-d') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('investor.invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>طباعة الفاتورة</span>
            </a>
            <a href="{{ route('investor.invoices.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                العودة
            </a>
        </div>
    </div>

    <!-- Client info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
        <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
            <i class="fa-solid fa-user-tag text-emerald-500"></i>
            <span>بيانات العميل الصادرة له الفاتورة</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-500 block mb-0.5">اسم العميل:</span>
                <span class="font-bold text-slate-900">{{ $invoice->client_name }}</span>
            </div>
            <div>
                <span class="text-slate-500 block mb-0.5">رقم الجوال:</span>
                <span class="font-mono text-slate-900">{{ $invoice->client_phone ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block mb-0.5">الرقم الضريبي:</span>
                <span class="font-mono text-slate-900">{{ $invoice->client_tax_number ?? 'غير مسجل' }}</span>
            </div>
        </div>
    </div>

    <!-- Items table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">بنود وتفاصيل الفاتورة</h2>
        </div>

        <table class="w-full text-right text-xs">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">الوصف</th>
                    <th class="py-3 px-4 text-center">الكمية</th>
                    <th class="py-3 px-4">سعر الوحدة</th>
                    <th class="py-3 px-4 text-left">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items ?? [] as $i => $item)
                    <tr>
                        <td class="py-3 px-4 text-slate-400 font-mono">{{ $i + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-slate-800">{{ $item['description'] ?? '-' }}</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-700">{{ $item['quantity'] ?? 1 }}</td>
                        <td class="py-3 px-4 text-slate-700">{{ number_format($item['unit_price'] ?? 0, 2) }} ر.س</td>
                        <td class="py-3 px-4 text-left font-black text-slate-900">{{ number_format($item['total'] ?? 0, 2) }} ر.س</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 text-xs">
                <tr>
                    <td colspan="4" class="py-3 px-4 font-black text-slate-900 text-base text-left">المبلغ الإجمالي للفاتورة:</td>
                    <td class="py-3 px-4 font-black text-emerald-600 text-base text-left">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($invoice->notes)
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-xs text-slate-600">
            <h4 class="font-bold text-slate-800 mb-1">ملاحظات:</h4>
            <p class="whitespace-pre-line">{{ $invoice->notes }}</p>
        </div>
    @endif

</div>
@endsection

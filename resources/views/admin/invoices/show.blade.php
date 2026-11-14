@extends('layouts.admin')

@section('title', 'تفاصيل الفاتورة ' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 font-mono">{{ $invoice->invoice_number }}</h1>
                @if($invoice->status === 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">فاتورة مسددة</span>
                @elseif($invoice->status === 'issued')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">فاتورة صادرة</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">فاتورة ملغاة</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">تاريخ الإصدار: {{ $invoice->issue_date->format('Y-m-d') }} @if($invoice->due_date) | تاريخ الاستحقاق: {{ $invoice->due_date->format('Y-m-d') }} @endif</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>طباعة الفاتورة الرسمية</span>
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                العودة
            </a>
        </div>
    </div>

    <!-- Details Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Party 1: Issuer -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-building text-amber-500"></i>
                <span>المصدر (المؤجر / المستثمر)</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">الاسم:</span>
                    <span class="font-bold text-slate-800">{{ $invoice->investor?->full_name ?? 'إدارة أجنحة البرج' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">الهاتف:</span>
                    <span class="font-mono text-slate-800">{{ $invoice->investor?->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">الرقم الضريبي:</span>
                    <span class="font-mono text-slate-800">{{ $invoice->investor?->tax_number ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Party 2: Client -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-user-tag text-amber-500"></i>
                <span>العميل / المستأجر</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">اسم العميل:</span>
                    <span class="font-bold text-slate-800">{{ $invoice->client_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">الهاتف:</span>
                    <span class="font-mono text-slate-800">{{ $invoice->client_phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">الرقم الضريبي:</span>
                    <span class="font-mono text-slate-800">{{ $invoice->client_tax_number ?? 'غير مسجل' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">بنود الفاتورة</h2>
            <span class="text-xs text-slate-500">رسوم المنصة: <strong class="text-emerald-600 font-bold">{{ number_format($invoice->service_fee, 2) }} ر.س</strong></span>
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
                    <td colspan="4" class="py-2.5 px-4 font-bold text-slate-600 text-left">المجموع قبل الضريبة:</td>
                    <td class="py-2.5 px-4 font-bold text-slate-800 text-left">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
                </tr>
                @if($invoice->tax_amount > 0)
                    <tr>
                        <td colspan="4" class="py-2.5 px-4 font-bold text-slate-600 text-left">ضريبة القيمة المضافة:</td>
                        <td class="py-2.5 px-4 font-bold text-slate-800 text-left">{{ number_format($invoice->tax_amount, 2) }} ر.س</td>
                    </tr>
                @endif
                <tr class="bg-slate-100/70 border-t border-slate-200">
                    <td colspan="4" class="py-3 px-4 font-black text-slate-900 text-base text-left">المبلغ الإجمالي المستحق:</td>
                    <td class="py-3 px-4 font-black text-emerald-600 text-base text-left">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($invoice->notes)
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-xs text-slate-600">
            <h4 class="font-bold text-slate-800 mb-1">ملاحظات الفاتورة:</h4>
            <p class="whitespace-pre-line">{{ $invoice->notes }}</p>
        </div>
    @endif

    <!-- Status Modification -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-bold text-slate-800">تحديث حالة سداد الفاتورة</h3>
            <p class="text-[11px] text-slate-500">تعديل حالة الفاتورة بين صادرة أو مسددة أو ملغاة</p>
        </div>

        <form action="{{ route('admin.invoices.update-status', $invoice->id) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold">
                <option value="issued" {{ $invoice->status === 'issued' ? 'selected' : '' }}>صادرة (بانتظار السداد)</option>
                <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>مسددة بالكامل</option>
                <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>إلغاء الفاتورة</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                تحديث
            </button>
        </form>
    </div>

</div>
@endsection

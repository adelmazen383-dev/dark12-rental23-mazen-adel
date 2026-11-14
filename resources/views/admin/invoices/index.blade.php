@extends('layouts.admin')

@section('title', 'إدارة الفواتير الإلكترونية المعتمدة')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">الفواتير الإلكترونية المعتمدة</h1>
            <p class="text-xs text-slate-500 mt-1">إصدار ومتابعة الفواتير الضريبية وتوثيقها وتحصيل رسوم الخدمة المستقلة</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-file-invoice"></i>
                <span>إصدار فاتورة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Revenue & Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إجمالي الفواتير الصادرة</span>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $invoices->total() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إيرادات رسوم إصدار الفواتير للمنصة</span>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalFees, 2) }} <span class="text-xs font-bold text-slate-500">ر.س</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إجمالي مبالغ الفواتير الصادرة</span>
                <p class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format(\App\Models\Invoice::sum('total_amount'), 2) }} <span class="text-xs font-bold text-slate-500">ر.س</span></p>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.invoices.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">بحث برقم الفاتورة، اسم العميل أو الهاتف</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفاتورة، اسم العميل، الهاتف..." class="w-full pl-3 pr-9 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">حالة الفاتورة</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">جميع الحالات</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>صادرة</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مسددة</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition text-center">
                    تصفية
                </button>
                <a href="{{ route('admin.invoices.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">رقم الفاتورة</th>
                        <th class="py-3.5 px-4">المستثمر (المصدر)</th>
                        <th class="py-3.5 px-4">العميل / المستأجر</th>
                        <th class="py-3.5 px-4">تاريخ الإصدار</th>
                        <th class="py-3.5 px-4">المبلغ الإجمالي</th>
                        <th class="py-3.5 px-4">رسوم المنصة</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.invoices.show', $inv->id) }}" class="text-amber-600 hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $inv->investor?->full_name ?? 'إدارة البرج' }}</p>
                                <span class="text-[10px] text-slate-400">{{ $inv->contract ? 'عقد ' . $inv->contract->contract_number : '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $inv->client_name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $inv->client_phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <div>{{ $inv->issue_date->format('Y-m-d') }}</div>
                                @if($inv->due_date)
                                    <div class="text-[10px] text-slate-400">استحقاق: {{ $inv->due_date->format('Y-m-d') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900">
                                {{ number_format($inv->total_amount, 2) }} ر.س
                                @if($inv->tax_amount > 0)
                                    <div class="text-[10px] text-slate-400 font-normal">شامل ضريبة {{ number_format($inv->tax_amount, 2) }} ر.س</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                    +{{ number_format($inv->service_fee, 2) }} ر.س
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($inv->status === 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">مدفوعة</span>
                                @elseif($inv->status === 'issued')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">صادرة</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">ملغاة</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="عرض الفاتورة">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $inv->id) }}" target="_blank" class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="طباعة الفاتورة">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لا توجد فواتير إلكترونية صادرة بعد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

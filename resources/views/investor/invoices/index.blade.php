@extends('layouts.investor')

@section('title', 'الفواتير الإلكترونية المعتمدة')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">فواتيري المعتمدة</h1>
            <p class="text-xs text-slate-500 mt-1">إصدار وتوثيق فواتير إلكترونية رسمية لعملائك ونزلائك</p>
        </div>
        <a href="{{ route('investor.invoices.create') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-file-invoice"></i>
            <span>إصدار فاتورة جديدة</span>
        </a>
    </div>

    <!-- Notice Card -->
    <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 text-xs text-emerald-900 flex items-start gap-3">
        <i class="fa-solid fa-receipt text-emerald-600 text-base mt-0.5"></i>
        <div class="space-y-1">
            <p class="font-bold">خدمة إصدار الفواتير الضريبية والرسمية المعتمدة</p>
            <p class="text-emerald-800">
                تتيح لك المنصة إصدار فواتير رسمية للنزلاء والعملاء سواء المرتبطين بحجوزات أو عقود أو خدمات إضافية. رسوم خدمة إصدار الفاتورة هي <strong class="font-black text-emerald-950">{{ number_format($serviceFee, 2) }} ر.س</strong> تخصم من محفظتك.
            </p>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">رقم الفاتورة</th>
                        <th class="py-3.5 px-4">العميل / المستأجر</th>
                        <th class="py-3.5 px-4">العقد المرتبط</th>
                        <th class="py-3.5 px-4">تاريخ الإصدار</th>
                        <th class="py-3.5 px-4">المبلغ الإجمالي</th>
                        <th class="py-3.5 px-4">رسوم الخدمة</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('investor.invoices.show', $inv->id) }}" class="text-emerald-600 hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $inv->client_name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $inv->client_phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $inv->contract ? 'عقد ' . $inv->contract->contract_number : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $inv->issue_date->format('Y-m-d') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ number_format($inv->total_amount, 2) }} ر.س
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ number_format($inv->service_fee, 2) }} ر.س
                            </td>
                            <td class="py-3.5 px-4">
                                @if($inv->status === 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">مسددة</span>
                                @elseif($inv->status === 'issued')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">صادرة</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">ملغاة</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('investor.invoices.show', $inv->id) }}" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="عرض الفاتورة">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('investor.invoices.print', $inv->id) }}" target="_blank" class="p-1.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="طباعة الفاتورة">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لم تصدر أي فواتير بعد</p>
                                <a href="{{ route('investor.invoices.create') }}" class="inline-block mt-3 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition">
                                    إصدار أول فاتورة
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

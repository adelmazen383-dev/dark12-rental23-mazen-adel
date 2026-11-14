@extends('layouts.investor')

@section('title', 'عقود الإيجار المعتمدة')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">عقود الإيجار الخاصة بي</h1>
            <p class="text-xs text-slate-500 mt-1">طلب وتوثيق عقود الإيجار الرسمية المعتمدة لشققك السكنية والفندقية</p>
        </div>
        <a href="{{ route('investor.contracts.create') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-file-contract"></i>
            <span>طلب إصدار عقد جديد</span>
        </a>
    </div>

    <!-- Notice Card -->
    <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 text-xs text-emerald-900 flex items-start gap-3">
        <i class="fa-solid fa-shield-halved text-emerald-600 text-base mt-0.5"></i>
        <div class="space-y-1">
            <p class="font-bold">خدمة توثيق العقود الإلكترونية المعتمدة للبرج</p>
            <p class="text-emerald-800">
                يمكنك إصدار عقد إيجار إلكتروني رسمي وموثق لأي مستأجر سواء أكان الحجز تم عبر المنصة أو بشكل خارجي مباشر. رسوم خدمة إصدار العقد هي <strong class="font-black text-emerald-950">{{ number_format($serviceFee, 2) }} ر.س</strong> تخصم من محفظتك.
            </p>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">رقم العقد</th>
                        <th class="py-3.5 px-4">المستأجر</th>
                        <th class="py-3.5 px-4">الوحدة</th>
                        <th class="py-3.5 px-4">الفترة</th>
                        <th class="py-3.5 px-4">الإيجار والتأمين</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contracts as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('investor.contracts.show', $c->id) }}" class="text-emerald-600 hover:underline">
                                    {{ $c->contract_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $c->tenant_name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $c->tenant_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                شقة {{ $c->unit_number }} (الدور {{ $c->floor_number }})
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <div>{{ $c->start_date->format('Y-m-d') }}</div>
                                <div class="text-[10px] text-slate-400">إلى {{ $c->end_date->format('Y-m-d') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900">{{ number_format($c->rent_amount, 2) }} ر.س</span>
                                <span class="text-[10px] text-slate-400 block">تأمين: {{ number_format($c->insurance_amount, 2) }} ر.س</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($c->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">معتمد ونشط</span>
                                @elseif($c->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 animate-pulse">قيد المراجعة</span>
                                @elseif($c->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">منتهي</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">ملغي</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('investor.contracts.show', $c->id) }}" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="عرض العقد">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('investor.contracts.print', $c->id) }}" target="_blank" class="p-1.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="طباعة العقد">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-contract text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لم تصدر أي عقود إيجار بعد</p>
                                <a href="{{ route('investor.contracts.create') }}" class="inline-block mt-3 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition">
                                    طلب إصدار أول عقد
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

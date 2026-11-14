@extends('layouts.investor')

@section('title', 'تفاصيل العقد ' . $contract->contract_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 font-mono">{{ $contract->contract_number }}</h1>
                @if($contract->status === 'active')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">عقد ساري ونشط</span>
                @elseif($contract->status === 'pending')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد المراجعة</span>
                @elseif($contract->status === 'completed')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">منتهي</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">ملغي</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">تاريخ الإصدار: {{ $contract->created_at->format('Y-m-d') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('investor.contracts.print', $contract->id) }}" target="_blank" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>طباعة العقد الرسمي</span>
            </a>
            <a href="{{ route('investor.contracts.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                العودة
            </a>
        </div>
    </div>

    <!-- Details Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Lessor Card -->
        <div class="bg-white rounded-2xl border-2 border-emerald-300 p-5 shadow-sm space-y-3 bg-emerald-50/20">
            <h2 class="text-sm font-bold text-slate-900 border-b border-emerald-200 pb-2 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-emerald-600"></i>
                    <span>الطرف الأول (المؤجر)</span>
                </span>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <i class="fa-solid fa-lock text-[9px]"></i>
                    <span>مثبت رسمي</span>
                </span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">اسم المؤجر:</span>
                    <span class="font-bold text-slate-900">{{ $contract->effective_lessor_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">الهوية الوطنية / السجل:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $contract->effective_lessor_national_id }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-mono text-slate-700" dir="ltr">{{ $contract->investor->phone }}</span>
                </div>
            </div>
        </div>

        <!-- Tenant Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-user-check text-sky-500"></i>
                <span>الطرف الثاني (المستأجر)</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">اسم المستأجر:</span>
                    <span class="font-bold text-slate-800">{{ $contract->tenant_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">الهوية / الإقامة:</span>
                    <span class="font-mono text-slate-800">{{ $contract->tenant_national_id }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-mono text-slate-800" dir="ltr">{{ $contract->tenant_phone }}</span>
                </div>
            </div>
        </div>

        <!-- Apartment Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-door-open text-emerald-500"></i>
                <span>بيانات الشقة والمدة</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">الوحدة والدور:</span>
                    <span class="font-bold text-slate-800">شقة {{ $contract->unit_number }} (الدور {{ $contract->floor_number }})</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">تاريخ البداية:</span>
                    <span class="font-mono text-slate-800">{{ $contract->start_date->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">تاريخ الانتهاء:</span>
                    <span class="font-mono text-slate-800">{{ $contract->end_date->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Financial info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-coins text-emerald-500"></i>
            <span>البيانات المالية والشروط</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">قيمة الإيجار</span>
                <span class="text-base font-black text-slate-900">{{ number_format($contract->rent_amount, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">الدفع: {{ $contract->payment_type_label }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">مبلغ التأمين المسترد</span>
                <span class="text-base font-black text-slate-900">{{ number_format($contract->insurance_amount, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">مبلغ مسترد بعد الإخلاء</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">رسوم إصدار العقد المدفوعة</span>
                <span class="text-base font-black text-emerald-600">{{ number_format($contract->service_fee, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">خُصمت من المحفظة</span>
            </div>
        </div>

        @if($contract->special_terms)
            <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                <h4 class="font-bold text-slate-900 mb-1">الشروط والأحكام الخاصة:</h4>
                <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $contract->special_terms }}</p>
            </div>
        @endif
    </div>

</div>
@endsection

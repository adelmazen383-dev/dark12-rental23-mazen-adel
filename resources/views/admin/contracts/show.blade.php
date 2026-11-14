@extends('layouts.admin')

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
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">منتهي الصلاحية</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">ملغي</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">تاريخ الإصدار: {{ $contract->created_at->format('Y-m-d H:i') }} | المحرر: {{ $contract->issuer?->name ?? 'النظام الآلي' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.contracts.print', $contract->id) }}" target="_blank" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>طباعة العقد الرسمي</span>
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                العودة
            </a>
        </div>
    </div>

    <!-- Contract Details Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Party 1: Landlord (Investor) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-user-tie text-amber-500"></i>
                <span>الطرف الأول (المؤجر)</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">اسم المؤجر (الطرف الأول):</span>
                    <span class="font-bold text-slate-900">{{ $contract->effective_lessor_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">رقم الهوية الوطنية / السجل:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $contract->effective_lessor_national_id }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-mono text-slate-800">{{ $contract->investor?->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">الحساب البنكي / الآيبان:</span>
                    <span class="font-mono text-[11px] text-slate-700">{{ $contract->investor?->iban ?? 'غير محدد' }}</span>
                </div>
            </div>
        </div>

        <!-- Party 2: Tenant -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                <i class="fa-solid fa-user-check text-amber-500"></i>
                <span>الطرف الثاني (المستأجر)</span>
            </h2>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">اسم المستأجر:</span>
                    <span class="font-bold text-slate-800">{{ $contract->tenant_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">رقم الهوية / الإقامة:</span>
                    <span class="font-mono text-slate-800">{{ $contract->tenant_national_id }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-mono text-slate-800">{{ $contract->tenant_phone }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">شقة الإقامة:</span>
                    <span class="font-bold text-slate-800">شقة {{ $contract->unit_number }} (الدور {{ $contract->floor_number }})</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Financial & Terms -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
            <i class="fa-solid fa-coins text-amber-500"></i>
            <span>البيانات المالية وشروط التعاقد</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">قيمة الإيجار</span>
                <span class="text-base font-black text-slate-900">{{ number_format($contract->rent_amount, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">الدفع: {{ $contract->payment_type_label }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">مبلغ التأمين المسترد</span>
                <span class="text-base font-black text-slate-900">{{ number_format($contract->insurance_amount, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">يسترد عند الإخلاء</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-500 block mb-1">مدة العقد الإجمالية</span>
                <span class="text-base font-black text-slate-900">{{ $contract->duration_in_days }} يوم</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">من {{ $contract->start_date->format('Y-m-d') }}</span>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                <span class="text-emerald-700 block mb-1 font-semibold">رسوم المنصة المحصلة</span>
                <span class="text-base font-black text-emerald-800">{{ number_format($contract->service_fee, 2) }} ر.س</span>
                <span class="text-[10px] text-emerald-600 block mt-0.5">إيراد توثيق العقد</span>
            </div>
        </div>

        @if($contract->special_terms)
            <div class="mt-4 p-4 rounded-xl bg-amber-50/50 border border-amber-200/60 text-xs text-slate-700">
                <h4 class="font-bold text-slate-900 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
                    <span>الشروط والأحكام الخاصة:</span>
                </h4>
                <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $contract->special_terms }}</p>
            </div>
        @endif
    </div>

    <!-- Status Modification Action -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-bold text-slate-800">تحديث حالة العقد</h3>
            <p class="text-[11px] text-slate-500">يمكنك تغيير حالة العقد وفق الإجراءات المتبعة في الإدارة</p>
        </div>

        <form action="{{ route('admin.contracts.update-status', $contract->id) }}" method="POST" class="flex items-center gap-2">
            @csrf
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold">
                <option value="pending" {{ $contract->status === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="active" {{ $contract->status === 'active' ? 'selected' : '' }}>تفعيل واعتماد العقد</option>
                <option value="completed" {{ $contract->status === 'completed' ? 'selected' : '' }}>إنهاء العقد (مكتمل)</option>
                <option value="cancelled" {{ $contract->status === 'cancelled' ? 'selected' : '' }}>إلغاء العقد</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                حفظ التغيير
            </button>
        </form>
    </div>

</div>
@endsection

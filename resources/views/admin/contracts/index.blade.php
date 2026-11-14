@extends('layouts.admin')

@section('title', 'إدارة عقود الإيجار المعتمدة')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">عقود الإيجار الإلكترونية</h1>
            <p class="text-xs text-slate-500 mt-1">توثيق وإصدار عقود الإيجار الموحدة للمستثمرين والنزلاء ورسوم الخدمة</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.contracts.create') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-file-contract"></i>
                <span>إصدار عقد إيجار جديد</span>
            </a>
        </div>
    </div>

    <!-- Revenue & Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إجمالي العقود الصادرة</span>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $contracts->total() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">إيرادات رسوم توثيق العقود</span>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalFees, 2) }} <span class="text-xs font-bold text-slate-500">ر.س</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">العقود النشطة الحالية</span>
                <p class="text-2xl font-black text-indigo-600 mt-0.5">{{ \App\Models\RentalContract::where('status', 'active')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.contracts.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">بحث برقم العقد، اسم المستأجر أو الهاتف</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم العقد، اسم المستأجر، الشقة..." class="w-full pl-3 pr-9 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">حالة العقد</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة / طلب مستثمر</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط ومعتمد</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>منتهي الصلاحية</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition text-center">
                    تصفية
                </button>
                <a href="{{ route('admin.contracts.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">رقم العقد</th>
                        <th class="py-3.5 px-4">المستثمر (المؤجر)</th>
                        <th class="py-3.5 px-4">المستأجر</th>
                        <th class="py-3.5 px-4">الوحدة / الدور</th>
                        <th class="py-3.5 px-4">مدة العقد</th>
                        <th class="py-3.5 px-4">قيمة الإيجار</th>
                        <th class="py-3.5 px-4">رسوم المنصة</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contracts as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.contracts.show', $c->id) }}" class="text-amber-600 hover:underline">
                                    {{ $c->contract_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $c->investor?->full_name ?? 'إدارة البرج' }}</p>
                                <span class="text-[10px] text-slate-400">{{ $c->investor?->phone ?? '-' }}</span>
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
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ number_format($c->rent_amount, 2) }} ر.س
                                <div class="text-[10px] text-slate-400 font-normal">تأمين: {{ number_format($c->insurance_amount, 2) }} ر.س</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                    +{{ number_format($c->service_fee, 2) }} ر.س
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($c->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">ساري ونشط</span>
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
                                    <a href="{{ route('admin.contracts.show', $c->id) }}" class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="عرض التفاصيل">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.contracts.print', $c->id) }}" target="_blank" class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition" title="طباعة العقد">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-contract text-4xl mb-2 text-slate-300"></i>
                                <p class="text-sm">لا توجد عقود إيجار مسجلة بعد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $contracts->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

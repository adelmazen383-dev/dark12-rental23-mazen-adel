@extends('layouts.admin')

@section('title', 'إدارة المستثمرين والتراخيص')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل المستثمرين وتراخيص الشقق</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة طلبات الاعتماد، التراخيص السياحية، المحافظ، والاشتراكات الشهرية</p>
        </div>
    </div>

    <!-- Status Tabs (Specification Requirement) -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-2 text-xs">
        <a href="{{ route('admin.investors.index') }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            <span>جميع المستثمرين</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ !request('status') ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['all'] }}</span>
        </a>

        <a href="{{ route('admin.investors.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-500 text-slate-950' : 'bg-white text-amber-700 hover:bg-amber-50' }}">
            <span>قيد المراجعة</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800">{{ $statusCounts['pending'] }}</span>
        </a>

        <a href="{{ route('admin.investors.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50' }}">
            <span>معتمد</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $statusCounts['approved'] }}</span>
        </a>

        <a href="{{ route('admin.investors.index', ['status' => 'suspended']) }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ request('status') === 'suspended' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 hover:bg-rose-50' }}">
            <span>موقوف</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800">{{ $statusCounts['suspended'] }}</span>
        </a>

        <a href="{{ route('admin.investors.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ request('status') === 'rejected' ? 'bg-gray-700 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
            <span>مرفوض</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-700">{{ $statusCounts['rejected'] }}</span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.investors.index') }}" method="GET" class="flex gap-3 text-xs">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم، رقم الجوال، أو رقم الهوية..."
                class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
            <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white font-bold rounded-xl">بحث</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">المستثمر</th>
                        <th class="p-3.5">الهوية والاتصال</th>
                        <th class="p-3.5">الترخيص السياحي</th>
                        <th class="p-3.5">عدد الشقق</th>
                        <th class="p-3.5">رصيد المحفظة</th>
                        <th class="p-3.5">حالة الحساب</th>
                        <th class="p-3.5 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($investors as $inv)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5">
                                <a href="{{ route('admin.investors.show', $inv->id) }}" class="font-bold text-slate-900 text-sm hover:underline">
                                    {{ $inv->full_name }}
                                </a>
                                <div class="text-[10px] text-slate-400">مسجل منذ {{ $inv->created_at->format('Y-m-d') }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-mono text-slate-700">هوية: {{ $inv->id_number }}</div>
                                <div class="text-slate-500" dir="ltr">{{ $inv->phone }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">{{ $inv->license_name }}</div>
                                <div class="text-[10px] text-slate-500">رقم: {{ $inv->license_number }}</div>
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">
                                {{ $inv->apartments->count() }} شقة
                            </td>
                            <td class="p-3.5">
                                <span class="font-black text-emerald-700 text-sm">
                                    {{ number_format($inv->wallet?->balance ?? 0, 2) }} ر.س
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($inv->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">معتمد</span>
                                @elseif($inv->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">قيد المراجعة</span>
                                @elseif($inv->status === 'suspended')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">موقوف</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">مرفوض</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('admin.investors.show', $inv->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-bold rounded-lg transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <span>الملف والمستندات</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">لا يوجد مستثمرون في هذا التصنيف.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $investors->links() }}
        </div>
    </div>

</div>
@endsection

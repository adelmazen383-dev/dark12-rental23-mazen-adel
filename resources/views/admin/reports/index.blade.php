@extends('layouts.admin')

@section('title', 'التقارير المالية')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">تقارير أرباح منصة دَارِك</h1>
            <p class="text-xs text-slate-500 mt-1">كشف الدخل الصافي للمنصة فقط (عمولات الحجوزات، اشتراكات المستثمرين، رسوم توثيق العقود، وغرامات التحايل)</p>
        </div>
        <a href="{{ route('admin.reports.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-file-excel"></i>
            <span>تصدير تقرير CSV</span>
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
            <div class="w-full sm:w-auto">
                <label class="block font-semibold text-slate-600 mb-1">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block font-semibold text-slate-600 mb-1">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 text-white font-bold rounded-xl">
                عرض التقرير
            </button>
        </form>
    </div>

    <!-- Financial Metrics Cards (Platform Profit Only) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Net Platform Earnings (Main highlight) -->
        <div class="sm:col-span-2 lg:col-span-3 bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 text-white rounded-2xl p-6 shadow-lg border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-amber-400 block mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-coins"></i>
                    <span>صافي أرباح ومكاسب منصة دَارِك المحققة:</span>
                </span>
                <div class="text-3xl sm:text-4xl font-black text-amber-400 tracking-tight">
                    {{ number_format($netPlatformRevenue, 2) }} <span class="text-sm font-semibold text-slate-300">ريال سعودي</span>
                </div>
                <p class="text-xs text-slate-300 mt-1">
                    هذا الرقم يمثل دخل المنصة الفعلي فقط، ومستبعد منه تماماً مبالغ الإيجارات التي تذهب للمستثمرين.
                </p>
            </div>
            <div class="text-xs space-y-1.5 bg-slate-800/80 p-3.5 rounded-xl border border-slate-700/80 sm:text-left">
                <div class="text-slate-300">حجوزات المنصة: <strong class="text-amber-400 font-mono">{{ $confirmedBookingsCount }}</strong> حجز</div>
                <div class="text-slate-300">عقود موثقة: <strong class="text-amber-400 font-mono">{{ $contractsCount }}</strong> عقد</div>
                <div class="text-slate-400 text-[11px]">مستردات: {{ number_format($totalRefunds, 2) }} ر.س</div>
            </div>
        </div>

        <!-- Booking Commission -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-sky-700 block mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check text-sky-500"></i>
                <span>رسوم وعمولات الحجوزات:</span>
            </span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($bookingFeesRevenue, 2) }} <span class="text-xs font-normal text-slate-500">ر.س</span></div>
            <span class="text-[10px] text-slate-400 mt-1 block">رسوم مستقطعة آلياً من محافظ المستثمرين عند الحجز المؤكد</span>
        </div>

        <!-- Contract Documentation Fees -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-indigo-700 block mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-file-contract text-indigo-500"></i>
                <span>رسوم توثيق عقود الإيجار:</span>
            </span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($contractFeesRevenue, 2) }} <span class="text-xs font-normal text-slate-500">ر.س</span></div>
            <span class="text-[10px] text-slate-400 mt-1 block">دخل مستقل من خدمة إصدار وتوثيق عقود الإيجار الرسمية</span>
        </div>

        <!-- Investor Subscriptions -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-emerald-700 block mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-id-card-clip text-emerald-500"></i>
                <span>إيرادات الاشتراكات الشهرية:</span>
            </span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($subscriptionRevenue, 2) }} <span class="text-xs font-normal text-slate-500">ر.س</span></div>
            <span class="text-[10px] text-slate-400 mt-1 block">رسوم عضويات المستثمرين وتجديد تراخيص عرض الشقق</span>
        </div>

        @if($penaltyFeesRevenue > 0)
        <!-- Penalty Fees -->
        <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
            <span class="text-xs font-semibold text-rose-700 block mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                <span>غرامات ورسوم الصفقات غير المسجلة:</span>
            </span>
            <div class="text-2xl font-black text-rose-700">{{ number_format($penaltyFeesRevenue, 2) }} <span class="text-xs font-normal text-slate-500">ر.س</span></div>
            <span class="text-[10px] text-slate-400 mt-1 block">مبالغ محصلة عند ضبط صفقات تمت خارج المنصة</span>
        </div>
        @endif
    </div>

    <!-- Daily Breakdown Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">سجل العمليات المالية والخصومات في الفترة المحددة</h3>
            <span class="text-xs text-slate-500">{{ $dailyTransactions->total() }} حركة مالية</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-3">التاريخ والوقت</th>
                        <th class="p-3">المستثمر</th>
                        <th class="p-3">نوع الحركة</th>
                        <th class="p-3">المبلغ</th>
                        <th class="p-3">البيان / رقم الحجز</th>
                        <th class="p-3">الرصيد بعد الحركة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dailyTransactions as $tr)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3 text-slate-500">{{ $tr->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $tr->wallet->investor->full_name }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($tr->type === 'deposit') bg-emerald-100 text-emerald-800
                                    @elseif($tr->type === 'deduction') bg-rose-100 text-rose-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $tr->type_label }}
                                </span>
                            </td>
                            <td class="p-3 font-bold {{ $tr->type === 'deposit' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $tr->type === 'deposit' ? '+' : '-' }}{{ number_format($tr->amount, 2) }} ر.س
                            </td>
                            <td class="p-3 text-slate-800">{{ $tr->description }}</td>
                            <td class="p-3 font-bold text-slate-700">{{ number_format($tr->balance_after, 2) }} ر.س</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-400">لا توجد حركات مالية في هذه الفترة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $dailyTransactions->appends(request()->query())->links() }}
        </div>
    </div>

</div>
@endsection

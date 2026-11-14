@extends('layouts.investor')

@section('title', 'الاشتراك الشهري')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">الاشتراك الشهري في المنصة</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة خطة الاشتراك، متابعة تاريخ التجديد، وسداد الدفعات الشهرية</p>
        </div>
    </div>

    <!-- Active Subscription Status Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 text-white rounded-2xl p-6 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <span class="text-xs text-slate-300 font-semibold block mb-1">حالة الاشتراك الشهري</span>
                @if($activeSubscription)
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-sky-400">نشط ومفعل</span>
                        <span class="text-xs text-slate-300">({{ $activeSubscription->daysRemaining() }} يوم متبقي)</span>
                    </div>
                    <div class="mt-2 text-xs text-slate-300 space-y-1">
                        <div>تاريخ البداية: <strong>{{ $activeSubscription->start_date->format('Y-m-d') }}</strong></div>
                        <div>تاريخ الانتهاء والتجديد: <strong>{{ $activeSubscription->end_date->format('Y-m-d') }}</strong></div>
                    </div>
                @else
                    <div class="text-2xl font-black text-rose-400">الاشتراك منتهي أو غير مفعل</div>
                    <p class="text-xs text-slate-300 mt-1">يتم إيقاف ظهور الشقق الجديدة في الباركود عند انتهاء الاشتراك.</p>
                @endif
            </div>

            <!-- Fee info -->
            <div class="bg-slate-800/80 rounded-xl p-4 border border-slate-700/80 text-xs sm:max-w-xs">
                <span class="text-amber-400 font-bold block mb-1">قيمة الاشتراك الشهري:</span>
                <div class="text-xl font-black text-white mb-1">{{ number_format($monthlyFee, 2) }} ريال / شهر</div>
                <p class="text-slate-400 text-[10px]">
                    * الاشتراك الشهري منفصل تماماً عن رصيد المحفظة ويشمل خدمة عرض الشقق والاستقبال والربط بالباركود.
                </p>
            </div>
        </div>
    </div>

    <!-- Renewal Guide -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex items-center gap-2 border-b pb-3">
            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-900">طريقة تجديد الاشتراك الشهري</h3>
                <p class="text-xs text-slate-500">قم بتحويل مبلغ الاشتراك إلى حساب المنصة ثم أرسل الإيصال لاعتماد التجديد فوراً</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($bankAccounts as $bank)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-900">{{ $bank->bank_name }}</span>
                        <span class="text-slate-500">{{ $bank->account_name }}</span>
                    </div>
                    <div class="font-mono text-amber-700 font-bold bg-white p-2 rounded-lg border text-left" dir="ltr">
                        {{ $bank->iban }}
                    </div>
                </div>
            @endforeach
        </div>

        @php
            $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
        @endphp
        <div class="pt-2 flex justify-end">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $adminPhone) }}?text={{ urlencode('مرحباً، أود تجديد الاشتراك الشهري كمستثمر باسم: ' . $investor->full_name) }}" target="_blank"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>إرسال إيصال التجديد لإدارة البرج</span>
            </a>
        </div>
    </div>

    <!-- Subscription History Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">سجل الاشتراكات السابقة</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-3">الفترة</th>
                        <th class="p-3">تاريخ البداية</th>
                        <th class="p-3">تاريخ الانتهاء</th>
                        <th class="p-3">المبلغ المدفوع</th>
                        <th class="p-3">الحالة</th>
                        <th class="p-3">مرجع السداد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscriptions as $sub)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">اشتراك شهري</td>
                            <td class="p-3">{{ $sub->start_date->format('Y-m-d') }}</td>
                            <td class="p-3">{{ $sub->end_date->format('Y-m-d') }}</td>
                            <td class="p-3 font-bold text-emerald-700">{{ number_format($sub->amount, 2) }} ر.س</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $sub->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $sub->isActive() ? 'نشط' : 'منتهي' }}
                                </span>
                            </td>
                            <td class="p-3 font-mono text-slate-500">{{ $sub->payment_reference ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">لا توجد اشتراكات سابقة مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $subscriptions->links() }}
        </div>
    </div>

</div>
@endsection

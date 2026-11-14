@extends('layouts.investor')

@section('title', 'لوحة تحكم المستثمر')

@section('content')
<div class="space-y-6">

    <!-- Welcome & Status Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-2xl p-5 sm:p-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                    @if($investor->status === 'approved') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                    @elseif($investor->status === 'pending') bg-amber-500/20 text-amber-300 border border-amber-500/30
                    @elseif($investor->status === 'suspended') bg-rose-500/20 text-rose-300 border border-rose-500/30
                    @else bg-gray-500/20 text-gray-300 @endif mb-2">
                    حالة الحساب: {{ $investor->status === 'approved' ? 'معتمد ومفعل' : ($investor->status === 'pending' ? 'قيد المراجعة والتدقيق' : ($investor->status === 'suspended' ? 'موقوف مؤقتاً' : 'مرفوض')) }}
                </span>
                <h1 class="text-xl sm:text-2xl font-black">مرحباً بك، {{ $investor->full_name }}</h1>
                <p class="text-xs text-slate-300 mt-1">متابعة شققك في البرج والحجوزات ومحفظتك المالية</p>
            </div>

            @if($investor->status === 'approved')
                <a href="{{ route('investor.apartments.create') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm w-fit">
                    <i class="fa-solid fa-plus"></i>
                    <span>إضافة شقة جديدة</span>
                </a>
            @endif
        </div>
    </div>

    @if($investor->status === 'suspended')
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg flex-shrink-0 mt-0.5"></i>
            <div>
                <h4 class="font-bold text-sm text-rose-800">حسابك موقوف مؤقتاً</h4>
                <p class="mt-1 leading-relaxed">{{ $investor->suspension_reason ?: 'يرجى مراجعة إدارة البرج لاستكمال متطلبات الترخيص أو تجديد الاشتراك.' }}</p>
            </div>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Wallet Balance Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">رصيد المحفظة</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-700">{{ number_format($wallet?->balance ?? 0, 2) }} <span class="text-xs font-normal">ر.س</span></div>
                <a href="{{ route('investor.wallet.index') }}" class="text-[11px] text-emerald-600 font-bold hover:underline mt-2 inline-flex items-center gap-1">
                    <span>إرسال طلب شحن</span>
                    <span>&larr;</span>
                </a>
            </div>
        </div>

        <!-- Subscription Status Card -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">الاشتراك الشهري</span>
                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-repeat"></i>
                </span>
            </div>
            <div>
                @if($subscription)
                    <div class="text-2xl font-black text-sky-700">{{ $subscription->daysRemaining() }} <span class="text-xs font-normal">يوم متبقي</span></div>
                    <span class="text-[10px] text-slate-400 block mt-1">ينتهي في {{ $subscription->end_date->format('Y-m-d') }}</span>
                @else
                    <div class="text-xl font-bold text-rose-600">غير نشط</div>
                    <a href="{{ route('investor.subscription.index') }}" class="text-[11px] text-rose-600 font-bold hover:underline mt-1 inline-block">تفعيل الاشتراك &larr;</a>
                @endif
            </div>
        </div>

        <!-- Apartments Count -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">شققك بالبرج</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-door-open"></i>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $apartmentsCount }} <span class="text-xs font-normal text-slate-500">شقة مسجلة</span></div>
                <a href="{{ route('investor.apartments.index') }}" class="text-[11px] text-amber-700 font-bold hover:underline mt-2 inline-block">
                    إدارة الشقق والوسائط &larr;
                </a>
            </div>
        </div>

        <!-- Active Bookings -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-500">الحجوزات القائمة</span>
                <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-purple-700">{{ $activeBookingsCount }} <span class="text-xs font-normal text-slate-500">حجز مؤكد</span></div>
                <a href="{{ route('investor.bookings.index') }}" class="text-[11px] text-purple-700 font-bold hover:underline mt-2 inline-block">
                    عرض جميع الحجوزات &larr;
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">أحدث حجوزات شققك</h3>
            <a href="{{ route('investor.bookings.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                عرض الكل &larr;
            </a>
        </div>

        @if($recentBookings->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                لا توجد حجوزات مسجلة بعد على شققك.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                        <tr>
                            <th class="p-3">رقم الحجز</th>
                            <th class="p-3">الشقة</th>
                            <th class="p-3">النزيل</th>
                            <th class="p-3">تاريخ الإقامة</th>
                            <th class="p-3">المبلغ الإجمالي</th>
                            <th class="p-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentBookings as $b)
                            <tr>
                                <td class="p-3 font-mono font-bold text-amber-700">{{ $b->reference_number }}</td>
                                <td class="p-3 font-semibold">شقة {{ $b->apartment->apartment_number }} (الدور {{ $b->apartment->floor_number }})</td>
                                <td class="p-3">
                                    <div>{{ $b->customer_name }}</div>
                                    <div class="text-[10px] text-slate-400" dir="ltr">{{ $b->customer_phone }}</div>
                                </td>
                                <td class="p-3">{{ $b->checkin_date->format('Y-m-d') }} &rarr; {{ $b->checkout_date->format('Y-m-d') }}</td>
                                <td class="p-3 font-black text-emerald-700">{{ number_format($b->total_amount, 2) }} ر.س</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                        @if($b->status === 'confirmed') bg-emerald-100 text-emerald-800
                                        @elseif($b->status === 'pending_verification') bg-blue-100 text-blue-800
                                        @elseif($b->status === 'cancelled') bg-rose-100 text-rose-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $b->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Inspections (Viewings) -->
    @if($recentInspections->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900">طلبات معاينة شققك الأخيرة</h3>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentInspections as $ins)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">{{ $ins->customer_name }}</span>
                            <span class="text-slate-400 mr-2" dir="ltr">{{ $ins->customer_phone }}</span>
                            <div class="text-slate-500 mt-0.5">شقة {{ $ins->apartment->apartment_number }} • {{ $ins->notes ?: 'بدون ملاحظات' }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            {{ $ins->status === 'completed' ? 'تمت' : ($ins->status === 'scheduled' ? 'مجدولة' : 'بانتظار التنسيق') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@extends('layouts.admin')

@section('title', 'الرئيسية والإحصائيات')

@section('content')
<div class="space-y-6">

    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">لوحة المتابعة المركزية</h1>
            <p class="text-xs text-slate-500 mt-1">نظرة شاملة على الشقق، الحجوزات، التحويلات، وأرصدة المستثمرين</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.apartments.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة شقة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pending Receipts (Urgent) -->
        <div class="bg-white rounded-2xl p-4 border border-rose-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-rose-600">إيصالات بانتظار التحقق</span>
                <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-receipt"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['pending_receipts'] }}</div>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending_verification']) }}" class="text-[11px] text-rose-600 font-bold hover:underline mt-2 inline-block">
                مراجعة واعتماد الحجوزات &larr;
            </a>
            @if($stats['pending_receipts'] > 0)
                <span class="absolute top-2 left-2 w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
            @endif
        </div>

        <!-- Pending Investors -->
        <div class="bg-white rounded-2xl p-4 border border-amber-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-amber-700">مستثمرون بانتظار الاعتماد</span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-clock"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['pending_investors'] }}</div>
            <a href="{{ route('admin.investors.index', ['status' => 'pending']) }}" class="text-[11px] text-amber-700 font-bold hover:underline mt-2 inline-block">
                تدقيق التراخيص والهويات &larr;
            </a>
        </div>

        <!-- Confirmed Bookings -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-600">الحجوزات المؤكدة</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['confirmed_bookings'] }}</div>
            <span class="text-[11px] text-slate-400 mt-2 block">حجوزات اعتُمدت رسميًا</span>
        </div>

        <!-- Total Booking Fees (Platform Revenue) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-slate-600">إيرادات رسوم الحجوزات</span>
                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-sky-700">{{ number_format($stats['total_booking_fees'], 2) }} <span class="text-xs font-normal">ر.س</span></div>
            <span class="text-[11px] text-slate-400 mt-2 block">رسوم ثابتة مخصومة من المحافظ</span>
        </div>
    </div>

    <!-- Secondary Financial Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-500 font-medium block">إيرادات الاشتراكات الشهرية:</span>
                <span class="text-lg font-black text-slate-900">{{ number_format($stats['total_subscriptions'], 2) }} ر.س</span>
            </div>
            <i class="fa-solid fa-repeat text-slate-400 text-xl"></i>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-500 font-medium block">إجمالي أرصدة محافظ المستثمرين:</span>
                <span class="text-lg font-black text-emerald-700">{{ number_format($stats['total_wallets_balance'], 2) }} ر.س</span>
            </div>
            <i class="fa-solid fa-wallet text-emerald-500 text-xl"></i>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-500 font-medium block">إجمالي شقق البرج:</span>
                <span class="text-lg font-black text-slate-900">{{ $stats['active_apartments'] }} <span class="text-xs font-normal text-slate-400">نشطة من {{ $stats['total_apartments'] }}</span></span>
            </div>
            <i class="fa-solid fa-hotel text-slate-400 text-xl"></i>
        </div>
    </div>

    <!-- Pending Receipts Table (Quick Action Required) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"></span>
                <h3 class="font-bold text-sm text-gray-900">إيصالات تحويل بانتظار التحقق والاعتماد</h3>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-bold text-amber-600 hover:underline">
                عرض جميع الحجوزات &larr;
            </a>
        </div>

        @if($pendingReceipts->isEmpty())
            <div class="p-8 text-center text-xs text-gray-500">
                <i class="fa-regular fa-circle-check text-2xl text-emerald-500 mb-2 block"></i>
                لا توجد إيصالات تحويل معلقة حالياً، جميع الحجوزات معالجة.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="p-3">رقم الحجز</th>
                            <th class="p-3">العميل</th>
                            <th class="p-3">الشقة والدور</th>
                            <th class="p-3">المبلغ</th>
                            <th class="p-3">الإيصال</th>
                            <th class="p-3">تاريخ الرفع</th>
                            <th class="p-3 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingReceipts as $receipt)
                            @php $b = $receipt->booking; @endphp
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="p-3 font-mono font-bold text-amber-700">
                                    <a href="{{ route('admin.bookings.show', $b->id) }}" class="hover:underline">
                                        {{ $b->reference_number }}
                                    </a>
                                </td>
                                <td class="p-3">
                                    <div class="font-semibold text-gray-900">{{ $b->customer_name }}</div>
                                    <div class="text-[10px] text-gray-500" dir="ltr">{{ $b->customer_phone }}</div>
                                </td>
                                <td class="p-3">شقة {{ $b->apartment->apartment_number }} (الدور {{ $b->apartment->floor_number }})</td>
                                <td class="p-3 font-black text-emerald-700">{{ number_format($b->total_amount, 2) }} ر.س</td>
                                <td class="p-3">
                                    <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg font-bold flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                        <span>معاينة</span>
                                    </a>
                                </td>
                                <td class="p-3 text-gray-500">{{ $receipt->created_at->diffForHumans() }}</td>
                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Confirm and verify button -->
                                        <form action="{{ route('admin.bookings.confirm', $b->id) }}" method="POST" onsubmit="return confirm('هل تأكدت من وصول المبلغ في الحساب البنكي؟ سيتم اعتماد الحجز وخصم الرسوم وإشعار العميل والمستثمر.');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition flex items-center gap-1" title="اعتماد الحجز وخصم الرسوم">
                                                <i class="fa-solid fa-check"></i>
                                                <span>اعتماد الحجز</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.bookings.show', $b->id) }}" class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg" title="التفاصيل">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Pending Investors Section -->
    @if($pendingInvestors->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-amber-100 bg-amber-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="font-bold text-sm text-gray-900">طلبات تسجيل مستثمرين جدد قيد المراجعة</h3>
                </div>
                <a href="{{ route('admin.investors.index', ['status' => 'pending']) }}" class="text-xs font-bold text-amber-700 hover:underline">
                    عرض جميع الطلبات &larr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="p-3">اسم المستثمر</th>
                            <th class="p-3">رقم الهوية</th>
                            <th class="p-3">رقم الجوال</th>
                            <th class="p-3">الترخيص السياحي</th>
                            <th class="p-3">تاريخ التقديم</th>
                            <th class="p-3 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingInvestors as $inv)
                            <tr class="hover:bg-amber-50/30 transition">
                                <td class="p-3 font-bold text-gray-900">{{ $inv->full_name }}</td>
                                <td class="p-3 font-mono">{{ $inv->id_number }}</td>
                                <td class="p-3" dir="ltr">{{ $inv->phone }}</td>
                                <td class="p-3">
                                    <span class="font-semibold text-gray-800">{{ $inv->license_name }}</span>
                                    <span class="text-[10px] text-gray-500 block">رقم: {{ $inv->license_number }}</span>
                                </td>
                                <td class="p-3 text-gray-500">{{ $inv->created_at->diffForHumans() }}</td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('admin.investors.show', $inv->id) }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <span>فحص المستندات والاعتماد</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

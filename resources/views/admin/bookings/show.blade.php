@extends('layouts.admin')

@section('title', 'تفاصيل الحجز - ' . $booking->reference_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ $booking->reference_number }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                    @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-800
                    @elseif($booking->status === 'pending_verification') bg-blue-100 text-blue-800
                    @elseif($booking->status === 'cancelled') bg-rose-100 text-rose-800
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
            <p class="text-xs text-slate-500">تم الإنشاء في: {{ $booking->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.bookings.index') }}" class="px-3.5 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                &rarr; العودة للحجوزات
            </a>
        </div>
    </div>

    <!-- Booking Details and Calculations -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Customer & Apartment -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-user-check text-amber-500"></i>
                <span>بيانات النزيل والشقة</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">اسم العميل:</span>
                    <span class="font-bold text-slate-800">{{ $booking->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-bold text-slate-800" dir="ltr">{{ $booking->customer_phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">الشقة المحجوزة:</span>
                    <span class="font-bold text-slate-800">شقة {{ $booking->apartment->apartment_number }} (الدور {{ $booking->apartment->floor_number }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">صاحب الشقة:</span>
                    <a href="{{ route('admin.investors.show', $booking->apartment->investor_id) }}" class="font-bold text-amber-700 hover:underline">
                        {{ $booking->apartment->investor->full_name }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">تاريخ الدخول:</span>
                    <span class="font-bold text-slate-800">{{ $booking->checkin_date->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">تاريخ الخروج:</span>
                    <span class="font-bold text-slate-800">{{ $booking->checkout_date->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">عدد الليالي:</span>
                    <span class="font-bold text-slate-800">{{ $booking->nights_count }} ليالي</span>
                </div>
            </div>

            @if($booking->notes)
                <div class="p-3 rounded-xl bg-slate-50 text-xs text-slate-600 border border-slate-100">
                    <strong>ملاحظات العميل:</strong> {{ $booking->notes }}
                </div>
            @endif
        </div>

        <!-- Financial Breakdown -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-amber-500"></i>
                <span>تفاصيل المبالغ المالية</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">سعر الليلة:</span>
                    <span class="font-bold text-slate-800">{{ number_format($booking->daily_rate, 2) }} ر.س</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">إجمالي الإيجار ({{ $booking->nights_count }} ليالي):</span>
                    <span class="font-bold text-slate-800">{{ number_format($booking->daily_rate * $booking->nights_count, 2) }} ر.س</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">مبلغ التأمين المسترد:</span>
                    <span class="font-bold text-slate-800">{{ number_format($booking->insurance_amount, 2) }} ر.س</span>
                </div>
                @if($booking->extra_fees > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-500">رسوم إضافية:</span>
                        <span class="font-bold text-slate-800">{{ number_format($booking->extra_fees, 2) }} ر.س</span>
                    </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-black text-emerald-700">
                    <span>إجمالي المبلغ المطلوب تحويله:</span>
                    <span>{{ number_format($booking->total_amount, 2) }} ر.س</span>
                </div>
            </div>

            @if($booking->confirmed_at)
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                    <div class="font-bold flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-shield-check"></i>
                        <span>تم اعتماد الحجز رسمياً</span>
                    </div>
                    <div>بتاريخ: {{ $booking->confirmed_at->format('Y-m-d H:i') }} بواسطة {{ $booking->confirmedByUser?->name }}</div>
                </div>
            @endif

            @if($booking->cancelled_at)
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                    <div class="font-bold flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-ban"></i>
                        <span>حجز ملغي</span>
                    </div>
                    <div>سبب الإلغاء: {{ $booking->cancellation_reason }}</div>
                </div>
            @endif
        </div>

    </div>

    <!-- Receipts Verification Section (Specification Requirement) -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
        <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-amber-500"></i>
            <span>إيصالات التحويل البنكي المرفوعة من العميل</span>
        </h3>

        @if($booking->receipts->isEmpty())
            <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                لم يقم العميل برفع إيصال التحويل بعد.
            </div>
        @else
            <div class="space-y-4">
                @foreach($booking->receipts as $receipt)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border flex items-center justify-center text-blue-600 text-lg shadow-sm">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="font-bold text-xs text-slate-900">إيصال تحويل رقم #{{ $receipt->id }}</div>
                                <div class="text-[10px] text-slate-500">تم الرفع في: {{ $receipt->created_at->format('Y-m-d H:i') }}</div>
                                <div class="mt-1">
                                    @if($receipt->status === 'verified')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">تم التحقق بنجاح</span>
                                    @elseif($receipt->status === 'rejected')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">مرفوض: {{ $receipt->rejection_reason }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">قيد المراجعة</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank" class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                <span>معاينة الإيصال</span>
                            </a>

                            @if($receipt->status === 'pending')
                                <button type="button" onclick="openRejectReceiptModal({{ $receipt->id }})" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg text-xs font-bold transition">
                                    رفض الإيصال
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Administrative Action Bar (Critical Specification Workflow) -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h4 class="font-bold text-sm text-amber-400">الإجراء الإداري للحجز</h4>
            <p class="text-[11px] text-slate-300 mt-0.5">
                تأكد من مطابقة وصول المبلغ الفعلي في كشف الحساب البنكي قبل الضغط على تأكيد واعتماد الحجز.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($booking->status !== 'confirmed' && $booking->status !== 'completed' && $booking->status !== 'cancelled')
                <!-- Final Confirmation Button -->
                <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" onsubmit="return confirm('تأكيد التحقق من وصول المبلغ بنكياً؟ سيتم اعتماد الحجز وخصم رسوم المنصة من محفظة المستثمر وإشعار العميل.');">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black rounded-xl text-xs transition flex items-center gap-1.5 shadow-md">
                        <i class="fa-solid fa-check-double"></i>
                        <span>التحقق من الدفع واعتماد الحجز</span>
                    </button>
                </form>
            @endif

            @if($booking->status === 'confirmed')
                <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition">
                        اكتمال الإقامة والمغادرة
                    </button>
                </form>
            @endif

            @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                <button type="button" onclick="document.getElementById('cancelBookingModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition">
                    إلغاء الحجز
                </button>
            @endif
        </div>
    </div>

</div>

<!-- Modal: Reject Receipt -->
<div id="rejectReceiptModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-rose-700">رفض إيصال التحويل</h3>
            <button onclick="document.getElementById('rejectReceiptModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="rejectReceiptForm" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">سبب رفض الإيصال (سيصل للعميل عبر واتساب) *</label>
                <textarea name="rejection_reason" required rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="مثال: صورة الحوالة غير واضحة / المبلغ المحول غير مطابق / لم تصل الحوالة للبنك..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('rejectReceiptModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cancel Booking -->
<div id="cancelBookingModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-rose-700">إلغاء الحجز</h3>
            <button onclick="document.getElementById('cancelBookingModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الإلغاء *</label>
                <textarea name="cancellation_reason" required rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="اكتب سبب إلغاء الحجز..."></textarea>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                ملاحظة: إذا كان الحجز مؤكداً وتم خصم رسوم الحجز مسبقاً، فسيتم إعادة رسوم الحجز تلقائياً إلى محفظة المستثمر وفق سياسة المنصة.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('cancelBookingModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">تراجع</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold">تأكيد الإلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openRejectReceiptModal(receiptId) {
        const form = document.getElementById('rejectReceiptForm');
        form.action = `/admin/bookings/{{ $booking->id }}/receipts/${receiptId}/reject`;
        document.getElementById('rejectReceiptModal').classList.remove('hidden');
    }
</script>
@endsection

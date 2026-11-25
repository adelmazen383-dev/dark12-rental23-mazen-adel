@extends('layouts.client')

@section('title', 'حالة الطلب - حجز ' . $booking->reference_number)
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="space-y-6 max-w-xl mx-auto">

    <!-- Success Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 text-center">
        <!-- Status Icon -->
        <div class="w-20 h-20 bg-amber-100 rounded-3xl mx-auto flex items-center justify-center text-amber-600 text-3xl shadow-inner mb-4 animate-bounce">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>

        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 mb-2">
            {{ $booking->status_label }}
        </span>

        <h2 class="text-xl sm:text-2xl font-black text-gray-900 mb-2">
            تم استلام إيصال التحويل بنجاح!
        </h2>

        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto leading-relaxed">
            شكراً لاختيارك أجنحة البرج. طلبك الآن قيد المراجعة والتحقق المالي من قِبل إدارة البرج، وسيصلك تأكيد فوري عبر واتساب فور مطابقة الحوالة.
        </p>

        <!-- Reference & Reception Key Passcode Cards -->
        <div class="my-6 grid grid-cols-1 sm:grid-cols-2 gap-3 text-white">
            <div class="p-4 rounded-2xl bg-slate-900 flex flex-col items-center justify-center border border-slate-800">
                <span class="text-[11px] text-slate-400 mb-1">الرقم المرجعي للحجز</span>
                <span class="text-xl font-black text-white tracking-wider font-mono">{{ $booking->reference_number }}</span>
                <span class="text-[10px] text-slate-400 mt-1">مرجعك الرسمي للمتابعة</span>
            </div>

            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-600 to-amber-700 flex flex-col items-center justify-center shadow-md">
                <div class="flex items-center gap-1.5 text-xs text-amber-100 font-bold mb-1">
                    <i class="fa-solid fa-key"></i>
                    <span>كود استلام المفتاح (الريسبشن)</span>
                </div>
                <span class="text-2xl font-black text-white tracking-wider font-mono bg-black/20 px-3 py-1 rounded-xl">{{ $booking->reception_code ?? 'RCP-' . substr($booking->reference_number, 3, 6) }}</span>
                <span class="text-[10px] text-amber-100 mt-1 font-medium">أبرز هذا الكود بالاستقبال لاستلام كارت الشقة</span>
            </div>
        </div>

        <!-- Summary Details -->
        <div class="text-right bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2 text-xs">
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">اسم العميل:</span>
                <span class="font-bold text-gray-800">{{ $booking->customer_name }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">رقم الشقة:</span>
                <span class="font-bold text-gray-800">شقة {{ $booking->apartment->apartment_number }} (الدور {{ $booking->apartment->floor_number }})</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-200/60">
                <span class="text-gray-500">تاريخ الإقامة:</span>
                <span class="font-bold text-gray-800">{{ $booking->checkin_date->format('Y-m-d') }} إلى {{ $booking->checkout_date->format('Y-m-d') }} ({{ $booking->nights_count }} ليالي)</span>
            </div>
            <div class="flex justify-between py-1 text-emerald-800 font-bold text-sm">
                <span>المبلغ الإجمالي المحول:</span>
                <span>{{ number_format($booking->total_amount, 2) }} ر.س</span>
            </div>
        </div>

        <!-- Steps Timeline -->
        <div class="mt-6 text-right">
            <h4 class="text-xs font-bold text-gray-700 mb-3">ماذا يحدث الآن؟</h4>
            <div class="space-y-3 text-xs">
                <div class="flex items-start gap-2.5">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-gray-600">تم إرسال إشعار آلي فوري إلى واتساب إدارة البرج.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-gray-600">تم إرسال إشعار آلي إلى صاحب الشقة بتفاصيل الحجز والإيصال.</span>
                </div>
                <div class="flex items-start gap-2.5">
                    <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-spinner animate-spin"></i>
                    </div>
                    <span class="text-gray-800 font-bold">جاري مطابقة الحوالة البنكية واعتماد الحجز رسمياً.</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <form action="{{ route('client.chat.start', $booking->apartment_id) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="customer_name" value="{{ $booking->customer_name }}">
                <input type="hidden" name="customer_phone" value="{{ $booking->customer_phone }}">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="initial_message" value="مرحباً، قمت بتأكيد حجز الشقة رقم {{ $booking->apartment->apartment_number }} (رقم الحجز: {{ $booking->reference_number }}).">
                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-comments text-base"></i>
                    <span>محادثة صاحب الشقة مباشرة 💬</span>
                </button>
            </form>

            <a href="{{ route('client.apartments.index') }}" class="py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition text-center flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-house text-sm"></i>
                <span>الرئيسية</span>
            </a>
        </div>
    </div>

</div>
@endsection

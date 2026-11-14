@extends('layouts.client')

@section('title', 'تفاصيل الحجز - ' . $booking->reference_number)

@section('content')
<div class="space-y-6 max-w-xl mx-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs text-gray-400 font-semibold block">الرقم المرجعي</span>
                <span class="text-xl font-black text-amber-600 font-mono">{{ $booking->reference_number }}</span>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold 
                    @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-800
                    @elseif($booking->status === 'pending_verification') bg-blue-100 text-blue-800
                    @elseif($booking->status === 'cancelled') bg-rose-100 text-rose-800
                    @else bg-amber-100 text-amber-800 @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
        </div>

        <div class="py-4 space-y-3 text-xs border-b border-gray-100">
            <div class="flex justify-between">
                <span class="text-gray-500">اسم العميل:</span>
                <span class="font-bold text-gray-800">{{ $booking->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">رقم الشقة:</span>
                <span class="font-bold text-gray-800">شقة {{ $booking->apartment->apartment_number }} - الدور {{ $booking->apartment->floor_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">تاريخ الدخول:</span>
                <span class="font-bold text-gray-800">{{ $booking->checkin_date->format('Y-m-d') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">تاريخ الخروج:</span>
                <span class="font-bold text-gray-800">{{ $booking->checkout_date->format('Y-m-d') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">عدد الليالي:</span>
                <span class="font-bold text-gray-800">{{ $booking->nights_count }} ليالي</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-100 text-sm font-black text-emerald-700">
                <span>المبلغ الإجمالي:</span>
                <span>{{ number_format($booking->total_amount, 2) }} ر.س</span>
            </div>
        </div>

        @if($booking->status === 'pending_payment')
            <div class="mt-6">
                <a href="{{ route('client.booking.payment', $booking->reference_number) }}" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>استكمال التحويل ورفع الإيصال</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

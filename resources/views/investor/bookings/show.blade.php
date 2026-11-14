@extends('layouts.investor')

@section('title', 'تفاصيل الحجز - ' . $booking->reference_number)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 font-mono">{{ $booking->reference_number }}</h1>
            <p class="text-xs text-slate-500 mt-1">شقة {{ $booking->apartment->apartment_number }} (الدور {{ $booking->apartment->floor_number }})</p>
        </div>
        <a href="{{ route('investor.bookings.index') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-600">
            &rarr; العودة للحجوزات
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b">
            <span class="text-xs font-bold text-slate-500">حالة الحجز:</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold 
                @if($booking->status === 'confirmed') bg-emerald-100 text-emerald-800
                @elseif($booking->status === 'pending_verification') bg-blue-100 text-blue-800
                @elseif($booking->status === 'cancelled') bg-rose-100 text-rose-800
                @else bg-amber-100 text-amber-800 @endif">
                {{ $booking->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px]">اسم النزيل</span>
                <span class="font-bold text-slate-800 text-sm">{{ $booking->customer_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">رقم الجوال</span>
                <span class="font-bold text-slate-800 text-sm" dir="ltr">{{ $booking->customer_phone }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">تاريخ الدخول</span>
                <span class="font-bold text-slate-800">{{ $booking->checkin_date->format('Y-m-d') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">تاريخ الخروج</span>
                <span class="font-bold text-slate-800">{{ $booking->checkout_date->format('Y-m-d') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">عدد الليالي</span>
                <span class="font-bold text-slate-800">{{ $booking->nights_count }} ليلة</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">سعر الليلة</span>
                <span class="font-bold text-slate-800">{{ number_format($booking->daily_rate, 2) }} ر.س</span>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500">إجمالي قيمة الإيجار:</span>
                <span class="font-bold">{{ number_format($booking->daily_rate * $booking->nights_count, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">مبلغ التأمين (مسترد للنزيل عند الخروج):</span>
                <span>{{ number_format($booking->insurance_amount, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between pt-2 border-t font-black text-sm text-emerald-700">
                <span>المبلغ الكلي المحول من النزيل:</span>
                <span>{{ number_format($booking->total_amount, 2) }} ر.س</span>
            </div>
        </div>
    </div>

</div>
@endsection

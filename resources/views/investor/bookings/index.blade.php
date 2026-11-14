@extends('layouts.investor')

@section('title', 'حجوزات شققي')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل حجوزات شققي بالبرج</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة الحجوزات المؤكدة، القادمة، والمنتهية لجميع وحداتك</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-2 text-xs">
        <a href="{{ route('investor.bookings.index') }}" class="px-4 py-2 rounded-xl font-bold transition {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            الكل
        </a>
        <a href="{{ route('investor.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50' }}">
            المؤكدة
        </a>
        <a href="{{ route('investor.bookings.index', ['status' => 'pending_verification']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'pending_verification' ? 'bg-blue-600 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
            بانتظار التحقق المالي
        </a>
        <a href="{{ route('investor.bookings.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 hover:bg-rose-50' }}">
            الملغاة
        </a>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-3.5">رقم الحجز</th>
                        <th class="p-3.5">الشقة والدور</th>
                        <th class="p-3.5">النزيل</th>
                        <th class="p-3.5">تاريخ الدخول</th>
                        <th class="p-3.5">تاريخ الخروج</th>
                        <th class="p-3.5">الليالي</th>
                        <th class="p-3.5">إجمالي المبلغ</th>
                        <th class="p-3.5">الحالة</th>
                        <th class="p-3.5 text-center">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5 font-mono font-bold text-amber-700">
                                <a href="{{ route('investor.bookings.show', $b->id) }}" class="hover:underline">{{ $b->reference_number }}</a>
                            </td>
                            <td class="p-3.5 font-bold text-slate-900">شقة {{ $b->apartment->apartment_number }} (الدور {{ $b->apartment->floor_number }})</td>
                            <td class="p-3.5">
                                <div>{{ $b->customer_name }}</div>
                                <div class="text-[10px] text-slate-400" dir="ltr">{{ $b->customer_phone }}</div>
                            </td>
                            <td class="p-3.5">{{ $b->checkin_date->format('Y-m-d') }}</td>
                            <td class="p-3.5">{{ $b->checkout_date->format('Y-m-d') }}</td>
                            <td class="p-3.5">{{ $b->nights_count }} ليلة</td>
                            <td class="p-3.5 font-black text-emerald-700">{{ number_format($b->total_amount, 2) }} ر.س</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                    @if($b->status === 'confirmed') bg-emerald-100 text-emerald-800
                                    @elseif($b->status === 'pending_verification') bg-blue-100 text-blue-800
                                    @elseif($b->status === 'cancelled') bg-rose-100 text-rose-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center">
                                <a href="{{ route('investor.bookings.show', $b->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg inline-block">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">لا توجد حجوزات مسجلة في هذا القسم.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $bookings->links() }}
        </div>
    </div>

</div>
@endsection

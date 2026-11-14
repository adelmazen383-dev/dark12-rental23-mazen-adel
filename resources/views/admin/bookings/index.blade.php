@extends('layouts.admin')

@section('title', 'إدارة الحجوزات والإيصالات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل الحجوزات والتحقق من الدفع</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة إيصالات التحويل البنكي، اعتماد الحجوزات، وإدارة الإلغاءات</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-2 text-xs">
        <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-xl font-bold transition {{ !request('status') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            الكل
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending_verification']) }}" class="px-4 py-2 rounded-xl font-bold transition flex items-center gap-1.5 {{ request('status') === 'pending_verification' ? 'bg-blue-600 text-white' : 'bg-white text-blue-700 hover:bg-blue-50' }}">
            <span>بانتظار التحقق من الدفع</span>
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50' }}">
            مؤكد
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'pending_payment']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'pending_payment' ? 'bg-amber-500 text-slate-950' : 'bg-white text-amber-700 hover:bg-amber-50' }}">
            بانتظار التحويل
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 hover:bg-rose-50' }}">
            ملغي
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl font-bold transition {{ request('status') === 'completed' ? 'bg-gray-700 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
            مكتمل
        </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">الرقم المرجعي</label>
                <input type="text" name="reference" value="{{ request('reference') }}" placeholder="BK-..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">العميل (اسم أو جوال)</label>
                <input type="text" name="customer" value="{{ request('customer') }}" placeholder="ابحث بالاسم أو الهاتف..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">رقم الشقة</label>
                <input type="text" name="apartment_number" value="{{ request('apartment_number') }}" placeholder="504"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition">بحث</button>
                @if(request()->hasAny(['reference', 'customer', 'apartment_number']))
                    <a href="{{ route('admin.bookings.index') }}" class="p-2 bg-slate-100 text-slate-600 rounded-xl"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">رقم الحجز</th>
                        <th class="p-3.5">العميل</th>
                        <th class="p-3.5">الشقة والمستثمر</th>
                        <th class="p-3.5">التواريخ والليالي</th>
                        <th class="p-3.5">المبلغ</th>
                        <th class="p-3.5">الإيصال</th>
                        <th class="p-3.5">الحالة</th>
                        <th class="p-3.5 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $b)
                        @php $latestReceipt = $b->receipts->last(); @endphp
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5 font-mono font-bold text-amber-700">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="hover:underline">
                                    {{ $b->reference_number }}
                                </a>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">{{ $b->customer_name }}</div>
                                <div class="text-[10px] text-slate-500" dir="ltr">{{ $b->customer_phone }}</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">شقة {{ $b->apartment->apartment_number }} (الدور {{ $b->apartment->floor_number }})</div>
                                <div class="text-[10px] text-slate-400">مالك الشقة: {{ $b->apartment->investor->full_name }}</div>
                            </td>
                            <td class="p-3.5">
                                <div>{{ $b->checkin_date->format('Y-m-d') }} &rarr; {{ $b->checkout_date->format('Y-m-d') }}</div>
                                <div class="text-[10px] text-slate-500">{{ $b->nights_count }} ليالي</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-black text-emerald-700 text-sm">{{ number_format($b->total_amount, 2) }} ر.س</div>
                                <div class="text-[10px] text-slate-400">تأمين: {{ number_format($b->insurance_amount, 2) }}</div>
                            </td>
                            <td class="p-3.5">
                                @if($latestReceipt)
                                    <a href="{{ asset('storage/' . $latestReceipt->file_path) }}" target="_blank" class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-receipt"></i>
                                        <span>معاينة</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">لم يُرفع</span>
                                @endif
                            </td>
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
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-bold rounded-lg transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>مراجعة واعتماد</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">لا توجد حجوزات مسجلة بهذا المعيار.</td>
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

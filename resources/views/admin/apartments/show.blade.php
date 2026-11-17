@extends('layouts.admin')

@section('title', 'تفاصيل شقة ' . $apartment->apartment_number)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">شقة {{ $apartment->apartment_number }} - الدور {{ $apartment->floor_number }}</h1>
            <p class="text-xs text-slate-500 mt-1">المستثمر: {{ $apartment->investor->full_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.apartments.edit', $apartment->id) }}" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl text-xs">
                تعديل الشقة
            </a>
            <a href="{{ route('admin.apartments.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                &rarr; العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Media Section -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-900 mb-4">الوسائط المرفوعة (صور وفيديوهات)</h3>

        @if($apartment->media->isEmpty())
            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-200">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/apartment-default.svg') }}" class="w-20 h-14 object-cover rounded-lg border border-slate-300">
                    <div>
                        <p class="text-xs font-bold text-slate-800">تستخدم الشقة حالياً التصميم الفندقي الافتراضي لبرج دَارِك</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">تظهر الصورة الفندقية الفاخرة للنزلاء تلقائياً، ويمكن رفع صور وفيديوهات مخصصة</p>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($apartment->media as $med)
                    <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-900">
                        @if($med->type === 'video')
                            <video class="w-full h-36 object-cover" controls>
                                <source src="{{ asset('storage/' . $med->file_path) }}">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $med->file_path) }}" class="w-full h-36 object-cover" onerror="this.onerror=null; this.src='{{ asset('images/apartment-default.svg') }}';">
                        @endif

                        <form action="{{ route('admin.apartments.delete-media', [$apartment->id, $med->id]) }}" method="POST" onsubmit="return confirm('حذف هذا الملف نهائياً؟');" class="absolute top-2 left-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 bg-rose-600/90 text-white rounded-lg text-xs hover:bg-rose-700 transition" title="حذف">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bookings on this apartment -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="text-sm font-bold text-slate-900 mb-4">آخر الحجوزات على هذه الشقة</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-3">رقم الحجز</th>
                        <th class="p-3">العميل</th>
                        <th class="p-3">تاريخ الدخول</th>
                        <th class="p-3">تاريخ الخروج</th>
                        <th class="p-3">المبلغ</th>
                        <th class="p-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($apartment->bookings as $b)
                        <tr>
                            <td class="p-3 font-mono font-bold text-amber-700">{{ $b->reference_number }}</td>
                            <td class="p-3">{{ $b->customer_name }}</td>
                            <td class="p-3">{{ $b->checkin_date->format('Y-m-d') }}</td>
                            <td class="p-3">{{ $b->checkout_date->format('Y-m-d') }}</td>
                            <td class="p-3 font-bold text-emerald-700">{{ number_format($b->total_amount, 2) }} ر.س</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold 
                                    @if($b->status === 'confirmed') bg-emerald-100 text-emerald-800
                                    @elseif($b->status === 'cancelled') bg-rose-100 text-rose-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-4 text-center text-slate-400">لا توجد حجوزات مسجلة على هذه الشقة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

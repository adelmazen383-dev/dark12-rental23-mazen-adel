@extends('layouts.investor')

@section('title', 'شقق البرج الخاصة بي')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">شقق البرج المسجلة بحسابي</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة بيانات شققك، تحديث الأسعار، ورفع الفيديوهات والصور</p>
        </div>
        @if($investor->status === 'approved')
            <a href="{{ route('investor.apartments.create') }}" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة شقة جديدة</span>
            </a>
        @endif
    </div>

    @if($apartments->isEmpty())
        <div class="bg-white rounded-2xl p-10 text-center border border-slate-200">
            <i class="fa-solid fa-door-open text-3xl text-slate-400 mb-2"></i>
            <h3 class="font-bold text-sm text-slate-800">لا توجد شقق مسجلة بعد</h3>
            <p class="text-xs text-slate-400 mt-1">عند اعتماد حسابك، يمكنك إضافة شققك وعرضها لنزلاء البرج.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($apartments as $apt)
                @php $cover = $apt->images->first(); @endphp
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div class="relative h-44 bg-slate-900">
                        @if($cover)
                            <img src="{{ asset('storage/' . $cover->file_path) }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('images/apartment-default.svg') }}';">
                        @else
                            <img src="{{ asset('images/apartment-default.svg') }}" class="w-full h-full object-cover">
                        @endif

                        <div class="absolute top-3 right-3 flex gap-1.5">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-slate-900/85 text-amber-400">
                                شقة {{ $apt->apartment_number }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white/90 text-slate-800">
                                الدور {{ $apt->floor_number }}
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $apt->is_active ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                {{ $apt->is_active ? 'ظاهرة للنزلاء' : 'معطلة من الإدارة' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-slate-500">{{ $apt->rooms_count }} غرف • {{ $apt->bathrooms_count }} حمام</span>
                                <span class="font-black text-emerald-700 text-base">{{ number_format($apt->daily_price, 0) }} ر.س / ليلة</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mb-4">تأمين مسترد: {{ number_format($apt->insurance_amount, 0) }} ر.س</div>
                        </div>

                        <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                            <a href="{{ route('investor.apartments.show', $apt->id) }}" class="flex-1 py-2 bg-slate-900 hover:bg-emerald-600 hover:text-white text-white font-bold rounded-xl text-xs transition text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-photo-film"></i>
                                <span>الوسائط والتفاصيل</span>
                            </a>
                            <a href="{{ route('investor.apartments.edit', $apt->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl" title="تعديل الأسعار">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                        <a href="{{ route('investor.apartments.show', $apt->id) }}" class="w-full py-1.5 mt-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition border border-rose-200 text-center flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-calendar-xmark text-xs"></i>
                            <span>تحديد التواريخ المحجوزة / المغلقة</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $apartments->links() }}
        </div>
    @endif

</div>
@endsection

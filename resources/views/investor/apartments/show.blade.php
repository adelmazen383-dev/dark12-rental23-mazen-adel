@extends('layouts.investor')

@section('title', 'شقة ' . $apartment->apartment_number)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">شقة {{ $apartment->apartment_number }} (الدور {{ $apartment->floor_number }})</h1>
            <p class="text-xs text-slate-500 mt-1">سعر الليلة: {{ number_format($apartment->daily_price, 2) }} ريال | التأمين: {{ number_format($apartment->insurance_amount, 2) }} ريال</p>
        </div>
        <a href="{{ route('investor.apartments.index') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-600">
            &rarr; العودة للقائمة
        </a>
    </div>

    <!-- Upload Media Form -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i>
            <span>إضافة وتحديث وسائط الشقة (صور وفيديو)</span>
        </h3>

        <form action="{{ route('investor.apartments.upload-media', $apartment->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">صور جديدة للشقة (متعدد)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">فيديو جولة الشقة (MP4)</label>
                <input type="file" name="video" accept="video/*" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>

            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                    رفع الوسائط
                </button>
            </div>
        </form>
    </div>

    <!-- Blocked Dates Management (إدارة فترات الإشغال ومنع الحجوزات) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-xmark text-rose-500"></i>
                    <span>إدارة التواريخ المغلقة وفترات الإشغال الخارجي</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    حدد التواريخ التي قمت بتأجير الشقة فيها أو تريد إغلاقها؛ سيقوم النظام بمنع أي نزيل من حجز هذه التواريخ تلقائياً.
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 self-start sm:self-auto">
                {{ $apartment->blockedDates->count() }} فترات مغلقة حالياً
            </span>
        </div>

        <!-- Add New Blocked Period Form -->
        <form action="{{ route('investor.apartments.blocked-dates.store', $apartment->id) }}" method="POST" class="bg-slate-50 p-4 sm:p-5 rounded-xl border border-slate-200 space-y-4">
            @csrf
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-emerald-600"></i>
                <span>إضافة فترة عدم توفر جديدة:</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">من تاريخ (بداية الإشغال) *</label>
                    <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">إلى تاريخ (نهاية الإشغال) *</label>
                    <input type="date" name="end_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+3 days')) }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">سبب عدم التوفر *</label>
                    <select name="reason" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="مؤجرة خارجياً">مؤجرة خارجياً (عقد خاص)</option>
                        <option value="محجوزة مسبقاً">محجوزة مسبقاً لعميل</option>
                        <option value="صيانة وتجهيز">أعمال صيانة وتجهيز</option>
                        <option value="استخدام شخصي للمالك">استخدام خاص للمالك</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملاحظات داخلية (اختياري)</label>
                    <input type="text" name="notes" placeholder="ملاحظة..."
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm shadow-rose-600/20">
                    <i class="fa-solid fa-lock"></i>
                    <span>تأكيد إغلاق التواريخ ومنع الحجوزات فيها</span>
                </button>
            </div>
        </form>

        <!-- Existing Blocked Periods Table / List -->
        <div class="space-y-2">
            <h4 class="text-xs font-bold text-slate-700">الفترات والتواريخ المحجوزة والمغلقة حالياً:</h4>

            @if($apartment->blockedDates->isEmpty())
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base mb-1 block"></i>
                    لا توجد فترات مغلقة يدوياً لهذه الشقة. الشقة متاحة لجميع الأيام ما لم يكن هناك حجز مؤكد.
                </div>
            @else
                <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white">
                    @foreach($apartment->blockedDates as $blocked)
                        <div class="p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    <i class="fa-solid fa-ban"></i>
                                </div>
                                <div class="text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 font-mono">
                                            من {{ $blocked->start_date->format('Y-m-d') }} إلى {{ $blocked->end_date->format('Y-m-d') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ $blocked->reason }}
                                        </span>
                                    </div>
                                    @if($blocked->notes)
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $blocked->notes }}</p>
                                    @endif
                                </div>
                            </div>

                            <form action="{{ route('investor.apartments.blocked-dates.destroy', [$apartment->id, $blocked->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من فتح هذه التواريخ وإتاحتها للحجز من جديد؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-lock-open text-xs"></i>
                                    <span>إلغاء المنع وفتح التواريخ</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Media Gallery -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-900 mb-4">الوسائط الحالية للشقة</h3>

        @if($apartment->media->isEmpty())
            <p class="text-xs text-slate-400">لا توجد صور أو فيديوهات مرفوعة لهذه الشقة.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($apartment->media as $med)
                    <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-900">
                        @if($med->type === 'video')
                            <video class="w-full h-36 object-cover" controls>
                                <source src="{{ asset('storage/' . $med->file_path) }}">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $med->file_path) }}" class="w-full h-36 object-cover">
                        @endif

                        <form action="{{ route('investor.apartments.delete-media', [$apartment->id, $med->id]) }}" method="POST" onsubmit="return confirm('حذف هذا الملف؟');" class="absolute top-2 left-2">
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

</div>
@endsection

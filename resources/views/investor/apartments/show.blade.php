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

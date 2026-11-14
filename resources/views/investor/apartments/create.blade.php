@extends('layouts.investor')

@section('title', 'إضافة شقة جديدة')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إضافة شقة جديدة للبرج</h1>
            <p class="text-xs text-slate-500 mt-1">أدخل بيانات ومواصفات الشقة ليتم عرضها لنزلاء البرج</p>
        </div>
        <a href="{{ route('investor.apartments.index') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-600">
            &rarr; العودة
        </a>
    </div>

    <form action="{{ route('investor.apartments.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الشقة *</label>
                <input type="text" name="apartment_number" value="{{ old('apartment_number') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500" placeholder="602">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الدور *</label>
                <input type="number" name="floor_number" value="{{ old('floor_number') }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500" placeholder="6">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">نوع الإطلالة</label>
                <input type="text" name="view_type" value="{{ old('view_type') }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500" placeholder="بحرية / مدينة">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد الغرف *</label>
                <input type="number" name="rooms_count" value="{{ old('rooms_count', 1) }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد الصالات *</label>
                <input type="number" name="living_rooms_count" value="{{ old('living_rooms_count', 1) }}" required min="0"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد دورات المياه *</label>
                <input type="number" name="bathrooms_count" value="{{ old('bathrooms_count', 1) }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">سعر الليلة (ريال) *</label>
                <input type="number" name="daily_price" value="{{ old('daily_price') }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">مبلغ التأمين المسترد (ريال) *</label>
                <input type="number" name="insurance_amount" value="{{ old('insurance_amount', 300) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رسوم إضافية</label>
                <input type="number" name="extra_fees" value="{{ old('extra_fees', 0) }}" min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت الدخول *</label>
                <input type="time" name="checkin_time" value="{{ old('checkin_time', '14:00') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت الخروج *</label>
                <input type="time" name="checkout_time" value="{{ old('checkout_time', '12:00') }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المميزات والتجهيزات</label>
            <input type="text" name="features" value="{{ old('features') }}"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500" placeholder="مطبخ متكامل، واي فاي، دخول ذكي">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الوصف التفصيلي</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">صور الشقة (متعدد)</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">فيديو كامل للشقة (MP4)</label>
                <input type="file" name="video" accept="video/*"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                إضافة الشقة
            </button>
        </div>
    </form>
</div>
@endsection

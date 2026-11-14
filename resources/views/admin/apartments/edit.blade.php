@extends('layouts.admin')

@section('title', 'تعديل شقة ' . $apartment->apartment_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">تعديل شقة {{ $apartment->apartment_number }}</h1>
            <p class="text-xs text-slate-500 mt-1">تحديث بيانات الشقة، الأسعار، أو إضافة صور وفيديوهات جديدة</p>
        </div>
        <a href="{{ route('admin.apartments.index') }}" class="text-xs font-bold text-slate-600 hover:text-amber-600">
            &rarr; العودة للشقق
        </a>
    </div>

    <form action="{{ route('admin.apartments.update', $apartment->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <!-- Investor selection -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المستثمر المالك للشقة *</label>
            <select name="investor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                @foreach($investors as $inv)
                    <option value="{{ $inv->id }}" {{ old('investor_id', $apartment->investor_id) == $inv->id ? 'selected' : '' }}>
                        {{ $inv->full_name }} (هوية: {{ $inv->id_number }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الشقة *</label>
                <input type="text" name="apartment_number" value="{{ old('apartment_number', $apartment->apartment_number) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الدور *</label>
                <input type="number" name="floor_number" value="{{ old('floor_number', $apartment->floor_number) }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">نوع الإطلالة</label>
                <input type="text" name="view_type" value="{{ old('view_type', $apartment->view_type) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد الغرف *</label>
                <input type="number" name="rooms_count" value="{{ old('rooms_count', $apartment->rooms_count) }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد الصالات *</label>
                <input type="number" name="living_rooms_count" value="{{ old('living_rooms_count', $apartment->living_rooms_count) }}" required min="0"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عدد دورات المياه *</label>
                <input type="number" name="bathrooms_count" value="{{ old('bathrooms_count', $apartment->bathrooms_count) }}" required min="1"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">سعر الليلة (ريال) *</label>
                <input type="number" name="daily_price" value="{{ old('daily_price', $apartment->daily_price) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">مبلغ التأمين المسترد (ريال) *</label>
                <input type="number" name="insurance_amount" value="{{ old('insurance_amount', $apartment->insurance_amount) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رسوم إضافية</label>
                <input type="number" name="extra_fees" value="{{ old('extra_fees', $apartment->extra_fees) }}" min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت تسجيل الدخول *</label>
                <input type="time" name="checkin_time" value="{{ old('checkin_time', $apartment->checkin_time) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت تسجيل الخروج *</label>
                <input type="time" name="checkout_time" value="{{ old('checkout_time', $apartment->checkout_time) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المميزات والتجهيزات</label>
            <input type="text" name="features" value="{{ old('features', $apartment->features) }}"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">{{ old('description', $apartment->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">إضافة صور جديدة</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">تحديث فيديو الشقة</label>
                <input type="file" name="video" accept="video/*"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-slate-100 text-xs">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_active" value="1" {{ $apartment->is_active ? 'checked' : '' }} class="rounded text-amber-500">
                <span>تفعيل ونشر الشقة في الباركود</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                <input type="checkbox" name="is_available" value="1" {{ $apartment->is_available ? 'checked' : '' }} class="rounded text-amber-500">
                <span>متاحة للحجز حالياً</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-bold text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-200">
                <input type="checkbox" name="admin_approved_override" value="1" {{ $apartment->admin_approved_override ? 'checked' : '' }} class="rounded text-indigo-600">
                <span>موافقة الإدارة الاستثنائية للظهور (حتى لو أوقف المستثمر أو انتهى اشتراكه)</span>
            </label>
        </div>

        <!-- Secret Admin-Only Homepage Feature Section -->
        <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 text-xs space-y-3">
            <div class="flex items-center gap-2 text-amber-900 font-bold">
                <i class="fa-solid fa-user-shield text-amber-600"></i>
                <span>خاص بالإدارة وسري (لا يظهر لصاحب الشقة نهائياً)</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="is_featured_on_homepage" value="1" {{ old('is_featured_on_homepage', $apartment->is_featured_on_homepage) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                    <span>تثبيت وعرض الشقة في الصفحة الأولى للنزلاء</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-slate-600">ترتيب الظهور:</span>
                    <input type="number" name="homepage_order" value="{{ old('homepage_order', $apartment->homepage_order) }}" class="w-16 px-2 py-1 rounded-lg border border-amber-300 text-center font-bold text-xs">
                </div>
            </div>
            <p class="text-[11px] text-amber-800/80">
                عند تفعيل هذا الخيار، ستظهر هذه الشقة في مقدمة الصفحة الأولى عند مسح الباركود، مع بقاء هذه الميزة مخفية بالكامل عن المستثمر في لوحته.
            </p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition">
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection

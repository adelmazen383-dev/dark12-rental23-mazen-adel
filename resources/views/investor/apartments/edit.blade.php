@extends('layouts.investor')

@section('title', 'تعديل شقة ' . $apartment->apartment_number)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">تعديل شقة {{ $apartment->apartment_number }}</h1>
            <p class="text-xs text-slate-500 mt-1">تحديث الأسعار والمواصفات</p>
        </div>
        <a href="{{ route('investor.apartments.index') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-600">
            &rarr; العودة
        </a>
    </div>

    <form action="{{ route('investor.apartments.update', $apartment->id) }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">سعر الليلة (ريال) *</label>
                <input type="number" name="daily_price" value="{{ old('daily_price', $apartment->daily_price) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">مبلغ التأمين المسترد (ريال) *</label>
                <input type="number" name="insurance_amount" value="{{ old('insurance_amount', $apartment->insurance_amount) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">رسوم إضافية</label>
                <input type="number" name="extra_fees" value="{{ old('extra_fees', $apartment->extra_fees) }}" min="0" step="0.01"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">نوع الإطلالة</label>
                <input type="text" name="view_type" value="{{ old('view_type', $apartment->view_type) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت الدخول *</label>
                <input type="time" name="checkin_time" value="{{ old('checkin_time', $apartment->checkin_time) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وقت الخروج *</label>
                <input type="time" name="checkout_time" value="{{ old('checkout_time', $apartment->checkout_time) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المميزات والتجهيزات</label>
            <input type="text" name="features" value="{{ old('features', $apartment->features) }}"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الوصف التفصيلي</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">{{ old('description', $apartment->description) }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer font-bold text-xs text-slate-800">
                <input type="checkbox" name="is_available" value="1" {{ $apartment->is_available ? 'checked' : '' }} class="rounded text-emerald-500">
                <span>متاحة للحجز الآن</span>
            </label>

            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection

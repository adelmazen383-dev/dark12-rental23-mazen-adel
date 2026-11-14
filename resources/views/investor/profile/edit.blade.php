@extends('layouts.investor')

@section('title', 'الملف الشخصي والبنكي')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    <div>
        <h1 class="text-2xl font-black text-slate-900">الملف الشخصي والحساب البنكي</h1>
        <p class="text-xs text-slate-500 mt-1">تحديث معلومات الاتصال، رقم واتساب للإشعارات، وبيانات الحساب البنكي لتحويل الإيجارات</p>
    </div>

    <!-- Official Documents (Read-only for security) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        <h3 class="font-bold text-sm text-slate-900 border-b pb-3 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-500"></i>
            <span>الوثائق الرسمية المعتمدة (لا يمكن تعديلها إلا عبر الإدارة)</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px]">الاسم الكامل المعتمد</span>
                <span class="font-bold text-slate-800 text-sm">{{ $investor->full_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">رقم الهوية الوطنية / الإقامة</span>
                <span class="font-mono font-bold text-slate-800 text-sm">{{ $investor->id_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">رقم الترخيص السياحي</span>
                <span class="font-mono font-bold text-slate-800">{{ $investor->license_number }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">اسم المنشأة بالترخيص</span>
                <span class="font-bold text-slate-800">{{ $investor->license_name }}</span>
            </div>
        </div>
    </div>

    <!-- Editable Contact & Payout Details Form -->
    <form action="{{ route('investor.profile.update') }}" method="POST" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <h3 class="font-bold text-sm text-slate-900 border-b pb-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-phone text-emerald-500"></i>
                <span>بيانات الاتصال وإشعارات واتساب</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الجوال *</label>
                    <input type="text" name="phone" value="{{ old('phone', $investor->phone) }}" required dir="ltr"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 text-left">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم واتساب المعتمد للإشعارات *</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $investor->whatsapp) }}" required dir="ltr"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 text-left">
                    <p class="text-[10px] text-slate-400 mt-1">تصلك عليه إشعارات فورية عند حجز أي شقة أو رفع إيصال أو طلب معاينة.</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-bold text-sm text-slate-900 border-b pb-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-emerald-500"></i>
                <span>بيانات الحساب البنكي لاستلام الإيجارات</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم البنك *</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $investor->bank_name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم صاحب الحساب *</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $investor->bank_account_name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الآيبان IBAN *</label>
                    <input type="text" name="iban" value="{{ old('iban', $investor->iban) }}" required dir="ltr"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 text-left">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition">
                حفظ التعديلات
            </button>
        </div>
    </form>

</div>
@endsection

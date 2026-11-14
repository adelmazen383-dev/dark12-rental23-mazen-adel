@extends('layouts.investor')

@section('title', 'طلب إصدار وتوثيق عقد إيجار')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">طلب إصدار عقد إيجار موحد</h1>
            <p class="text-xs text-slate-500 mt-1">توثيق عقد إيجار إلكتروني رسمي ومحمٍ من إدارة البرج</p>
        </div>
        <a href="{{ route('investor.contracts.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>

    <!-- Wallet & Fee Warning -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <p class="font-bold text-amber-950">رسوم خدمة إصدار وتوثيق العقد: <span class="font-black">{{ number_format($serviceFee, 2) }} ر.س</span></p>
                <p class="text-amber-800 text-[11px] mt-0.5">رصيد محفظتك الحالي: <strong>{{ number_format($wallet?->balance ?? 0, 2) }} ر.س</strong></p>
            </div>
        </div>
        @if(($wallet?->balance ?? 0) < $serviceFee)
            <a href="{{ route('investor.wallet.index') }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition whitespace-nowrap flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-paper-plane"></i>
                <span>إرسال طلب شحن</span>
            </a>
        @endif
    </div>

    <form action="{{ route('investor.contracts.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Unit Selection -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-building text-emerald-500"></i>
                <span>بيانات الوحدة السكنية بالبرج</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اختر من شققك المسجلة</label>
                    <select name="apartment_id" id="apartment_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                        <option value="">-- إدخال يدوي --</option>
                        @foreach($apartments as $apt)
                            <option value="{{ $apt->id }}" data-floor="{{ $apt->floor_number }}" data-unit="{{ $apt->apartment_number }}" {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                                شقة {{ $apt->apartment_number }} (الدور {{ $apt->floor_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الشقة <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit_number" id="unit_number" value="{{ old('unit_number') }}" required placeholder="مثال: 204" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الدور <span class="text-rose-500">*</span></label>
                    <input type="number" name="floor_number" id="floor_number" value="{{ old('floor_number') }}" required placeholder="مثال: 2" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>
            </div>
        </div>

        <!-- First Party (Lessor / Investor) - Strictly Locked -->
        <div class="p-5 rounded-2xl bg-slate-50 border-2 border-emerald-300/80 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <h2 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-emerald-600 text-base"></i>
                    <span>الطرف الأول (المؤجر / المستثمر):</span>
                </h2>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                    <i class="fa-solid fa-lock text-[9px]"></i>
                    <span>بيانات موثقة وثابتة رسمياً</span>
                </span>
            </div>

            <div class="bg-amber-50/70 p-2.5 rounded-xl border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 flex-shrink-0"></i>
                <p>
                    <strong>تنبيه قانوني ملزم:</strong> اسمك ورقمك الوطني يتم تضمينهما وتثبيتهما تلقائياً في جميع العقود الصادرة من حسابك، <strong>ولا يمكن تعديلهما أو حذفهما تحت أي ظرف</strong> لضمان حجية وتوثيق العقد رسمياً وحفظ حقوق الطرفين.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>اسم المؤجر (الطرف الأول):</span>
                        <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" value="{{ $investor->full_name }}" readonly disabled
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-slate-200/70 text-slate-800 font-bold text-xs cursor-not-allowed select-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>رقم الهوية الوطنية / الإقامة / السجل:</span>
                        <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" value="{{ $investor->id_number }}" readonly disabled
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-slate-200/70 text-slate-900 font-mono font-bold text-xs cursor-not-allowed select-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>رقم الجوال المعتمد:</span>
                        <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i>
                    </label>
                    <input type="text" value="{{ $investor->phone }}" readonly disabled dir="ltr"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-slate-200/70 text-slate-700 font-mono text-xs cursor-not-allowed select-none">
                </div>
            </div>
        </div>

        <!-- Tenant Info -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user text-emerald-500"></i>
                <span>بيانات المستأجر</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اسم المستأجر الكامل <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required placeholder="الاسم الرباعي" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">الهوية الوطنية / الإقامة <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_national_id" value="{{ old('tenant_national_id') }}" required placeholder="10 أرقام" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم جوال المستأجر <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_phone" value="{{ old('tenant_phone') }}" required placeholder="05XXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs font-mono">
                </div>
            </div>
        </div>

        <!-- Duration & Rent -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-calendar-days text-emerald-500"></i>
                <span>مدة الإيجار والقيمة المالية</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">تاريخ بداية العقد <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">تاريخ نهاية العقد <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">دورية السداد <span class="text-rose-500">*</span></label>
                    <select name="payment_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                        <option value="monthly" {{ old('payment_type') === 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="daily" {{ old('payment_type') === 'daily' ? 'selected' : '' }}>يومي</option>
                        <option value="quarterly" {{ old('payment_type') === 'quarterly' ? 'selected' : '' }}>ربع سنوي (3 أشهر)</option>
                        <option value="semi_annual" {{ old('payment_type') === 'semi_annual' ? 'selected' : '' }}>نصف سنوي (6 أشهر)</option>
                        <option value="annual" {{ old('payment_type') === 'annual' ? 'selected' : '' }}>سنوي</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">مبلغ الإيجار الدوري (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="rent_amount" value="{{ old('rent_amount') }}" required placeholder="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">مبلغ التأمين المسترد (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="insurance_amount" value="{{ old('insurance_amount', 0) }}" required placeholder="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1 text-xs">شروط أو التزامات إضافية بالعقد</label>
            <textarea name="special_terms" rows="3" placeholder="أي بنود إضافية خاصة بالشقة أو شروط الاستخدام..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">{{ old('special_terms') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('investor.contracts.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                إلغاء
            </a>
            <button type="submit" @if(($wallet?->balance ?? 0) < $serviceFee) disabled @endif class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-slate-950 font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-contract"></i>
                <span>تأكيد الطلب وخصم الرسوم ({{ number_format($serviceFee, 2) }} ر.س)</span>
            </button>
        </div>
    </form>

</div>

<script>
    document.getElementById('apartment_id')?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('unit_number').value = opt.dataset.unit || '';
            document.getElementById('floor_number').value = opt.dataset.floor || '';
        }
    });
</script>
@endsection

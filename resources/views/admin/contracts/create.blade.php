@extends('layouts.admin')

@section('title', 'إصدار عقد إيجار إلكتروني')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إصدار وتوثيق عقد إيجار جديد</h1>
            <p class="text-xs text-slate-500 mt-1">إنشاء عقد إيجار رسمي موحد مع تحديد رسوم الخدمة للإدارة</p>
        </div>
        <a href="{{ route('admin.contracts.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>

    <form action="{{ route('admin.contracts.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Section 1: Investor & Unit Information -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-building text-amber-500"></i>
                <span>بيانات المؤجر (المستثمر) والوحدة</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">المستثمر / المؤجر <span class="text-rose-500">*</span></label>
                    <select name="investor_id" id="investor_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="">-- اختر المستثمر --</option>
                        @foreach($investors as $inv)
                            <option value="{{ $inv->id }}" {{ old('investor_id') == $inv->id ? 'selected' : '' }}>
                                {{ $inv->full_name }} (رصيد المحفظة: {{ number_format($inv->wallet?->balance ?? 0, 2) }} ر.س)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">ربط بشقة في النظام (اختياري)</label>
                    <select name="apartment_id" id="apartment_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="">-- غير مرتبطة بشقة محددة --</option>
                        @foreach($investors as $inv)
                            @foreach($inv->apartments as $apt)
                                <option value="{{ $apt->id }}" data-investor="{{ $inv->id }}" data-floor="{{ $apt->floor_number }}" data-unit="{{ $apt->apartment_number }}" {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                                    شقة {{ $apt->apartment_number }} - الدور {{ $apt->floor_number }} ({{ $inv->full_name }})
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الشقة / الجناح <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit_number" id="unit_number" value="{{ old('unit_number') }}" required placeholder="مثال: 104" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الدور <span class="text-rose-500">*</span></label>
                    <input type="number" name="floor_number" id="floor_number" value="{{ old('floor_number') }}" required placeholder="مثال: 3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>
            </div>
        </div>

        <!-- Section 2: Tenant Information -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user-check text-amber-500"></i>
                <span>بيانات المستأجر (الطرف الثاني)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اسم المستأجر الكامل <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" required placeholder="الاسم الرباعي" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الهوية الوطنية / الإقامة <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_national_id" value="{{ old('tenant_national_id') }}" required placeholder="10 أرقام" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم الجوال <span class="text-rose-500">*</span></label>
                    <input type="text" name="tenant_phone" value="{{ old('tenant_phone') }}" required placeholder="05XXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs font-mono">
                </div>
            </div>
        </div>

        <!-- Section 3: Financial & Duration Details -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-money-bill-wave text-amber-500"></i>
                <span>بيانات المدة والمالية</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">تاريخ بداية العقد <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">تاريخ نهاية العقد <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">قيمة الإيجار (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="rent_amount" value="{{ old('rent_amount') }}" required placeholder="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">مبلغ التأمين المسترد (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="insurance_amount" value="{{ old('insurance_amount', 0) }}" required placeholder="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">دورية الدفع <span class="text-rose-500">*</span></label>
                    <select name="payment_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="monthly" {{ old('payment_type') === 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="daily" {{ old('payment_type') === 'daily' ? 'selected' : '' }}>يومي</option>
                        <option value="quarterly" {{ old('payment_type') === 'quarterly' ? 'selected' : '' }}>ربع سنوي (كل 3 أشهر)</option>
                        <option value="semi_annual" {{ old('payment_type') === 'semi_annual' ? 'selected' : '' }}>نصف سنوي (كل 6 أشهر)</option>
                        <option value="annual" {{ old('payment_type') === 'annual' ? 'selected' : '' }}>سنوي دفعة واحدة</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">رسوم إصدار العقد المعتمدة للمنصة (ر.س) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="service_fee" value="{{ old('service_fee', $serviceFee) }}" required class="w-full px-3 py-2.5 rounded-xl border border-amber-300 bg-amber-50 focus:ring-2 focus:ring-amber-500 text-xs font-bold text-amber-900">
                    <span class="text-[10px] text-slate-400 mt-1 block">محددة مسبقاً في إعدادات المنصة، يمكن تعديلها لهذا العقد إن لزم الأمر</span>
                </div>
            </div>
        </div>

        <!-- Section 4: Special Terms & Deductions -->
        <div class="space-y-4">
            <div>
                <label class="block font-semibold text-slate-700 mb-1 text-xs">شروط وأحكام خاصة إضافية بالعقد</label>
                <textarea name="special_terms" rows="3" placeholder="أي بنود إضافية يتفق عليها المؤجر والمستأجر..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">{{ old('special_terms') }}</textarea>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="deduct_from_wallet" value="1" {{ old('deduct_from_wallet', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-800">خصم رسوم الخدمة تلقائياً من محفظة المستثمر المحددة</span>
                        <p class="text-[10px] text-slate-500 mt-0.5">سيتم قيد العملية في كشف حساب محفظة المستثمر كرسم توثيق وإصدار عقد</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.contracts.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>توثيق وإصدار العقد الآن</span>
            </button>
        </div>
    </form>

</div>

<script>
    // Auto populate unit and floor when apartment is selected
    document.getElementById('apartment_id')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            document.getElementById('unit_number').value = selected.dataset.unit || '';
            document.getElementById('floor_number').value = selected.dataset.floor || '';
            if (selected.dataset.investor) {
                document.getElementById('investor_id').value = selected.dataset.investor;
            }
        }
    });
</script>
@endsection

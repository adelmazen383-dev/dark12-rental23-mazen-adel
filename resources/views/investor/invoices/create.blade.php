@extends('layouts.investor')

@section('title', 'إصدار فاتورة رسمية')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إصدار فاتورة إلكترونية جديدة</h1>
            <p class="text-xs text-slate-500 mt-1">إنشاء فاتورة رسمية معتمدة للنزيل أو العميل</p>
        </div>
        <a href="{{ route('investor.invoices.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>

    <!-- Wallet fee notice -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <p class="font-bold text-amber-950">رسوم إصدار الفاتورة المعتمدة: <span class="font-black">{{ number_format($serviceFee, 2) }} ر.س</span></p>
                <p class="text-amber-800 text-[11px] mt-0.5">رصيد محفظتك الحالي: <strong>{{ number_format($wallet?->balance ?? 0, 2) }} ر.س</strong></p>
            </div>
        </div>
        @if(($wallet?->balance ?? 0) < $serviceFee)
            <a href="{{ route('investor.wallet.index') }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition whitespace-nowrap">
                شحن المحفظة
            </a>
        @endif
    </div>

    <form action="{{ route('investor.invoices.store') }}" method="POST" id="investorInvoiceForm" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Client & Contract -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user-tag text-emerald-500"></i>
                <span>بيانات العميل أو النزيل</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">ربط بعقد إيجار معتمد (اختياري)</label>
                    <select name="rental_contract_id" id="rental_contract_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                        <option value="">-- فاتورة مستقلة --</option>
                        @foreach($contracts as $cnt)
                            <option value="{{ $cnt->id }}" data-tenant="{{ $cnt->tenant_name }}" data-phone="{{ $cnt->tenant_phone }}" {{ old('rental_contract_id') == $cnt->id ? 'selected' : '' }}>
                                عقد {{ $cnt->contract_number }} - {{ $cnt->tenant_name }} (شقة {{ $cnt->unit_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اسم العميل / المستأجر <span class="text-rose-500">*</span></label>
                    <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}" required placeholder="اسم النزيل" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم جوال العميل</label>
                    <input type="text" name="client_phone" id="client_phone" value="{{ old('client_phone') }}" placeholder="05XXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">الرقم الضريبي للعميل (إن وجد)</label>
                    <input type="text" name="client_tax_number" value="{{ old('client_tax_number') }}" placeholder="3XXXXXXXXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">تاريخ الإصدار <span class="text-rose-500">*</span></label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div>
            <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-emerald-500"></i>
                    <span>بنود الفاتورة</span>
                </h2>
                <button type="button" id="addItemBtn" class="px-3 py-1.5 bg-emerald-100 text-emerald-800 hover:bg-emerald-200 font-bold rounded-lg text-xs transition flex items-center gap-1">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>إضافة بند آخر</span>
                </button>
            </div>

            <div class="space-y-3" id="itemsContainer">
                <div class="item-row grid grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="block font-semibold text-slate-600 mb-1">وصف الخدمة / البند</label>
                        <input type="text" name="items_desc[]" required placeholder="مثال: إيجار شقة فندقية أسبوعي" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <label class="block font-semibold text-slate-600 mb-1">الكمية</label>
                        <input type="number" step="1" min="1" name="items_qty[]" value="1" required class="item-qty w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-center">
                    </div>
                    <div class="col-span-5 sm:col-span-3">
                        <label class="block font-semibold text-slate-600 mb-1">سعر الوحدة (ر.س)</label>
                        <input type="number" step="0.01" min="0" name="items_price[]" value="0.00" required class="item-price w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold">
                    </div>
                    <div class="col-span-3 sm:col-span-1 flex items-end justify-center pt-5">
                        <button type="button" class="remove-item-btn p-2 text-rose-500 hover:text-rose-700 transition" title="حذف البند">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-400 block mb-1">إجمالي الفاتورة:</span>
                <span id="totalDisplay" class="text-2xl font-black text-emerald-400">0.00 ر.س</span>
            </div>
            <div class="text-left">
                <span class="text-slate-400 block mb-1">رسوم إصدار الفاتورة:</span>
                <span class="text-base font-bold text-amber-400">{{ number_format($serviceFee, 2) }} ر.س</span>
                <span class="text-[10px] text-slate-400 block">تخصم من المحفظة</span>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1 text-xs">ملاحظات بالفاتورة (اختياري)</label>
            <textarea name="notes" rows="2" placeholder="أي تفاصيل أو تعليمات تود إظهارها على الفاتورة..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-500 text-xs">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('investor.invoices.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                إلغاء
            </a>
            <button type="submit" @if(($wallet?->balance ?? 0) < $serviceFee) disabled @endif class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-slate-950 font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>إصدار الفاتورة وخصم الرسوم ({{ number_format($serviceFee, 2) }} ر.س)</span>
            </button>
        </div>
    </form>

</div>

<script>
    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            total += (qty * price);
        });
        document.getElementById('totalDisplay').innerText = total.toFixed(2) + ' ر.س';
    }

    document.getElementById('itemsContainer').addEventListener('input', calculateTotals);

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'item-row grid grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs';
        row.innerHTML = `
            <div class="col-span-12 sm:col-span-6">
                <input type="text" name="items_desc[]" required placeholder="وصف الخدمة / البند" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs">
            </div>
            <div class="col-span-4 sm:col-span-2">
                <input type="number" step="1" min="1" name="items_qty[]" value="1" required class="item-qty w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-center">
            </div>
            <div class="col-span-5 sm:col-span-3">
                <input type="number" step="0.01" min="0" name="items_price[]" value="0.00" required class="item-price w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold">
            </div>
            <div class="col-span-3 sm:col-span-1 flex items-end justify-center">
                <button type="button" class="remove-item-btn p-2 text-rose-500 hover:text-rose-700 transition" title="حذف البند">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });

    document.getElementById('itemsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            } else {
                alert('يجب أن تحتوي الفاتورة على بند واحد على الأقل');
            }
        }
    });

    document.getElementById('rental_contract_id')?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('client_name').value = opt.dataset.tenant || '';
            document.getElementById('client_phone').value = opt.dataset.phone || '';
        }
    });

    calculateTotals();
</script>
@endsection

@extends('layouts.admin')

@section('title', 'إصدار فاتورة إلكترونية')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إصدار فاتورة رسمية معتمدة</h1>
            <p class="text-xs text-slate-500 mt-1">إنشاء فاتورة ضريبية رسمية للعميل مع تحصيل رسوم إصدار الفاتورة للمنصة</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة للقائمة</span>
        </a>
    </div>

    <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
        @csrf

        <!-- Section 1: Parties Info -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user-tag text-amber-500"></i>
                <span>الأطراف والربط التعاقدي</span>
            </h2>

            @if(request('type') === 'wallet_recharge' && $targetInvestor)
                <div class="p-3 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-emerald-600 text-base"></i>
                    <span><strong>إصدار فاتورة شحن محفظة:</strong> للمستثمر <strong>{{ $targetInvestor->full_name }}</strong> (رقم الحساب: #INV-{{ $targetInvestor->id }}). تصدر الفاتورة لسدادها وإيداع الرصيد في محفظته.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">المستثمر (المؤجر) المصدر للفاتورة</label>
                    <select name="investor_id" id="investor_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="">-- إصدار باسم إدارة البرج مباشرة --</option>
                        @foreach($investors as $inv)
                            <option value="{{ $inv->id }}" {{ old('investor_id', $targetInvestor?->id) == $inv->id ? 'selected' : '' }}>
                                {{ $inv->full_name }} (رصيد: {{ number_format($inv->wallet?->balance ?? 0, 2) }} ر.س)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">ربط بعقد إيجار معتمد (اختياري)</label>
                    <select name="rental_contract_id" id="rental_contract_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                        <option value="">-- فاتورة مستقلة بدون عقد --</option>
                        @foreach($contracts as $cnt)
                            <option value="{{ $cnt->id }}" data-tenant="{{ $cnt->tenant_name }}" data-phone="{{ $cnt->tenant_phone }}" data-investor="{{ $cnt->investor_id }}" {{ old('rental_contract_id') == $cnt->id ? 'selected' : '' }}>
                                عقد {{ $cnt->contract_number }} - {{ $cnt->tenant_name }} (شقة {{ $cnt->unit_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اسم العميل / المستأجر <span class="text-rose-500">*</span></label>
                    <input type="text" name="client_name" id="client_name" value="{{ old('client_name', request('type') === 'wallet_recharge' && $targetInvestor ? $targetInvestor->full_name : '') }}" required placeholder="اسم النزيل أو الشركة" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم جوال العميل</label>
                    <input type="text" name="client_phone" id="client_phone" value="{{ old('client_phone', request('type') === 'wallet_recharge' && $targetInvestor ? $targetInvestor->phone : '') }}" placeholder="05XXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">الرقم الضريبي للعميل (إن وجد)</label>
                    <input type="text" name="client_tax_number" value="{{ old('client_tax_number') }}" placeholder="3XXXXXXXXXXXXXX" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">تاريخ الإصدار <span class="text-rose-500">*</span></label>
                        <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Line Items -->
        <div>
            <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-amber-500"></i>
                    <span>بنود الفاتورة والخدمات</span>
                </h2>
                <button type="button" id="addItemBtn" class="px-3 py-1.5 bg-amber-100 text-amber-800 hover:bg-amber-200 font-bold rounded-lg text-xs transition flex items-center gap-1">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>إضافة بند آخر</span>
                </button>
            </div>

            <div class="space-y-3" id="itemsContainer">
                <div class="item-row grid grid-cols-12 gap-2 items-center bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                    <div class="col-span-12 sm:col-span-6">
                        <label class="block font-semibold text-slate-600 mb-1">وصف البند / الخدمة</label>
                        <input type="text" name="items_desc[]" required placeholder="مثال: إيجار شقة سكنية لشهر مايو" class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs">
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

        <!-- Section 3: Totals & Fees -->
        <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">المجموع قبل الضريبة:</span>
                    <span id="subtotalDisplay" class="text-lg font-black text-amber-400">0.00 ر.س</span>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">نسبة ضريبة القيمة المضافة %</label>
                    <input type="number" step="0.01" min="0" max="100" id="tax_rate" name="tax_rate" value="0" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs font-bold">
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">قيمة الضريبة:</span>
                    <span id="taxAmountDisplay" class="text-lg font-black text-slate-200">0.00 ر.س</span>
                </div>

                <div>
                    <span class="text-slate-400 block mb-1">المبلغ الإجمالي للفاتورة:</span>
                    <span id="totalDisplay" class="text-xl font-black text-emerald-400">0.00 ر.س</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="text-amber-400 font-bold">رسوم إصدار الفاتورة للمنصة:</span>
                    <input type="number" step="0.01" name="service_fee" value="{{ old('service_fee', $serviceFee) }}" required class="w-24 px-2 py-1 rounded bg-slate-800 border border-amber-400 text-amber-300 font-black text-center">
                    <span class="text-slate-400">ر.س (دخل مستقل للمنصة)</span>
                </div>

                <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                    <input type="checkbox" name="deduct_from_wallet" value="1" {{ old('deduct_from_wallet', '1') ? 'checked' : '' }} class="rounded text-amber-500 border-slate-700">
                    <span>خصم الرسوم تلقائياً من محفظة المستثمر المختار</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-semibold text-slate-700 mb-1 text-xs">ملاحظات أو شروط إضافية تظهر بالفاتورة</label>
            <textarea name="notes" rows="2" placeholder="ملاحظات الحساب البنكي، طريقة السداد، إلخ..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-amber-500 text-xs">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.invoices.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i>
                <span>إصدار واعتماد الفاتورة</span>
            </button>
        </div>
    </form>

</div>

<script>
    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            subtotal += (qty * price);
        });

        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const taxAmount = (subtotal * taxRate) / 100;
        const total = subtotal + taxAmount;

        document.getElementById('subtotalDisplay').innerText = subtotal.toFixed(2) + ' ر.س';
        document.getElementById('taxAmountDisplay').innerText = taxAmount.toFixed(2) + ' ر.س';
        document.getElementById('totalDisplay').innerText = total.toFixed(2) + ' ر.س';
    }

    document.getElementById('itemsContainer').addEventListener('input', calculateTotals);
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);

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

    // Auto fill from contract
    document.getElementById('rental_contract_id')?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('client_name').value = opt.dataset.tenant || '';
            document.getElementById('client_phone').value = opt.dataset.phone || '';
            if (opt.dataset.investor) {
                document.getElementById('investor_id').value = opt.dataset.investor;
            }
        }
    });

    calculateTotals();
</script>
@endsection

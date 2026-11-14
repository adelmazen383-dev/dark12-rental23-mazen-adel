@extends('layouts.admin')

@section('title', 'إعدادات النظام والرسوم')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div>
        <h1 class="text-2xl font-black text-slate-900">إعدادات النظام والرسوم المالية</h1>
        <p class="text-xs text-slate-500 mt-1">تحديد قيم الاشتراكات ورسوم الحجوزات وحسابات البنك المعتمدة دون الحاجة لتعديل البرمجة</p>
    </div>

    <!-- Financial Settings Form -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-900 border-b pb-3 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-coins text-amber-500"></i>
            <span>الرسوم المالية والاشتراكات (قابلة للتعديل مباشرة)</span>
        </h3>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        قيمة الاشتراك الشهري للمستثمر (ريال) *
                    </label>
                    <input type="number" name="monthly_subscription" value="{{ $settings['monthly_subscription'] ?? 500 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">يتم احتساب هذا المبلغ شهرياً لتجديد اشتراك المستثمر بالمنصة.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        رسوم المنصة لكل حجز مؤكد (ريال) *
                    </label>
                    <input type="number" name="booking_fee" value="{{ $settings['booking_fee'] ?? 50 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">تُخصم تلقائياً من محفظة المستثمر عند اعتماد الحجز فقط (وليس عند المعاينة).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-contract text-emerald-600"></i>
                        <span>رسوم إصدار وتوثيق عقد إيجار رسمي (ريال) *</span>
                    </label>
                    <input type="number" name="contract_service_fee" value="{{ $settings['contract_service_fee'] ?? 150 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs focus:ring-2 focus:ring-emerald-500 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">مصدر دخل للمنصة: رسوم خدمة إعداد وتوثيق عقود الإيجار (حتى لو لم يتم التأجير عبر المنصة).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                        <span>رسوم إصدار فاتورة ضريبية رسمية (ريال) *</span>
                    </label>
                    <input type="number" name="invoice_service_fee" value="{{ $settings['invoice_service_fee'] ?? 50 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs focus:ring-2 focus:ring-emerald-500 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">مصدر دخل للمنصة: رسوم إصدار الفواتير الإلكترونية المعتمدة للعملاء والمستثمرين.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        حد التنبيه لانخفاض رصيد المحفظة (ريال) *
                    </label>
                    <input type="number" name="low_balance_threshold" value="{{ $settings['low_balance_threshold'] ?? 100 }}" required min="0"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">يصل تنبيه للمستثمر عبر واتساب عند وصول رصيده لهذا الحد.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        رقم واتساب الإدارة المعتمد للإشعارات *
                    </label>
                    <input type="text" name="admin_whatsapp" value="{{ $settings['admin_whatsapp'] ?? '0500000000' }}" required dir="ltr"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 text-left">
                    <p class="text-[10px] text-slate-400 mt-1">تصل إليه إشعارات الحجوزات ورفع الإيصالات وطلبات المعاينة.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        اسم المنصة / البرج *
                    </label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'أجنحة البرج' }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        أيام التنبيه قبل انتهاء الاشتراك الشهري
                    </label>
                    <input type="number" name="subscription_reminder_days" value="{{ $settings['subscription_reminder_days'] ?? 7 }}" min="1"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl text-xs transition">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>

    <!-- Platform Bank Accounts Management -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-amber-500"></i>
                    <span>الحسابات البنكية المعتمدة للتحويل (تظهر للنزلاء في الباركود)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">الحسابات التي يقوم العميل بتحويل مبلغ الإيجار والتأمين إليها</p>
            </div>
            <button type="button" onclick="document.getElementById('addBankModal').classList.remove('hidden')" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة حساب بنكي</span>
            </button>
        </div>

        <div class="space-y-3">
            @forelse($bankAccounts as $bank)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-slate-900">{{ $bank->bank_name }} - {{ $bank->account_name }}</div>
                        <div class="font-mono text-xs text-slate-600 mt-0.5" dir="ltr">{{ $bank->iban }}</div>
                    </div>

                    <form action="{{ route('admin.bank-accounts.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('حذف هذا الحساب البنكي؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="حذف">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-4 text-center text-xs text-slate-400">لا توجد حسابات بنكية مضافة بعد.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Modal: Add Bank Account -->
<div id="addBankModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-slate-900">إضافة حساب بنكي معتمد</h3>
            <button onclick="document.getElementById('addBankModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.bank-accounts.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم البنك *</label>
                <input type="text" name="bank_name" required placeholder="مثال: البنك الأهلي السعودي" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم صاحب الحساب (المؤسسة أو الشركة) *</label>
                <input type="text" name="account_name" required placeholder="مؤسسة أجنحة البرج الفندقية" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الآيبان IBAN *</label>
                <input type="text" name="iban" required dir="ltr" placeholder="SA0000000000000000000000" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 text-left">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addBankModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold">إضافة الحساب</button>
            </div>
        </form>
    </div>
</div>
@endsection

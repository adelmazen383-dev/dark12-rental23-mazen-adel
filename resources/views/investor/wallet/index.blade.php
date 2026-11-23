@extends('layouts.investor')

@section('title', 'محفظتي وكشف الحساب')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">محفظة المستثمر المالية</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة الرصيد، كشف الحساب المفصل، شحن الرصيد، ورسوم الحجوزات</p>
        </div>
    </div>

    <!-- Balance Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white rounded-2xl p-6 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <span class="text-xs text-slate-300 font-semibold block mb-1">الرصيد المتاح حالياً بالمحفظة</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-black text-emerald-400">{{ number_format($wallet->balance, 2) }}</span>
                    <span class="text-sm font-bold text-slate-300">ريال سعودي</span>
                </div>

                @if($wallet->balance <= $lowThreshold)
                    <div class="mt-3 px-3 py-1.5 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>تنبيه: رصيدك أقل من الحد الأدنى ({{ $lowThreshold }} ريال). يرجى الشحن لضمان استمرار استقبال الحجوزات.</span>
                    </div>
                @endif
            </div>

            <!-- Booking Fee Disclosure -->
            <div class="bg-slate-800/80 rounded-xl p-4 border border-slate-700/80 text-xs space-y-1.5 sm:max-w-xs">
                <span class="text-amber-400 font-bold block mb-1">سياسة المنصة ورسوم المعاملات:</span>
                <p class="text-slate-300">
                    يتم خصم <strong class="text-emerald-400 font-black">{{ number_format($bookingFee, 0) }} ريال</strong> عند تأكيد كل حجز إيجار.
                </p>
                <p class="text-slate-300">
                    يتم خصم <strong class="text-sky-400 font-black">100 ريال</strong> لكل عقد إيجار موثق يتم إصداره.
                </p>
                <p class="text-slate-400 text-[10px]">
                    * يجب توفر رصيد بالمحفظة لظهور شققك للنزلاء واستقبال الحجوزات.
                </p>
            </div>
        </div>
    </div>

    <!-- Recharge Guide & Invoicing Workflow (طلب شحن المحفظة وسداد الفاتورة) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-5">
        <div class="flex items-center gap-2 border-b pb-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-slate-900">طريقة شحن رصيد المحفظة والخصم التلقائي</h3>
                <p class="text-xs text-slate-500">نظام فواتير دفع معتمدة من إدارة البرج وإيداع مباشر في محفظتك</p>
            </div>
        </div>

        <!-- 3-Step Visual Process -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-900 text-amber-400 text-xs font-black flex items-center justify-center">1</span>
                    <h4 class="font-bold text-xs text-slate-900">تواصل مع إدارة البرج</h4>
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    اضغط على زر الواتساب بالأسفل للتواصل المباشر مع رقم إدارة البرج، وحدد المبلغ الذي ترغب في شحنه بالمحفظة.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-900 text-amber-400 text-xs font-black flex items-center justify-center">2</span>
                    <h4 class="font-bold text-xs text-slate-900">استلام فاتورة سداد رسمية</h4>
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    ترسل لك الإدارة فوراً فاتورة دفع إلكترونية رسمية برقم مرجعي لسداد المبلغ عبر الحسابات البنكية المعتمدة.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-black flex items-center justify-center">3</span>
                    <h4 class="font-bold text-xs text-emerald-900">شحن الرصيد والخصم التلقائي</h4>
                </div>
                <p class="text-[11px] text-emerald-800 leading-relaxed">
                    بعد السداد تقوم الإدارة بإيداع الرصيد في محفظتك، وأي تعامل بالمنصة (رسوم حجز، توثيق عقد، فاتورة) يتم خصمه تلقائياً دون الحاجة للدفع في كل معاملة!
                </p>
            </div>
        </div>

        <!-- Bank Accounts Details -->
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-700 block">حسابات المنصة البنكية المعتمدة لسداد فواتير الشحن:</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($bankAccounts as $bank)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-900">{{ $bank->bank_name }}</span>
                            <span class="text-slate-500">{{ $bank->account_name }}</span>
                        </div>
                        <div class="font-mono text-amber-700 font-bold bg-white p-2 rounded-lg border text-left" dir="ltr">
                            {{ $bank->iban }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @php
            $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
            $investor = auth()->user()->investor;
            $whatsappMsg = "مرحباً إدارة البرج، أرغب في شحن محفظتي كمستثمر باسم: {$investor?->full_name} (رقم المستثمر: #INV-{$investor?->id}). يرجى إرسال فاتورة سداد بالمبلغ المطلوب.";
        @endphp
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
            <div class="flex items-center gap-2 text-xs text-emerald-900">
                <i class="fa-brands fa-whatsapp text-lg text-emerald-600"></i>
                <span>رقم واتساب إدارة البرج المالي: <strong class="font-mono" dir="ltr">{{ $adminPhone }}</strong></span>
            </div>

            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $adminPhone) }}?text={{ urlencode($whatsappMsg) }}" target="_blank"
                class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>إرسال طلب شحن</span>
            </a>
        </div>
    </div>

    <!-- Transaction Ledger / Statement -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">كشف حساب حركات المحفظة</h3>
            <span class="text-xs text-slate-500">{{ $transactions->total() }} حركة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-3">التاريخ والوقت</th>
                        <th class="p-3">نوع الحركة</th>
                        <th class="p-3">المبلغ</th>
                        <th class="p-3">البيان والتفاصيل</th>
                        <th class="p-3">الرصيد بعد الحركة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tr)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3 text-slate-500">{{ $tr->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold 
                                    @if($tr->type === 'deposit') bg-emerald-100 text-emerald-800
                                    @elseif($tr->type === 'deduction') bg-rose-100 text-rose-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $tr->type_label }}
                                </span>
                            </td>
                            <td class="p-3 font-black {{ $tr->type === 'deposit' ? 'text-emerald-700' : ($tr->type === 'refund' ? 'text-blue-700' : 'text-rose-700') }}">
                                {{ $tr->type === 'deposit' || $tr->type === 'refund' ? '+' : '-' }}{{ number_format($tr->amount, 2) }} ر.س
                            </td>
                            <td class="p-3 text-slate-800">{{ $tr->description }}</td>
                            <td class="p-3 font-bold text-slate-700">{{ number_format($tr->balance_after, 2) }} ر.س</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">لا توجد حركات مالية مسجلة بعد في المحفظة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection

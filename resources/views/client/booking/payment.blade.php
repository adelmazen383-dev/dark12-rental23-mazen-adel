@extends('layouts.client')

@section('title', 'التحويل البنكي ورفع الإيصال - حجز ' . $booking->reference_number)
@section('meta_robots', 'noindex, nofollow')
@section('push_booking_reference', $booking->reference_number)

@section('content')
<div class="space-y-6">

    @include('partials.client-push-banner', [
        'pushTitle' => 'فعّل إشعار تأكيد الحجز وكود استلام المفتاح',
        'pushSubtitle' => 'اضغط هنا لتصلك رسالة فورية على جوالك (أو الآيفون) بمجرد مطابقة التحويل وتأكيد الحجز حتى لو أغلقت الموقع.'
    ])

    <!-- Progress Steps Header -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between text-xs font-bold text-gray-400">
            <div class="flex items-center gap-1.5 text-emerald-600">
                <i class="fa-solid fa-circle-check"></i>
                <span>1. اختيار الشقة</span>
            </div>
            <div class="h-0.5 flex-1 bg-amber-400 mx-2"></div>
            <div class="flex items-center gap-1.5 text-amber-600">
                <i class="fa-solid fa-circle-dot"></i>
                <span>2. التحويل ورفع الإيصال</span>
            </div>
            <div class="h-0.5 flex-1 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-1.5 text-gray-400">
                <i class="fa-regular fa-circle"></i>
                <span>3. تأكيد الحجز النهائي</span>
            </div>
        </div>
    </div>

    <!-- Booking Summary Card -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs text-gray-400 font-semibold block">الرقم المرجعي للحجز:</span>
                <span class="text-lg font-black text-amber-600 tracking-wider">{{ $booking->reference_number }}</span>
            </div>
            <div class="text-left">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                    بانتظار التحويل البنكي
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 border-b border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block text-[10px]">العميل</span>
                <span class="font-bold text-gray-800">{{ $booking->customer_name }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px]">الشقة والدور</span>
                <span class="font-bold text-gray-800">شقة {{ $booking->apartment->apartment_number }} - الدور {{ $booking->apartment->floor_number }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px]">فترة الإقامة</span>
                <span class="font-bold text-gray-800">{{ $booking->nights_count }} ليالي ({{ $booking->checkin_date->format('Y-m-d') }} إلى {{ $booking->checkout_date->format('Y-m-d') }})</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px]">المبلغ الإجمالي</span>
                <span class="font-black text-emerald-700 text-base">{{ number_format($booking->total_amount, 2) }} ر.س</span>
            </div>
        </div>

        <!-- Breakdown Details -->
        <div class="pt-3 text-xs text-gray-600 space-y-1">
            <div class="flex justify-between">
                <span>قيمة الإقامة ({{ $booking->nights_count }} ليلة × {{ number_format($booking->daily_rate, 0) }} ريال):</span>
                <span>{{ number_format($booking->daily_rate * $booking->nights_count, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between">
                <span>مبلغ التأمين (مسترد بالكامل عند الخروج):</span>
                <span>{{ number_format($booking->insurance_amount, 2) }} ر.س</span>
            </div>
            @if($booking->extra_fees > 0)
                <div class="flex justify-between">
                    <span>رسوم إضافية:</span>
                    <span>{{ number_format($booking->extra_fees, 2) }} ر.س</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Platform Bank Accounts (Specification Requirement) -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 sm:p-6 shadow-md border border-slate-700">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-white">بيانات الحساب البنكي المعتمد للتحويل</h3>
                <p class="text-[11px] text-slate-300">يرجى تحويل المبلغ المطلوب إلى أحد الحسابات التالية:</p>
            </div>
        </div>

        <div class="space-y-3 mt-4">
            @forelse($bankAccounts as $bank)
                <div class="bg-slate-800/80 rounded-xl p-4 border border-slate-700/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-amber-400 font-bold text-sm">{{ $bank->bank_name }}</span>
                        <span class="text-slate-300">اسم الحساب: <strong class="text-white">{{ $bank->account_name }}</strong></span>
                    </div>

                    <div class="flex items-center justify-between bg-slate-950/60 p-2.5 rounded-lg border border-slate-700">
                        <span class="font-mono text-xs text-amber-300 tracking-wider" dir="ltr" id="iban_{{ $bank->id }}">{{ $bank->iban }}</span>
                        <button type="button" onclick="copyIban('{{ $bank->iban }}')" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded font-bold text-[11px] transition flex items-center gap-1">
                            <i class="fa-regular fa-copy"></i>
                            <span>نسخ الآيبان</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-slate-800/80 rounded-xl p-4 text-xs text-amber-300">
                    يرجى مراجعة موظف الاستقبال للحصول على بيانات التحويل البنكي المعتمد.
                </div>
            @endforelse

            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 text-xs text-amber-200 flex items-center justify-between">
                <span>المبلغ المطلوب تحويله بدقة:</span>
                <span class="text-lg font-black text-amber-400">{{ number_format($booking->total_amount, 2) }} ريال</span>
            </div>
        </div>
    </div>

    <!-- Receipt Upload Form (Specification Requirement) -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-200">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm text-gray-900">رفع إيصال التحويل البنكي</h3>
                <p class="text-xs text-gray-500">ارفع صورة الإشعار أو ملف PDF للتحقق من الدفع</p>
            </div>
        </div>

        <form action="{{ route('client.booking.upload-receipt', $booking->reference_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
            @csrf
            <input type="hidden" name="push_endpoint" class="js-push-endpoint" value="">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">ملف الإيصال (صورة أو PDF) *</label>
                <div class="border-2 border-dashed border-gray-300 hover:border-amber-500 rounded-2xl p-6 text-center bg-gray-50 transition cursor-pointer" onclick="document.getElementById('receipt_file').click()">
                    <i class="fa-solid fa-file-invoice text-3xl text-gray-400 mb-2"></i>
                    <p class="text-xs font-bold text-gray-700">اضغط هنا لاختيار صورة الإيصال أو ملف PDF</p>
                    <p class="text-[10px] text-gray-400 mt-1">يدعم JPG, PNG, PDF بحد أقصى 10 ميجابايت</p>
                    <input type="file" name="receipt" id="receipt_file" required accept="image/*,.pdf" class="hidden" onchange="previewFile(this)">
                    <div id="file_selected_name" class="mt-2 text-xs font-bold text-emerald-600 hidden"></div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-check-circle"></i>
                <span>إرسال الإيصال وتأكيد الطلب</span>
            </button>
        </form>

        <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 leading-relaxed">
            <strong>تنويه هام:</strong> بعد رفع الإيصال، سيصل إشعار فوري لإدارة المنصة وصاحب الشقة، وستتحول حالة طلبك إلى «بانتظار التحقق من الدفع»، وسيتم اعتماد الحجز النهائي بعد التحقق الفعلي من وصول المبلغ في الحساب البنكي.
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function copyIban(iban) {
        navigator.clipboard.writeText(iban).then(() => {
            if (window.DarkClientPush) {
                window.DarkClientPush.showToast('✅ تم نسخ الآيبان', 'تم نسخ رقم الآيبان بنجاح: ' + iban, '#');
            }
        }).catch(() => {});
    }

    function previewFile(input) {
        const nameDiv = document.getElementById('file_selected_name');
        if (input.files && input.files[0]) {
            nameDiv.textContent = 'الملف المختار: ' + input.files[0].name;
            nameDiv.classList.remove('hidden');
        }
    }
</script>
@endsection

@extends('layouts.admin')

@section('title', 'استقبال وأمن البرج - تسليم المفاتيح ورصد النزلاء')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl font-black">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900">استقبال وأمن البرج (بوابة تسليم المفاتيح)</h1>
                    <p class="text-xs text-slate-500">الحاجز الميداني الأول: التحقق من كود الحجز السري، تسليم كروت الدخول، وكشف أي تأجير خارج المنصة</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('unregisteredDealModal').classList.remove('hidden')" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>ضبط نزيل غير مسجل (إثبات صفقة مباشرة)</span>
            </button>
        </div>
    </div>

    <!-- Live Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">نزلاء متواجدون حالياً بالبرج</span>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ $inHouseCount }} نزيل</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-plane-arrival"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">وصول وإقامات اليوم</span>
                <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $todayArrivals->total() }} حجز</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-shield-virus"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500">تنبيهات اشتباه التفاف</span>
                <p class="text-2xl font-black text-rose-600 mt-0.5">{{ $bypassAlerts->count() }} حالة</p>
            </div>
        </div>
    </div>

    <!-- Quick Check-in By Passcode Form -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-2xl p-6 text-white shadow-md">
        <div class="max-w-3xl">
            <span class="text-amber-400 font-bold text-xs uppercase tracking-wider block mb-1">
                <i class="fa-solid fa-qrcode ml-1"></i> تسليم المفتاح الفوري عبر كود الاستقبال
            </span>
            <h2 class="text-lg sm:text-xl font-black mb-2">أدخل كود استلام المفتاح (Reception Passcode) الخاص بالنزيل:</h2>
            <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                لا يُسلَّم كارت الدخول الإلكتروني أو مفتاح الشقة لأي شخص إلا بإبراز هذا الكود المكوّن من 6 خانات، للتأكد التام من تحصيل حقوق المنصة وتوثيق هوية المستأجر قانونياً.
            </p>

            <form action="{{ route('admin.reception.check-in') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="relative flex-1">
                    <input type="text" name="reception_code" required placeholder="مثال: RCP-A7B2X9 أو رقم الحجز BK-XXXX" autocomplete="off" class="w-full px-4 py-3 rounded-xl bg-slate-950/80 border border-slate-700 text-amber-400 font-mono font-black text-base focus:ring-2 focus:ring-amber-500 placeholder-slate-500 uppercase" dir="ltr">
                    <i class="fa-solid fa-key absolute right-3.5 top-3.5 text-slate-500"></i>
                </div>
                <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 whitespace-nowrap">
                    <i class="fa-solid fa-check-double"></i>
                    <span>تحقق وتسليم المفتاح 🔑</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Lookup / Inspection Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-4">
        <form action="{{ route('admin.reception.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 text-xs">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="استعلام برقم الشقة (مثال: 204)، أو اسم النزيل، أو رقم الجوال..." class="w-full pl-3 pr-10 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-3 text-slate-400"></i>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition">
                بحث واستعلام
            </button>
            @if(request('q'))
                <a href="{{ route('admin.reception.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition text-center">
                    إلغاء البحث
                </a>
            @endif
        </form>

        <!-- Search Result Output -->
        @if(request('q'))
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-3">
                <div class="flex items-center justify-between border-b pb-2">
                    <span class="font-bold text-slate-800">نتائج الاستعلام عن: "{{ request('q') }}"</span>
                </div>

                @if($searchResult)
                    <div class="p-4 rounded-xl bg-white border border-emerald-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">حجز رسمي مسجل</span>
                                <span class="font-mono font-bold text-amber-700">{{ $searchResult->reference_number }}</span>
                                <span class="font-mono font-bold bg-slate-900 text-amber-400 px-2 py-0.5 rounded text-[10px]">كود الدخول: {{ $searchResult->reception_code }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 mt-1">النزيل: {{ $searchResult->customer_name }} ({{ $searchResult->customer_phone }})</h4>
                            <p class="text-slate-500 text-xs mt-0.5">
                                شقة {{ $searchResult->apartment->apartment_number }} (الدور {{ $searchResult->apartment->floor_number }}) | المالك: {{ $searchResult->apartment->investor?->full_name }} | الإقامة: {{ $searchResult->checkin_date->format('Y-m-d') }} إلى {{ $searchResult->checkout_date->format('Y-m-d') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(!$searchResult->isReceptionCheckedIn())
                                <form action="{{ route('admin.reception.check-in') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="reception_code" value="{{ $searchResult->reception_code }}">
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-key"></i>
                                        <span>تسليم المفتاح الآن</span>
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                    <i class="fa-solid fa-check ml-1"></i> تم الدخول ({{ $searchResult->reception_checked_in_at->format('H:i') }})
                                </span>
                                @if(!$searchResult->reception_checked_out_at)
                                    <form action="{{ route('admin.reception.check-out', $searchResult->id) }}" method="POST" onsubmit="return confirm('تأكيد استلام المفتاح وتسجيل خروج النزيل؟');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition">
                                            تسجيل الخروج واستلام الكارت
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
                            <div>
                                <strong class="block font-bold">لا يوجد حجز رسمي مسجل بهذه البيانات!</strong>
                                <span>إذا كان النزيل متواجداً أمام الاستقبال ويرغب في استلام المفتاح، يرجى التحقق من وجود محادثات أدناه أو تسجيل الواقعة مباشرة لخصم العمولة.</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Linked Inquiries & Chats Found -->
                @if($searchChats && $searchChats->isNotEmpty())
                    <div class="mt-3 space-y-2">
                        <span class="font-bold text-slate-700 block">محادثات استفسار سابقة مرتبطة بهذا الرقم/النزيل:</span>
                        @foreach($searchChats as $sc)
                            <div class="p-3 rounded-xl bg-white border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900">شقة {{ $sc->apartment->apartment_number }} (المالك: {{ $sc->investor?->full_name }})</span>
                                    <span class="text-slate-500 text-[11px] block">حالة التفاوض: <strong class="text-amber-700">{{ $sc->deal_status }}</strong> | آخر رسالة: {{ $sc->last_message_at?->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.chats.show', $sc->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[11px]">
                                        معاينة الشات
                                    </a>
                                    <button type="button" onclick="openUnregisteredModalWith('{{ $sc->apartment_id }}', '{{ $sc->customer_name }}', '{{ $sc->customer_phone }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-[11px]">
                                        🚨 إثبات الصفقة للشقة {{ $sc->apartment->apartment_number }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Circumvention & Bypass Alerts (Radar) -->
    @if($bypassAlerts->isNotEmpty())
        <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 animate-pulse"></span>
                    <h3 class="font-bold text-sm text-rose-900">رادار كشف الالتفاف والتهرب من العمولة (تنبيهات فورية للشات والاستبيان)</h3>
                </div>
                <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full">{{ $bypassAlerts->count() }} تنبيه</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-rose-50/50 text-slate-600 font-bold border-b">
                        <tr>
                            <th class="p-3">النزيل</th>
                            <th class="p-3">المالك (المستثمر)</th>
                            <th class="p-3">الشقة</th>
                            <th class="p-3">نوع التنبيه وسبب الاشتباه</th>
                            <th class="p-3">حالة الصفقة</th>
                            <th class="p-3 text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bypassAlerts as $ba)
                            <tr class="hover:bg-rose-50/20">
                                <td class="p-3 font-bold text-slate-900">{{ $ba->customer_name }} ({{ $ba->customer_phone }})</td>
                                <td class="p-3">{{ $ba->investor?->full_name }}</td>
                                <td class="p-3 font-bold text-slate-800">شقة {{ $ba->apartment->apartment_number }}</td>
                                <td class="p-3 text-rose-700 font-semibold">{{ $ba->bypass_attempt_reason ?? 'رصد تبادل وسائل اتصال أو تضارب استبيان' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">{{ $ba->deal_status }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('admin.chats.show', $ba->id) }}" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold rounded-lg text-[11px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>فحص الشات</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Today's Arrivals & In-House Ledger -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-day text-amber-500"></i>
                <h3 class="font-bold text-sm text-slate-900">سجل حركة النزلاء وتسليم المفاتيح بالاستقبال</h3>
            </div>
            <span class="text-xs text-slate-500">{{ $todayArrivals->total() }} سجل</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b">
                    <tr>
                        <th class="p-3.5">رقم الحجز</th>
                        <th class="p-3.5">كود استلام المفتاح</th>
                        <th class="p-3.5">الشقة والمالك</th>
                        <th class="p-3.5">النزيل</th>
                        <th class="p-3.5">الإقامة</th>
                        <th class="p-3.5">حالة المفتاح والدخول</th>
                        <th class="p-3.5 text-center">إجراءات الاستقبال</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($todayArrivals as $bk)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3.5 font-mono font-bold text-amber-700">
                                <a href="{{ route('admin.bookings.show', $bk->id) }}" class="hover:underline">{{ $bk->reference_number }}</a>
                                @if($bk->is_unregistered_deal)
                                    <span class="block text-[9px] font-sans font-bold text-rose-600">🚨 صفقة مرصودة بالاستقبال</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="font-mono font-black text-xs px-2.5 py-1 rounded-lg bg-slate-900 text-amber-400">
                                    {{ $bk->reception_code ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <strong class="text-slate-800">شقة {{ $bk->apartment->apartment_number }}</strong> (الدور {{ $bk->apartment->floor_number }})
                                <span class="block text-[10px] text-slate-400">المالك: {{ $bk->apartment->investor?->full_name }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900">{{ $bk->customer_name }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $bk->customer_phone }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600">
                                {{ $bk->checkin_date->format('m-d') }} إلى {{ $bk->checkout_date->format('m-d') }} ({{ $bk->nights_count }} ليالٍ)
                            </td>
                            <td class="p-3.5">
                                @if($bk->isReceptionCheckedIn())
                                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                                        <i class="fa-solid fa-key text-xs"></i>
                                        <span>تم تسليم المفتاح ({{ $bk->reception_checked_in_at->format('H:i') }})</span>
                                    </div>
                                    @if($bk->reception_checked_out_at)
                                        <span class="text-[10px] text-slate-400 block">غادر: {{ $bk->reception_checked_out_at->format('Y-m-d H:i') }}</span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">بانتظار الوصول والاستلام</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if(!$bk->isReceptionCheckedIn())
                                        <form action="{{ route('admin.reception.check-in') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="reception_code" value="{{ $bk->reception_code }}">
                                            <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs transition shadow-sm">
                                                تسليم المفتاح 🔑
                                            </button>
                                        </form>
                                    @elseif(!$bk->reception_checked_out_at)
                                        <form action="{{ route('admin.reception.check-out', $bk->id) }}" method="POST" onsubmit="return confirm('تسجيل خروج النزيل واستلام الكارت؟');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-lg text-xs transition">
                                                تسجيل خروج
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">مكتمل</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">لا توجد حركات تسليم مفاتيح مسجلة اليوم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $todayArrivals->links() }}
        </div>
    </div>

</div>

<!-- Modal: Record Unregistered Deal (ضبط نزيل غير مسجل) -->
<div id="unregisteredDealModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <div class="flex items-center gap-2 text-rose-700">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                <h3 class="font-bold text-base">إثبات صفقة مباشرة وضبط نزيل غير مسجل</h3>
            </div>
            <button onclick="document.getElementById('unregisteredDealModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs leading-relaxed">
            <strong>نظام حماية إيرادات البرج:</strong> عند رصد نزيل متواجد بشقة دون حجز رسمي بالمنصة، يقوم موظف الاستقبال بتسجيل الواقعة هنا؛ سيتم فوراً إنشاء الحجز وإثبات بيانات النزيل أمنياً، مع <strong>خصم عمولة المنصة ({{ $bookingFee }} ر.س) + غرامة الالتفاف ({{ $penaltyFee }} ر.س)</strong> تلقائياً من محفظة مالك الشقة.
        </div>

        <form action="{{ route('admin.reception.record-unregistered-deal') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">الشقة المعنية *</label>
                <select name="apartment_id" id="modalApartmentId" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500">
                    <option value="">اختر الشقة...</option>
                    @foreach($allApartments as $apt)
                        <option value="{{ $apt->id }}" data-price="{{ $apt->daily_price }}">
                            شقة {{ $apt->apartment_number }} (الدور {{ $apt->floor_number }}) - المالك: {{ $apt->investor?->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">اسم النزيل المتواجد *</label>
                    <input type="text" name="customer_name" id="modalCustomerName" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="الاسم الكامل للنزيل">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">رقم جوال النزيل *</label>
                    <input type="text" name="customer_phone" id="modalCustomerPhone" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="05XXXXXXXX">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">عدد الليالي *</label>
                    <input type="number" name="nights_count" value="1" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">السعر اليومي (ريال) *</label>
                    <input type="number" name="daily_rate" id="modalDailyRate" value="300" min="1" step="0.01" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">ملاحظات موظف الاستقبال أو الأمن</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="مثال: حضر النزيل وذكر أنه متفق مع المالك هاتفياً، وتم تسليمه المفتاح وإثبات الواقعة"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="document.getElementById('unregisteredDealModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md">إثبات الصفقة وتسليم المفتاح وخصم العمولة</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUnregisteredModalWith(aptId, name, phone) {
        document.getElementById('unregisteredDealModal').classList.remove('hidden');
        if (aptId) document.getElementById('modalApartmentId').value = aptId;
        if (name) document.getElementById('modalCustomerName').value = name;
        if (phone) document.getElementById('modalCustomerPhone').value = phone;
    }
</script>
@endsection

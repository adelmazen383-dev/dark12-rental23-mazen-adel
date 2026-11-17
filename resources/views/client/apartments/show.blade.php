@extends('layouts.client')

@section('title', 'تفاصيل شقة ' . $apartment->apartment_number . ' - الدور ' . $apartment->floor_number)

@section('content')
<div class="space-y-6">

    <!-- Back button & Quick WhatsApp Row -->
    <div class="flex items-center justify-between flex-wrap gap-3 pb-2">
        <a href="{{ route('client.apartments.index') }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-700 hover:text-amber-600 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs transition">
            <i class="fa-solid fa-arrow-right text-amber-500"></i>
            <span>العودة لقائمة الشقق المتاحة</span>
        </a>

        <a href="#card-chat"
           class="inline-flex items-center gap-2 text-xs font-black text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 px-3.5 py-2 rounded-xl shadow-2xs transition">
            <i class="fa-solid fa-comments text-amber-600 text-sm"></i>
            <span>محادثة مباشرة مع المالك (شات فوري)</span>
        </a>
    </div>

    <!-- Video Showcase (if available) -->
    @if($videos->isNotEmpty())
        <div class="bg-black rounded-2xl overflow-hidden shadow-lg border border-slate-800">
            <div class="p-3 bg-slate-900 flex items-center justify-between text-xs text-slate-300 border-b border-slate-800">
                <span class="font-bold flex items-center gap-1.5 text-amber-400">
                    <i class="fa-solid fa-circle-play text-rose-500"></i>
                    <span>فيديو جولة كاملة للشقة</span>
                </span>
                <span class="text-[11px] text-slate-400">شقة {{ $apartment->apartment_number }} - الدور {{ $apartment->floor_number }}</span>
            </div>
            <div class="relative w-full aspect-video bg-black flex items-center justify-center">
                <video controls playsinline class="w-full h-full object-contain">
                    <source src="{{ asset('storage/' . $videos->first()->file_path) }}" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو مباشرة.
                </video>
            </div>
        </div>
    @endif

    <!-- Image Gallery -->
    @if($images->isNotEmpty())
        <div class="space-y-2">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">صور الشقة والتجهيزات</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach($images as $img)
                    <a href="{{ asset('storage/' . $img->file_path) }}" target="_blank" class="block h-28 sm:h-36 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 group">
                        <img src="{{ asset('storage/' . $img->file_path) }}" alt="صورة الشقة" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Apartment Info Card -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-amber-500 text-slate-950">
                        شقة رقم {{ $apartment->apartment_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-900 text-white">
                        الدور {{ $apartment->floor_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        متاحة للحجز الفوري
                    </span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">
                    جناح فاخر بالدور {{ $apartment->floor_number }}
                </h2>
            </div>

            <!-- Price -->
            <div class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-3 sm:text-left">
                <span class="text-xs text-amber-800 font-semibold block">سعر الإقامة لليلة:</span>
                <span class="text-2xl font-black text-amber-700">{{ number_format($apartment->daily_price, 0) }}</span>
                <span class="text-xs text-amber-800 font-bold">ريال سعودي</span>
            </div>
        </div>

        <!-- Specifications Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 border-b border-gray-100 text-xs">
            <div class="p-3 rounded-xl bg-gray-50 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-bed"></i>
                </div>
                <div>
                    <span class="text-gray-500 block text-[10px]">غرف النوم</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $apartment->rooms_count }} غرف</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-couch"></i>
                </div>
                <div>
                    <span class="text-gray-500 block text-[10px]">الصالات</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $apartment->living_rooms_count }} صالة</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-bath"></i>
                </div>
                <div>
                    <span class="text-gray-500 block text-[10px]">دورات المياه</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $apartment->bathrooms_count }} حمام</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <div>
                    <span class="text-gray-500 block text-[10px]">الإطلالة</span>
                    <span class="font-bold text-gray-800 text-sm">{{ $apartment->view_type ?: 'إطلالة البرج' }}</span>
                </div>
            </div>
        </div>

        <!-- Times and Policies -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 py-4 border-b border-gray-100 text-xs">
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                <span class="text-gray-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-clock text-amber-500"></i> وقت الدخول والخروج:
                </span>
                <span class="font-bold text-gray-900">
                    دخول {{ date('h:i A', strtotime($apartment->checkin_time)) }} / خروج {{ date('h:i A', strtotime($apartment->checkout_time)) }}
                </span>
            </div>

            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                <span class="text-gray-600 flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-shield-heart text-amber-500"></i> مبلغ التأمين:
                </span>
                <span class="font-bold text-gray-900">
                    {{ number_format($apartment->insurance_amount, 0) }} ريال (يسترد بالكامل عند الخروج السليم)
                </span>
            </div>
        </div>

        <!-- Features Tags -->
        @if($apartment->features)
            <div class="py-4 border-b border-gray-100">
                <h4 class="text-xs font-bold text-gray-700 mb-2">المميزات والتجهيزات:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode('،', str_replace(',', '،', $apartment->features)) as $feature)
                        @if(trim($feature))
                            <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-check text-emerald-500 text-[10px]"></i>
                                <span>{{ trim($feature) }}</span>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Description -->
        @if($apartment->description)
            <div class="pt-4 text-xs text-gray-600 leading-relaxed">
                <h4 class="text-xs font-bold text-gray-700 mb-1">وصف الشقة:</h4>
                <p>{{ $apartment->description }}</p>
            </div>
        @endif
    </div>

    <!-- Client Decision / Action Section -->
    <div class="space-y-4">
        <!-- Quick Option Tabs for Mobile & Desktop -->
        <div class="bg-white p-2 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center gap-1.5 text-xs font-bold">
                <button type="button" onclick="switchOptionTab('book')" id="btn-tab-book"
                        class="flex-1 py-3 px-2 rounded-xl bg-amber-500 text-slate-950 shadow-sm flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>1. حجز مباشر وتأكيد</span>
                </button>
                <button type="button" onclick="switchOptionTab('chat')" id="btn-tab-chat"
                        class="flex-1 py-3 px-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-comments text-sm text-emerald-600"></i>
                    <span>2. محادثة المالك</span>
                </button>
                <button type="button" onclick="switchOptionTab('inspect')" id="btn-tab-inspect"
                        class="flex-1 py-3 px-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-key text-sm text-blue-600"></i>
                    <span>3. طلب معاينة</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Option 1: Booking Selection Confirmation (Primary) -->
            <div id="card-book" class="bg-gradient-to-br from-amber-50 to-white rounded-2xl p-5 shadow-sm border-2 border-amber-400 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">الخيار الأول: تأكيد اختيار الشقة والحجز</h3>
                    </div>
                    <p class="text-xs text-amber-900/80 mb-4">
                        أدخل بيانات إقامتك للانتقال إلى صفحة التحويل البنكي ورفع الإيصال وتوليد كود الاستقبال.
                    </p>

                    <!-- Booking Form -->
                    <form action="{{ route('client.booking.store', $apartment->id) }}" method="POST" class="space-y-3" id="bookingForm">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">اسم العميل الثلاثي *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name') }}"
                                class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="الاسم كما في الهوية">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">رقم الجوال *</label>
                            <input type="text" name="customer_phone" required value="{{ old('customer_phone') }}" dir="ltr"
                                class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none text-left" placeholder="05xxxxxxxx">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">تاريخ الدخول *</label>
                                <input type="date" name="checkin_date" id="checkin_date" required min="{{ date('Y-m-d') }}" value="{{ old('checkin_date', date('Y-m-d')) }}"
                                    class="w-full px-2.5 py-2 rounded-xl border border-amber-300 bg-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">تاريخ الخروج *</label>
                                <input type="date" name="checkout_date" id="checkout_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('checkout_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                    class="w-full px-2.5 py-2 rounded-xl border border-amber-300 bg-white text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Live Cost Calculation Preview -->
                        <div class="p-3 rounded-xl bg-amber-100/60 border border-amber-200 text-xs space-y-1">
                            <div class="flex justify-between text-gray-600">
                                <span>عدد الليالي:</span>
                                <span id="preview_nights" class="font-bold text-gray-900">1 ليلة</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>قيمة الإيجار:</span>
                                <span id="preview_rent" class="font-bold text-gray-900">{{ number_format($apartment->daily_price, 2) }} ر.س</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>مبلغ التأمين المسترد:</span>
                                <span class="font-bold text-gray-900">{{ number_format($apartment->insurance_amount, 2) }} ر.س</span>
                            </div>
                            @if($apartment->extra_fees > 0)
                                <div class="flex justify-between text-gray-600">
                                    <span>رسوم إضافية:</span>
                                    <span class="font-bold text-gray-900">{{ number_format($apartment->extra_fees, 2) }} ر.س</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-1 border-t border-amber-200 text-amber-900 font-black text-sm">
                                <span>الإجمالي المطلوب تحويله:</span>
                                <span id="preview_total">{{ number_format($apartment->daily_price + $apartment->insurance_amount + $apartment->extra_fees, 2) }} ر.س</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-amber-500/20">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>تأكيد الاختيار والانتقال للتحويل البنكي</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Option 2: Live Chat with Landlord -->
            <div id="card-chat" class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">الخيار الثاني: محادثة صاحب الشقة</h3>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <p class="text-xs text-gray-500">
                            هل لديك استفسار محدد أو ترغب بالتفاوض؟ ابدأ محادثة فورية ومباشرة مع مالك الشقة الآن.
                        </p>
                        <a href="{{ route('client.chat.index') }}" class="text-[11px] text-amber-600 font-bold hover:underline whitespace-nowrap flex items-center gap-1 self-start sm:self-auto">
                            <i class="fa-solid fa-comments"></i>
                            <span>محادثاتي السابقة</span>
                        </a>
                    </div>

                    <!-- Chat Start Form -->
                    <form action="{{ route('client.chat.start', $apartment->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">اسمك الكريم *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="اسمك"
                                class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">رقم الجوال *</label>
                            <input type="text" name="customer_phone" required value="{{ old('customer_phone') }}" dir="ltr"
                                class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none text-left" placeholder="05xxxxxxxx">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">رسالتك أو استفسارك الأول</label>
                            <input type="text" name="initial_message" placeholder="مرحباً، أود الاستفسار عن الشقة..."
                                class="w-full px-3 py-2 rounded-xl border border-emerald-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>بدء المحادثة الفورية 💬</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Option 3: Inspection Request -->
            <div id="card-inspect" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h3 class="font-bold text-sm text-gray-900">الخيار الثالث: طلب معاينة الشقة</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        هل ترغب في رؤية الشقة على الطبيعة أولاً؟ أرسل طلب معاينة وسيصل إشعار فوري للإدارة وممثل البرج لمرافقتك.
                    </p>

                    <!-- Inspection Form -->
                    <form action="{{ route('client.inspection.store', $apartment->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">اسمك الكريم *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name') }}"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">رقم الجوال للتواصل *</label>
                            <input type="text" name="customer_phone" required value="{{ old('customer_phone') }}" dir="ltr"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none text-left" placeholder="05xxxxxxxx">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">ملاحظات أو الوقت المناسب للمعاينة</label>
                            <input type="text" name="notes" placeholder="أنا متواجد حالياً في الاستقبال..."
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-bell"></i>
                            <span>إرسال طلب معاينة الآن</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Important Notice according to spec -->
    <div class="p-4 rounded-xl bg-gray-100 border border-gray-200 text-xs text-gray-600 flex items-start gap-2.5">
        <i class="fa-solid fa-circle-exclamation text-amber-600 text-base flex-shrink-0 mt-0.5"></i>
        <div class="leading-relaxed">
            <span class="font-bold text-gray-800">ملاحظة نظامية هامة:</span>
            تأكيد اختيار الشقة لا يعتبر حجزاً نهائياً؛ ولا يعتبر الحجز نهائياً ومؤكداً إلا بعد استيفاء شروط الحجز والتحقق الفعلي من وصول التحويل البنكي وتأكيد الإدارة للوحدة.
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    const dailyPrice = {{ (float)$apartment->daily_price }};
    const insuranceAmount = {{ (float)$apartment->insurance_amount }};
    const extraFees = {{ (float)$apartment->extra_fees }};

    const checkinInput = document.getElementById('checkin_date');
    const checkoutInput = document.getElementById('checkout_date');
    const previewNights = document.getElementById('preview_nights');
    const previewRent = document.getElementById('preview_rent');
    const previewTotal = document.getElementById('preview_total');

    function calculateTotal() {
        const checkin = new Date(checkinInput.value);
        const checkout = new Date(checkoutInput.value);

        if (checkout > checkin) {
            const diffTime = Math.abs(checkout - checkin);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const rent = diffDays * dailyPrice;
            const total = rent + insuranceAmount + extraFees;

            previewNights.textContent = diffDays + ' ليلة';
            previewRent.textContent = rent.toFixed(2) + ' ر.س';
            previewTotal.textContent = total.toFixed(2) + ' ر.س';
        }
    }

    checkinInput.addEventListener('change', function() {
        const minCheckout = new Date(this.value);
        minCheckout.setDate(minCheckout.getDate() + 1);
        checkoutInput.min = minCheckout.toISOString().split('T')[0];
        if (new Date(checkoutInput.value) <= new Date(this.value)) {
            checkoutInput.value = checkoutInput.min;
        }
        calculateTotal();
    });

    checkoutInput.addEventListener('change', calculateTotal);

    function switchOptionTab(tab) {
        const cards = {
            'book': document.getElementById('card-book'),
            'chat': document.getElementById('card-chat'),
            'inspect': document.getElementById('card-inspect')
        };
        const btns = {
            'book': document.getElementById('btn-tab-book'),
            'chat': document.getElementById('btn-tab-chat'),
            'inspect': document.getElementById('btn-tab-inspect')
        };

        // If screen is mobile, toggle visibility
        const isMobile = window.innerWidth < 1024;
        if (isMobile) {
            Object.keys(cards).forEach(key => {
                if (key === tab) {
                    cards[key].classList.remove('hidden');
                } else {
                    cards[key].classList.add('hidden');
                }
            });
        }

        // Update button styles
        Object.keys(btns).forEach(key => {
            if (key === tab) {
                btns[key].className = 'flex-1 py-3 px-2 rounded-xl bg-amber-500 text-slate-950 shadow-sm flex items-center justify-center gap-1.5 font-bold transition';
            } else {
                btns[key].className = 'flex-1 py-3 px-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 flex items-center justify-center gap-1.5 font-bold transition';
            }
        });

        // Scroll to action section smoothly
        cards[tab]?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Initialize tabs on load
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#chatOption') {
            switchOptionTab('chat');
        } else if (window.location.hash === '#inspectOption') {
            switchOptionTab('inspect');
        } else {
            switchOptionTab('book');
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            document.getElementById('card-book')?.classList.remove('hidden');
            document.getElementById('card-chat')?.classList.remove('hidden');
            document.getElementById('card-inspect')?.classList.remove('hidden');
        } else {
            // Re-apply active mobile tab
            const activeBtn = document.querySelector('#btn-tab-chat.bg-amber-500') ? 'chat'
                            : (document.querySelector('#btn-tab-inspect.bg-amber-500') ? 'inspect' : 'book');
            switchOptionTab(activeBtn);
        }
    });
</script>
@endsection

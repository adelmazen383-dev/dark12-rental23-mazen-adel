@extends('layouts.client')

@section('title', 'منصة دارك لتنظيم الإيجار - تصفح الوحدات والشقق الفاخرة المتاحة')

@section('content')
<div class="space-y-8">
    <style>
        /* ==========================================================================
           LUXURY APARTMENTS DIRECTORY STYLING & KEYFRAMES
           ========================================================================== */
        @keyframes floatSlowHero {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(4deg); }
        }
        @keyframes pulseGlowAmber {
            0%, 100% { box-shadow: 0 0 25px rgba(245, 158, 11, 0.25); }
            50% { box-shadow: 0 0 45px rgba(245, 158, 11, 0.5); }
        }
        @keyframes shimmerGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes crownBounce {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-3px) rotate(-8deg); }
        }

        .hero-gold-glow { animation: pulseGlowAmber 5s infinite; }
        .hero-shape-float { animation: floatSlowHero 8s ease-in-out infinite; }
        .crown-anim { animation: crownBounce 2.5s ease-in-out infinite; }

        /* Card 3D Perspective & Sheen */
        .apt-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            background: #FFFFFF;
            border-radius: 1.5rem;
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }
        .apt-card:hover {
            transform: translateY(-8px) scale(1.015);
            border-color: #F59E0B;
            box-shadow: 0 20px 35px -10px rgba(245, 158, 11, 0.18), 0 10px 25px -5px rgba(15, 23, 42, 0.1);
        }

        /* Image hover zoom */
        .apt-img-wrap {
            position: relative;
            overflow: hidden;
        }
        .apt-card:hover .apt-img {
            transform: scale(1.08);
        }
        .apt-img {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Gradient Sheen Overlay */
        .gradient-sheen {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.2) 100%);
        }
    </style>

    <!-- =========================================================================
         SECTION 1: HERO SPOTLIGHT (دارك - الفخامة والأمان في تنظيم الإيجار)
         ========================================================================= -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950 text-white p-6 sm:p-10 shadow-2xl border-2 border-amber-500/30 hero-gold-glow">
        <!-- Ambient Glowing Blobs -->
        <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-amber-500/15 blur-3xl pointer-events-none hero-shape-float"></div>
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-indigo-500/15 blur-3xl pointer-events-none" style="animation: floatSlowHero 10s ease-in-out infinite reverse;"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <!-- Brand Avatar & Text -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-right gap-5 flex-1">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden bg-white p-1.5 shadow-2xl border-2 border-amber-400 flex-shrink-0 group hover:scale-105 transition duration-300">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-2xl">
                </div>

                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>المنصة الرسمية المعتمدة لتنظيم وتوثيق الإيجار</span>
                        <i class="fa-solid fa-sparkles text-amber-300 text-[10px]"></i>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                        منصة دَارِك لتنظيم الإيجار
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl font-medium">
                        بوابتك العقارية الآمنة لاختيار وتأجير أرقى الشقق والأجنحة السكنية. نوفر تجربة سلسة تمتاز بالشفافية الكاملة، العقود المعتمدة، وتسليم فوري للمفاتيح عبر مكتب الاستقبال الميداني.
                    </p>

                    <!-- Interactive Quick Action Pills -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 pt-2">
                        <a href="{{ route('client.services') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold text-xs transition shadow-md shadow-indigo-600/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-cyan-300"></i>
                            <span>استكشف باقة خدماتنا</span>
                            <span class="text-[9px] bg-white/20 px-1.5 py-0.5 rounded-md">جديد</span>
                        </a>

                        <a href="{{ route('client.about') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 hover:text-white font-bold text-xs border border-white/15 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-amber-400"></i>
                            <span>لماذا يفضل العملاء دارك؟ (من نحن)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fast Stat Ticker Card on Right -->
            <div class="w-full md:w-auto flex-shrink-0">
                <div class="bg-slate-900/80 backdrop-blur-md border border-white/10 p-4 sm:p-5 rounded-2xl shadow-xl space-y-3 min-w-[240px]">
                    <div class="flex items-center justify-between pb-2 border-b border-white/10 text-xs text-slate-300">
                        <span class="font-bold flex items-center gap-1 text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>استقبال البرج متصل</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">24/7 Live</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                            <span class="text-xl font-black text-amber-400 block font-mono">{{ $apartments->total() ?? $apartments->count() }}</span>
                            <span class="text-[10px] text-slate-400 font-bold">وحدة متاحة</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/5 border border-white/5">
                            <span class="text-xl font-black text-emerald-400 block font-mono">100%</span>
                            <span class="text-[10px] text-slate-400 font-bold">حجز موثق وآمن</span>
                        </div>
                    </div>

                    @php
                        $receptionWa = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
                        $cleanWa = preg_replace('/[^0-9]/', '', $receptionWa);
                        if (str_starts_with($cleanWa, '05')) { $cleanWa = '966' . substr($cleanWa, 1); }
                    @endphp
                    <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('مرحباً منصة دارك، أرغب في الاستفسار السريع عن الوحدات المتوفرة اليوم.') }}" 
                       target="_blank"
                       class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/30">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>تواصل فوري مع الاستقبال</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 2: PROMINENT INTERACTIVE DISCOVERY CARDS ("من نحن" + "خدماتنا")
         ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card A: من نحن وتوجيهات الأمان -->
        <a href="{{ route('client.about') }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-white border-2 border-amber-300/80 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-amber-500 transition duration-300 flex flex-col justify-between">
            <div class="flex items-start gap-4">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-slate-950 flex items-center justify-center text-2xl flex-shrink-0 shadow-md group-hover:scale-110 group-hover:rotate-3 transition duration-300">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                            معايير الجودة والموثوقية
                        </span>
                        <i class="fa-solid fa-arrow-left text-amber-600 transform group-hover:-translate-x-1.5 transition text-xs"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-amber-700 transition">
                        لماذا يفضل العملاء والمُلاك منصة دارك؟ (من نحن)
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        نهتم باختيار وتدقيق العين العقارية لضمان أعلى معايير الجودة، النظافة، والتأثيث الفاخر، مع متابعة مستمرة وتواصل مباشر عبر الواتساب.
                    </p>
                </div>
            </div>

            <!-- Mini Checklist -->
            <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-[11px] font-bold text-amber-900">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> جودة موثوقة</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> مواقع مميزة</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> دعم دائم 24/7</span>
            </div>
        </a>

        <!-- Card B: خدماتنا العقارية المتكاملة -->
        <a href="{{ route('client.services') }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500/10 via-cyan-500/5 to-white border-2 border-indigo-200 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-indigo-500 transition duration-300 flex flex-col justify-between">
            <div class="flex items-start gap-4">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md group-hover:scale-110 group-hover:-rotate-3 transition duration-300">
                    <i class="fa-solid fa-layer-group text-cyan-300"></i>
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                            حلول عقارية متكاملة
                        </span>
                        <i class="fa-solid fa-arrow-left text-indigo-600 transform group-hover:-translate-x-1.5 transition text-xs"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-indigo-700 transition">
                        باقة خدماتنا المتخصصة (تأجير، بيع، وإدارة أملاك)
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        تأجير يومي فندقي، تأجير شهري وسنوي عائلي، وساطة وبيع العقارات باحترافية، وإدارة وتشغيل الوحدات السكنية لتحقيق أعلى عائد استثماري.
                    </p>
                </div>
            </div>

            <!-- Mini Checklist -->
            <div class="mt-4 pt-3 border-t border-indigo-100 flex items-center justify-between text-[11px] font-bold text-indigo-900">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-star text-amber-500"></i> إقامات قصيرة</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-house-chimney text-cyan-600"></i> إيجار سنوي</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-key text-emerald-600"></i> تشغيل وإدارة</span>
            </div>
        </a>
    </div>

    <!-- =========================================================================
         SECTION 3: SECURITY ADVISORY (تنويه أمني وحماية النزلاء)
         ========================================================================= -->
    <div class="bg-gradient-to-r from-amber-50 via-amber-50/70 to-white rounded-2xl border-2 border-amber-300/80 p-5 sm:p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <h3 class="font-black text-base text-amber-950 flex items-center gap-2">
                        <span>إرشادات أمان وضمان الحجز للعملاء:</span>
                    </h3>
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full inline-block">
                        حمايتك مسؤوليتنا الأولى
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs text-slate-800">
                    <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-circle-exclamation text-amber-600 flex-shrink-0"></i>
                        <span>احذر من المحتالين والحسابات الوهمية أو الوسطاء غير المعتمدين.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 flex-shrink-0"></i>
                        <span class="font-bold text-amber-950">التعامل والحجز يكون حصرياً عبر منصة دارك المعتمدة.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-ban text-rose-600 flex-shrink-0"></i>
                        <span>عدم تحويل أي مبالغ إلى حسابات شخصية عشوائية غير مسجلة في الفواتير.</span>
                    </div>

                    <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm p-2.5 rounded-xl border border-amber-200 shadow-2xs">
                        <i class="fa-solid fa-building-circle-check text-sky-600 flex-shrink-0"></i>
                        <span>التأكد من بيانات الوحدة ورقم الدور قبل إتمام الدفع أو الاستلام.</span>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex items-center gap-2 bg-amber-100/90 p-2.5 rounded-xl border border-amber-300 text-amber-950 font-black">
                        <i class="fa-solid fa-stamp text-amber-700 flex-shrink-0"></i>
                        <span>المنصة هي القناة المعتمدة لتنظيم ومتابعة عمليات الإيجار وتسليم المفاتيح بالبرج.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 4: ADVANCED FILTER & SEARCH BAR
         ========================================================================= -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
            <h3 class="font-black text-sm text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-amber-500"></i>
                <span>تصفية وتحديد الشقق المتاحة:</span>
            </h3>
            @if(request()->hasAny(['floor', 'apartment_number', 'rooms']))
                <a href="{{ route('client.apartments.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 transition">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>إلغاء التصفية</span>
                </a>
            @endif
        </div>

        <form action="{{ route('client.apartments.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1.5">الدور السكني:</label>
                <select name="floor" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
                    <option value="">جميع الأدوار</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>الدور {{ $floor }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1.5">رقم الشقة:</label>
                <input type="text" name="apartment_number" value="{{ request('apartment_number') }}" placeholder="مثال: 101، 204..."
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1.5">عدد الغرف:</label>
                <select name="rooms" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:bg-white focus:outline-none transition">
                    <option value="">أي عدد غرف</option>
                    <option value="1" {{ request('rooms') == 1 ? 'selected' : '' }}>غرفة واحدة فاخرة</option>
                    <option value="2" {{ request('rooms') == 2 ? 'selected' : '' }}>غرفتان فأكثر (عائلي)</option>
                    <option value="3" {{ request('rooms') == 3 ? 'selected' : '' }}>3 غرف فأكثر (كبير)</option>
                </select>
            </div>

            <div class="flex items-end gap-2 col-span-2 sm:col-span-1">
                <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-amber-500/20 active:scale-95">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>بحث وتصفية</span>
                </button>
            </div>
        </form>
    </div>

    <!-- =========================================================================
         SECTION 5: APARTMENTS SHOWCASE GRID
         ========================================================================= -->
    @if($apartments->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-4">
            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full mx-auto flex items-center justify-center text-3xl">
                <i class="fa-solid fa-door-closed"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">لا توجد شقق متاحة حالياً وفق هذه المعايير</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    يرجى تعديل معايير البحث أو اختيار دور آخر، أو تواصل مع الاستقبال لتوفير أقرب وحدة مناسبة لك.
                </p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ route('client.apartments.index') }}" class="text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 px-5 py-2.5 rounded-xl transition">
                    عرض جميع الشقق
                </a>
                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('مرحباً، أبحث عن شقة بمواصفات معينة ولم أجدها بالقائمة.') }}" target="_blank" class="text-xs font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 px-5 py-2.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>طلب وحدة مخصصة من الاستقبال</span>
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($apartments as $apt)
                @php
                    $coverImage = $apt->images->first();
                    $hasVideo = $apt->videos->isNotEmpty();
                    $featuresArr = $apt->features ? array_slice(array_map('trim', explode(',', $apt->features)), 0, 3) : [];
                @endphp
                
                <div class="apt-card flex flex-col justify-between group">
                    <div>
                        <!-- Image Container with Overlays -->
                        <div class="apt-img-wrap h-52 sm:h-56 bg-slate-900">
                            @if($coverImage)
                                <img src="{{ asset('storage/' . $coverImage->file_path) }}" 
                                     alt="شقة {{ $apt->apartment_number }}" 
                                     class="apt-img w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-b from-slate-800 to-slate-950 space-y-2">
                                    <i class="fa-solid fa-building text-3xl text-slate-600"></i>
                                    <span class="text-xs font-bold text-slate-400">جاري تجهيز الصور الفندقية</span>
                                </div>
                            @endif

                            <!-- Top Badges: Apartment & Floor -->
                            <div class="absolute top-3 right-3 flex items-center gap-1.5 z-10">
                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-950/85 backdrop-blur-md text-amber-400 border border-amber-500/30 shadow-md">
                                    شقة {{ $apt->apartment_number }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-white/95 backdrop-blur-md text-slate-800 shadow-md">
                                    الدور {{ $apt->floor_number }}
                                </span>
                            </div>

                            <!-- Video Play Indicator -->
                            @if($hasVideo)
                                <div class="absolute bottom-3 right-3 z-10">
                                    <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-600/90 backdrop-blur-md text-white flex items-center gap-1.5 shadow-md">
                                        <i class="fa-solid fa-circle-play text-white animate-pulse"></i>
                                        <span>فيديو جولة</span>
                                    </span>
                                </div>
                            @endif

                            <!-- Featured or Available Badge -->
                            <div class="absolute bottom-3 left-3 z-10">
                                @if($apt->is_featured_on_homepage)
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md flex items-center gap-1.5">
                                        <i class="fa-solid fa-crown text-[10px] crown-anim"></i>
                                        <span>مختارة ومميزة</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-600 text-white shadow-md flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                        <span>متاحة للحجز الآن</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Apartment Specs & Content -->
                        <div class="p-5 space-y-4">
                            <!-- Specifications Row -->
                            <div class="flex items-center flex-wrap gap-2 text-xs text-slate-700 pb-3 border-b border-slate-100">
                                <span class="flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-100">
                                    <i class="fa-solid fa-bed text-amber-500"></i>
                                    <span>{{ $apt->rooms_count }} غرف</span>
                                </span>
                                @if($apt->living_rooms_count)
                                    <span class="flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-100">
                                        <i class="fa-solid fa-couch text-amber-500"></i>
                                        <span>{{ $apt->living_rooms_count }} صالة</span>
                                    </span>
                                @endif
                                <span class="flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-100">
                                    <i class="fa-solid fa-bath text-amber-500"></i>
                                    <span>{{ $apt->bathrooms_count }} حمام</span>
                                </span>
                                @if($apt->view_type)
                                    <span class="text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-100 truncate max-w-[130px] font-medium" title="{{ $apt->view_type }}">
                                        <i class="fa-solid fa-mountain-sun text-slate-400 ml-1"></i>{{ $apt->view_type }}
                                    </span>
                                @endif
                            </div>

                            <!-- Features Pill List -->
                            @if(!empty($featuresArr))
                                <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-slate-600">
                                    @foreach($featuresArr as $feat)
                                        <span class="px-2 py-0.5 rounded-lg bg-amber-50 border border-amber-200/60 text-amber-900 font-semibold">
                                            ✓ {{ $feat }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Price Section -->
                            <div class="flex items-baseline justify-between pt-1">
                                <div>
                                    <span class="text-[11px] text-slate-400 block font-semibold">سعر الإقامة:</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-black text-slate-900 font-mono">{{ number_format($apt->daily_price, 0) }}</span>
                                        <span class="text-xs font-black text-amber-700">ريال / ليلة</span>
                                    </div>
                                </div>
                                <div class="text-left text-xs">
                                    <span class="text-slate-400 block text-[10px]">تأمين مسترد</span>
                                    <span class="font-bold text-slate-700 font-mono">{{ number_format($apt->insurance_amount, 0) }} ر.س</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Bottom Action Buttons (Dual conversion buttons) -->
                    <div class="p-5 pt-0">
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                            <!-- View Details & Book -->
                            <a href="{{ route('client.apartments.show', $apt->id) }}" 
                               class="py-3 bg-slate-950 hover:bg-slate-800 text-white font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                                <i class="fa-solid fa-eye text-amber-400"></i>
                                <span>تفاصيل وحجز</span>
                            </a>

                            <!-- Instant WhatsApp Enquiry with Unit Details -->
                            @php
                                $unitMsg = "مرحباً منصة دارك، أرغب في الاستفسار عن الشقة رقم ({$apt->apartment_number}) بالدور ({$apt->floor_number}) بسعر ({$apt->daily_price} ريال/ليلة). هل هي متاحة اليوم؟";
                            @endphp
                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode($unitMsg) }}" 
                               target="_blank"
                               class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-600/20 active:scale-95">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>حجز واتساب</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $apartments->links() }}
        </div>
    @endif
</div>
@endsection

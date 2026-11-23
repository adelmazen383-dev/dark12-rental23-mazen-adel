@extends('layouts.client')

@section('title', 'منصة دارك لتنظيم الإيجار - تصفح وحجز الوحدات السكنية الفاخرة')

@section('content')
<div class="space-y-6">
    <style>
        /* ==========================================================================
           APARTMENTS VIBRANT ANIMATIONS & MOTION EFFECTS
           ========================================================================== */
        @keyframes floatHeroCard {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes pulseGlowGold {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.2); }
            50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.45); }
        }
        @keyframes crownTilt {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-12deg); }
            75% { transform: rotate(12deg); }
        }
        @keyframes playPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.25); opacity: 0.8; }
        }
        @keyframes shimmerSweep {
            0% { transform: translateX(100%) rotate(25deg); }
            100% { transform: translateX(-200%) rotate(25deg); }
        }
        @keyframes liveDot {
            0% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.6); opacity: 0; }
            100% { transform: scale(0.9); opacity: 0; }
        }

        .anim-crown { animation: crownTilt 3s ease-in-out infinite; }
        .anim-play { animation: playPulse 1.8s ease-in-out infinite; }
        .anim-hero-glow { animation: pulseGlowGold 4s infinite; }

        /* Apartment Card 3D Tilt & Lift */
        .apartment-card {
            background: #FFFFFF;
            border-radius: 1.5rem;
            border: 1px solid #E2E8F0;
            overflow: hidden;
            position: relative;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .apartment-card:hover {
            transform: translateY(-8px) scale(1.01);
            border-color: #F59E0B;
            box-shadow: 0 20px 40px -15px rgba(245, 158, 11, 0.25), 0 8px 20px -6px rgba(15, 23, 42, 0.1);
        }

        /* Image Shimmer Light Sweep on Hover */
        .image-container {
            position: relative;
            overflow: hidden;
        }
        .image-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.25) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(30deg) translateX(150%);
            pointer-events: none;
            transition: transform 0.8s ease;
        }
        .apartment-card:hover .image-container::after {
            transform: rotate(30deg) translateX(-150%);
        }

        .apartment-card:hover .apartment-photo {
            transform: scale(1.07);
        }
        .apartment-photo {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Specification Pill Interactive Bounce */
        .spec-pill {
            transition: all 0.25s ease;
        }
        .spec-pill:hover {
            background-color: #FEF3C7;
            color: #92400E;
            transform: translateY(-2px);
            border-color: #FDE68A;
        }
        .spec-pill:hover i {
            transform: scale(1.2);
        }
        .spec-pill i {
            transition: transform 0.25s ease;
        }

        /* WhatsApp Button Radar Wave */
        .wa-btn-pulse {
            position: relative;
            overflow: hidden;
        }
        .wa-btn-pulse:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
        }

        /* Radar Ping */
        .radar-ping {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background-color: #10B981;
            animation: liveDot 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>

    <!-- =========================================================================
         SECTION 1: HERO HEADER (Welcome & Quick Stats - Clean without clutter)
         ========================================================================= -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950 text-white p-6 sm:p-8 shadow-xl border border-amber-500/30 anim-hero-glow">
        <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-indigo-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Brand & Info -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-right gap-4 flex-1">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-white p-1 shadow-xl border-2 border-amber-400 flex-shrink-0 group hover:scale-105 transition duration-300">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
                </div>

                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <span>البوابة الرسمية لتنظيم وتوثيق وحجز الشقق السكنية</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        منصة دَارِك لتنظيم الإيجار
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl font-medium">
                        تصفح الوحدات المتاحة واحجز إقامتك بكل سهولة وأمان، مع استلام فوري للمفاتيح عبر كود الاستقبال المعتمد.
                    </p>
                </div>
            </div>

            <!-- Live Status & Platform Protection -->
            <div class="flex-shrink-0 w-full md:w-auto">
                <div class="bg-slate-900/90 backdrop-blur-md border border-white/10 p-4 rounded-2xl shadow-lg space-y-3 text-center sm:text-right">
                    <div class="flex items-center justify-between gap-3 text-xs">
                        <span class="flex items-center gap-2 font-bold text-emerald-400">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="radar-ping"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span>المنصة متصلة وجاهزة</span>
                        </span>
                        <span class="font-mono font-bold text-amber-400 text-xs bg-amber-400/10 px-2 py-0.5 rounded-md">
                            {{ $apartments->total() ?? $apartments->count() }} شقة متاحة
                        </span>
                    </div>

                    <div class="p-2 rounded-xl bg-white/5 border border-white/5 text-[11px] text-slate-300">
                        <i class="fa-solid fa-shield-check text-emerald-400 ml-1"></i>
                        <span>حجز وتوثيق ومحادثات آمنة 100% داخل الموقع</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 2: INSTRUCTIONS & SAFETY ADVISORY (تعليمات وتوجيهات الحجز الهامة)
         ========================================================================= -->
    <div class="bg-gradient-to-r from-amber-50 via-amber-50/70 to-white rounded-2xl border-2 border-amber-300/80 p-5 sm:p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-1 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <h3 class="font-black text-base text-amber-950 flex items-center gap-2">
                        <span>تعليمات أمنية وإرشادات هامة للنزلاء:</span>
                    </h3>
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full inline-block self-start sm:self-auto">
                        لضمان حجز آمن وموثق
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs text-slate-800">
                    <div class="flex items-center gap-2.5 bg-white/90 backdrop-blur-sm p-3 rounded-xl border border-amber-200 shadow-2xs hover:border-amber-300 transition">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-sm flex-shrink-0"></i>
                        <span>احذر من المحتالين والحسابات الوهمية؛ لا تتعامل خارج المنصة.</span>
                    </div>

                    <div class="flex items-center gap-2.5 bg-white/90 backdrop-blur-sm p-3 rounded-xl border border-amber-200 shadow-2xs hover:border-amber-300 transition">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm flex-shrink-0"></i>
                        <span class="font-bold text-amber-950">الحجز وتوليد كود الدخول يتم حصرياً عبر منصة دارك.</span>
                    </div>

                    <div class="flex items-center gap-2.5 bg-white/90 backdrop-blur-sm p-3 rounded-xl border border-amber-200 shadow-2xs hover:border-amber-300 transition">
                        <i class="fa-solid fa-ban text-rose-600 text-sm flex-shrink-0"></i>
                        <span>عدم تحويل أي مبالغ إلى حسابات شخصية عشوائية غير مسجلة بالفاتورة.</span>
                    </div>

                    <div class="flex items-center gap-2.5 bg-white/90 backdrop-blur-sm p-3 rounded-xl border border-amber-200 shadow-2xs hover:border-amber-300 transition">
                        <i class="fa-solid fa-building-circle-check text-sky-600 text-sm flex-shrink-0"></i>
                        <span>التأكد من رقم الشقة والدور وبيانات الإقامة قبل إتمام الحجز.</span>
                    </div>

                    <div class="col-span-1 md:col-span-2 flex items-center gap-2.5 bg-amber-100/90 p-3 rounded-xl border border-amber-300 text-amber-950 font-bold">
                        <i class="fa-solid fa-stamp text-amber-700 text-sm flex-shrink-0"></i>
                        <span>المنصة هي القناة المعتمدة لتنظيم عمليات الإيجار وتسليم المفتاح عبر كود مشفر عند الاستقبال.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 3: APARTMENTS SHOWCASE GRID (عالي الأنيميشن والحركة)
         ========================================================================= -->
    @if($apartments->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-4">
            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full mx-auto flex items-center justify-center text-3xl">
                <i class="fa-solid fa-door-closed"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base sm:text-lg font-black text-slate-900">لا توجد شقق متاحة حالياً للحجز</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    لا تتوفر وحدات شاغرة في الوقت الحالي، يرجى معاودة الزيارة لاحقاً.
                </p>
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
                
                <div class="apartment-card flex flex-col justify-between group">
                    <div>
                        <!-- Image Container with Overlays & Light Sweep -->
                        <div class="image-container h-52 sm:h-56 bg-slate-900">
                            @if($coverImage)
                                <img src="{{ asset('storage/' . $coverImage->file_path) }}" 
                                     alt="شقة {{ $apt->apartment_number }}" 
                                     class="apartment-photo w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('images/apartment-default.svg') }}';">
                            @else
                                <img src="{{ asset('images/apartment-default.svg') }}" 
                                     alt="شقة {{ $apt->apartment_number }}" 
                                     class="apartment-photo w-full h-full object-cover">
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

                            <!-- Video Indicator with Pulse Animation -->
                            @if($hasVideo)
                                <div class="absolute bottom-3 right-3 z-10">
                                    <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-rose-600/95 backdrop-blur-md text-white flex items-center gap-1.5 shadow-md">
                                        <i class="fa-solid fa-circle-play text-white anim-play"></i>
                                        <span>فيديو جولة</span>
                                    </span>
                                </div>
                            @endif

                            <!-- Featured or Available Badge -->
                            <div class="absolute bottom-3 left-3 z-10">
                                @if($apt->is_featured_on_homepage)
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-md flex items-center gap-1.5">
                                        <i class="fa-solid fa-crown text-[10px] anim-crown"></i>
                                        <span>مختارة ومميزة</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-600 text-white shadow-md flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                        <span>متاحة للحجز</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Apartment Specs & Content -->
                        <div class="p-5 space-y-3.5">
                            <!-- Specifications Row with Interactive Pills -->
                            <div class="flex items-center flex-wrap gap-2 text-xs text-slate-700 pb-3 border-b border-slate-100">
                                <span class="spec-pill flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200">
                                    <i class="fa-solid fa-bed text-amber-500"></i>
                                    <span>{{ $apt->rooms_count }} غرف</span>
                                </span>
                                @if($apt->living_rooms_count)
                                    <span class="spec-pill flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200">
                                        <i class="fa-solid fa-couch text-amber-500"></i>
                                        <span>{{ $apt->living_rooms_count }} صالة</span>
                                    </span>
                                @endif
                                <span class="spec-pill flex items-center gap-1.5 font-bold bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200">
                                    <i class="fa-solid fa-bath text-amber-500"></i>
                                    <span>{{ $apt->bathrooms_count }} حمام</span>
                                </span>
                                @if($apt->view_type)
                                    <span class="spec-pill text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200 truncate max-w-[130px] font-medium" title="{{ $apt->view_type }}">
                                        <i class="fa-solid fa-mountain-sun text-slate-400 ml-1"></i>{{ $apt->view_type }}
                                    </span>
                                @endif
                            </div>

                            <!-- Features List -->
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
                                        <span class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ number_format($apt->daily_price, 0) }}</span>
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

                    <!-- Card Actions -->
                    <div class="p-5 pt-0">
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                            <!-- Details & Book Button -->
                            <a href="{{ route('client.apartments.show', $apt->id) }}" 
                               class="py-3 bg-slate-950 hover:bg-slate-800 text-white font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                                <i class="fa-solid fa-eye text-amber-400"></i>
                                <span>تفاصيل وحجز</span>
                            </a>

                            <!-- Direct Platform Chat with Landlord (Instant Alert) -->
                            <a href="{{ route('client.apartments.show', $apt->id) }}#card-chat" 
                               class="py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                                <i class="fa-solid fa-comments text-sm text-slate-950"></i>
                                <span>محادثة المالك</span>
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

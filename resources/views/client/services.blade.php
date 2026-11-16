@extends('layouts.client')

@section('title', 'خدماتنا المتخصصة - منصة دارك لتنظيم الإيجار')

@section('content')
<div class="services-wrapper -mt-2 -mx-3 sm:-mx-6 pb-12 overflow-hidden">
    <style>
        /* ==========================================================================
           ULTRA MODERN LUXURY & CYBER-CHIC SERVICES DESIGN SYSTEM
           ========================================================================== */
        :root {
            --srv-primary: #6366F1;
            --srv-primary-dark: #4338CA;
            --srv-secondary: #06B6D4;
            --srv-accent: #F59E0B;
            --srv-emerald: #10B981;
            --srv-rose: #F43F5E;
            --srv-dark: #0B0F19;
            --srv-card-bg: #111827;
            --srv-border: rgba(255, 255, 255, 0.08);
        }

        /* Ambient Glow & Mesh Background */
        .cyber-bg {
            background-color: #070B14;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(245, 158, 11, 0.08) 0px, transparent 60%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(244, 63, 94, 0.1) 0px, transparent 50%);
            position: relative;
        }

        /* Animated Grid Pattern */
        .cyber-grid {
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Floating Orbs */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-25px) rotate(180deg) scale(1.08); }
        }
        @keyframes floatReverse {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(20px) rotate(-180deg) scale(0.95); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; filter: blur(40px); }
            50% { opacity: 0.8; filter: blur(55px); }
        }
        @keyframes textShimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes borderPulse {
            0%, 100% { border-color: rgba(99, 102, 241, 0.3); box-shadow: 0 0 15px rgba(99, 102, 241, 0.2); }
            50% { border-color: rgba(6, 182, 212, 0.6); box-shadow: 0 0 30px rgba(6, 182, 212, 0.35); }
        }
        @keyframes radarWave {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        @keyframes badgeBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .anim-float { animation: floatSlow 12s ease-in-out infinite; }
        .anim-float-rev { animation: floatReverse 15s ease-in-out infinite; }
        .anim-pulse-glow { animation: pulseGlow 6s ease-in-out infinite; }
        .badge-bounce { animation: badgeBounce 3s ease-in-out infinite; }

        /* Shimmering Text Gradient */
        .shimmer-text {
            background: linear-gradient(90deg, #FFFFFF 0%, #A5B4FC 25%, #38BDF8 50%, #FDE047 75%, #FFFFFF 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: textShimmer 6s linear infinite;
        }

        /* 3D Holographic Neumorphic Bento Cards */
        .bento-card {
            background: rgba(17, 24, 39, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.75rem;
            position: relative;
            overflow: hidden;
            transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
        }

        .bento-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(800px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255, 255, 255, 0.08), transparent 40%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 2;
        }

        .bento-card:hover {
            transform: translateY(-8px) scale(1.01);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.25), 0 0 35px -5px rgba(6, 182, 212, 0.2), inset 0 1px 0 0 rgba(255, 255, 255, 0.2);
        }

        .bento-card:hover::before {
            opacity: 1;
        }

        /* Glowing Edge Strip on Top */
        .glow-strip-indigo { background: linear-gradient(90deg, #6366F1, #818CF8, #06B6D4); }
        .glow-strip-amber { background: linear-gradient(90deg, #F59E0B, #FBBF24, #EF4444); }
        .glow-strip-emerald { background: linear-gradient(90deg, #10B981, #34D399, #06B6D4); }
        .glow-strip-cyan { background: linear-gradient(90deg, #06B6D4, #38BDF8, #818CF8); }

        /* Neon Tag */
        .neon-tag {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        /* 3D Icon Box */
        .icon-box-3d {
            width: 58px;
            height: 58px;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.4), inset 0 1px 1px 0 rgba(255, 255, 255, 0.2);
        }

        .bento-card:hover .icon-box-3d {
            transform: scale(1.12) rotate(-6deg);
        }

        /* Interactive Filter Tab */
        .tab-btn {
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.45);
        }
        .tab-btn:not(.active) {
            background: rgba(255, 255, 255, 0.05);
            color: #94A3B8;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .tab-btn:not(.active):hover {
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Action Buttons with Flowing Glow */
        .btn-order-glow {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1E293B, #0F172A);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 12px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
        }
        .btn-order-glow:hover {
            border-color: rgba(99, 102, 241, 0.8);
            background: linear-gradient(135deg, #4F46E5, #6366F1);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: #FFFFFF;
        }

        /* Scroll Reveal Animation Classes */
        .reveal-node {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-node.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Calculator Interactive Card */
        .calc-input-range {
            -webkit-appearance: none;
            width: 100%;
            height: 8px;
            border-radius: 5px;
            background: #1F2937;
            outline: none;
        }
        .calc-input-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #F59E0B;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.7);
            border: 2px solid #FFFFFF;
        }

        /* Modal styling */
        .cyber-modal {
            background: rgba(11, 15, 25, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), 0 0 40px rgba(99, 102, 241, 0.2);
        }
    </style>

    <!-- =========================================================================
         SECTION 1: HERO SPOTLIGHT (Split-screen 3D dynamic presentation)
         ========================================================================= -->
    <div class="cyber-bg cyber-grid text-white px-4 sm:px-8 pt-10 pb-16 sm:pb-24 rounded-3xl mb-10 border border-white/10 overflow-hidden relative">
        <!-- Floating Ambient Glow Orbs -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-600/25 anim-pulse-glow pointer-events-none"></div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full bg-cyan-500/20 anim-pulse-glow pointer-events-none" style="animation-delay: -3s;"></div>
        <div class="absolute -bottom-20 right-1/3 w-72 h-72 rounded-full bg-amber-500/15 anim-pulse-glow pointer-events-none" style="animation-delay: -5s;"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <!-- Breadcrumbs / Top Badge -->
            <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-300 text-xs font-bold badge-bounce">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-cyan-500"></span>
                    </span>
                    <span>خدماتنا المتخصصة والشاملة</span>
                    <i class="fa-solid fa-sparkles text-amber-400 text-xs"></i>
                </div>

                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-white/5 px-3 py-1.5 rounded-full border border-white/5">
                    <a href="{{ route('client.apartments.index') }}" class="hover:text-amber-400 transition">الرئيسية</a>
                    <span>/</span>
                    <span class="text-amber-400 font-extrabold">باقة الخدمات</span>
                </div>
            </div>

            <!-- Hero Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left/Right Text Column (RTL: Right side) -->
                <div class="lg:col-span-7 space-y-5 text-right">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black leading-tight tracking-tight">
                        <span class="block text-white mb-1">حلول عقارية متكاملة</span>
                        <span class="shimmer-text">تلبي جميع تطلعاتك</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-300 font-medium leading-relaxed max-w-2xl">
                        نقدم منظومة متقدمة من الخدمات العقارية المصممة بعناية لتوفير أقصى درجات الراحة، الشفافية، والسهولة في حجز وتسويق وإدارة العقارات وفق أحدث المعايير التنظيمية المعتمدة بالمملكة.
                    </p>

                    <!-- Fast Feature Highlights Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-2 text-xs">
                        <div class="flex items-center gap-2 bg-white/5 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                            <i class="fa-solid fa-bolt text-amber-400 text-base"></i>
                            <span class="font-bold text-slate-200">حجز وتأكيد فوري</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/5 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                            <i class="fa-solid fa-shield-halved text-emerald-400 text-base"></i>
                            <span class="font-bold text-slate-200">عقود معتمدة رسمياً</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-center gap-2 bg-white/5 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                            <i class="fa-solid fa-headset text-cyan-400 text-base"></i>
                            <span class="font-bold text-slate-200">دعم واستقبال 24/7</span>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-4">
                        <a href="#services-grid" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-slate-950 font-black text-xs sm:text-sm hover:scale-105 transition shadow-lg shadow-amber-500/25 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>استكشف كافة الخدمات أدناه</span>
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </a>

                        <a href="{{ route('investor.register') }}" 
                           class="px-5 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-handshake text-slate-950 text-base"></i>
                            <span>انضم كمستثمر ووفر وحدتك معنا</span>
                        </a>
                    </div>
                </div>

                <!-- 3D Interactive Isometric Card Display (Left side) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-sm">
                        <!-- Holographic Ring Backing -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-cyan-400 rounded-3xl blur-2xl opacity-30 anim-pulse-glow"></div>

                        <!-- Card Body -->
                        <div class="relative bg-gradient-to-b from-slate-900/90 to-slate-950/90 border border-white/20 p-6 rounded-3xl shadow-2xl backdrop-blur-xl space-y-4">
                            <!-- Card Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-500/30">
                                        <i class="fa-solid fa-building-flag"></i>
                                    </div>
                                    <div>
                                        <span class="block font-black text-sm text-white">منصة دَارِك</span>
                                        <span class="text-[10px] text-slate-400">نظام إدارة وتأجير العقارات الذكي</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    خدمات متزامنة
                                </span>
                            </div>

                            <!-- Interactive Live Stats Mini Tickers -->
                            <div class="space-y-2.5 text-xs">
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition">
                                    <div class="flex items-center gap-2 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>تأجير يومي فاخر</span>
                                    </div>
                                    <span class="font-black text-amber-400 font-mono">حجز لحظي 100%</span>
                                </div>

                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition">
                                    <div class="flex items-center gap-2 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                        <span>تأجير شهري وسنوي</span>
                                    </div>
                                    <span class="font-black text-cyan-300 font-mono">عقود معتمدة</span>
                                </div>

                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition">
                                    <div class="flex items-center gap-2 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span>تشغيل وتسويق الوحدات</span>
                                    </div>
                                    <span class="font-black text-indigo-300 font-mono">أعلى عائد للمالك</span>
                                </div>

                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition">
                                    <div class="flex items-center gap-2 text-slate-300">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        <span>وساطة وبيع العقارات</span>
                                    </div>
                                    <span class="font-black text-rose-300 font-mono">تسويق احترافي 4K</span>
                                </div>
                            </div>

                            <!-- Micro Trust Badge -->
                            <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                                <span class="flex items-center gap-1 text-emerald-400">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>نظام آمن ومحمي 100%</span>
                                </span>
                                <span>خدمة الاستقبال متوفرة 24 ساعة</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 2: INTERACTIVE FILTER TABS
         ========================================================================= -->
    <div class="max-w-6xl mx-auto px-4 mb-8" id="services-grid">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-900/80 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-white/10 shadow-lg">
            <div>
                <h3 class="text-white font-black text-base flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-400"></i>
                    <span>تصفح الخدمات حسب اهتمامك:</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">اختر الفئة للتركيز على ما يهمك فوراً</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0" id="filter-tabs-container">
                <button onclick="filterServices('all', this)" class="tab-btn active">
                    <i class="fa-solid fa-asterisk text-[10px] ml-1"></i>
                    <span>جميع الخدمات</span>
                </button>
                <button onclick="filterServices('tenant', this)" class="tab-btn">
                    <i class="fa-solid fa-key text-[10px] ml-1"></i>
                    <span>للنزلاء والمستأجرين</span>
                </button>
                <button onclick="filterServices('investor', this)" class="tab-btn">
                    <i class="fa-solid fa-chart-line text-[10px] ml-1"></i>
                    <span>للملاك والمستثمرين</span>
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 3: THE CORE BENTO-GRID OF SERVICES (4 Major Pillars from Screenshot + 2 Extras)
         ========================================================================= -->
    <div class="max-w-6xl mx-auto px-4 mb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- ===================== SERVICE 1: التأجير اليومي ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="tenant">
                <div class="h-1.5 w-full glow-strip-indigo"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                <i class="fa-solid fa-star text-amber-400"></i>
                                <span>خيار شائع للإقامات القصيرة</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">01</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-indigo-500 to-indigo-700 text-white text-2xl shadow-indigo-500/30">
                                <i class="fa-solid fa-house-chimney-window"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">التأجير اليومي</h3>
                                <p class="text-xs text-indigo-300 font-bold">إقامات قصيرة شائقة ومجهزة بالكامل</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            نوفر شقق وأجنحة سكنية مجهزة بأرقى الأثاث والديكورات الحديثة، مع تعقيم ونظافة فندقية كاملة قبل كل استلام لضمان تجربة إقامة استثنائية.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>شقق مفروشة ومجهزة بأحدث الأجهزة الذكية والتكييف.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>خيارات متنوعة تناسب الأفراد، العائلات ورجال الأعمال.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تسليم مرن وسريع عبر موظف الاستقبال الميداني.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('التأجير اليومي', 'إقامات شائقة ومجهزة بأحدث الأجهزة والأثاث الفاخر مع حجز سريع')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                        <a href="{{ route('client.apartments.index') }}" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-amber-400 border border-white/10 transition" title="عرض الشقق المتاحة اليوم">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===================== SERVICE 2: التأجير الشهري والسنوي ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="tenant">
                <div class="h-1.5 w-full glow-strip-amber"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fa-solid fa-heart text-rose-400"></i>
                                <span>استقرار وراحة عائلية</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">02</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-amber-500 to-amber-700 text-slate-950 text-2xl shadow-amber-500/30">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">التأجير الشهري والسنوي</h3>
                                <p class="text-xs text-amber-300 font-bold">خيارات سكنية مريحة بمواقع مميزة</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            توفير خيارات سكنية متعددة للإيجار الشهري أو السنوي في أكثر المناطق الحيوية، مع إمكانية توثيق العقد الإلكتروني الموحد لضمان الاستقرار.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>وحدات سكنية بمساحات مختلفة وأدوار متعددة.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>خيارات مفروشة بأناقة أو غير مفروشة حسب رغبتك.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تسهيلات دفع مرنة ومتابعة صيانة دورية للمبنى.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('التأجير الشهري والسنوي', 'عقود موثقة وخيارات سكنية بمساحات ومواقع مميزة تناسب العائلات')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                        <a href="{{ route('client.apartments.index') }}" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-amber-400 border border-white/10 transition" title="تصفح الوحدات">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===================== SERVICE 3: بيع العقارات والوساطة ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="investor">
                <div class="h-1.5 w-full glow-strip-emerald"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <i class="fa-solid fa-handshake text-emerald-400"></i>
                                <span>صفقات عقارية ناجحة وموثوقة</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">03</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-emerald-500 to-emerald-700 text-white text-2xl shadow-emerald-500/30">
                                <i class="fa-solid fa-city"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">بيع العقارات والوساطة</h3>
                                <p class="text-xs text-emerald-300 font-bold">تسويق احترافي ووساطة عقارية معتمدة</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            نسوق العقارات المعروضة للبيع (شقق، فلل، مباني وأبراج، أدوار) بأساليب تسويق عصرية وتصوير سينمائي احترافي يصل للمشتري الجاد والمستثمر.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تسويق احترافي واسع النطاق لعقارك عبر قنواتنا المعتمدة.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تقييم وتثمين العقار وعرضه بصور وفيديو 4K عالي الجودة.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>إشراف قانوني وتوثيق كافة مراحل الإفراغ العقاري بأمان.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('بيع العقارات والوساطة', 'تسويق عقارات البيع والوساطة مع تقييم وتصوير احترافي واستهداف مشترين حقيقيين')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===================== SERVICE 4: إدارة وتسويق الوحدات السكنية ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="investor">
                <div class="h-1.5 w-full glow-strip-cyan"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                <i class="fa-solid fa-gem text-cyan-400"></i>
                                <span>أعلى عائد وأفضل تشغيل استثماري</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">04</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-cyan-500 to-cyan-700 text-slate-950 text-2xl shadow-cyan-500/30">
                                <i class="fa-solid fa-key-skeleton-left-right fa-key"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">إدارة وتسويق الوحدات</h3>
                                <p class="text-xs text-cyan-300 font-bold">حلول شاملة للمُلاك والمستثمرين</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            نقدم للمُلاك والمستثمرين خدمة إدارة الوحدات والعين العقارية من الألف إلى الياء، لتشغيل وحداتك السكنية وتحقيق أعلى دخل دون أي عناء تشغيلي.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تسويق الوحدات الشاغرة بسرعة فائقة وتقليص فترات الخلو.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>متابعة دورية، صيانة شاملة ونظافة فندقية مستمرة.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>لوحة تحكم للمستثمر لمتابعة الحجوزات والأرباح بالريال.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('إدارة وتسويق الوحدات السكنية', 'إدارة كاملة لعقارك، صيانة، نظافة فندقية، وتحصيل عوائد الإيجار شهرياً للمستثمر')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                        <a href="{{ route('investor.register') }}" class="p-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-500/30 transition text-xs font-bold" title="سجل كمستثمر">
                            انضم كمستثمر
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===================== SERVICE 5: خدمة الاستقبال والأمان الميداني ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="tenant">
                <div class="h-1.5 w-full bg-gradient-to-r from-purple-500 via-pink-500 to-indigo-500"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                <i class="fa-solid fa-shield-cat text-purple-400"></i>
                                <span>حماية وأمان وحضور ميداني 24/7</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">05</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-purple-500 to-pink-600 text-white text-2xl shadow-purple-500/30">
                                <i class="fa-solid fa-bell-concierge"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">استقبال البرج والمساندة</h3>
                                <p class="text-xs text-purple-300 font-bold">تسليم ذكي ومساعدة متواصلة</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            خدمة استقبال مخصصة لراحة النزلاء والملاك، تسليم إلكتروني للمفاتيح بكود تحقق مشفر، ومعاينة دقيقة لحالة الوحدة عند الدخول والخروج.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>تسجيل دخول ذكي (Check-in) بكود رقمي موثق.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>منع أي محاولات تسليم عشوائية أو احتيالية.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>فريق جاهز لخدمتك والإجابة عن أي استفسار طوال إقامتك.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('خدمة الاستقبال والمساندة الميدانية', 'تسليم المفاتيح بكود رقمي مشفر، إشراف ومساندة النزيل على مدار 24 ساعة')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===================== SERVICE 6: التوثيق والعقود الرقمية والفوترة ===================== -->
            <div class="bento-card srv-item reveal-node" data-category="investor">
                <div class="h-1.5 w-full bg-gradient-to-r from-sky-400 via-teal-400 to-emerald-500"></div>
                <div class="p-6 sm:p-7 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Header / Badges -->
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="neon-tag bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                <i class="fa-solid fa-file-signature text-sky-400"></i>
                                <span>ضمان نظامي وقانوني 100%</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-mono">06</span>
                        </div>

                        <!-- 3D Icon & Title -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="icon-box-3d bg-gradient-to-br from-teal-500 to-emerald-600 text-white text-2xl shadow-teal-500/30">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white">العقود والفوترة الرسمية</h3>
                                <p class="text-xs text-teal-300 font-bold">توثيق إلكتروني معتمد وسندات قبض</p>
                            </div>
                        </div>

                        <!-- Description & Features List -->
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            إصدار عقود تأجير إلكترونية متوافقة مع الأنظمة الرسمية، إصدار فواتير وسندات قبض معتمدة، وتأمين مسترد يحفظ حق الجميع بلا أي تعقيد.
                        </p>

                        <div class="space-y-2.5 text-xs text-slate-300 pt-2 border-t border-white/10">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>توثيق رسمي للعقد يحمي المالك والمستأجر.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>فواتير ضريبية وسندات قبض فورية لكل عملية دفع.</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i>
                                <span>إدارة نظامية لمبالغ التأمين المستردة بكل شفافية.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <button onclick="openServiceModal('العقود والتوثيق والفوترة الرسمية', 'إصدار عقود معتمدة، فواتير ضريبية، وسندات قبض رسمية لحفظ حقوق جميع الأطراف')" 
                                class="btn-order-glow flex-1">
                            <span>التفاصيل والطلب</span>
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         SECTION 4: INTERACTIVE ROI & PRICING CALCULATOR (دلع وانيميشن تفاعلي)
         ========================================================================= -->
    <div class="max-w-4xl mx-auto px-4 mb-16">
        <div class="relative rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 p-6 sm:p-8 border-2 border-indigo-500/30 shadow-2xl overflow-hidden">
            <div class="absolute -top-12 -left-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- Title -->
                <div class="text-center space-y-2">
                    <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold inline-flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-amber-400"></i>
                        <span>حاسبة العائد والتشغيل التقديري</span>
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white">احسب عائد عقارك التقريبي معنا</h3>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto">
                        سواء كنت ترغب في معرفة تكلفة إقامتك أو حساب العائد التشغيلي المتوقع لشقتك عند إدارتها عبر منصة دارك.
                    </p>
                </div>

                <!-- Calculator Body -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center pt-4">
                    <!-- Controls -->
                    <div class="space-y-4 bg-white/5 p-5 rounded-2xl border border-white/10">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">نوع الخدمة المطلوبة:</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <button type="button" id="calc-type-daily" onclick="setCalcType('daily')" class="p-2.5 rounded-xl font-bold border transition text-center bg-indigo-600 text-white border-indigo-400">
                                    تأجير يومي فندقي
                                </button>
                                <button type="button" id="calc-type-monthly" onclick="setCalcType('monthly')" class="p-2.5 rounded-xl font-bold border transition text-center bg-white/5 text-slate-300 border-white/10 hover:bg-white/10">
                                    إدارة واستثمار شهري
                                </button>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center text-xs font-bold text-slate-300 mb-2">
                                <span>عدد الغرف والتجهيز:</span>
                                <span id="rooms-label" class="text-amber-400 font-mono">2 غرف (جناح عائلي)</span>
                            </div>
                            <input type="range" min="1" max="4" value="2" id="calc-rooms" oninput="updateCalculation()" class="calc-input-range">
                        </div>

                        <div id="duration-wrapper">
                            <div class="flex justify-between items-center text-xs font-bold text-slate-300 mb-2">
                                <span id="duration-text">مدة الإقامة / التشغيل المتوقعة:</span>
                                <span id="duration-label" class="text-cyan-400 font-mono">5 ليالي</span>
                            </div>
                            <input type="range" min="1" max="30" value="5" id="calc-duration" oninput="updateCalculation()" class="calc-input-range">
                        </div>
                    </div>

                    <!-- Realtime Result Display -->
                    <div class="bg-gradient-to-br from-indigo-900/60 to-slate-900/80 p-6 rounded-2xl border border-indigo-400/40 text-center space-y-4 flex flex-col justify-center items-center shadow-inner">
                        <span class="text-xs font-bold text-slate-300 uppercase tracking-wider" id="result-title">
                            تقدير القيمة الإجمالية للإقامة
                        </span>

                        <div class="flex items-baseline justify-center gap-2">
                            <span id="calculated-total" class="text-4xl sm:text-5xl font-black text-amber-400 font-mono tracking-tight">1,750</span>
                            <span class="text-sm font-bold text-slate-300">ريال سعودي</span>
                        </div>

                        <p class="text-[11px] text-slate-400 leading-normal max-w-xs">
                            * هذا التقدير تقريبي ويشمل التجهيزات الفندقية، الصيانة والخدمات الميدانية. يمكنك حجز استشارة دقيقة الآن.
                        </p>

                        <a href="{{ route('investor.register') }}" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-handshake text-sm"></i>
                            <span>تسجيل عقارك والبدء في الاستثمار فوراً</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 5: 4-STEP STREAMLINED WORKFLOW (كيف نعمل؟)
         ========================================================================= -->
    <div class="max-w-6xl mx-auto px-4 mb-16">
        <div class="text-center space-y-2 mb-10">
            <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold inline-flex items-center gap-2">
                <i class="fa-solid fa-arrows-spin text-cyan-400"></i>
                <span>رحلة التعامل السلسة</span>
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">4 خطوات سريعة للبدء</h2>
            <p class="text-xs sm:text-sm text-slate-400 max-w-lg mx-auto">
                من أول نقرة حتى استلام المفتاح أو تحصيل أول أرباح لعقارك
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Step 1 -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-indigo-500/50 transition relative group">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                    1
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">اختيار أو إدراج الوحدة</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    تصفح الوحدات المتاحة للحجز، أو أدرج بيانات عقارك معنا كمستثمر بضغطة زر واحدة.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-amber-500/50 transition relative group">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                    2
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">معاينة وتوثيق ذكي</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    فحص كامل للوحدة وصور وفيديوهات حقيقية، أو طلب معاينة ميدانية مسبقة بكل شفافية.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-emerald-500/50 transition relative group">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                    3
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">دفع آمن وتأكيد فوري</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    طرق دفع إلكترونية رسمية، تحويل بنكي معتمد، وإصدار فواتير وسندات موثقة فوراً.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-cyan-500/50 transition relative group">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xl font-bold mb-4 group-hover:scale-110 transition">
                    4
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">استلام ودعم مستمر</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    استلام المفتاح عبر كود رقمي آمن، ومتابعة استقبال ميدانية مستمرة طوال فترة إقامتك.
                </p>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 6: CLIENT & INVESTOR REVIEWS TICKER
         ========================================================================= -->
    <div class="max-w-6xl mx-auto px-4 mb-16">
        <div class="p-6 sm:p-8 rounded-3xl bg-slate-900/60 border border-white/10 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-comments text-amber-400"></i>
                        <span>آراء عملائنا ومستثمرينا</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">تجارب حقيقية تثبت التزامنا بأعلى معايير الجودة والأمان</p>
                </div>
                <div class="flex items-center gap-1 text-amber-400 text-sm">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span class="text-xs font-black text-white mr-2">4.9 / 5 تقييم عام</span>
                </div>
            </div>

            <!-- Testimonial Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500 text-white font-bold flex items-center justify-center text-xs">
                            ع.م
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-white">عبدالله المنصور</h5>
                            <span class="text-[10px] text-indigo-300">نزيل إقامة يومية</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "الشقة كانت نظيفة جداً مثل الفنادق 5 نجوم والأجهزة كلها تعمل بكفاءة. والتسليم عبر كود البوابة بدون أي تأخير."
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-xs">
                            س.ح
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-white">سعود الحربي</h5>
                            <span class="text-[10px] text-amber-300">مالك 3 وحدات سكنية</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "سلمتهم إدارة الشقق وخلال أول شهر نسبة الإشغال تجاوزت 85% مع تقارير واضحة وعوائد في موعدها بدون وجع رأس."
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-xs">
                            ف.ق
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-white">فيصل القحطاني</h5>
                            <span class="text-[10px] text-emerald-300">مستأجر عقد سنوي</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "توثيق العقد تم بسلاسة وسرعة، وفريق الاستقبال ودودين وخدومين جداً عند أي استفسار أو صيانة."
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 7: FAQ ACCORDION
         ========================================================================= -->
    <div class="max-w-4xl mx-auto px-4 mb-16">
        <div class="text-center space-y-2 mb-8">
            <h3 class="text-2xl font-black text-white">الأسئلة الأكثر شيوعاً حول خدماتنا</h3>
            <p class="text-xs text-slate-400">إجابات واضحة ومباشرة لتوفير وقتك</p>
        </div>

        <div class="space-y-3">
            <!-- FAQ 1 -->
            <div class="rounded-2xl bg-slate-900/80 border border-white/10 overflow-hidden">
                <button onclick="toggleFaq(1)" class="w-full p-4 text-right flex items-center justify-between text-white font-bold text-xs sm:text-sm hover:text-amber-400 transition">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-indigo-400"></i>
                        <span>كيف يتم تأكيد الحجز واستلام المفتاح للشقق المفروشة؟</span>
                    </span>
                    <i id="faq-icon-1" class="fa-solid fa-chevron-down text-xs transition duration-300"></i>
                </button>
                <div id="faq-content-1" class="hidden p-4 pt-0 text-xs text-slate-300 leading-relaxed border-t border-white/5">
                    بمجرد اختيار الشقة وتأكيد الحجز والدفع أو رفع الإيصال، يتم تزويدك بكود حجز رقمي خاص بك. عند وصولك للبرج، يقوم موظف الاستقبال بتسليمك المفتاح ومرافقتك للشقة فور إبراز الكود.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="rounded-2xl bg-slate-900/80 border border-white/10 overflow-hidden">
                <button onclick="toggleFaq(2)" class="w-full p-4 text-right flex items-center justify-between text-white font-bold text-xs sm:text-sm hover:text-amber-400 transition">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-indigo-400"></i>
                        <span>هل يمكنني كمالك أو مستثمر عرض شقتي لإدارتها عبر دارك؟</span>
                    </span>
                    <i id="faq-icon-2" class="fa-solid fa-chevron-down text-xs transition duration-300"></i>
                </button>
                <div id="faq-content-2" class="hidden p-4 pt-0 text-xs text-slate-300 leading-relaxed border-t border-white/5">
                    نعم بالتأكيد! يمكنك التسجيل مباشرة عبر زر "تسجيل كمستثمر"، أو التواصل مع فريقنا الميداني لمعاينة وحدتك والبدء في تشغيلها وتسويقها بأعلى عائد استثماري ممكن.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="rounded-2xl bg-slate-900/80 border border-white/10 overflow-hidden">
                <button onclick="toggleFaq(3)" class="w-full p-4 text-right flex items-center justify-between text-white font-bold text-xs sm:text-sm hover:text-amber-400 transition">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-indigo-400"></i>
                        <span>كيف تضمن المنصة سلامة التعامل وعدم وجود احتيال؟</span>
                    </span>
                    <i id="faq-icon-3" class="fa-solid fa-chevron-down text-xs transition duration-300"></i>
                </button>
                <div id="faq-content-3" class="hidden p-4 pt-0 text-xs text-slate-300 leading-relaxed border-t border-white/5">
                    جميع الحجوزات والمعاملات المالية تتم حصرياً عبر قنوات منصة دارك المعتمدة وحساباتها البنكية الرسمية. كما نوفر موظف استقبال رسمي بالبرج للتحقق وتسليم الوحدات لمنع أي وساطات وهمية.
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 8: HIGH CONVERTING CALL TO ACTION
         ========================================================================= -->
    <div class="max-w-5xl mx-auto px-4">
        <div class="relative rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-950 to-amber-950 p-8 sm:p-12 text-center text-white border-2 border-amber-500/40 shadow-2xl overflow-hidden">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-500/25 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-5 max-w-2xl mx-auto">
                <span class="px-3.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-bold inline-flex items-center gap-2">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>جاهزون لخدمتك على مدار الساعة</span>
                </span>

                <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                    اختر الخدمة التي تناسبك ودع الباقي لفريق دارك المحترف
                </h2>

                <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-medium">
                    سواء كنت تبحث عن سكن فندقي فاخر، عقد شهري أو سنوي مستقر، أو إدارة كاملة لعقارك، نحن خيارك الأوثق والأكثر أماناً.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 pt-4">
                    <a href="{{ route('client.apartments.index') }}" class="px-6 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm transition shadow-lg shadow-amber-500/30 flex items-center gap-2">
                        <i class="fa-solid fa-building-user"></i>
                        <span>تصفح الشقق المتاحة للحجز الآن</span>
                    </a>

                    <a href="{{ route('investor.register') }}"
                       class="px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/15 text-white font-black text-xs sm:text-sm border border-white/20 transition shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-amber-400 text-lg"></i>
                        <span>انضم كمستثمر عقاري</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     INTERACTIVE SERVICE REQUEST MODAL (مع التجهيز الفوري للطلب بالواتساب)
     ========================================================================= -->
<div id="serviceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md hidden transition-all duration-300 opacity-0">
    <div class="cyber-modal rounded-3xl max-w-lg w-full p-6 sm:p-7 relative border border-white/20 transform scale-95 transition-all duration-300" id="modalCard">
        <!-- Close Button -->
        <button onclick="closeServiceModal()" class="absolute top-5 left-5 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Header -->
        <div class="space-y-2 mb-6">
            <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-[11px] font-bold border border-indigo-500/30 inline-block">
                طلب وتفاصيل الخدمة
            </span>
            <h3 id="modalServiceTitle" class="text-xl font-black text-white">عنوان الخدمة</h3>
            <p id="modalServiceDesc" class="text-xs text-slate-300 leading-relaxed">وصف الخدمة هنا...</p>
        </div>

        <!-- Form fields -->
        <div class="space-y-3.5 mb-6 text-xs">
            <div>
                <label class="block font-bold text-slate-300 mb-1">الاسم الكريم:</label>
                <input type="text" id="custName" placeholder="أدخل اسمك الكريم..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-white/15 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">رقم الهاتف أو الجوال:</label>
                <input type="tel" id="custPhone" placeholder="05xxxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-white/15 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400 text-left" dir="ltr">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">ملاحظات أو تفاصيل إضافية:</label>
                <textarea id="custNotes" rows="2" placeholder="أية رغبات خاصة (المدينة، الحي، عدد الغرف، الميزانية)..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-white/15 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400"></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <button onclick="submitServiceRequest()" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/30">
                <i class="fa-solid fa-check text-base"></i>
                <span>تأكيد إرسال الطلب</span>
            </button>
            <button onclick="closeServiceModal()" class="py-3 px-4 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 font-bold text-xs transition">
                إلغاء
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // -------------------------------------------------------------
    // Scroll Reveal Intersection Observer
    // -------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        const revealNodes = document.querySelectorAll('.reveal-node');
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('revealed');
                    }, index * 80);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        revealNodes.forEach(node => observer.observe(node));

        // 3D Card Hover Dynamic Light Angle
        const bentoCards = document.querySelectorAll('.bento-card');
        bentoCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        // Initialize Calculator
        updateCalculation();
    });

    // -------------------------------------------------------------
    // Service Filter System (All / Tenant / Investor)
    // -------------------------------------------------------------
    function filterServices(category, btnElement) {
        // Tab buttons styling
        const container = document.getElementById('filter-tabs-container');
        container.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');

        // Filter cards
        const cards = document.querySelectorAll('.srv-item');
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            if (category === 'all' || cardCat === category) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
    }

    // -------------------------------------------------------------
    // Interactive ROI & Price Calculator
    // -------------------------------------------------------------
    let currentCalcType = 'daily';

    function setCalcType(type) {
        currentCalcType = type;
        const btnDaily = document.getElementById('calc-type-daily');
        const btnMonthly = document.getElementById('calc-type-monthly');
        const durationText = document.getElementById('duration-text');
        const resultTitle = document.getElementById('result-title');

        if (type === 'daily') {
            btnDaily.className = 'p-2.5 rounded-xl font-bold border transition text-center bg-indigo-600 text-white border-indigo-400';
            btnMonthly.className = 'p-2.5 rounded-xl font-bold border transition text-center bg-white/5 text-slate-300 border-white/10 hover:bg-white/10';
            durationText.innerText = 'مدة الإقامة المتوقعة:';
            resultTitle.innerText = 'تقدير القيمة الإجمالية للإقامة';
        } else {
            btnMonthly.className = 'p-2.5 rounded-xl font-bold border transition text-center bg-amber-600 text-white border-amber-400';
            btnDaily.className = 'p-2.5 rounded-xl font-bold border transition text-center bg-white/5 text-slate-300 border-white/10 hover:bg-white/10';
            durationText.innerText = 'مدة الاستثمار / التشغيل بالشهور:';
            resultTitle.innerText = 'صافي العائد التقديري للمالك';
        }
        updateCalculation();
    }

    function updateCalculation() {
        const rooms = parseInt(document.getElementById('calc-rooms').value);
        const duration = parseInt(document.getElementById('calc-duration').value);

        // Update labels
        const roomTexts = {
            1: 'استديو / غرفة مفردة فاخرة',
            2: '2 غرف (جناح عائلي رحب)',
            3: '3 غرف نوم وصالة واسعة',
            4: '4 غرف نوم (شقة بنتهاوس كاملة)'
        };
        document.getElementById('rooms-label').innerText = roomTexts[rooms] || (rooms + ' غرف');

        if (currentCalcType === 'daily') {
            document.getElementById('duration-label').innerText = duration + ' ليالي';
            // Base daily rate estimation
            const baseRates = { 1: 220, 2: 350, 3: 480, 4: 650 };
            const rate = baseRates[rooms] || 350;
            // Discount for longer stay
            let discount = 1;
            if (duration >= 14) discount = 0.85;
            else if (duration >= 7) discount = 0.90;
            
            const total = Math.round(rate * duration * discount);
            animateNumber('calculated-total', total);
        } else {
            document.getElementById('duration-label').innerText = duration + ' شهور';
            // Expected monthly revenue generation for investor
            const monthlyRevenues = { 1: 4500, 2: 7200, 3: 9800, 4: 13500 };
            const monthly = monthlyRevenues[rooms] || 7200;
            const total = monthly * duration;
            animateNumber('calculated-total', total);
        }
    }

    function animateNumber(elementId, targetNumber) {
        const elem = document.getElementById(elementId);
        if (!elem) return;
        const current = parseInt(elem.innerText.replace(/,/g, '')) || 0;
        const diff = targetNumber - current;
        const steps = 15;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            const val = Math.round(current + (diff * (step / steps)));
            elem.innerText = val.toLocaleString();
            if (step >= steps) {
                clearInterval(timer);
                elem.innerText = targetNumber.toLocaleString();
            }
        }, 20);
    }

    function requestQuoteWithCalc() {
        window.location.href = "{{ route('investor.register') }}";
    }

    // -------------------------------------------------------------
    // Service Request Modal System
    // -------------------------------------------------------------
    let selectedServiceName = '';

    function openServiceModal(name, desc) {
        selectedServiceName = name;
        document.getElementById('modalServiceTitle').innerText = name;
        document.getElementById('modalServiceDesc').innerText = desc;

        const modal = document.getElementById('serviceModal');
        const card = document.getElementById('modalCard');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 10);
    }

    function closeServiceModal() {
        const modal = document.getElementById('serviceModal');
        const card = document.getElementById('modalCard');

        modal.classList.add('opacity-0');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function submitServiceRequest() {
        const name = document.getElementById('custName').value.trim() || 'عميل دارك';
        const phone = document.getElementById('custPhone').value.trim();
        if (!phone) {
            alert('يرجى إدخال رقم الهاتف للتواصل معك.');
            return;
        }
        alert(`شكراً لك أ/ ${name}!\nتم استلام طلبك لخدمة "${selectedServiceName}" بنجاح، وسيتم التواصل معك مباشرة.`);
        closeServiceModal();
    }

    // -------------------------------------------------------------
    // FAQ Accordion Toggle
    // -------------------------------------------------------------
    function toggleFaq(id) {
        const content = document.getElementById('faq-content-' + id);
        const icon = document.getElementById('faq-icon-' + id);

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180', 'text-amber-400');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180', 'text-amber-400');
        }
    }
</script>
@endsection

<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="sboSkl7bJ_OFgDJocQCr2IWCxhAEfVkPDm8O8gbFE7Q" />

    {{-- ===================== SEO Core ===================== --}}
    <title>@yield('title', 'منصة دارك لتنظيم الإيجار | حجز شقق مفروشة ووحدات سكنية في السعودية')</title>
    <meta name="description" content="@yield('meta_description', 'منصة دارك لتنظيم الإيجار - احجز شقتك في السعودية بأمان وسهولة. شقق مفروشة وأجنحة سكنية للإيجار اليومي والشهري والسنوي في الرياض وجدة والدمام وجميع مدن المملكة مع توثيق رقمي وتواصل مباشر.')">
    <meta name="keywords"    content="@yield('meta_keywords', 'حجز شقق السعودية, إيجار شقق الرياض, شقق مفروشة جدة, وحدات سكنية الدمام, شقق فندقية الخبر, إيجار شقق مكة, منصة دارك, دارك لتنظيم الإيجار, حجز شقق اونلاين, إيجار سكني السعودية, شقق للإيجار, إيجار شهري, إيجار سنوي')">
    <meta name="robots"      content="@yield('meta_robots', 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1')">
    <meta name="author"      content="منصة دارك لتنظيم الإيجار">
    <meta name="language"    content="ar">
    <meta name="geo.region"  content="SA">
    <meta name="geo.placename" content="Saudi Arabia">
    <meta name="geo.country" content="SA">
    <meta name="ICBM"        content="24.7136, 46.6753">

    {{-- ===================== Canonical ===================== --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- ===================== Open Graph (Facebook / WhatsApp / LinkedIn) ===================== --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:locale"      content="ar_SA">
    <meta property="og:site_name"   content="منصة دارك">
    <meta name="application-name"   content="منصة دارك">
    <meta property="og:title"       content="@yield('og_title', 'منصة دارك لتنظيم الإيجار - حجز شقق في السعودية بأمان')">
    <meta property="og:description" content="@yield('og_description', 'احجز شقتك في الرياض أو جدة أو الدمام أو أي مدينة سعودية بأمان وسهولة. تواصل مباشر مع الملاك وتوثيق رقمي كامل.')">
    <meta property="og:url"         content="@yield('canonical', url()->current())">
    <meta property="og:image"       content="@yield('og_image', asset('images/icon-512.png'))">
    <meta property="og:image:alt"   content="@yield('og_image_alt', 'منصة دارك لتنظيم الإيجار في السعودية')">

    {{-- ===================== Twitter Card ===================== --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('twitter_title', 'منصة دارك لتنظيم الإيجار - حجز شقق في السعودية')">
    <meta name="twitter:description" content="@yield('twitter_description', 'احجز شقتك بأمان في جميع مدن السعودية عبر منصة دارك لتنظيم الإيجار.')">
    <meta name="twitter:image"       content="@yield('og_image', asset('images/icon-512.png'))">

    {{-- ===================== Favicon & PWA (Square icons for Google Search + iPhone Home Screen) ===================== --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest"               href="{{ url('/manifest-client.json') }}">
    <meta name="theme-color"           content="#F59E0B">
    <meta name="mobile-web-app-capable"       content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title"   content="دارك">

    {{-- ===================== Sitemap reference ===================== --}}
    <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}">

    {{-- ===================== JSON-LD: WebSite + Organization + RealEstateAgent ===================== --}}
    @php
        $globalSchemas = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'منصة دارك',
                'alternateName' => ['دارك', 'منصة دارك لتنظيم الإيجار', 'منصة دارك لتأجير الشقق', 'Darak'],
                'url' => url('/'),
                'inLanguage' => 'ar',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/') . '?apartment_number={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'منصة دارك',
                'alternateName' => ['دارك', 'منصة دارك لتنظيم الإيجار', 'Darak'],
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/icon-512.png'),
                    'width' => 512,
                    'height' => 512,
                ],
                'image' => asset('images/icon-512.png'),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'RealEstateAgent',
                'name' => 'منصة دارك لتنظيم الإيجار',
                'alternateName' => ['منصة دارك', 'دارك لتأجير الشقق'],
                'description' => 'منظومة عقارية متكاملة لحجز الشقق المفروشة والإيجارات اليومية والشهرية والسنوية في جميع مدن المملكة العربية السعودية، وإدارة وتشغيل العقارات للمستثمرين.',
                'url' => url('/'),
                'logo' => asset('images/icon-512.png'),
                'image' => asset('images/icon-512.png'),
                'inLanguage' => 'ar',
                'areaServed' => [
                    ['@type' => 'City', 'name' => 'الرياض', 'sameAs' => 'https://www.wikidata.org/wiki/Q3692'],
                    ['@type' => 'City', 'name' => 'جدة', 'sameAs' => 'https://www.wikidata.org/wiki/Q131491'],
                    ['@type' => 'City', 'name' => 'الدمام', 'sameAs' => 'https://www.wikidata.org/wiki/Q204163'],
                    ['@type' => 'City', 'name' => 'مكة المكرمة'],
                    ['@type' => 'City', 'name' => 'المدينة المنورة'],
                    ['@type' => 'City', 'name' => 'الخبر'],
                    ['@type' => 'City', 'name' => 'الطائف'],
                    ['@type' => 'City', 'name' => 'أبها'],
                    ['@type' => 'Country', 'name' => 'المملكة العربية السعودية', 'sameAs' => 'https://www.wikidata.org/wiki/Q851'],
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => url('/') . '?apartment_number={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($globalSchemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @yield('structured_data')

    {{-- ===================== Extra head ===================== --}}
    @yield('head')

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d',
                            400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309',
                            800: '#92400e', 900: '#78350f', 950: '#451a03',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Smooth Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #F59E0B;
        }

        /* Glassmorphism helpers */
        .glass-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Global Keyframe Animations */
        @keyframes pulseGlowRing {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        @keyframes pulseGreenRing {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes floatMini {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-4px); }
        }

        .pulse-amber-ring { animation: pulseGlowRing 2.5s infinite; }
        .pulse-green-ring { animation: pulseGreenRing 2s infinite; }
        .float-mini { animation: floatMini 3s ease-in-out infinite; }

        /* Modern card hover lift */
        .hover-lift {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
        }

        /* Active navigation indicator */
        .nav-item-active {
            color: #D97706 !important;
            position: relative;
        }
        .nav-item-active::after {
            content: '';
            position: absolute;
            bottom: -6px;
            right: 0;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, #F59E0B, #D97706);
            border-radius: 9999px;
        }

        /* Mobile safe bottom */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .mobile-bottom-dock {
                padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
            }
        }

        /* Enhanced Smooth Animations & Micro-interactions */
        @keyframes fadeUpIn {
            from { opacity: 0; transform: translateY(22px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes slideInBubble {
            from { opacity: 0; transform: translateY(10px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes shimmerSweepBtn {
            0% { transform: translateX(120%) skewX(-20deg); }
            100% { transform: translateX(-220%) skewX(-20deg); }
        }
        @keyframes bellRingSoft {
            0%, 100% { transform: rotate(0); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-12deg); }
            45% { transform: rotate(8deg); }
            60% { transform: rotate(-6deg); }
            75% { transform: rotate(3deg); }
        }

        .reveal-item {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-item.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .animate-fade-in { animation: fadeUpIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .msg-bubble-anim { animation: slideInBubble 0.3s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .btn-press { transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease, background-color 0.2s ease; }
        .btn-press:active { transform: scale(0.96); }
        .btn-shimmer { position: relative; overflow: hidden; }
        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 45%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
            transform: translateX(120%) skewX(-20deg);
            pointer-events: none;
        }
        .btn-shimmer:hover::after {
            animation: shimmerSweepBtn 0.85s ease forwards;
        }
        .bell-ring-anim { animation: bellRingSoft 2.2s ease-in-out infinite; transform-origin: top center; }

        @media (prefers-reduced-motion: reduce) {
            .reveal-item { opacity: 1 !important; transform: none !important; transition: none !important; }
            .animate-fade-in, .msg-bubble-anim, .bell-ring-anim, .pulse-amber-ring, .pulse-green-ring, .float-mini { animation: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white">

    <!-- Top Announcement Bar (Discreet, high-trust) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 text-white text-[11px] py-1.5 px-4 font-bold border-b border-amber-500/20">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px]">
                    <i class="fa-solid fa-shield-check text-amber-400"></i>
                    <span>بوابة رسمية معتمدة</span>
                </span>
                <span class="hidden sm:inline text-slate-300">نظام موحد لتنظيم وحماية وتوثيق إيجار الوحدات السكنية</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-amber-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-xs"></i>
                    <span>محادثات مباشرة وإشعارات فورية مع المُلّاك</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Main Header / Navigation Bar -->
    <header class="sticky top-0 z-40 glass-nav border-b border-slate-200/80 shadow-sm transition-all duration-300">
        <div class="max-w-6xl mx-auto px-4 h-16 sm:h-20 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand Identity -->
            <a href="{{ route('client.apartments.index') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl overflow-hidden border-2 border-amber-400/80 shadow-md bg-white flex items-center justify-center p-0.5 flex-shrink-0 group-hover:scale-105 transition duration-300">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-black text-base sm:text-lg text-slate-900 tracking-tight">منصة دَارِك</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-xs">لتنظيم الإيجار</span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold hidden sm:block">حلول سكنية واستثمارية متكاملة بأعلى درجات الأمان</p>
                </div>
            </a>

            <!-- Desktop & Tablet Navigation Menu -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-700">
                <a href="{{ route('client.apartments.index') }}" 
                   class="transition-colors hover:text-amber-600 py-2 flex items-center gap-1.5 {{ request()->routeIs('client.apartments.index') ? 'nav-item-active font-extrabold' : '' }}">
                    <i class="fa-solid fa-hotel text-amber-500 text-sm"></i>
                    <span>تصفح الشقق</span>
                </a>

                <a href="{{ route('client.chat.index') }}" 
                   class="transition-colors hover:text-amber-600 py-2 flex items-center gap-1.5 {{ request()->routeIs('client.chat.*') ? 'nav-item-active font-extrabold text-amber-600' : '' }}">
                    <i class="fa-solid fa-comments text-amber-500 text-sm"></i>
                    <span>محادثاتي</span>
                </a>

                <a href="{{ route('client.services') }}" 
                   class="transition-colors hover:text-amber-600 py-2 flex items-center gap-1.5 {{ request()->routeIs('client.services') ? 'nav-item-active font-extrabold' : '' }}">
                    <i class="fa-solid fa-layer-group text-indigo-500 text-sm"></i>
                    <span>خدماتنا</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-indigo-100 text-indigo-700 font-black">جديد</span>
                </a>

                <a href="{{ route('client.about') }}" 
                   class="transition-colors hover:text-amber-600 py-2 flex items-center gap-1.5 {{ request()->routeIs('client.about') ? 'nav-item-active font-extrabold' : '' }}">
                    <i class="fa-solid fa-shield-halved text-amber-500 text-sm"></i>
                    <span>من نحن</span>
                </a>
            </nav>

            <!-- User Auth Buttons & Mobile Menu Toggle -->
            <div class="flex items-center gap-2">
                <!-- Client Instant Notifications Bell -->
                <div class="relative" id="clientNotifWrapper">
                    <button type="button" id="clientNotifBellBtn" onclick="DarkClientPush.togglePanel()"
                        class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition btn-press"
                        title="تفعيل إشعارات الحجز والمحادثات الفورية">
                        <i id="clientBellIcon" class="fa-solid fa-bell text-sm"></i>
                        <span id="clientBellDot" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        <span id="clientBellPulse" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    </button>

                    <!-- Client Push Popover -->
                    <div id="clientNotifPanel" class="hidden fixed inset-x-3 top-20 sm:top-full sm:mt-2 sm:inset-x-auto sm:left-0 sm:absolute w-auto sm:w-80 max-w-sm mx-auto bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-50 text-right space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-bell text-amber-500"></i>
                                <span>إشعارات الحجز والردود الفورية</span>
                            </span>
                            <button type="button" onclick="DarkClientPush.togglePanel()" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg sm:hidden">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div id="clientPanelStatus" class="text-xs rounded-xl p-3 bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed">
                            فعّل الإشعارات ليصلك رد صاحب الشقة وتأكيد الحجز وكود الاستقبال فوراً حتى لو أغلقت الموقع.
                        </div>

                        <div id="clientPanelIosGuide" class="hidden rounded-xl p-3 bg-amber-50 border border-amber-200 text-amber-950 text-[11px] space-y-1.5 leading-relaxed">
                            <div class="font-black flex items-center gap-1.5">
                                <i class="fa-brands fa-apple text-sm"></i>
                                <span>لتفعيل الإشعارات على الآيفون (iPhone):</span>
                            </div>
                            <ol class="list-decimal list-inside space-y-1 font-medium">
                                <li>افتح الموقع في متصفح <strong>Safari</strong>.</li>
                                <li>اضغط زر <strong>المشاركة</strong> <i class="fa-solid fa-arrow-up-from-bracket text-sky-600"></i> بالأسفل.</li>
                                <li>اختر <strong>«إضافة إلى الشاشة الرئيسية»</strong>.</li>
                                <li>افتح تطبيق <strong>«دارك»</strong> من شاشتك واضغط <strong>«تفعيل الإشعارات»</strong>.</li>
                            </ol>
                        </div>

                        <div class="space-y-2">
                            <button type="button" id="clientPanelEnableBtn" onclick="DarkClientPush.enable(true)"
                                class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center justify-center gap-2 shadow-sm btn-press">
                                <i class="fa-solid fa-bell"></i>
                                <span>تفعيل الإشعارات على هذا الجهاز</span>
                            </button>
                            <button type="button" id="clientPanelTestBtn" onclick="DarkClientPush.test()"
                                class="hidden w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm btn-press">
                                <i class="fa-solid fa-paper-plane text-amber-400"></i>
                                <span>🔔 إرسال إشعار تجريبي لجهازي</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="clientNotifBackdrop" onclick="DarkClientPush.togglePanel()" class="hidden fixed inset-0 bg-slate-950/35 backdrop-blur-xs z-40"></div>

                @auth
                    <div class="flex items-center gap-2">
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('investor.dashboard') }}" 
                           class="text-xs font-bold px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 hover:from-amber-400 hover:to-amber-500 transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span class="hidden sm:inline">لوحة التحكم</span>
                            <span>({{ Str::limit(Auth::user()->name, 8) }})</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="تسجيل الخروج" 
                                    class="text-xs font-bold p-2 sm:px-3 sm:py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span class="hidden sm:inline">خروج</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('login') }}" class="text-xs font-bold px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-arrow-right-to-bracket text-amber-600"></i>
                            <span>دخول</span>
                        </a>
                        <a href="{{ route('investor.register') }}" class="text-xs font-black px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 hover:from-amber-400 hover:to-amber-500 transition flex items-center gap-1.5 shadow-sm hidden xs:flex">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>تسجيل مستثمر</span>
                        </a>
                    </div>
                @endauth

                <!-- Mobile Hamburger Toggle Button -->
                <button type="button" onclick="toggleMobileDrawer()" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-out Drawer Navigation -->
    <div id="mobileDrawer" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 md:hidden">
        <div id="mobileDrawerContent" class="fixed top-0 right-0 bottom-0 w-72 bg-white p-6 shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300">
            <div class="space-y-6">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl overflow-hidden border border-amber-400 bg-white p-0.5">
                            <img src="{{ asset('images/dark-logo.jpg') }}" alt="دارك" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <span class="font-black text-slate-900 text-sm">منصة دَارِك</span>
                    </div>
                    <button onclick="toggleMobileDrawer()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="space-y-2">
                    <a href="{{ route('client.apartments.index') }}" onclick="toggleMobileDrawer()" 
                       class="flex items-center gap-3 p-3 rounded-2xl font-bold text-xs {{ request()->routeIs('client.apartments.index') ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-hotel text-amber-500 text-sm w-5"></i>
                        <span>تصفح الشقق المتاحة</span>
                    </a>

                    <a href="{{ route('client.chat.index') }}" onclick="toggleMobileDrawer()" 
                       class="flex items-center gap-3 p-3 rounded-2xl font-bold text-xs {{ request()->routeIs('client.chat.*') ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-comments text-amber-500 text-sm w-5"></i>
                        <span>محادثاتي مع المُلّاك</span>
                    </a>

                    <a href="{{ route('client.services') }}" onclick="toggleMobileDrawer()" 
                       class="flex items-center gap-3 p-3 rounded-2xl font-bold text-xs {{ request()->routeIs('client.services') ? 'bg-indigo-50 text-indigo-800 border border-indigo-200' : 'text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-layer-group text-indigo-500 text-sm w-5"></i>
                        <span>خدماتنا المتخصصة</span>
                        <span class="mr-auto text-[9px] px-2 py-0.5 rounded-full bg-indigo-600 text-white">جديد</span>
                    </a>

                    <a href="{{ route('client.about') }}" onclick="toggleMobileDrawer()" 
                       class="flex items-center gap-3 p-3 rounded-2xl font-bold text-xs {{ request()->routeIs('client.about') ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'text-slate-700 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-shield-halved text-amber-500 text-sm w-5"></i>
                        <span>من نحن وتوجيهات الأمان</span>
                    </a>
                </div>

                <!-- Fast Actions -->
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <a href="{{ route('investor.register') }}" class="block w-full text-center py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md shadow-amber-500/20">
                        <i class="fa-solid fa-user-plus ml-1"></i>
                        <span>تسجيل كمستثمر عقاري</span>
                    </a>
                    <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50">
                        <span>تسجيل الدخول للنظام</span>
                    </a>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="text-[11px] text-slate-400 text-center pt-4 border-t border-slate-100">
                منصة دارك لتنظيم الإيجار &copy; {{ date('Y') }}
            </div>
        </div>
    </div>

    <!-- Flash Alerts -->
    <div class="max-w-6xl mx-auto px-4 w-full mt-3">
        @if(session('success'))
            <div class="p-4 mb-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-base"></i>
                </div>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm shadow-sm">
                <div class="font-bold mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                    <span>يرجى تصحيح الأخطاء التالية:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pr-4 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Wrapper -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-3 sm:px-6 py-5 pb-28 md:pb-12">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white border-t border-slate-800 pt-10 pb-20 sm:pb-10 text-xs text-slate-400">
        <div class="max-w-6xl mx-auto px-4 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pb-8 border-b border-slate-800">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white p-0.5 border border-amber-400">
                            <img src="{{ asset('images/dark-logo.jpg') }}" alt="دارك" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <div>
                            <span class="font-black text-base text-white block">منصة دَارِك لتنظيم الإيجار</span>
                            <span class="text-[11px] text-amber-400">بوابتك الآمنة لإدارة وتأجير الوحدات السكنية</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-md">
                        منظومة عقارية متكاملة لحجز الشقق المفروشة والإيجارات الشهرية والسنوية، وإدارة وتشغيل العقارات للمستثمرين وفق أحدث المعايير التنظيمية.
                    </p>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-white text-sm mb-3">روابط هامة</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('client.apartments.index') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-angle-left text-[10px] text-amber-500"></i> تصفح الشقق المتاحة</a></li>
                        <li><a href="{{ route('client.services') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-angle-left text-[10px] text-amber-500"></i> خدماتنا المتخصصة</a></li>
                        <li><a href="{{ route('client.about') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-angle-left text-[10px] text-amber-500"></i> من نحن وتوجيهات الأمان</a></li>
                        <li><a href="{{ route('investor.register') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-angle-left text-[10px] text-amber-500"></i> انضم كمستثمر عقاري</a></li>
                    </ul>
                </div>

                <!-- Col 3: Support & System info -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-white text-sm mb-3">حجز رقمي وتواصل مباشر</h4>
                    <p class="text-[11px] text-slate-400">تواصل مباشر ومحادثات آمنة داخل المنصة مع ملّاك الوحدات، مع إشعارات فورية وتوثيق رقمي لكافة تفاصيل الحجز.</p>
                    <a href="{{ route('client.apartments.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 hover:bg-amber-500 hover:text-slate-950 transition font-bold text-xs mt-1">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        <span>تصفح الشقق المتاحة</span>
                    </a>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
                <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} لمنصة دَارِك لتنظيم الإيجار</p>
                <div class="flex items-center gap-4">
                    <span>حماية أمنية وتوثيق رقمي</span>
                    <span>•</span>
                    <span class="text-amber-400 font-semibold">حجز إلكتروني وإشعارات فورية</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- =========================================================================
         MOBILE APP-LIKE FLOATING BOTTOM DOCK (Ultra comfortable for phone users)
         ========================================================================= -->
    <div class="md:hidden fixed bottom-3 left-3 right-3 z-50">
        <div class="bg-slate-900/95 backdrop-blur-xl border border-white/15 rounded-3xl px-3 py-2.5 shadow-2xl flex items-center justify-around mobile-bottom-dock">
            
            <!-- Tab 1: Apartments -->
            <a href="{{ route('client.apartments.index') }}" 
               class="flex flex-col items-center gap-1 transition {{ request()->routeIs('client.apartments.index') ? 'text-amber-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                <i class="fa-solid fa-hotel text-base"></i>
                <span class="text-[10px] font-bold">الشقق</span>
            </a>

            <!-- Tab 2: My Chats -->
            <a href="{{ route('client.chat.index') }}" 
               class="flex flex-col items-center gap-1 transition relative {{ request()->routeIs('client.chat.*') ? 'text-amber-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                <i class="fa-solid fa-comments text-base"></i>
                <span class="text-[10px] font-bold">محادثاتي</span>
            </a>

            <!-- Center Action Button: Quick Book & Browse Float -->
            <a href="{{ route('client.apartments.index') }}" 
               class="-mt-6 flex flex-col items-center justify-center w-12 h-12 rounded-full bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 shadow-lg shadow-amber-500/40 p-2.5 hover:scale-110 active:scale-95 transition">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </a>

            <!-- Tab 3: Services -->
            <a href="{{ route('client.services') }}" 
               class="flex flex-col items-center gap-1 transition relative {{ request()->routeIs('client.services') ? 'text-indigo-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                <i class="fa-solid fa-layer-group text-base"></i>
                <span class="text-[10px] font-bold">خدماتنا</span>
            </a>

            <!-- Tab 4: Account / Login -->
            @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('investor.dashboard') }}" 
                   class="flex flex-col items-center gap-1 transition {{ request()->is('admin*') || request()->is('investor*') ? 'text-amber-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                    <i class="fa-solid fa-gauge-high text-base text-amber-400"></i>
                    <span class="text-[10px] font-bold">لوحتي</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="flex flex-col items-center gap-1 transition {{ request()->routeIs('login') ? 'text-amber-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                    <i class="fa-solid fa-user-circle text-base"></i>
                    <span class="text-[10px] font-bold">حسابي</span>
                </a>
            @endauth

        </div>
    </div>

    <!-- Floating Live Client Toast Notification -->
    <div id="clientLiveToast" class="fixed bottom-20 md:bottom-6 right-4 left-4 sm:left-auto sm:w-96 z-50 transform translate-y-24 opacity-0 transition-all duration-500 ease-out pointer-events-none">
        <div class="bg-slate-900/95 backdrop-blur-xl border border-amber-500/50 rounded-2xl shadow-2xl text-white pointer-events-auto overflow-hidden">
            <div id="clientToastProgress" class="h-0.5 bg-amber-500 w-full transition-all duration-[7000ms] ease-linear" style="width:100%"></div>
            <div class="p-3.5 flex items-start gap-3">
                <div id="clientToastIcon" class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg flex-shrink-0 border border-amber-500/30">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h5 id="clientToastTitle" class="font-black text-xs sm:text-sm text-white leading-tight">تنبيه من منصة دارك</h5>
                        <button type="button" onclick="DarkClientPush.dismissToast()" class="text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <p id="clientToastBody" class="text-[11px] sm:text-xs text-slate-300 mt-0.5 leading-relaxed line-clamp-2"></p>
                    <a id="clientToastLink" href="#" class="hidden mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] transition">
                        <span>فتح الآن</span>
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Floating Button -->
    <button type="button" id="scrollTopBtn" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="hidden fixed bottom-24 md:bottom-6 left-4 z-40 w-10 h-10 rounded-full bg-slate-900/90 hover:bg-amber-500 text-white hover:text-slate-950 shadow-lg border border-white/15 flex items-center justify-center transition-all duration-300 btn-press"
        title="العودة للأعلى">
        <i class="fa-solid fa-arrow-up text-sm"></i>
    </button>

    <script>
        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const content = document.getElementById('mobileDrawerContent');

            if (drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    drawer.classList.remove('opacity-0');
                    content.classList.remove('translate-x-full');
                }, 10);
            } else {
                drawer.classList.add('opacity-0');
                content.classList.add('translate-x-full');
                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 300);
            }
        }
        // Clear any old persistent tokens to guarantee tenant privacy
        try {
            localStorage.removeItem('dark_last_chat_token');
            localStorage.removeItem('dark_last_chat_apt');
            document.cookie = "dark_client_phone=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        } catch(e) {}

        // Scroll Reveal Observer & Scroll-To-Top Button
        document.addEventListener('DOMContentLoaded', function () {
            const revealEls = document.querySelectorAll('.reveal-item');
            if ('IntersectionObserver' in window && revealEls.length) {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

                revealEls.forEach((el) => obs.observe(el));

                // Safety fallback so content never stays hidden on fast jumps
                setTimeout(() => {
                    revealEls.forEach((el) => {
                        const rect = el.getBoundingClientRect();
                        if (rect.top < window.innerHeight + 120) {
                            el.classList.add('is-visible');
                        }
                    });
                }, 500);
            } else {
                revealEls.forEach((el) => el.classList.add('is-visible'));
            }

            const topBtn = document.getElementById('scrollTopBtn');
            window.addEventListener('scroll', function () {
                if (!topBtn) return;
                if (window.scrollY > 360) {
                    topBtn.classList.remove('hidden');
                } else {
                    topBtn.classList.add('hidden');
                }
            }, { passive: true });
        });

        // =========================================================================
        // Universal Client Web Push & Live Notification Engine (iPhone PWA + Android + PC)
        // =========================================================================
        window.DarkClientPush = (function () {
            const SW_URL = @json(url('/sw.js'));
            const SW_SCOPE = @json(rtrim(parse_url(url('/'), PHP_URL_PATH) ?: '', '/') . '/');
            const VAPID_KEY_URL = @json(route('client.api.vapid-key'));
            const EMBEDDED_VAPID_KEY = @json(\App\Services\WebPushService::getPublicKey());
            const SUBSCRIBE_URL = @json(route('client.api.push-subscribe'));
            const TEST_URL = @json(route('client.api.test-push'));
            const CSRF_TOKEN = @json(csrf_token());

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            const isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;

            let context = {
                conversationToken: @json(trim($__env->yieldContent('push_conversation_token')) ?: null),
                bookingReference: @json(trim($__env->yieldContent('push_booking_reference')) ?: null),
            };

            let swReg = null;
            let cachedPublicKey = EMBEDDED_VAPID_KEY || null;
            let currentEndpoint = null;
            let toastTimer = null;

            function detectDeviceLabel() {
                const ua = navigator.userAgent || '';
                if (isIOS) return isStandalone ? 'iPhone/iPad PWA' : 'iPhone/iPad Safari';
                if (/Android/i.test(ua)) return /Chrome/i.test(ua) ? 'Android Chrome' : 'Android Browser';
                if (/Edg/i.test(ua)) return 'Desktop Edge';
                if (/Chrome/i.test(ua)) return 'Desktop Chrome';
                if (/Firefox/i.test(ua)) return 'Desktop Firefox';
                if (/Safari/i.test(ua)) return 'macOS Safari';
                return 'Web Browser';
            }

            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            function arrayBufferToBase64Url(buffer) {
                if (!buffer) return '';
                const bytes = new Uint8Array(buffer);
                let binary = '';
                for (let i = 0; i < bytes.byteLength; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
            }

            function uint8ArraysEqual(a, b) {
                if (!a || !b || a.byteLength !== b.byteLength) return false;
                const viewA = new Uint8Array(a);
                const viewB = new Uint8Array(b);
                for (let i = 0; i < viewA.length; i++) {
                    if (viewA[i] !== viewB[i]) return false;
                }
                return true;
            }

            function playChime() {
                try {
                    const AC = window.AudioContext || window.webkitAudioContext;
                    if (!AC) return;
                    const ctx = new AC();
                    const now = ctx.currentTime;
                    [[659.25, 0, 0.35], [880, 0.12, 0.6]].forEach(([freq, delay, end]) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, now + delay);
                        gain.gain.setValueAtTime(0.28, now + delay);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + end);
                        osc.connect(gain).connect(ctx.destination);
                        osc.start(now + delay);
                        osc.stop(now + end);
                    });
                } catch (_) {}
            }

            function vibratePhone() {
                if ('vibrate' in navigator) {
                    try { navigator.vibrate([200, 100, 250]); } catch (_) {}
                }
            }

            function showToast(title, body, url) {
                const toast = document.getElementById('clientLiveToast');
                if (!toast) return;
                document.getElementById('clientToastTitle').textContent = title || 'تنبيه من منصة دارك';
                document.getElementById('clientToastBody').textContent = body || '';
                const linkEl = document.getElementById('clientToastLink');
                if (linkEl) {
                    if (url && url !== '#') {
                        linkEl.href = url;
                        linkEl.classList.remove('hidden');
                    } else {
                        linkEl.classList.add('hidden');
                    }
                }
                const progress = document.getElementById('clientToastProgress');
                if (progress) {
                    progress.style.transition = 'none';
                    progress.style.width = '100%';
                    setTimeout(() => {
                        progress.style.transition = 'width 7000ms linear';
                        progress.style.width = '0%';
                    }, 40);
                }
                toast.classList.remove('translate-y-24', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(dismissToast, 7000);
            }

            function dismissToast() {
                const toast = document.getElementById('clientLiveToast');
                if (!toast) return;
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }

            function populateEndpointInputs(endpoint) {
                if (!endpoint) return;
                currentEndpoint = endpoint;
                document.querySelectorAll('.js-push-endpoint').forEach((el) => {
                    el.value = endpoint;
                });
            }

            async function ensureServiceWorker() {
                if (!('serviceWorker' in navigator)) return null;
                if (!swReg) {
                    swReg = await navigator.serviceWorker.register(SW_URL, {
                        scope: SW_SCOPE,
                        updateViaCache: 'none'
                    });
                }
                await navigator.serviceWorker.ready;
                return swReg;
            }

            async function sync(showFeedback, overrideContext) {
                if (overrideContext) {
                    context = { ...context, ...overrideContext };
                }
                try {
                    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return false;
                    const reg = await ensureServiceWorker();
                    if (!reg || !reg.pushManager) return false;

                    let pubKey = cachedPublicKey;
                    if (!pubKey) {
                        const r = await fetch(VAPID_KEY_URL, { headers: { 'Accept': 'application/json' } });
                        if (r.ok) {
                            const d = await r.json();
                            pubKey = d.publicKey;
                            cachedPublicKey = pubKey;
                        }
                    }
                    if (!pubKey) return false;

                    const appServerKey = urlBase64ToUint8Array(pubKey);
                    let sub = await reg.pushManager.getSubscription();
                    if (sub && sub.options && sub.options.applicationServerKey) {
                        if (!uint8ArraysEqual(sub.options.applicationServerKey, appServerKey.buffer)) {
                            await sub.unsubscribe();
                            sub = null;
                        }
                    }
                    if (!sub) {
                        sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: appServerKey
                        });
                    }

                    const subJson = sub.toJSON ? sub.toJSON() : {};
                    const keys = subJson.keys || {};
                    const p256dh = keys.p256dh || (sub.getKey ? arrayBufferToBase64Url(sub.getKey('p256dh')) : '');
                    const auth = keys.auth || (sub.getKey ? arrayBufferToBase64Url(sub.getKey('auth')) : '');
                    const endpoint = subJson.endpoint || sub.endpoint;
                    if (!endpoint || !p256dh || !auth) return false;

                    populateEndpointInputs(endpoint);

                    const res = await fetch(SUBSCRIBE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            endpoint: endpoint,
                            keys: { p256dh, auth },
                            contentEncoding: (window.PushManager && PushManager.supportedContentEncodings && PushManager.supportedContentEncodings[0]) || 'aes128gcm',
                            conversation_token: context.conversationToken || null,
                            booking_reference: context.bookingReference || null,
                            device_label: detectDeviceLabel()
                        })
                    });

                    if (!res.ok) return false;
                    if (showFeedback) {
                        showToast('🔔 تم تفعيل الإشعارات بنجاح!', 'ستصلك تنبيهات رد المالك وتأكيد الحجز فورياً حتى لو أغلقت الموقع.', '#');
                    }
                    refreshUI();
                    return true;
                } catch (err) {
                    console.warn('Client WebPush sync error:', err);
                    return false;
                }
            }

            async function enable(sendTestAfter) {
                playChime();
                vibratePhone();

                if (isIOS && !isStandalone) {
                    togglePanel(true);
                    return false;
                }

                if (!('Notification' in window) || !('PushManager' in window)) {
                    showToast('تنبيه المتصفح', 'هذا المتصفح لا يدعم إشعارات الخلفية، ولكن التحديثات المباشرة تعمل داخل الصفحة.', '#');
                    return false;
                }

                let perm = Notification.permission;
                if (perm !== 'granted') {
                    perm = await Notification.requestPermission();
                }

                if (perm === 'granted') {
                    const ok = await sync(true);
                    if (ok && sendTestAfter) {
                        await test();
                    }
                    return ok;
                } else {
                    refreshUI();
                    showToast('لم يتم السماح بالإشعارات', 'يمكنك تفعيلها لاحقاً من إعدادات المتصفح بجانب رابط الموقع.', '#');
                    return false;
                }
            }

            async function test() {
                if (!currentEndpoint) {
                    await sync(false);
                }
                if (!currentEndpoint) {
                    showToast('تنبيه', 'يرجى الضغط على تفعيل الإشعارات أولاً.', '#');
                    return;
                }
                try {
                    const res = await fetch(TEST_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            endpoint: currentEndpoint,
                            url: window.location.href
                        })
                    });
                    const data = await res.json();
                    showToast(data.success ? '🚀 وصل الإشعار التجريبي!' : '⚠️ تعذر الإرسال', data.message || '', '#');
                } catch (_) {
                    showToast('❌ خطأ', 'تعذر إرسال الإشعار التجريبي حالياً.', '#');
                }
            }

            function refreshUI() {
                const dot = document.getElementById('clientBellDot');
                const pulse = document.getElementById('clientBellPulse');
                const icon = document.getElementById('clientBellIcon');
                const statusBox = document.getElementById('clientPanelStatus');
                const iosGuide = document.getElementById('clientPanelIosGuide');
                const enableBtn = document.getElementById('clientPanelEnableBtn');
                const testBtn = document.getElementById('clientPanelTestBtn');

                const pageCards = document.querySelectorAll('.js-client-push-card');

                if (isIOS && !isStandalone) {
                    if (iosGuide) iosGuide.classList.remove('hidden');
                    if (enableBtn) enableBtn.classList.add('hidden');
                    if (testBtn) testBtn.classList.add('hidden');
                    if (pulse) pulse.classList.remove('hidden');
                    if (statusBox) {
                        statusBox.innerHTML = '<strong class="text-amber-800">للمستخدمين على الآيفون:</strong> أضف الموقع للشاشة الرئيسية أولاً لتفعيل إشعارات الخلفية.';
                    }
                    pageCards.forEach((card) => {
                        const iosHint = card.querySelector('.js-ios-hint');
                        const enableWrap = card.querySelector('.js-push-enable-wrap');
                        const activeWrap = card.querySelector('.js-push-active-wrap');
                        if (iosHint) iosHint.classList.remove('hidden');
                        if (enableWrap) enableWrap.classList.add('hidden');
                        if (activeWrap) activeWrap.classList.add('hidden');
                    });
                    return;
                }

                if (iosGuide) iosGuide.classList.add('hidden');

                if (!('Notification' in window) || !('PushManager' in window)) {
                    return;
                }

                if (Notification.permission === 'granted') {
                    if (dot) dot.classList.remove('hidden');
                    if (pulse) pulse.classList.add('hidden');
                    if (icon) icon.classList.remove('bell-ring-anim');
                    if (enableBtn) enableBtn.classList.add('hidden');
                    if (testBtn) testBtn.classList.remove('hidden');
                    if (statusBox) {
                        statusBox.innerHTML = '<span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check ml-1"></i> الإشعارات الفورية مفعّلة على جهازك!</span><p class="text-[11px] text-slate-600 mt-1">ستصلك الردود وتأكيد الحجز فورياً حتى لو أغلقت الموقع.</p>';
                        statusBox.className = 'text-xs rounded-xl p-3 bg-emerald-50 border border-emerald-200 leading-relaxed';
                    }
                    pageCards.forEach((card) => {
                        const iosHint = card.querySelector('.js-ios-hint');
                        const enableWrap = card.querySelector('.js-push-enable-wrap');
                        const activeWrap = card.querySelector('.js-push-active-wrap');
                        if (iosHint) iosHint.classList.add('hidden');
                        if (enableWrap) enableWrap.classList.add('hidden');
                        if (activeWrap) activeWrap.classList.remove('hidden');
                    });
                } else if (Notification.permission === 'default') {
                    if (dot) dot.classList.add('hidden');
                    if (pulse) pulse.classList.remove('hidden');
                    if (enableBtn) enableBtn.classList.remove('hidden');
                    if (testBtn) testBtn.classList.add('hidden');
                    pageCards.forEach((card) => {
                        const iosHint = card.querySelector('.js-ios-hint');
                        const enableWrap = card.querySelector('.js-push-enable-wrap');
                        const activeWrap = card.querySelector('.js-push-active-wrap');
                        if (iosHint) iosHint.classList.add('hidden');
                        if (enableWrap) enableWrap.classList.remove('hidden');
                        if (activeWrap) activeWrap.classList.add('hidden');
                    });
                } else {
                    if (dot) dot.classList.add('hidden');
                    if (pulse) pulse.classList.add('hidden');
                    if (enableBtn) enableBtn.classList.add('hidden');
                    if (testBtn) testBtn.classList.add('hidden');
                    if (statusBox) {
                        statusBox.innerHTML = '<span class="text-rose-700 font-bold">الإشعارات محظورة من المتصفح.</span> اضغط أيقونة الإعدادات بجانب رابط الموقع واسمح بالإشعارات.';
                    }
                }
            }

            function togglePanel(forceOpen) {
                const panel = document.getElementById('clientNotifPanel');
                const backdrop = document.getElementById('clientNotifBackdrop');
                if (!panel) return;
                const willOpen = typeof forceOpen === 'boolean' ? forceOpen : panel.classList.contains('hidden');
                panel.classList.toggle('hidden', !willOpen);
                if (backdrop) backdrop.classList.toggle('hidden', !willOpen);
                if (willOpen) refreshUI();
            }

            async function init() {
                document.addEventListener('click', function (e) {
                    const wrapper = document.getElementById('clientNotifWrapper');
                    const panel = document.getElementById('clientNotifPanel');
                    const backdrop = document.getElementById('clientNotifBackdrop');
                    if (wrapper && panel && !wrapper.contains(e.target)) {
                        panel.classList.add('hidden');
                        if (backdrop) backdrop.classList.add('hidden');
                    }
                });

                if ('serviceWorker' in navigator) {
                    try {
                        await ensureServiceWorker();
                        navigator.serviceWorker.addEventListener('message', function (event) {
                            if (!event.data) return;
                            if (event.data.type === 'DARK_NAVIGATE' && event.data.url) {
                                window.location.href = event.data.url;
                            } else if (event.data.type === 'DARK_PUSH_RECEIVED' && event.data.payload) {
                                playChime();
                                vibratePhone();
                                showToast(event.data.payload.title, event.data.payload.body, event.data.payload.url || '#');
                            }
                        });
                    } catch (_) {}
                }

                refreshUI();

                if ('Notification' in window && Notification.permission === 'granted') {
                    await sync(false);
                }
            }

            document.addEventListener('DOMContentLoaded', init);

            return {
                enable,
                sync,
                test,
                togglePanel,
                showToast,
                dismissToast,
                playChime,
                vibratePhone,
                refreshUI,
                isIOS: () => isIOS,
                isStandalone: () => isStandalone
            };
        })();
    </script>

    @yield('scripts')
</body>
</html>

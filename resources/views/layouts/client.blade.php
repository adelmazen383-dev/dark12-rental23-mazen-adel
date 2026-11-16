<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة دارك لتنظيم الإيجار - بوابتك الآمنة لحجز الوحدات')</title>

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

            <!-- Tab 2: Services -->
            <a href="{{ route('client.services') }}" 
               class="flex flex-col items-center gap-1 transition relative {{ request()->routeIs('client.services') ? 'text-indigo-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                <i class="fa-solid fa-layer-group text-base"></i>
                <span class="text-[10px] font-bold">خدماتنا</span>
                <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
            </a>

            <!-- Center Action Button: Quick Book & Browse Float -->
            <a href="{{ route('client.apartments.index') }}" 
               class="-mt-6 flex flex-col items-center justify-center w-12 h-12 rounded-full bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 shadow-lg shadow-amber-500/40 p-2.5 hover:scale-110 active:scale-95 transition">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </a>

            <!-- Tab 3: About Us -->
            <a href="{{ route('client.about') }}" 
               class="flex flex-col items-center gap-1 transition {{ request()->routeIs('client.about') ? 'text-amber-400 scale-105' : 'text-slate-400 hover:text-slate-200' }}">
                <i class="fa-solid fa-shield-halved text-base"></i>
                <span class="text-[10px] font-bold">من نحن</span>
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
    </script>

    @yield('scripts')
</body>
</html>

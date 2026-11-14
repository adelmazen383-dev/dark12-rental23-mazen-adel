<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'منصة دارك لتنظيم الإيجار')</title>
    <!-- Tailwind CSS CDN for instant styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white">

    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('client.apartments.index') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl overflow-hidden border border-amber-200/60 shadow-sm bg-white flex items-center justify-center p-0.5 flex-shrink-0">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
                </div>
                <div>
                    <h1 class="font-black text-base text-gray-900 leading-tight flex items-center gap-1.5">
                        <span>منصة دَارِك</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">لتنظيم الإيجار</span>
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">بوابتك الآمنة لحجز وتوثيق الوحدات السكنية</p>
                </div>
            </a>

            <!-- Navigation Links (Visible on Tablet & Desktop) -->
            <nav class="hidden md:flex items-center gap-5 text-xs font-bold text-slate-700">
                <a href="{{ route('client.apartments.index') }}" class="hover:text-amber-600 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-hotel text-amber-500"></i>
                    <span>الشقق المتاحة</span>
                </a>
                @php
                    $waPhone = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
                    $waClean = preg_replace('/[^0-9]/', '', $waPhone);
                    if (str_starts_with($waClean, '05')) { $waClean = '966' . substr($waClean, 1); }
                @endphp
                <a href="https://wa.me/{{ $waClean }}" target="_blank" class="text-emerald-700 hover:text-emerald-800 transition flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>استقبال البرج (24/7)</span>
                </a>
            </nav>

            @auth
                <div class="flex items-center gap-2">
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('investor.dashboard') }}" 
                       class="text-xs font-bold px-3 py-2 rounded-xl bg-amber-500 text-slate-950 hover:bg-amber-400 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>لوحة التحكم ({{ Str::limit(Auth::user()->name, 10) }})</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="تسجيل الخروج" 
                                class="text-xs font-bold px-3 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span class="hidden sm:inline">خروج</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs font-bold px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 transition flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-arrow-right-to-bracket text-amber-600"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                    <a href="{{ route('investor.register') }}" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-amber-500 text-slate-950 hover:bg-amber-400 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>تسجيل كمستثمر</span>
                    </a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Flash Alerts -->
    <div class="max-w-6xl mx-auto px-4 w-full mt-3">
        @if(session('success'))
            <div class="p-4 mb-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-3 sm:px-6 py-5 pb-24">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs text-gray-500 mb-14 sm:mb-0">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} لمنصة دَارِك لتنظيم الإيجار</p>
            <div class="flex items-center gap-4 text-xs font-medium">
                <a href="{{ route('investor.register') }}" class="text-amber-600 hover:underline">تسجيل كمستثمر</a>
                <span>•</span>
                <span class="text-gray-400">خدمة الاستقبال متوفرة 24/7</span>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar (Very comfortable for mobile users) -->
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2 flex items-center justify-around shadow-2xl safe-bottom">
        <a href="{{ route('client.apartments.index') }}" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->routeIs('client.apartments.index') ? 'text-amber-600' : 'text-slate-500' }}">
            <i class="fa-solid fa-hotel text-base"></i>
            <span>تصفح الشقق</span>
        </a>

        @php
            $receptionWa = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
            $cleanWa = preg_replace('/[^0-9]/', '', $receptionWa);
            if (str_starts_with($cleanWa, '05')) {
                $cleanWa = '966' . substr($cleanWa, 1);
            }
        @endphp
        <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('مرحباً منصة دارك، أرغب في الاستفسار عن الشقق المتاحة.') }}" target="_blank"
           class="flex items-center gap-1.5 px-3 py-2 rounded-full bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition">
            <i class="fa-brands fa-whatsapp text-sm"></i>
            <span>الاستقبال</span>
        </a>

        @auth
            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('investor.dashboard') }}" 
               class="flex flex-col items-center gap-1 text-[11px] font-bold text-amber-600">
                <i class="fa-solid fa-gauge-high text-base"></i>
                <span>لوحتي</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="flex flex-col items-center gap-1 text-[11px] font-bold text-rose-600">
                    <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                    <span>خروج</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-[11px] font-bold text-slate-600">
                <i class="fa-solid fa-arrow-right-to-bracket text-base text-amber-600"></i>
                <span>دخول</span>
            </a>
            <a href="{{ route('investor.register') }}" class="flex flex-col items-center gap-1 text-[11px] font-bold text-amber-600">
                <i class="fa-solid fa-user-plus text-base"></i>
                <span>تسجيل جديد</span>
            </a>
        @endauth
    </div>

    @yield('scripts')
</body>
</html>

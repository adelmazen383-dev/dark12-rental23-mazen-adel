<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <title>@yield('title', 'لوحة تحكم الإدارة') - منصة دارك</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex selection:bg-amber-500 selection:text-white">

    @php
        $pendingInvestorsCount = \App\Models\Investor::where('status','pending')->count();
    @endphp

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-200 flex-shrink-0 hidden md:flex flex-col min-h-screen border-l border-slate-800">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800 bg-slate-950/40">
            <div class="w-9 h-9 rounded-xl overflow-hidden bg-white p-0.5 border border-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/20 flex-shrink-0">
                <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار دارك" class="w-full h-full object-cover rounded-lg">
            </div>
            <div>
                <h2 class="font-bold text-sm text-white">إدارة منصة دَارِك</h2>
                <p class="text-xs text-amber-400/90 font-medium">لوحة التحكم المركزية</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-chart-pie w-5"></i>
                <span>الرئيسية والإحصائيات</span>
            </a>

            <div class="pt-3 pb-1 text-xs font-semibold text-slate-500 px-3 uppercase tracking-wider">إدارة العقارات والعملاء</div>

            <a href="{{ route('admin.apartments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.apartments.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-building-user w-5"></i>
                <span>الشقق والأدوار</span>
            </a>

            <a href="{{ route('admin.investors.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.investors.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-tie w-5"></i>
                    <span>المستثمرون والتراخيص</span>
                </div>
                @if($pendingInvestorsCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950 animate-pulse">{{ $pendingInvestorsCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.bookings.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-calendar-check w-5"></i>
                <span>الحجوزات والإيصالات</span>
            </a>

            <a href="{{ route('admin.reception.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.reception.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-key w-5 text-amber-400"></i>
                <span>استقبال وأمن البرج 🔑</span>
            </a>

            <a href="{{ route('admin.inspections.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.inspections.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-eye w-5"></i>
                <span>طلبات المعاينة</span>
            </a>

            <a href="{{ route('admin.chats.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.chats.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-comments w-5 text-amber-400"></i>
                <span>مراقبة المحادثات 💬</span>
            </a>

            <div class="pt-3 pb-1 text-xs font-semibold text-slate-500 px-3 uppercase tracking-wider">العقود والتوثيق</div>

            <a href="{{ route('admin.contracts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.contracts.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-file-contract w-5"></i>
                <span>عقود الإيجار المعتمدة</span>
            </a>

            <div class="pt-3 pb-1 text-xs font-semibold text-slate-500 px-3 uppercase tracking-wider">المالية والنظام</div>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5"></i>
                <span>التقارير المالية</span>
            </a>

            <a href="{{ route('admin.audit-log.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.audit-log.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-clock-rotate-left w-5"></i>
                <span>سجل التدقيق</span>
            </a>

            <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.notifications.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-brands fa-whatsapp w-5 text-emerald-400"></i>
                <span>سجل إشعارات واتساب</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.settings.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-gears w-5"></i>
                <span>إعدادات النظام والرسوم</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-950/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-amber-400 text-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400">مشرف النظام</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="تسجيل الخروج" class="p-2 text-rose-400 hover:text-rose-300 hover:bg-slate-800 rounded-lg transition">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Section -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <button type="button" onclick="document.getElementById('mobileAdminSidebar').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition flex items-center justify-center">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                <a href="{{ route('client.apartments.index') }}" target="_blank" class="text-xs px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 font-medium flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span class="hidden sm:inline">معاينة واجهة النزلاء (QR)</span>
                    <span class="sm:hidden">بوابة النزيل</span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 hidden sm:inline">{{ now()->translatedFormat('l d F Y') }}</span>
                @if($pendingInvestorsCount > 0)
                    <a href="{{ route('admin.investors.index', ['status' => 'pending']) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-800 text-xs font-bold hover:bg-amber-100 transition animate-pulse">
                        <i class="fa-solid fa-user-clock text-amber-600"></i>
                        <span>{{ $pendingInvestorsCount }} مستثمر بانتظار الموافقة</span>
                    </a>
                @endif
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
        </header>

        <!-- Mobile Drawer Menu (Visible on Mobile only when toggled) -->
        <div id="mobileAdminSidebar" class="hidden md:hidden bg-slate-900 text-white p-4 border-b border-slate-800 shadow-xl space-y-2 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="دارك" class="w-8 h-8 rounded-lg object-cover">
                    <span class="font-bold text-white">إدارة منصة دَارِك</span>
                </div>
                <button type="button" onclick="document.getElementById('mobileAdminSidebar').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <nav class="space-y-1 pt-1 max-h-[75vh] overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-chart-pie w-4"></i>
                    <span>الرئيسية والإحصائيات</span>
                </a>
                <a href="{{ route('admin.apartments.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.apartments.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-building-user w-4"></i>
                    <span>الشقق والأدوار</span>
                </a>
                <a href="{{ route('admin.investors.index') }}" class="flex items-center justify-between p-2 rounded-lg {{ request()->routeIs('admin.investors.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-user-tie w-4"></i>
                        <span>المستثمرون والتراخيص</span>
                    </div>
                    @if($pendingInvestorsCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-950">{{ $pendingInvestorsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-calendar-check w-4"></i>
                    <span>الحجوزات والإيصالات</span>
                </a>
                <a href="{{ route('admin.reception.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.reception.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-key w-4 text-amber-400"></i>
                    <span>استقبال وأمن البرج 🔑</span>
                </a>
                <a href="{{ route('admin.chats.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.chats.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-comments w-4 text-amber-400"></i>
                    <span>مراقبة المحادثات 💬</span>
                </a>
                <a href="{{ route('admin.contracts.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.contracts.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-file-contract w-4"></i>
                    <span>عقود الإيجار المعتمدة</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-4"></i>
                    <span>التقارير المالية</span>
                </a>
                <a href="{{ route('admin.audit-log.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.audit-log.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-clock-rotate-left w-4"></i>
                    <span>سجل التدقيق</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.notifications.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-brands fa-whatsapp w-4 text-emerald-400"></i>
                    <span>إشعارات واتساب</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-gears w-4"></i>
                    <span>إعدادات النظام والرسوم</span>
                </a>
            </nav>
        </div>

        <!-- Flash alerts -->
        <div class="px-4 md:px-8 pt-4">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 mb-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-info text-blue-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-8">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>

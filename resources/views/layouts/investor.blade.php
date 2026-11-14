<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة المستثمر') - أجنحة البرج</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex selection:bg-emerald-500 selection:text-white">

    @php
        $investor = auth()->user()->investor;
        $wallet = $investor?->wallet;
        $subscription = $investor?->activeSubscription;
        $unreadChats = $investor ? \App\Models\Message::whereIn('conversation_id', $investor->conversations()->pluck('id'))->whereIn('sender_type', ['customer', 'admin'])->where('is_read_by_investor', false)->count() : 0;
    @endphp

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-200 flex-shrink-0 hidden md:flex flex-col min-h-screen border-l border-slate-800">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800 bg-slate-950/40">
            <div class="w-9 h-9 rounded-xl overflow-hidden bg-white p-0.5 border border-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 flex-shrink-0">
                <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار دارك" class="w-full h-full object-cover rounded-lg">
            </div>
            <div>
                <h2 class="font-bold text-sm text-white">بوابة المستثمر</h2>
                <p class="text-xs text-emerald-400/90 font-medium">منصة دَارِك لتنظيم الإيجار</p>
            </div>
        </div>

        <!-- Quick Status Card -->
        <div class="p-4 mx-3 my-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-400">حالة الحساب</span>
                @if($investor && $investor->status === 'approved')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">معتمد</span>
                @elseif($investor && $investor->status === 'pending')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">قيد المراجعة</span>
                @elseif($investor && $investor->status === 'suspended')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">موقوف</span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-500/20 text-gray-400">مرفوض</span>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-700/40 text-xs">
                <span class="text-slate-400">رصيد المحفظة:</span>
                <span class="font-bold text-emerald-400">{{ number_format($wallet?->balance ?? 0, 2) }} ر.س</span>
            </div>

            <div class="flex items-center justify-between pt-1 text-xs">
                <span class="text-slate-400">الاشتراك:</span>
                @if($subscription)
                    <span class="text-sky-400 font-semibold">{{ $subscription->daysRemaining() }} يوم متبقي</span>
                @else
                    <span class="text-rose-400 font-semibold">غير نشط</span>
                @endif
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('investor.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.dashboard') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-chart-line w-5"></i>
                <span>لوحة التحكم</span>
            </a>

            <a href="{{ route('investor.apartments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.apartments.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-door-open w-5"></i>
                <span>شقق البرج الخاصة بي</span>
            </a>

            <a href="{{ route('investor.bookings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.bookings.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-calendar-check w-5"></i>
                <span>حجوزات شققي</span>
            </a>

            <a href="{{ route('investor.chats.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.chats.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-comments w-5 text-emerald-400"></i>
                    <span>محادثات النزلاء</span>
                </div>
                @if($unreadChats > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse">
                        {{ $unreadChats }}
                    </span>
                @endif
            </a>

            <a href="{{ route('investor.contracts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.contracts.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-file-contract w-5"></i>
                <span>عقود الإيجار المعتمدة</span>
            </a>

            <a href="{{ route('investor.wallet.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.wallet.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-wallet w-5"></i>
                <span>محفظتي وكشف الحساب</span>
            </a>

            <a href="{{ route('investor.subscription.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.subscription.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-repeat w-5"></i>
                <span>الاشتراك الشهري</span>
            </a>

            <a href="{{ route('investor.profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('investor.profile.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'hover:bg-slate-800 text-slate-300' }}">
                <i class="fa-solid fa-id-card w-5"></i>
                <span>الملف الشخصي والبنكي</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-950/30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-emerald-400 text-sm">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400">{{ $investor?->full_name }}</p>
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

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar for Mobile -->
        <header class="h-16 bg-white border-b border-gray-200 px-4 flex md:hidden items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('mobileInvestorSidebar').classList.toggle('hidden')" class="p-2 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div class="w-8 h-8 rounded-lg overflow-hidden border border-emerald-400 flex items-center justify-center">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="دارك" class="w-full h-full object-cover">
                </div>
                <span class="font-bold text-xs text-slate-900">بوابة المستثمر</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('investor.wallet.index') }}" class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                    {{ number_format($wallet?->balance ?? 0, 0) }} ر.س
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-rose-600 hover:text-rose-700">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Mobile Drawer for Investor -->
        <div id="mobileInvestorSidebar" class="hidden md:hidden bg-slate-900 text-white p-4 border-b border-slate-800 shadow-xl space-y-2 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <span class="font-bold text-emerald-400">قائمة المستثمر</span>
                <button type="button" onclick="document.getElementById('mobileInvestorSidebar').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <nav class="space-y-1 pt-1 max-h-[75vh] overflow-y-auto">
                <a href="{{ route('investor.dashboard') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.dashboard') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-chart-line w-4"></i>
                    <span>لوحة التحكم</span>
                </a>
                <a href="{{ route('investor.apartments.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.apartments.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-building w-4"></i>
                    <span>شقق البرج الخاصة بي</span>
                </a>
                <a href="{{ route('investor.bookings.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.bookings.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-calendar-check w-4"></i>
                    <span>الحجوزات والنزلاء</span>
                </a>
                <a href="{{ route('investor.chats.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.chats.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-comments w-4 text-emerald-400"></i>
                    <span>محادثات النزلاء</span>
                </a>
                <a href="{{ route('investor.contracts.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.contracts.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-file-contract w-4"></i>
                    <span>عقود الإيجار المعتمدة</span>
                </a>
                <a href="{{ route('investor.wallet.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.wallet.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-wallet w-4"></i>
                    <span>محفظتي وكشف الحساب</span>
                </a>
                <a href="{{ route('investor.subscription.index') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.subscription.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-repeat w-4"></i>
                    <span>الاشتراك الشهري</span>
                </a>
                <a href="{{ route('investor.profile.edit') }}" class="flex items-center gap-2.5 p-2 rounded-lg {{ request()->routeIs('investor.profile.*') ? 'bg-emerald-500 text-slate-950 font-bold' : 'text-slate-300' }}">
                    <i class="fa-solid fa-id-card w-4"></i>
                    <span>الملف الشخصي والترخيص</span>
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

            @if(session('warning'))
                <div class="p-4 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-bell text-amber-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('warning') }}</div>
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

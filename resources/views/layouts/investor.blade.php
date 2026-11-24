<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
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
        <!-- Top bar for Mobile & Desktop -->
        <header class="h-16 bg-white border-b border-gray-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <button type="button" onclick="document.getElementById('mobileInvestorSidebar').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div class="w-8 h-8 rounded-lg overflow-hidden border border-emerald-400 flex items-center justify-center">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="دارك" class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="font-bold text-xs sm:text-sm text-slate-900 block leading-tight">بوابة المستثمر</span>
                    <span class="text-[10px] text-slate-400 hidden sm:inline">منظومة إدارة وتأجير العقارات</span>
                </div>
            </div>

            <!-- Instant Phone Notifications Button & Actions -->
            <div class="flex items-center gap-2 sm:gap-3">

                <!-- Live Bell Notifications Button -->
                <button onclick="handleBellClick()" type="button" class="relative p-2 text-slate-600 hover:text-emerald-600 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 transition" title="الإشعارات الفورية (اضغط للاختبار أو التفعيل)">
                    <i id="navBellIcon" class="fa-solid fa-bell text-base"></i>
                    <span id="navAlertPulse" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span id="navAlertDot" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </button>

                <!-- Live Unread Chat Pill -->
                <a href="{{ route('investor.chats.index') }}" class="relative p-2 text-slate-600 hover:text-emerald-600 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 transition" title="المحادثات">
                    <i class="fa-solid fa-comments text-base"></i>
                    <span id="navUnreadChatBadge" class="absolute -top-1 -right-1 px-1.5 py-0.2 rounded-full text-[10px] font-black bg-rose-500 text-white {{ $unreadChats > 0 ? '' : 'hidden' }}">
                        {{ $unreadChats }}
                    </span>
                </a>

                <!-- Wallet Balance -->
                <a href="{{ route('investor.wallet.index') }}" class="text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-xl border border-emerald-200 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-wallet text-emerald-600"></i>
                    <span class="font-mono font-black">{{ number_format($wallet?->balance ?? 0, 0) }}</span>
                    <span class="text-[10px] hidden sm:inline">ر.س</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="تسجيل الخروج" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                        <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
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

    <!-- Floating Live Phone Notification Toast -->
    <div id="liveAlertToast" class="fixed bottom-5 right-5 left-5 sm:left-auto sm:w-96 z-50 transform translate-y-24 opacity-0 transition-all duration-500 ease-out pointer-events-none">
        <div class="bg-slate-900/97 backdrop-blur-xl border border-emerald-500/60 rounded-2xl shadow-2xl shadow-black/40 text-white pointer-events-auto overflow-hidden">
            <!-- Progress bar -->
            <div id="toastProgress" class="h-0.5 bg-emerald-500 w-full transition-all duration-[9000ms] ease-linear" style="width:100%"></div>
            <div class="p-4 flex items-start gap-3.5">
                <div id="toastIconBox" class="w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-500/30">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-0.5">
                        <h5 id="toastTitle" class="font-black text-sm text-white leading-tight">تنبيه جديد</h5>
                        <button onclick="dismissToastAlert()" type="button" class="text-slate-500 hover:text-white transition mt-0.5 flex-shrink-0">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <p id="toastMessage" class="text-xs text-slate-300 leading-relaxed mb-3 line-clamp-2">محتوى التنبيه...</p>
                    <div class="flex items-center gap-2">
                        <a id="toastActionBtn" href="#" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                            <span>عرض الآن</span>
                            <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        </a>
                        <span id="toastTime" class="text-[11px] text-slate-500 me-auto">الآن</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enable Native Notifications Button (shown once) -->
    <div id="notifPermBtn" class="fixed bottom-5 left-5 z-40 hidden">
        <button onclick="requestNotifPermission()" type="button"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white text-xs font-bold shadow-xl transition-all">
            <i class="fa-solid fa-bell-slash text-amber-400"></i>
            <span>فعّل الإشعارات الفورية</span>
        </button>
    </div>

    <!-- Live Alert Polling & Notification Engine -->
    <script>
        let lastSeenMessageId = null;
        let lastSeenBookingId = null;
        let isPolling = false;
        let chimeUnlocked = false;
        let toastTimeout = null;

        // ─── Init on page load ───────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            // Register Service Worker for PWA & Native Push capabilities
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            }

            // Check Native Notification permission
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    const btn = document.getElementById('notifPermBtn');
                    if (btn) btn.classList.remove('hidden');
                } else if (Notification.permission === 'granted') {
                    const dot = document.getElementById('navAlertDot');
                    if (dot) dot.classList.remove('hidden');
                }
            }

            // Audio unlock on user touch
            document.addEventListener('click', function unlockAudio() {
                chimeUnlocked = true;
                document.removeEventListener('click', unlockAudio);
            }, { once: true });

            // Start polling unread alerts (first check at 1.5s then every 5s)
            setTimeout(pollUnreadAlerts, 1500);
            setInterval(pollUnreadAlerts, 5000);
        });

        // ─── Permission handler ──────────────────────────────────
        function requestNotifPermission() {
            if (!('Notification' in window)) {
                triggerAlert('تنبيه المتصفح', 'المتصفح لا يدعم إشعارات النظام، لكن التنبيهات الصوتية والشاشية تعمل دائماً.', '#', 'system');
                return;
            }
            Notification.requestPermission().then(function (perm) {
                const btn = document.getElementById('notifPermBtn');
                if (perm === 'granted') {
                    if (btn) btn.classList.add('hidden');
                    const dot = document.getElementById('navAlertDot');
                    if (dot) dot.classList.remove('hidden');
                    triggerAlert('🔔 تم تفعيل إشعارات الجوال الفورية!', 'ستصلك التنبيهات مع نغمة واهتزاز أولاً بأول.', '#', 'system');
                } else if (btn) {
                    btn.classList.add('hidden');
                }
            });
        }

        // ─── Bell Click Handler (Quick Test / Enable) ────────────
        function handleBellClick() {
            if ('Notification' in window && Notification.permission === 'default') {
                requestNotifPermission();
            } else {
                triggerAlert('🔔 اختبار الإشعارات الفورية', 'نظام التنبيهات الصوتية والاهتزاز يعمل بنجاح على هاتفك.', '#', 'system');
            }
        }

        // ─── Poll backend ────────────────────────────────────────
        async function pollUnreadAlerts() {
            if (isPolling) return;
            isPolling = true;
            try {
                const res = await fetch("{{ route('investor.api.unread-alerts') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                // Update navbar unread badge
                const badge = document.getElementById('navUnreadChatBadge');
                if (badge) {
                    if (data.unread_messages_count > 0) {
                        badge.innerText = data.unread_messages_count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // Show alert pulse on bell if there's any active alert
                const pulse = document.getElementById('navAlertPulse');
                if (pulse) {
                    if (data.unread_messages_count > 0 || (data.unread_bookings_count && data.unread_bookings_count > 0)) {
                        pulse.classList.remove('hidden');
                    } else {
                        pulse.classList.add('hidden');
                    }
                }

                // New message notification
                if (data.latest_message && data.latest_message.id !== lastSeenMessageId) {
                    if (lastSeenMessageId !== null) {
                        triggerAlert(
                            `📩 رسالة من النزيل (${data.latest_message.sender_name})`,
                            `شقة ${data.latest_message.apartment_number}: "${data.latest_message.snippet}"`,
                            data.latest_message.url,
                            'message'
                        );
                    }
                    lastSeenMessageId = data.latest_message.id;
                }

                // New booking notification
                if (data.latest_booking && data.latest_booking.id !== lastSeenBookingId) {
                    if (lastSeenBookingId !== null) {
                        triggerAlert(
                            `🎉 حجز جديد #${data.latest_booking.reference}`,
                            `${data.latest_booking.customer_name} | ${data.latest_booking.total_amount} ريال | شقة ${data.latest_booking.apartment_number}`,
                            data.latest_booking.url,
                            'booking'
                        );
                    }
                    lastSeenBookingId = data.latest_booking.id;
                }
            } catch (_) { /* ignore network error */ } finally {
                isPolling = false;
            }
        }

        // ─── Trigger Alert (Chime + Vibrate + Native + Toast) ────
        function triggerAlert(title, message, url, type) {
            playChime();
            vibratePhone();

            // Native notification (Chrome Desktop & Mobile)
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    const n = new Notification(title, {
                        body: message,
                        icon: '/images/dark-logo.jpg',
                        badge: '/images/dark-logo.jpg',
                        vibrate: [200, 100, 200, 100, 300],
                        tag: type + '-' + Date.now(),
                        renotify: true,
                    });
                    n.onclick = () => {
                        window.focus();
                        if (url && url !== '#') window.location.href = url;
                    };
                } catch (e) {
                    // Mobile Android Chrome requires ServiceWorkerRegistration
                    if ('serviceWorker' in navigator && navigator.serviceWorker.ready) {
                        navigator.serviceWorker.ready.then(reg => {
                            reg.showNotification(title, {
                                body: message,
                                icon: '/images/dark-logo.jpg',
                                badge: '/images/dark-logo.jpg',
                                vibrate: [200, 100, 200, 100, 300],
                                data: { url: url }
                            });
                        }).catch(() => {});
                    }
                }
            }

            showToast(title, message, url, type);
        }

        // ─── Toast Alert UI ──────────────────────────────────────
        function showToast(title, message, url, type) {
            const toast = document.getElementById('liveAlertToast');
            if (!toast) return;

            document.getElementById('toastTitle').innerText = title;
            document.getElementById('toastMessage').innerText = message;
            const actionBtn = document.getElementById('toastActionBtn');
            if (actionBtn) {
                actionBtn.href = url || '#';
                actionBtn.style.display = (url && url !== '#') ? 'inline-flex' : 'none';
            }

            const iconBox = document.getElementById('toastIconBox');
            const progress = document.getElementById('toastProgress');

            if (type === 'booking') {
                iconBox.innerHTML = '<i class="fa-solid fa-calendar-check"></i>';
                iconBox.className = 'w-11 h-11 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl flex-shrink-0 border border-amber-500/40';
                if (progress) progress.className = 'h-0.5 bg-amber-500 w-full transition-all duration-[8000ms] ease-linear';
            } else if (type === 'message') {
                iconBox.innerHTML = '<i class="fa-solid fa-comments"></i>';
                iconBox.className = 'w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0 border border-emerald-500/40';
                if (progress) progress.className = 'h-0.5 bg-emerald-500 w-full transition-all duration-[8000ms] ease-linear';
            } else {
                iconBox.innerHTML = '<i class="fa-solid fa-bell"></i>';
                iconBox.className = 'w-11 h-11 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-xl flex-shrink-0 border border-sky-500/40';
                if (progress) progress.className = 'h-0.5 bg-sky-500 w-full transition-all duration-[8000ms] ease-linear';
            }

            // Animate progress bar
            if (progress) {
                progress.style.transition = 'none';
                progress.style.width = '100%';
                setTimeout(() => {
                    progress.style.transition = 'width 8000ms linear';
                    progress.style.width = '0%';
                }, 50);
            }

            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(dismissToastAlert, 8000);
        }

        function dismissToastAlert() {
            const toast = document.getElementById('liveAlertToast');
            if (toast) {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }
        }

        // ─── Melodic Audio Chime (Web Audio API) ─────────────────
        function playChime() {
            try {
                const AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return;
                const ctx = new AC();
                const now = ctx.currentTime;

                // High clarity melodic double-tone (E5 -> A5)
                [[659.25, 0, 0.45], [880, 0.14, 0.75]].forEach(([freq, delay, end]) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + delay);
                    gain.gain.setValueAtTime(0.35, now + delay);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + end);
                    osc.connect(gain).connect(ctx.destination);
                    osc.start(now + delay);
                    osc.stop(now + end);
                });
            } catch (_) {}
        }

        // ─── Phone Vibration Pattern ─────────────────────────────
        function vibratePhone() {
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200, 100, 300]);
            }
        }
    </script>

    @yield('scripts')
</body>
</html>

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
                <!-- Push Notification Enable / Status Button -->
                <button id="btnEnableNotifications" onclick="enablePhoneNotifications()" type="button" 
                        class="text-xs font-bold px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-300 hover:border-emerald-300 transition flex items-center gap-2 shadow-2xs">
                    <span class="relative flex h-2.5 w-2.5">
                        <span id="notifPingDot" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span id="notifStaticDot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span id="notifStatusText" class="hidden xs:inline">تفعيل إشعارات الجوال</span>
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
    <div id="liveAlertToast" class="fixed top-5 left-5 right-5 sm:left-auto sm:right-5 sm:w-96 z-50 transform -translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-900/95 backdrop-blur-xl border-2 border-emerald-500/80 rounded-2xl p-4 shadow-2xl text-white pointer-events-auto flex items-start gap-3.5">
            <div id="toastIconBox" class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-500/40">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <h5 id="toastTitle" class="font-black text-xs text-emerald-300 truncate">تنبيه فوري جديد</h5>
                    <span id="toastTime" class="text-[10px] text-slate-400">الآن</span>
                </div>
                <p id="toastMessage" class="text-xs text-slate-200 line-clamp-2 leading-relaxed mb-2.5">
                    محتوى التنبيه هنا...
                </p>
                <div class="flex items-center gap-2">
                    <a id="toastActionBtn" href="#" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition flex items-center gap-1.5 shadow-sm">
                        <span>عرض والرد الآن</span>
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    </a>
                    <button onclick="dismissToastAlert()" type="button" class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 text-xs font-bold transition">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Phone Notifications & Live Alert Polling Script -->
    <script>
        // Track last seen items to prevent repeating alerts
        let lastSeenMessageId = null;
        let lastSeenBookingId = null;
        let isPolling = false;

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function () {
            checkNotificationPermissionStatus();
            // Start polling every 6 seconds
            setInterval(pollUnreadAlerts, 6000);
            // Run immediate check
            setTimeout(pollUnreadAlerts, 1500);
        });

        function checkNotificationPermissionStatus() {
            const btn = document.getElementById('btnEnableNotifications');
            const statusText = document.getElementById('notifStatusText');
            const pingDot = document.getElementById('notifPingDot');
            const staticDot = document.getElementById('notifStaticDot');

            if (!('Notification' in window)) {
                if (btn) btn.style.display = 'none';
                return;
            }

            if (Notification.permission === 'granted') {
                if (statusText) statusText.innerText = 'إشعارات الهاتف مفعلة';
                if (pingDot) { pingDot.className = 'hidden'; }
                if (staticDot) { staticDot.className = 'relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500'; }
                if (btn) {
                    btn.classList.remove('bg-slate-100', 'text-slate-700', 'border-slate-300');
                    btn.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-300');
                }
            } else if (Notification.permission === 'denied') {
                if (statusText) statusText.innerText = 'الإشعارات محظورة بالمتصفح';
                if (pingDot) { pingDot.className = 'hidden'; }
                if (staticDot) { staticDot.className = 'relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500'; }
            } else {
                if (statusText) statusText.innerText = 'تفعيل إشعارات الجوال';
            }
        }

        async function enablePhoneNotifications() {
            if (!('Notification' in window)) {
                alert('عذراً، هذا المتصفح لا يدعم إشعارات النظام.');
                return;
            }

            try {
                const permission = await Notification.requestPermission();
                checkNotificationPermissionStatus();

                if (permission === 'granted') {
                    playNotificationChime();
                    vibratePhone();

                    // Register Service Worker for background push when site is closed
                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        try {
                            const reg = await navigator.serviceWorker.register('/sw.js');
                            const vapidRes = await fetch("{{ route('investor.api.vapid-key') }}");
                            const vapidData = await vapidRes.json();

                            if (vapidData.publicKey) {
                                const sub = await reg.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: urlBase64ToUint8Array(vapidData.publicKey)
                                });

                                // Send to server
                                await fetch("{{ route('investor.api.push-subscribe') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    },
                                    body: JSON.stringify(sub)
                                });
                            }
                        } catch (swErr) {
                            console.warn('Service worker push registration note:', swErr);
                        }
                    }

                    // Send test notification to verify
                    new Notification('✅ تم تفعيل إشعارات منصة دارك على هاتفك', {
                        body: 'ستصلك الآن تنبيهات صوتية واهتزاز فور وصول أي رسالة من نزيل أو تأكيد حجز حتى والموقع مغلق.',
                        icon: '/client-entry-qr.png',
                        badge: '/client-entry-qr.png',
                    });

                    showToastAlert('تم تفعيل إشعارات الهاتف!', 'ستصلك تنبيهات فورية بالصوت والنص عند وصول أي رسالة أو حجز جديد حتى لو كان الموقع مغلقاً.', '#', 'success');
                }
            } catch (err) {
                console.error('Error requesting notification permission:', err);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // Web Audio API Synthesized High-Priority Bell Sound (No external audio file needed)
        function playNotificationChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();

                // Two-tone bell chime
                const now = ctx.currentTime;
                
                // Tone 1
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now); // D5
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.5);

                // Tone 2 (higher note)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.15); // A5
                gain2.gain.setValueAtTime(0.35, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.75);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.75);
            } catch (e) {
                // Audio context may be restricted before user gesture
            }
        }

        function vibratePhone() {
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200, 100, 300]);
            }
        }

        // Poll backend for unread messages and bookings
        async function pollUnreadAlerts() {
            if (isPolling) return;
            isPolling = true;

            try {
                const res = await fetch("{{ route('investor.api.unread-alerts') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();

                // Update unread badge in top navbar
                const badge = document.getElementById('navUnreadChatBadge');
                if (badge) {
                    if (data.unread_messages_count > 0) {
                        badge.innerText = data.unread_messages_count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // Check for new customer message
                if (data.latest_message && data.latest_message.id !== lastSeenMessageId) {
                    if (lastSeenMessageId !== null) {
                        // Truly a new message that arrived!
                        triggerPhoneAlert(
                            `📩 رسالة من النزيل (${data.latest_message.sender_name})`,
                            `شقة ${data.latest_message.apartment_number}: "${data.latest_message.snippet}"`,
                            data.latest_message.url,
                            'message'
                        );
                    }
                    lastSeenMessageId = data.latest_message.id;
                }

                // Check for new booking
                if (data.latest_booking && data.latest_booking.id !== lastSeenBookingId) {
                    if (lastSeenBookingId !== null) {
                        triggerPhoneAlert(
                            `🎉 حجز جديد برقم #${data.latest_booking.reference}`,
                            `النزيل: ${data.latest_booking.customer_name} | المبلغ: ${data.latest_booking.total_amount} ريال | شقة ${data.latest_booking.apartment_number}`,
                            data.latest_booking.url,
                            'booking'
                        );
                    }
                    lastSeenBookingId = data.latest_booking.id;
                }

            } catch (err) {
                // Ignore transient network errors
            } finally {
                isPolling = false;
            }
        }

        function triggerPhoneAlert(title, message, url, type) {
            playNotificationChime();
            vibratePhone();

            // Native Phone / System Notification
            if ('Notification' in window && Notification.permission === 'granted') {
                const notif = new Notification(title, {
                    body: message,
                    icon: '/images/dark-logo.jpg',
                    badge: '/images/dark-logo.jpg',
                    vibrate: [200, 100, 200],
                    data: { url: url }
                });
                notif.onclick = function () {
                    window.focus();
                    if (url && url !== '#') window.location.href = url;
                };
            }

            // In-App Toast
            showToastAlert(title, message, url, type);
        }

        function showToastAlert(title, message, url, type) {
            const toast = document.getElementById('liveAlertToast');
            const titleElem = document.getElementById('toastTitle');
            const msgElem = document.getElementById('toastMessage');
            const actionBtn = document.getElementById('toastActionBtn');
            const iconBox = document.getElementById('toastIconBox');

            if (!toast) return;

            titleElem.innerText = title;
            msgElem.innerText = message;
            actionBtn.href = url;

            if (type === 'booking') {
                iconBox.innerHTML = '<i class="fa-solid fa-calendar-check text-amber-400"></i>';
                iconBox.className = 'w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg flex-shrink-0 border border-amber-500/40';
            } else {
                iconBox.innerHTML = '<i class="fa-solid fa-comments text-emerald-400"></i>';
                iconBox.className = 'w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-500/40';
            }

            toast.classList.remove('-translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            // Auto dismiss after 9 seconds
            setTimeout(dismissToastAlert, 9000);
        }

        function dismissToastAlert() {
            const toast = document.getElementById('liveAlertToast');
            if (toast) {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('-translate-y-24', 'opacity-0');
            }
        }
    </script>

    @yield('scripts')
</body>
</html>

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
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="دارك للمستثمر">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'بوابة المستثمر') - منصة دارك</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @keyframes invFadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bellWiggle {
            0%, 100% { transform: rotate(0deg); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-12deg); }
            45% { transform: rotate(10deg); }
            60% { transform: rotate(-8deg); }
            75% { transform: rotate(4deg); }
        }
        .inv-fade-up { animation: invFadeUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .bell-wiggle { animation: bellWiggle 1.2s ease-in-out infinite; transform-origin: top center; }
        @media (prefers-reduced-motion: reduce) {
            .inv-fade-up, .bell-wiggle { animation: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex selection:bg-emerald-500 selection:text-white">

    @php
        $investor = auth()->user()->investor;
        $wallet = $investor?->wallet;
        $subscription = $investor?->activeSubscription;
        $unreadChats = $investor ? \App\Models\Message::whereIn('conversation_id', $investor->conversations()->pluck('id'))->whereIn('sender_type', ['customer', 'admin'])->where('is_read_by_investor', false)->count() : 0;
        $pushSubsCount = $investor ? \App\Models\PushSubscription::where('investor_id', $investor->id)->count() : 0;
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

                <!-- Backdrop overlay on mobile -->
                <div id="notifBackdrop" onclick="toggleNotifPanel()" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 transition-opacity"></div>

                <!-- Live Bell Notifications Button + Control Panel -->
                <div class="relative" id="notifBellWrapper">
                    <button onclick="toggleNotifPanel()" type="button" class="relative p-2 text-slate-600 hover:text-emerald-600 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-200 transition" title="إعدادات واختبار إشعارات الحجز">
                        <i id="navBellIcon" class="fa-solid fa-bell text-base"></i>
                        <span id="navAlertPulse" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                        <span id="navAlertDot" class="hidden absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Notifications Control Popover (Fully centered on mobile, never cut off) -->
                    <div id="notifSettingsPanel" class="hidden fixed inset-x-3 top-20 sm:top-full sm:mt-2 sm:inset-x-auto sm:left-0 sm:right-auto sm:absolute w-auto sm:w-84 max-w-sm mx-auto bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-50 text-right space-y-3.5 transition-all">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <span class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-bell text-emerald-600"></i>
                                <span>إشعارات الحجوزات الفورية</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <span id="pushDevicesBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    <span id="pushDevicesCount">{{ $pushSubsCount }}</span> متصل
                                </span>
                                <button type="button" onclick="toggleNotifPanel()" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition sm:hidden" title="إغلاق">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div id="pushPanelStatus" class="text-xs rounded-xl p-3 bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed">
                            جاري فحص حالة الإشعارات على هذا الهاتف...
                        </div>

                        <!-- iPhone Safari Home Screen Setup Guide inside Bell Panel -->
                        <div id="iosPanelGuide" class="hidden rounded-xl p-3 bg-amber-50 border border-amber-200 text-amber-950 text-[11px] space-y-2 leading-relaxed">
                            <div class="font-black text-amber-900 flex items-center gap-1.5">
                                <i class="fa-brands fa-apple text-sm"></i>
                                <span>خطوات التفعيل على الآيفون (iPhone):</span>
                            </div>
                            <ol class="list-decimal list-inside space-y-1 text-amber-900 font-medium">
                                <li>افتح هذا الرابط في متصفح <strong>Safari</strong>.</li>
                                <li>اضغط أيقونة <strong>المشاركة</strong> <i class="fa-solid fa-arrow-up-from-bracket text-sky-600 mx-0.5"></i> بالأسفل.</li>
                                <li>اختر <strong>«إضافة إلى الشاشة الرئيسية»</strong> (Add to Home Screen).</li>
                                <li>افتح التطبيق من شاشة الآيفون واضغط <strong>«تفعيل الإشعارات»</strong>.</li>
                            </ol>
                        </div>

                        <div class="space-y-2" id="pushActionsWrapper">
                            <!-- Button shown ONLY when permission is NOT granted -->
                            <button id="panelEnablePushBtn" onclick="enablePushNotifications(false)" type="button" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fa-solid fa-bell"></i>
                                <span>تفعيل واستقبال الإشعارات (لمرة واحدة)</span>
                            </button>

                            <!-- Button shown when permission IS granted: Immediate test -->
                            <button id="panelTestPushBtn" onclick="sendServerTestPush(0)" type="button" class="hidden w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fa-solid fa-paper-plane text-amber-400"></i>
                                <span>🔔 إرسال إشعار فوري للتجربة</span>
                            </button>

                            <!-- Button shown when permission IS granted: 5-second delayed test (to test while website is closed) -->
                            <button id="panelTestDelayedPushBtn" onclick="sendServerTestPush(5)" type="button" class="hidden w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition flex items-center justify-center gap-2 shadow-sm">
                                <i class="fa-solid fa-clock"></i>
                                <span>⏰ تجربة الإشعار بعد 5 ثوانٍ (أغلق الموقع وجرب)</span>
                            </button>
                        </div>

                        <div id="pushTroubleshootLink" class="hidden text-center pt-1 border-t border-slate-100">
                            <button type="button" onclick="syncPushSubscription(true)" class="text-[10px] text-slate-400 hover:text-emerald-700 underline transition">
                                إعادة مزامنة هذا الهاتف مع السيرفر
                            </button>
                        </div>

                        <p class="text-[10px] text-slate-400 leading-relaxed text-center">
                            تصلك تنبيهات الحجوزات ورسائل النزلاء بصوت واهتزاز حتى لو كان المتصفح مغلقاً.
                        </p>
                    </div>
                </div>

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
        <main class="flex-1 p-4 md:p-8 space-y-5">
            <!-- Push Activation Banner (shown once ONLY if permission is default and not dismissed) -->
            <div id="pushEnableBanner" class="hidden rounded-2xl bg-gradient-to-l from-emerald-50 to-teal-50 border border-emerald-200 p-3.5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm flex-shrink-0 shadow-xs">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-slate-900">تفعيل إشعارات الحجوزات والرسائل الفورية</h4>
                        <p class="text-[11px] text-slate-600 mt-0.5">
                            لتصلك طلبات الإيجار ورسائل النزلاء بصوت واهتزاز فورياً حتى لو كان المتصفح مغلقاً.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-auto">
                    <button onclick="enablePushNotifications(false)" type="button" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>تفعيل لمرة واحدة</span>
                    </button>
                    <button onclick="dismissPushBanner()" type="button" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition" title="إخفاء">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- iPhone Safari Home Screen Guide Banner -->
            <div id="iosPushBanner" class="hidden rounded-2xl bg-gradient-to-l from-amber-50 via-white to-sky-50 border-2 border-amber-300 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 inv-fade-up">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
                        <i class="fa-brands fa-apple"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-xs sm:text-sm text-slate-900">تفعيل إشعارات الآيفون (توصلك فوراً والموقع مغلق)</h4>
                        <p class="text-[11px] text-slate-700 leading-relaxed">
                            على الآيفون: اضغط زر <strong>المشاركة</strong> <i class="fa-solid fa-arrow-up-from-bracket text-sky-600 mx-0.5"></i> في Safari ثم اختر <strong>«إضافة إلى الشاشة الرئيسية»</strong>، وبعدها افتح أيقونة <strong>«دارك للمستثمر»</strong> من شاشتك الرئيسية لتصلك الإشعارات فوراً!
                        </p>
                    </div>
                </div>
                <button onclick="dismissIosBanner()" type="button" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition self-end sm:self-center flex-shrink-0" title="إخفاء">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Blocked Notifications Hint -->
            <div id="pushBlockedBanner" class="hidden rounded-2xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-900 flex items-start gap-3">
                <i class="fa-solid fa-bell-slash text-rose-600 text-base flex-shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="font-black text-rose-950">الإشعارات محظورة في إعدادات المتصفح حالياً</h4>
                    <p class="mt-0.5 leading-relaxed text-[11px]">لكي تصلك تنبيهات الحجوزات والموقع مغلق، اضغط على أيقونة الإعدادات بجانب رابط الموقع في المتصفح واختر <strong>السماح بالإشعارات (Allow)</strong> ثم أعد تحميل الصفحة.</p>
                </div>
            </div>

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

    <!-- Enable Native Notifications Button (shown once when default) -->
    <div id="notifPermBtn" class="fixed bottom-5 left-5 z-40 hidden">
        <button onclick="enablePushNotifications(true)" type="button"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 border border-emerald-400 text-white text-xs font-black shadow-xl transition-all">
            <i class="fa-solid fa-bell text-amber-300 animate-pulse"></i>
            <span>فعّل الإشعارات الفورية</span>
        </button>
    </div>

    <!-- Live Alert Polling & Background Web Push Engine -->
    <script>
        const SW_URL = @json(url('/sw.js'));
        const SW_SCOPE = @json(rtrim(parse_url(url('/'), PHP_URL_PATH) ?: '', '/') . '/');
        const VAPID_KEY_URL = @json(route('investor.api.vapid-key'));
        const EMBEDDED_VAPID_PUBLIC_KEY = @json(\App\Services\WebPushService::getPublicKey());
        const PUSH_SUBSCRIBE_URL = @json(route('investor.api.push-subscribe'));
        const TEST_PUSH_URL = @json(route('investor.api.test-push'));
        const UNREAD_ALERTS_URL = @json(route('investor.api.unread-alerts'));
        const LOGO_ICON_URL = @json(asset('images/icon-192.png'));
        const CSRF_TOKEN = @json(csrf_token());

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;

        let lastSeenMessageId = null;
        let lastSeenBookingId = null;
        let lastSeenInspectionId = null;
        let isPolling = false;
        let chimeUnlocked = false;
        let toastTimeout = null;
        let swRegistration = null;
        let cachedVapidPublicKey = EMBEDDED_VAPID_PUBLIC_KEY || null;

        // Prevent automatic browser app install prompts completely
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            return false;
        });

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

        function arrayBufferToBase64Url(buffer) {
            if (!buffer) return '';
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        function extractSubscriptionPayload(sub) {
            const subJson = sub.toJSON ? sub.toJSON() : {};
            const keys = subJson.keys || {};
            const p256dh = keys.p256dh || (sub.getKey ? arrayBufferToBase64Url(sub.getKey('p256dh')) : '');
            const auth = keys.auth || (sub.getKey ? arrayBufferToBase64Url(sub.getKey('auth')) : '');
            const encoding = (window.PushManager && PushManager.supportedContentEncodings && PushManager.supportedContentEncodings[0]) || 'aes128gcm';
            return {
                endpoint: subJson.endpoint || sub.endpoint,
                keys: { p256dh, auth },
                contentEncoding: encoding,
                device_label: detectDeviceLabel()
            };
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

        function uint8ArraysEqual(a, b) {
            if (!a || !b || a.byteLength !== b.byteLength) return false;
            const viewA = new Uint8Array(a);
            const viewB = new Uint8Array(b);
            for (let i = 0; i < viewA.length; i++) {
                if (viewA[i] !== viewB[i]) return false;
            }
            return true;
        }

        // ─── Init on page load ──────────────────────────────────────
        document.addEventListener('DOMContentLoaded', async function () {
            // Audio unlock on user gesture
            document.addEventListener('click', function unlockAudio() {
                chimeUnlocked = true;
                document.removeEventListener('click', unlockAudio);
            }, { once: true });

            // Close popover when clicking outside on desktop
            document.addEventListener('click', function (e) {
                const wrapper = document.getElementById('notifBellWrapper');
                const panel = document.getElementById('notifSettingsPanel');
                const backdrop = document.getElementById('notifBackdrop');
                if (wrapper && panel && !wrapper.contains(e.target)) {
                    panel.classList.add('hidden');
                    if (backdrop) backdrop.classList.add('hidden');
                }
            });

            // Register Service Worker with updateViaCache: 'none' for instant updates
            if ('serviceWorker' in navigator) {
                try {
                    swRegistration = await navigator.serviceWorker.register(SW_URL, {
                        scope: SW_SCOPE,
                        updateViaCache: 'none'
                    });
                    await navigator.serviceWorker.ready;

                    navigator.serviceWorker.addEventListener('message', function (event) {
                        if (!event.data) return;
                        if (event.data.type === 'DARK_NAVIGATE' && event.data.url) {
                            window.location.href = event.data.url;
                        } else if (event.data.type === 'DARK_PUSH_RECEIVED' && event.data.payload) {
                            playChime();
                            vibratePhone();
                            showToast(event.data.payload.title, event.data.payload.body, event.data.payload.url || '#', 'booking');
                            pollUnreadAlerts();
                        }
                    });

                    if ('periodicSync' in swRegistration) {
                        try {
                            const status = await navigator.permissions.query({ name: 'periodic-background-sync' });
                            if (status.state === 'granted') {
                                await swRegistration.periodicSync.register('dark-poll-messages', {
                                    minInterval: 15 * 60 * 1000
                                });
                            }
                        } catch (_) {}
                    }
                } catch (err) {
                    console.warn('SW registration error:', err);
                }
            }

            // Refresh VAPID key silently in background so click handler stays 100% synchronous for iOS
            if (!cachedVapidPublicKey) {
                fetch(VAPID_KEY_URL, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.ok ? r.json() : null)
                    .then(d => { if (d && d.publicKey) cachedVapidPublicKey = d.publicKey; })
                    .catch(() => {});
            }

            refreshPushUiAndSync();

            // Start polling unread alerts (first check at 1.2s then every 5s while tab open)
            setTimeout(pollUnreadAlerts, 1200);
            setInterval(pollUnreadAlerts, 5000);
        });

        function dismissPushBanner() {
            const banner = document.getElementById('pushEnableBanner');
            if (banner) banner.classList.add('hidden');
            localStorage.setItem('dark_dismiss_push_banner', '1');
        }

        function dismissIosBanner() {
            const banner = document.getElementById('iosPushBanner');
            if (banner) banner.classList.add('hidden');
            localStorage.setItem('dark_dismiss_ios_banner', '1');
        }

        async function refreshPushUiAndSync() {
            const enableBanner = document.getElementById('pushEnableBanner');
            const iosBanner = document.getElementById('iosPushBanner');
            const iosPanelGuide = document.getElementById('iosPanelGuide');
            const blockedBanner = document.getElementById('pushBlockedBanner');
            const dot = document.getElementById('navAlertDot');
            const statusBox = document.getElementById('pushPanelStatus');
            const enableBtn = document.getElementById('panelEnablePushBtn');
            const testBtn = document.getElementById('panelTestPushBtn');
            const delayedBtn = document.getElementById('panelTestDelayedPushBtn');
            const troubleshootLink = document.getElementById('pushTroubleshootLink');

            if (isIOS && !isStandalone) {
                if (enableBanner) enableBanner.classList.add('hidden');
                if (enableBtn) enableBtn.classList.add('hidden');
                if (testBtn) testBtn.classList.add('hidden');
                if (delayedBtn) delayedBtn.classList.add('hidden');
                if (iosPanelGuide) iosPanelGuide.classList.remove('hidden');
                if (iosBanner && !localStorage.getItem('dark_dismiss_ios_banner')) {
                    iosBanner.classList.remove('hidden');
                }
                if (statusBox) {
                    statusBox.innerHTML = '<div class="flex items-center gap-1.5 text-amber-800 font-bold text-xs"><i class="fa-brands fa-apple text-slate-900 text-sm"></i><span>أضف الموقع للشاشة الرئيسية لتفعيل إشعارات الآيفون</span></div><p class="text-[11px] text-amber-950 mt-1 leading-relaxed">نظام iOS يرسل إشعارات الخلفية فقط بعد تثبيت الموقع على الشاشة الرئيسية عبر متصفح Safari.</p>';
                    statusBox.className = 'text-xs rounded-xl p-3 bg-amber-50 border border-amber-200 text-amber-950 leading-relaxed';
                }
                return;
            }

            if (iosBanner) iosBanner.classList.add('hidden');
            if (iosPanelGuide) iosPanelGuide.classList.add('hidden');

            if (!('Notification' in window) || !('PushManager' in window)) {
                if (statusBox) {
                    statusBox.innerHTML = '⚠️ هذا المتصفح لا يدعم إشعارات الخلفية، ولكن التنبيهات المباشرة داخل الصفحة تعمل.';
                }
                return;
            }

            if (Notification.permission === 'granted') {
                if (enableBanner) enableBanner.classList.add('hidden');
                if (blockedBanner) blockedBanner.classList.add('hidden');
                if (dot) dot.classList.remove('hidden');

                // When granted: HIDE the enable button completely, SHOW tests and status
                if (enableBtn) enableBtn.classList.add('hidden');
                if (testBtn) testBtn.classList.remove('hidden');
                if (delayedBtn) delayedBtn.classList.remove('hidden');
                if (troubleshootLink) troubleshootLink.classList.remove('hidden');

                if (statusBox) {
                    statusBox.innerHTML = '<div class="flex items-center gap-1.5 text-emerald-800 font-bold text-xs"><i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i><span>الإشعارات مفعلة ونشطة على هذا الهاتف</span></div><p class="text-[11px] text-emerald-900 mt-1 leading-relaxed">ستصلك إشعارات الحجوزات والرسائل في الخلفية تلقائياً دون الحاجة لفتح الموقع.</p>';
                    statusBox.className = 'text-xs rounded-xl p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 leading-relaxed';
                }

                // Silent background sync
                syncPushSubscription(false);
            } else if (Notification.permission === 'default') {
                const isDismissed = localStorage.getItem('dark_dismiss_push_banner');
                if (enableBanner && !isDismissed) enableBanner.classList.remove('hidden');
                if (blockedBanner) blockedBanner.classList.add('hidden');
                if (dot) dot.classList.add('hidden');

                if (enableBtn) enableBtn.classList.remove('hidden');
                if (testBtn) testBtn.classList.add('hidden');
                if (delayedBtn) delayedBtn.classList.add('hidden');
                if (troubleshootLink) troubleshootLink.classList.add('hidden');

                if (statusBox) {
                    statusBox.innerHTML = '<div class="flex items-center gap-1.5 text-amber-800 font-bold text-xs"><i class="fa-solid fa-bell text-amber-600 text-sm"></i><span>الإشعارات غير مفعلة على هذا الهاتف</span></div><p class="text-[11px] text-amber-950 mt-1 leading-relaxed">اضغط الزر الأخضر بالأسفل لتفعيلها مرة واحدة واستقبال تنبيهات المستأجرين فوراً.</p>';
                    statusBox.className = 'text-xs rounded-xl p-3 bg-amber-50 border border-amber-200 text-amber-950 leading-relaxed';
                }
            } else if (Notification.permission === 'denied') {
                if (enableBanner) enableBanner.classList.add('hidden');
                if (blockedBanner) blockedBanner.classList.remove('hidden');
                if (dot) dot.classList.add('hidden');

                if (enableBtn) enableBtn.classList.add('hidden');
                if (testBtn) testBtn.classList.add('hidden');
                if (delayedBtn) delayedBtn.classList.add('hidden');
                if (troubleshootLink) troubleshootLink.classList.add('hidden');

                if (statusBox) {
                    statusBox.innerHTML = '<div class="flex items-center gap-1.5 text-rose-800 font-bold text-xs"><i class="fa-solid fa-ban text-rose-600 text-sm"></i><span>الإشعارات محظورة في إعدادات المتصفح</span></div><p class="text-[11px] text-rose-900 mt-1 leading-relaxed">يرجى الضغط على إعدادات الموقع بجانب رابط المتصفح واختيار السماح بالإشعارات.</p>';
                    statusBox.className = 'text-xs rounded-xl p-3 bg-rose-50 border border-rose-200 text-rose-900 leading-relaxed';
                }
            }
        }

        // ─── Subscribe to Web Push (VAPID) and save endpoint to backend ─
        async function syncPushSubscription(showFeedback) {
            try {
                if (!('serviceWorker' in navigator) || !('PushManager' in window)) return false;

                const reg = swRegistration || await navigator.serviceWorker.ready;
                if (!reg || !reg.pushManager) return false;

                let publicKey = cachedVapidPublicKey;
                if (!publicKey) {
                    const keyRes = await fetch(VAPID_KEY_URL, { headers: { 'Accept': 'application/json' } });
                    if (!keyRes.ok) return false;
                    const keyData = await keyRes.json();
                    publicKey = keyData.publicKey;
                    cachedVapidPublicKey = publicKey;
                }
                if (!publicKey) return false;

                const appServerKey = urlBase64ToUint8Array(publicKey);
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

                const payload = extractSubscriptionPayload(sub);
                if (!payload.endpoint || !payload.keys.p256dh || !payload.keys.auth) {
                    return false;
                }

                const saveRes = await fetch(PUSH_SUBSCRIBE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(payload)
                });

                if (!saveRes.ok) return false;
                const saveData = await saveRes.json();
                const countEl = document.getElementById('pushDevicesCount');
                if (countEl && typeof saveData.subscriptions_count === 'number') {
                    countEl.innerText = saveData.subscriptions_count;
                }
                if (showFeedback) {
                    showToast('✅ تم ربط الجهاز بنجاح', 'تمت مزامنة هذا الهاتف (' + payload.device_label + ') مع السيرفر بنجاح.', '#', 'system');
                }
                return true;
            } catch (err) {
                console.warn('WebPush sync failed:', err);
                if (showFeedback) {
                    showToast('تعذر ربط الجهاز', 'تأكد من عدم حظر الإشعارات أو تفعيل وضع التصفح الخاص (Incognito).', '#', 'system');
                }
                return false;
            }
        }

        // ─── User-triggered Enable + Subscribe (preserves iOS user gesture) ─
        async function enablePushNotifications(sendTestAfter) {
            playChime();
            vibratePhone();

            if (isIOS && !isStandalone) {
                toggleNotifPanel();
                return;
            }

            if (!('Notification' in window)) {
                showToast('تنبيه المتصفح', 'المتصفح لا يدعم إشعارات النظام، لكن التنبيهات المباشرة تعمل.', '#', 'system');
                return;
            }

            let perm = Notification.permission;
            if (perm !== 'granted') {
                perm = await Notification.requestPermission();
            }

            if (perm === 'granted') {
                const synced = await syncPushSubscription(true);
                await refreshPushUiAndSync();

                if (synced && sendTestAfter) {
                    await sendServerTestPush(0);
                } else if (synced) {
                    showToast('🔔 تم تفعيل الإشعارات بنجاح!', 'ستصلك إشعارات الحجوزات والرسائل حتى لو كان الموقع مغلقاً.', '#', 'system');
                }
            } else {
                await refreshPushUiAndSync();
                showToast('لم يتم منح الصلاحية', 'يرجى السماح بالإشعارات من إعدادات المتصفح لتصلك الحجوزات فوراً.', '#', 'system');
            }
        }

        // ─── Trigger real server WebPush test (immediate or delayed) ─
        async function sendServerTestPush(delaySeconds) {
            delaySeconds = delaySeconds || 0;
            const btn = delaySeconds > 0
                ? document.getElementById('panelTestDelayedPushBtn')
                : document.getElementById('panelTestPushBtn');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = delaySeconds > 0
                    ? '<i class="fa-solid fa-spinner fa-spin"></i><span>جاري الجدولة...</span>'
                    : '<i class="fa-solid fa-spinner fa-spin"></i><span>جاري الإرسال...</span>';
            }
            try {
                if ('Notification' in window && Notification.permission === 'granted') {
                    await syncPushSubscription(false);
                }
                const url = delaySeconds > 0
                    ? (TEST_PUSH_URL + '?delay=' + delaySeconds)
                    : TEST_PUSH_URL;

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
                const data = await res.json();
                const countEl = document.getElementById('pushDevicesCount');
                if (countEl && typeof data.subscriptions_count === 'number') {
                    countEl.innerText = data.subscriptions_count;
                }
                if (data.success) {
                    if (data.delayed) {
                        showToast('⏰ تمت جدولة الإشعار!', data.message || 'أغلق الموقع والمتصفح الآن تماماً للتأكد من وصوله في الخلفية.', '#', 'system');
                    } else {
                        showToast('🚀 تم إرسال الإشعار!', data.message || 'تحقق من شريط الإشعارات أعلى هاتفك الآن.', '#', 'system');
                    }
                } else {
                    showToast('⚠️ تنبيه الإشعارات', data.message || 'يرجى تفعيل الإشعارات أولاً.', '#', 'system');
                }
            } catch (err) {
                showToast('❌ خطأ في الاتصال', 'تعذر إرسال الإشعار التجريبي الآن، حاول مرة أخرى.', '#', 'system');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        // ─── Bell Popover Toggle ────────────────────────────────────
        function toggleNotifPanel() {
            const panel = document.getElementById('notifSettingsPanel');
            const backdrop = document.getElementById('notifBackdrop');
            if (!panel) return;
            const willOpen = panel.classList.contains('hidden');
            panel.classList.toggle('hidden', !willOpen);
            if (backdrop) backdrop.classList.toggle('hidden', !willOpen);
            if (willOpen) {
                refreshPushUiAndSync();
            }
        }

        // ─── Poll backend (live alerts while tab is open) ───────────────
        async function pollUnreadAlerts() {
            if (isPolling) return;
            isPolling = true;
            try {
                const res = await fetch(UNREAD_ALERTS_URL, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                const countEl = document.getElementById('pushDevicesCount');
                if (countEl && typeof data.push_subscriptions_count === 'number') {
                    countEl.innerText = data.push_subscriptions_count;
                }

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
                    if (data.unread_messages_count > 0 || (data.unread_bookings_count && data.unread_bookings_count > 0) || (data.recent_inspections_count && data.recent_inspections_count > 0)) {
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
                            `🎉 طلب حجز جديد #${data.latest_booking.reference}`,
                            `${data.latest_booking.customer_name} | ${data.latest_booking.total_amount} ريال | شقة ${data.latest_booking.apartment_number}`,
                            data.latest_booking.url,
                            'booking'
                        );
                    }
                    lastSeenBookingId = data.latest_booking.id;
                }

                // New inspection request notification
                if (data.latest_inspection && data.latest_inspection.id !== lastSeenInspectionId) {
                    if (lastSeenInspectionId !== null) {
                        triggerAlert(
                            `👁 طلب معاينة لشقة ${data.latest_inspection.apartment_number}`,
                            `${data.latest_inspection.customer_name} | ${data.latest_inspection.customer_phone}`,
                            data.latest_inspection.url,
                            'booking'
                        );
                    }
                    lastSeenInspectionId = data.latest_inspection.id;
                }
            } catch (_) { /* ignore network error */ } finally {
                isPolling = false;
            }
        }

        // ─── Trigger Alert (Chime + Vibrate + Native + Toast) ────
        function triggerAlert(title, message, url, type) {
            playChime();
            vibratePhone();

            // Native notification via ServiceWorker first (works reliably on Android & Desktop), fallback to new Notification()
            if ('Notification' in window && Notification.permission === 'granted') {
                const notifOptions = {
                    body: message,
                    icon: LOGO_ICON_URL,
                    badge: LOGO_ICON_URL,
                    vibrate: [200, 100, 200, 100, 300],
                    tag: type + '-' + Date.now(),
                    renotify: true,
                    data: { url: url }
                };

                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(reg => {
                        return reg.showNotification(title, notifOptions);
                    }).catch(() => {
                        try {
                            const n = new Notification(title, notifOptions);
                            n.onclick = () => {
                                window.focus();
                                if (url && url !== '#') window.location.href = url;
                            };
                        } catch (_) {}
                    });
                } else {
                    try {
                        const n = new Notification(title, notifOptions);
                        n.onclick = () => {
                            window.focus();
                            if (url && url !== '#') window.location.href = url;
                        };
                    } catch (_) {}
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

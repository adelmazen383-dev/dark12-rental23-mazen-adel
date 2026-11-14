<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - منصة دَارِك لتنظيم الإيجار</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 selection:bg-amber-500 selection:text-white">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-6 sm:p-8 border border-slate-100">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <div class="w-20 h-20 mx-auto mb-3 rounded-2xl overflow-hidden bg-white p-1 border-2 border-amber-400 shadow-md flex items-center justify-center">
                <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
            </div>
            <h2 class="text-xl font-black text-gray-900">تسجيل الدخول للنظام</h2>
            <p class="text-xs text-gray-500 mt-1">منصة دَارِك لتنظيم الإيجار (بوابة الإدارة والمستثمرين)</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-amber-600 text-base"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5 flex-shrink-0"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="name@example.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-gray-700">كلمة المرور</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 hover:underline">
                        نسيت كلمة المرور؟
                    </a>
                </div>
                <input type="password" name="password" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-amber-500 focus:ring-amber-400">
                    <span class="text-gray-600 font-medium">تذكرني على هذا الجهاز</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl shadow-md transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                <span>تسجيل الدخول</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 space-y-3 text-center">
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-xs text-gray-600 font-medium mb-2">مستثمر جديد وترغب في تسجيل وحدتك؟</p>
                <a href="{{ route('investor.register') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                    <i class="fa-solid fa-user-plus text-amber-400"></i>
                    <span>إنشاء حساب مستثمر جديد وتسجيل شقة</span>
                </a>
            </div>

            <div>
                <a href="{{ route('client.apartments.index') }}" class="text-xs font-medium text-gray-400 hover:text-gray-700 flex items-center justify-center gap-1.5 transition">
                    <i class="fa-solid fa-qrcode text-amber-500"></i>
                    <span>العودة لبوابة العملاء وتصفح الوحدات</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
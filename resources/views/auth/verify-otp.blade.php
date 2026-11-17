<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال رمز التحقق (OTP) - منصة دَارِك</title>
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
            <h2 class="text-xl font-black text-gray-900">تأكيد رمز التحقق (OTP)</h2>
            <p class="text-xs text-gray-500 mt-1">منصة دَارِك لتنظيم الإيجار</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('demo_otp'))
            <div class="mb-4 p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-indigo-600 text-base"></i>
                    <span>رمز التحقق المرسل لبريدك: <strong class="font-mono text-sm tracking-wider text-indigo-700">{{ session('demo_otp') }}</strong></span>
                </div>
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

        <!-- Notice -->
        <div class="mb-5 p-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-envelope text-amber-500 text-sm"></i>
                <span class="truncate max-w-[200px]" dir="ltr">{{ $email }}</span>
            </div>
            <a href="{{ route('password.request') }}" class="text-[11px] text-amber-600 font-bold hover:underline">
                تغيير الإيميل
            </a>
        </div>

        <form action="{{ route('password.verify_submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <!-- OTP Input -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                    رمز التحقق (OTP) المكون من 6 أرقام *
                </label>
                <div class="relative">
                    <input type="text" name="otp" value="{{ old('otp') }}" required autofocus maxlength="6"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-center text-2xl font-black font-mono tracking-[10px] text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 placeholder:text-gray-300 placeholder:tracking-normal placeholder:text-sm"
                        placeholder="••••••" dir="ltr" inputmode="numeric" pattern="[0-9]*">
                </div>
                <span class="text-[11px] text-gray-400 mt-1 block">تحقق من صندوق الوارد (أو مجلد الرسائل غير المرغوب فيها Spam)</span>
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">كلمة المرور الجديدة * (8 خانات كحد أدنى)</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="••••••••">
            </div>

            <!-- Confirm New Password -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">تأكيد كلمة المرور الجديدة *</label>
                <input type="password" name="password_confirmation" required minlength="8"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl shadow-md transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock"></i>
                <span>تأكيد وتغيير كلمة المرور</span>
            </button>
        </form>

        <!-- Resend OTP option -->
        <div class="mt-5 text-center">
            <form action="{{ route('password.send_otp') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="text-xs text-slate-500 hover:text-amber-600 font-bold transition flex items-center justify-center gap-1.5 mx-auto">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>لم يصلك الرمز؟ إعادة إرسال OTP جديد</span>
                </button>
            </form>
        </div>

        <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between text-xs">
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-800 flex items-center gap-1">
                <i class="fa-solid fa-arrow-right"></i>
                <span>العودة لتسجيل الدخول</span>
            </a>
            <a href="{{ route('client.apartments.index') }}" class="text-amber-600 hover:text-amber-700 font-bold">
                تصفح الشقق
            </a>
        </div>
    </div>
</body>
</html>

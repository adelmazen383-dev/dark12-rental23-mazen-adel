<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعادة كلمة المرور - منصة دَارِك لتنظيم الإيجار</title>
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
            <h2 class="text-xl font-black text-gray-900">استعادة كلمة المرور</h2>
            <p class="text-xs text-gray-500 mt-1">منصة دَارِك لتنظيم الإيجار</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
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

        <!-- Security Notice -->
        <div class="mb-5 p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
            <i class="fa-solid fa-shield-halved text-amber-600 text-base mt-0.5 flex-shrink-0"></i>
            <p class="leading-relaxed">
                لحماية حسابك وعقودك، سيتم التحقق بمطابقة بريدك الإلكتروني مع <strong>رقم الهوية الوطنية أو رقم الجوال</strong> المسجل في حسابك وتعيين كلمة المرور الجديدة فوراً.
            </p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">البريد الإلكتروني المسجل *</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="name@example.com">
                </div>
            </div>

            <!-- Verification Field (ID or Phone) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">رقم الهوية الوطنية أو رقم الجوال المسجل *</label>
                <input type="text" name="verification_data" value="{{ old('verification_data') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 text-left" dir="ltr" placeholder="10xxxxxxxx أو 05xxxxxxxx">
                <span class="text-[11px] text-gray-400 mt-1 block">للتحقق من ملكية الحساب وأمانه</span>
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
                <i class="fa-solid fa-key"></i>
                <span>حفظ وتعيين كلمة المرور الجديدة</span>
            </button>
        </form>

        <!-- Direct Admin WhatsApp Support -->
        @php
            $adminWa = \App\Models\Setting::getValue('admin_whatsapp', '0500000000');
            $cleanWa = preg_replace('/[^0-9]/', '', $adminWa);
            if (str_starts_with($cleanWa, '05')) {
                $cleanWa = '966' . substr($cleanWa, 1);
            }
        @endphp
        <div class="mt-5 p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-600 text-lg"></i>
                <span>هل واجهت صعوبة؟ تواصل مع الدعم الفني</span>
            </div>
            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('مرحباً إدارة دارك، أرغب في إعادة تعيين كلمة المرور لحسابي.') }}" target="_blank"
               class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition flex items-center gap-1 shadow-xs">
                <span>مراسلة الإدارة</span>
            </a>
        </div>

        <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between text-xs">
            <a href="{{ route('login') }}" class="font-bold text-gray-700 hover:text-amber-600 flex items-center gap-1">
                <i class="fa-solid fa-arrow-right"></i>
                <span>العودة لتسجيل الدخول</span>
            </a>
            <a href="{{ route('client.apartments.index') }}" class="text-gray-400 hover:text-gray-700">
                <span>تصفح الشقق</span>
            </a>
        </div>
    </div>
</body>
</html>
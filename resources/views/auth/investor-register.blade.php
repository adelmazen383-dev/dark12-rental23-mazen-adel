<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>تسجيل مستثمر وشقة جديدة - منصة دَارِك لتنظيم الإيجار</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen py-10 px-4 selection:bg-amber-500 selection:text-white">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl p-6 sm:p-10 border border-slate-100">
        <!-- Header -->
        <div class="text-center mb-8 border-b border-gray-100 pb-6">
            <div class="w-20 h-20 mx-auto mb-3 rounded-2xl overflow-hidden bg-white p-1 border-2 border-amber-400 shadow-md flex items-center justify-center">
                <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-full h-full object-cover rounded-xl">
            </div>
            <h1 class="text-2xl font-black text-gray-900">تسجيل مستثمر وشقة جديدة</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-lg mx-auto">
                انضم إلى منصة دَارِك لتنظيم الإيجار لعرض وحدتك للنزلاء عبر الباركود وإدارة الحجوزات والعقود المعتمدة.
            </p>

            <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200 inline-flex items-center gap-2 text-xs text-slate-700">
                <i class="fa-solid fa-circle-info text-amber-600"></i>
                <span>لديك حساب مستثمر بالفعل؟</span>
                <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:text-amber-700 hover:underline">
                    تسجيل الدخول من هنا
                </a>
            </div>
        </div>

        <!-- مهم جداً: إرشادات وسياسة تفعيل الحساب والمحفظة للمستثمر -->
        <div class="mb-8 rounded-2xl bg-gradient-to-br from-amber-50 via-amber-50/60 to-white border-2 border-amber-300 p-5 sm:p-6 shadow-sm">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg flex-shrink-0 shadow-md">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div class="space-y-3 flex-1">
                    <div>
                        <h3 class="font-black text-sm sm:text-base text-amber-950 flex items-center gap-2">
                            <span>ملاحظات وإرشادات هامة للمستثمر قبل التسجيل:</span>
                        </h3>
                        <p class="text-[11px] sm:text-xs text-amber-800 font-semibold mt-0.5">
                            يرجى الاطلاع على آلية تفعيل الشقق ورسوم المعاملات المالية بالمنصة:
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                        <!-- البند 1: شحن المحفظة لظهور الشقق -->
                        <div class="bg-white/95 rounded-xl p-3.5 border border-amber-200/90 shadow-2xs space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                                <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-wallet"></i>
                                </span>
                                <span>تفعيل ظهور الشقق</span>
                            </div>
                            <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                بمجرد مراجعة واعتماد حسابك من الإدارة، <strong class="text-amber-950 font-black">يجب شحن محفظتك في الموقع</strong> حتى تظهر شققك للعملاء وتبدأ في استقبال الحجوزات.
                            </p>
                        </div>

                        <!-- البند 2: خصم 150 لكل تأكيد حجز -->
                        <div class="bg-white/95 rounded-xl p-3.5 border border-amber-200/90 shadow-2xs space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </span>
                                <span>خصم الحجوزات (الإيجار)</span>
                            </div>
                            <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                تخصم المنصة رسماً ثابتاً قدره <strong class="text-emerald-800 font-black text-xs">150 ريال</strong> من محفظتك لكل تأكيد حجز إيجار ناجح.
                            </p>
                        </div>

                        <!-- البند 3: خصم 100 لكل عقد -->
                        <div class="bg-white/95 rounded-xl p-3.5 border border-amber-200/90 shadow-2xs space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-file-contract"></i>
                                </span>
                                <span>خصم توثيق العقود</span>
                            </div>
                            <p class="text-[11px] text-slate-700 leading-relaxed font-medium">
                                تخصم المنصة رسماً قدره <strong class="text-sky-800 font-black text-xs">100 ريال</strong> من محفظتك لكل عقد إيجار موثق يتم إصداره عبر المنصة.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <div class="font-bold mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>يرجى تصحيح الأخطاء التالية:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('investor.register') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Account Credentials -->
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2 border-r-4 border-amber-500 pr-2">
                    <i class="fa-solid fa-lock text-amber-500"></i>
                    <span>بيانات حساب الدخول</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">اسم المستخدم *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">كلمة المرور *</label>
                        <input type="password" name="password" required dir="ltr" minlength="8"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">تأكيد كلمة المرور *</label>
                        <input type="password" name="password_confirmation" required dir="ltr"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left">
                    </div>
                </div>
            </div>

            <!-- 2. Investor Info -->
            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2 border-r-4 border-amber-500 pr-2">
                    <i class="fa-solid fa-user-shield text-amber-500"></i>
                    <span>بيانات المستثمر والتحقق من الهوية</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">الاسم الكامل (كما في الهوية) *</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الهوية الوطنية أو الإقامة *</label>
                        <input type="text" name="id_number" value="{{ old('id_number') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الجوال *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required dir="ltr"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left" placeholder="05xxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم واتساب للإشعارات الفورية *</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" required dir="ltr"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left" placeholder="05xxxxxxxx">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">صورة الهوية الوطنية / الإقامة (ملف أو صورة) *</label>
                        <input type="file" name="id_image" required accept="image/*,.pdf"
                            class="w-full px-3 py-2 rounded-xl border border-dashed border-gray-300 text-xs text-gray-600 bg-gray-50 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                    </div>
                </div>
            </div>

            <!-- 3. Tourism License -->
            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2 border-r-4 border-amber-500 pr-2">
                    <i class="fa-solid fa-stamp text-amber-500"></i>
                    <span>بيانات الترخيص السياحي (مطلوب وفق الأنظمة)</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الترخيص السياحي *</label>
                        <input type="text" name="license_number" value="{{ old('license_number') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">اسم المنشأة المسجل بالترخيص *</label>
                        <input type="text" name="license_name" value="{{ old('license_name') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">صورة وثيقة الترخيص (PDF أو صورة واضحة) *</label>
                        <input type="file" name="license_image" required accept="image/*,.pdf"
                            class="w-full px-3 py-2 rounded-xl border border-dashed border-gray-300 text-xs text-gray-600 bg-gray-50 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                    </div>
                </div>
            </div>

            <!-- 4. Payout Bank Details -->
            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2 border-r-4 border-amber-500 pr-2">
                    <i class="fa-solid fa-building-columns text-amber-500"></i>
                    <span>الحساب البنكي لتحويل الإيجارات</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">اسم البنك *</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="مثال: مصرف الراجحي">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">اسم صاحب الحساب *</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الآيبان IBAN *</label>
                        <input type="text" name="iban" value="{{ old('iban') }}" required dir="ltr"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none text-left" placeholder="SA0000000000000000000000">
                    </div>
                </div>
            </div>

            <!-- 5. Apartment Details -->
            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2 border-r-4 border-amber-500 pr-2">
                    <i class="fa-solid fa-door-open text-amber-500"></i>
                    <span>بيانات الشقة داخل البرج</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الشقة *</label>
                        <input type="text" name="apartment_number" value="{{ old('apartment_number') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="مثال: 504">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رقم الدور *</label>
                        <input type="number" name="floor_number" value="{{ old('floor_number') }}" required min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">نوع الإطلالة</label>
                        <input type="text" name="view_type" value="{{ old('view_type') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="بانورامية على المدينة / بحرية">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">عدد الغرف *</label>
                        <input type="number" name="rooms_count" value="{{ old('rooms_count', 1) }}" required min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">عدد الصالات *</label>
                        <input type="number" name="living_rooms_count" value="{{ old('living_rooms_count', 1) }}" required min="0"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">عدد دورات المياه *</label>
                        <input type="number" name="bathrooms_count" value="{{ old('bathrooms_count', 1) }}" required min="1"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">سعر الليلة (ريال) *</label>
                        <input type="number" name="daily_price" value="{{ old('daily_price') }}" required min="0" step="0.01"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">مبلغ التأمين المسترد (ريال) *</label>
                        <input type="number" name="insurance_amount" value="{{ old('insurance_amount', 300) }}" required min="0" step="0.01"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">رسوم إضافية (تنظيف / خدمات)</label>
                        <input type="number" name="extra_fees" value="{{ old('extra_fees', 0) }}" min="0" step="0.01"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">وقت تسجيل الدخول *</label>
                        <input type="time" name="checkin_time" value="{{ old('checkin_time', '14:00') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">وقت تسجيل الخروج *</label>
                        <input type="time" name="checkout_time" value="{{ old('checkout_time', '12:00') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">المميزات والتجهيزات (مفصولة بفواصل)</label>
                        <input type="text" name="features" value="{{ old('features') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="واي فاي فائق السرعة، مطبخ مجهز، شاشة ذكية 65 بوصة، دخول ذكي، موقف خاص">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">وصف تفصيلي للشقة</label>
                        <textarea name="description" rows="3"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="اكتب وصفاً جذاباً للشقة يوضح التفاصيل والراحة المتوفرة...">{{ old('description') }}</textarea>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">صور الشقة (يمكن اختيار صور متعددة)</label>
                        <input type="file" name="apartment_images[]" multiple accept="image/*"
                            class="w-full px-3 py-2 rounded-xl border border-dashed border-gray-300 text-xs text-gray-600 bg-gray-50 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">فيديو كامل للشقة (MP4, MOV)</label>
                        <input type="file" name="apartment_video" accept="video/*"
                            class="w-full px-3 py-2 rounded-xl border border-dashed border-gray-300 text-xs text-gray-600 bg-gray-50 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-100">
                <button type="submit" class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-2xl shadow-xl shadow-amber-500/20 transition text-base flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>إرسال طلب التسجيل للاعتماد</span>
                </button>
                <p class="text-[11px] text-gray-500 text-center mt-2">
                    بإرسال الطلب، أنت توافق على سياسات المنصة وشروط التشغيل (خصم 150 ريال لكل تأكيد حجز، 100 ريال لكل عقد، ولزوم شحن المحفظة لظهور الشقق بعد الاعتماد).
                </p>

                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                    <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>العودة لصفحة تسجيل الدخول</span>
                    </a>
                    <a href="{{ route('client.apartments.index') }}" class="hover:text-gray-800">
                        <span>تصفح الشقق</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

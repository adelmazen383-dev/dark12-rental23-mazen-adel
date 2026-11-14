<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد إيجار وحدة سكنية - {{ $contract->contract_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-border { border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 antialiased text-slate-800">

    <!-- Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="javascript:history.back()" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold transition hover:bg-slate-800 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-right"></i>
            <span>العودة</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>طباعة العقد أو حفظ كـ PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Document Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8 sm:p-12 border border-slate-200 print-border relative overflow-hidden">

        <!-- Watermark / Background Emblem -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.04] pointer-events-none">
            <img src="{{ asset('images/dark-stamp.jpg') }}" alt="علامة مائية دارك" class="w-[450px] h-[450px] object-contain rounded-full">
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between pb-6 border-b-2 border-slate-900 gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-18 h-18 rounded-2xl overflow-hidden bg-white p-1 border-2 border-amber-400 shadow-md flex items-center justify-center flex-shrink-0">
                    <img src="{{ asset('images/dark-logo.jpg') }}" alt="شعار منصة دارك" class="w-16 h-16 object-cover rounded-xl">
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900">منصة دَارِك لتنظيم الإيجار</h1>
                    <p class="text-xs text-slate-500 font-semibold">المنصة المعتمدة لتنظيم وتوثيق عمليات تأجير الوحدات السكنية</p>
                    <p class="text-[11px] text-emerald-700 font-bold mt-0.5 flex items-center gap-1">
                        <i class="fa-solid fa-certificate text-emerald-600"></i>
                        <span>وثيقة عقد إيجار موحدة ومعتمدة رسمياً</span>
                    </p>
                </div>
            </div>

            <!-- Contract Ref & QR Placeholder -->
            <div class="text-center sm:text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-2xs">
                <div class="text-[10px] text-slate-500 font-bold">رقم العقد المرجعي الموثق</div>
                <div class="text-base font-black text-slate-900 font-mono tracking-wider">{{ $contract->contract_number }}</div>
                <div class="text-[10px] text-slate-500 mt-1">تاريخ التوثيق: {{ $contract->created_at->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center my-6 relative z-10">
            <h2 class="text-lg font-black text-slate-900 underline decoration-amber-500 decoration-2 underline-offset-8">
                عقد إيجار وحدة سكنية معتمد
            </h2>
            <p class="text-xs text-slate-500 mt-2">
                تم الاتفاق والتراضي بين الأطراف الموضحة بياناتهم أدناه وفق الشروط والأحكام المنصوص عليها في هذه الوثيقة
            </p>
        </div>

        <!-- Parties Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6 text-xs relative z-10">
            <!-- First Party (Landlord / Investor) -->
            <div class="p-4 rounded-xl border-2 border-emerald-200 bg-emerald-50/30 space-y-2">
                <div class="font-black text-slate-900 text-sm pb-1 border-b border-emerald-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-700"></span>
                        <span>الطرف الأول (المؤجر / صاحب الوحدة):</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[9px]"></i>
                        <span>مثبت رسمياً</span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">اسم المؤجر المعتمد:</span>
                    <span class="font-black text-slate-900">{{ $contract->effective_lessor_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الهوية الوطنية / السجل:</span>
                    <span class="font-mono font-black text-slate-900">{{ $contract->effective_lessor_national_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الترخيص السياحي:</span>
                    <span class="font-mono text-slate-800">{{ $contract->investor->license_number ?: 'معتمد بنظام دارك' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-mono text-slate-900" dir="ltr">{{ $contract->investor->phone }}</span>
                </div>
            </div>

            <!-- Second Party (Tenant) -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2">
                <div class="font-black text-slate-900 text-sm pb-1 border-b border-slate-200 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>الطرف الثاني (المستأجر):</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">الاسم الكامل:</span>
                    <span class="font-bold text-slate-900">{{ $contract->tenant_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الهوية الوطنية / الإقامة:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $contract->tenant_national_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الجوال للتواصل:</span>
                    <span class="font-mono text-slate-900" dir="ltr">{{ $contract->tenant_phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">صفة العقد:</span>
                    <span class="font-bold text-emerald-700">مستأجر فعلي للوحدة</span>
                </div>
            </div>
        </div>

        <!-- Leased Unit & Financial Terms -->
        <div class="my-6 border border-slate-200 rounded-xl overflow-hidden text-xs">
            <div class="bg-slate-900 text-white font-bold p-3 flex items-center justify-between">
                <span>بيانات الوحدة المؤجرة والأحكام المالية</span>
                <span class="text-[10px] bg-amber-500 text-slate-950 px-2 py-0.5 rounded font-black">الدور {{ $contract->floor_number }}</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-x-reverse divide-y sm:divide-y-0 divide-slate-200 bg-white p-3 text-center">
                <div class="p-2">
                    <span class="text-slate-400 block text-[10px]">رقم الوحدة / الشقة</span>
                    <span class="font-black text-slate-900 text-sm">شقة {{ $contract->unit_number }}</span>
                </div>
                <div class="p-2">
                    <span class="text-slate-400 block text-[10px]">الدور بالبرج</span>
                    <span class="font-black text-slate-900 text-sm">الدور {{ $contract->floor_number }}</span>
                </div>
                <div class="p-2">
                    <span class="text-slate-400 block text-[10px]">قيمة الإيجار المتفق عليها</span>
                    <span class="font-black text-emerald-700 text-sm">{{ number_format($contract->rent_amount, 2) }} ر.س</span>
                </div>
                <div class="p-2">
                    <span class="text-slate-400 block text-[10px]">مبلغ التأمين المسترد</span>
                    <span class="font-black text-slate-900 text-sm">{{ number_format($contract->insurance_amount, 2) }} ر.س</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div>
                    <span class="text-slate-500">تاريخ بداية العقد:</span>
                    <strong class="text-slate-800 mr-1 font-mono">{{ $contract->start_date->format('Y-m-d') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">تاريخ نهاية العقد:</span>
                    <strong class="text-slate-800 mr-1 font-mono">{{ $contract->end_date->format('Y-m-d') }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">طريقة السداد:</span>
                    <strong class="text-slate-800 mr-1 font-bold">{{ $contract->payment_type_label }}</strong>
                </div>
            </div>
        </div>

        <!-- Contract Articles & Terms -->
        <div class="space-y-3 my-6 text-xs text-slate-700 leading-relaxed">
            <h3 class="font-black text-slate-900 text-sm border-b pb-1">بنود وشروط عقد الإيجار:</h3>
            <ol class="list-decimal list-inside space-y-1.5 pr-2">
                <li><strong>الغرض من الإيجار:</strong> يقر الطرف الثاني بأن استخدام الوحدة السكنية مخصص للسكن العائلي الهادئ فقط، ويلتزم بالأنظمة والتعليمات المعمول بها داخل البرج.</li>
                <li><strong>الاستلام والحالة:</strong> استلم الطرف الثاني الوحدة السكنية وجميع ملحقاتها وأجهزتها ومحتوياتها بحالة ممتازة وصالحة للاستخدام، ويلتزم بإعادتها بذات الحالة.</li>
                <li><strong>مبلغ التأمين:</strong> تم إيداع مبلغ التأمين الموضح أعلاه كضمان لسلامة محتويات الوحدة، ويسترد بالكامل عند انتهاء العقد وتسليم الوحدة دون تلفيات أو مستحقات متأخرة.</li>
                <li><strong>الالتزام بالهدوء والآداب العامة:</strong> يلتزم الطرف الثاني بالمحافظة على السكينة العامة وعدم إزعاج الجيران أو النزلاء الآخرين في البرج، والامتناع عن أي نشاط يخالف القوانين.</li>
                @if($contract->special_terms)
                    <li class="text-slate-900 font-semibold bg-amber-50 p-2 rounded-lg border border-amber-200">
                        <strong>شروط وبنود خاصة إضافية:</strong> {{ $contract->special_terms }}
                    </li>
                @endif
            </ol>
        </div>

        <!-- Signatures & Verification Seal -->
        <div class="grid grid-cols-3 gap-4 pt-8 border-t-2 border-slate-900 text-xs text-center relative z-10">
            <div>
                <p class="font-bold text-slate-800 mb-8">توقيع الطرف الأول (المؤجر)</p>
                <div class="border-t border-dashed border-slate-400 pt-1 text-slate-700 font-bold font-mono text-xs">{{ $contract->effective_lessor_name }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">الهوية: {{ $contract->effective_lessor_national_id }}</div>
            </div>

            <div class="flex flex-col items-center justify-center">
                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-amber-500/70 shadow-md rotate-[-6deg] bg-white p-1 flex items-center justify-center">
                    <img src="{{ asset('images/dark-stamp.jpg') }}" alt="ختم منصة دارك الرسمي" class="w-full h-full object-cover rounded-full">
                </div>
                <span class="text-[10px] font-black text-slate-900 mt-1">ختم منصة دَارِك المعتمد</span>
                <span class="text-[9px] text-slate-400 font-mono">رمز التوثيق: {{ substr(md5($contract->contract_number), 0, 10) }}</span>
            </div>

            <div>
                <p class="font-bold text-slate-800 mb-8">توقيع الطرف الثاني (المستأجر)</p>
                <div class="border-t border-dashed border-slate-400 pt-1 text-slate-700 font-bold font-mono text-xs">{{ $contract->tenant_name }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">الهوية: {{ $contract->tenant_national_id }}</div>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-500 relative z-10">
            تم إصدار وتوثيق هذه الوثيقة إلكترونياً عبر <strong>منصة دَارِك لتنظيم الإيجار</strong> وتعتبر نافذة وملزمة قانونياً بين الطرفين وفق الأنظمة المعتمدة.
        </div>
    </div>

</body>
</html>

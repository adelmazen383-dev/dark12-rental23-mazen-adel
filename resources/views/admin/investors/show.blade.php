@extends('layouts.admin')

@section('title', 'ملف المستثمر - ' . $investor->full_name)

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $investor->full_name }}</h1>
                @if($investor->status === 'approved')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">معتمد</span>
                @elseif($investor->status === 'pending')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">قيد المراجعة والتدقيق</span>
                @elseif($investor->status === 'suspended')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">موقوف</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">مرفوض</span>
                @endif
            </div>
            <p class="text-xs text-slate-500">تاريخ التسجيل: {{ $investor->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <!-- Status Management Actions -->
        <div class="flex flex-wrap items-center gap-2">
            @if($investor->status === 'pending' || $investor->status === 'rejected')
                <form action="{{ route('admin.investors.approve', $investor->id) }}" method="POST" onsubmit="return confirm('هل ترغب في اعتماد هذا المستثمر وتفعيل شققه؟');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-check"></i>
                        <span>اعتماد المستثمر</span>
                    </button>
                </form>
            @endif

            @if($investor->status === 'pending')
                <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-xmark"></i>
                    <span>رفض الطلب</span>
                </button>
            @endif

            @if($investor->status === 'approved')
                <button type="button" onclick="document.getElementById('suspendModal').classList.remove('hidden')" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i>
                    <span>إيقاف المستثمر مؤقتاً</span>
                </button>
            @endif

            @if($investor->status === 'suspended')
                <form action="{{ route('admin.investors.reactivate', $investor->id) }}" method="POST" onsubmit="return confirm('إعادة تفعيل المستثمر وتنشيط شققه؟');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>إلغاء الإيقاف وإعادة التفعيل</span>
                    </button>
                </form>
            @endif


            <!-- Add Balance Button -->
            <button type="button" onclick="document.getElementById('addBalanceModal').classList.remove('hidden')" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-amber-400 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i>
                <span>شحن رصيد المحفظة</span>
            </button>

            <!-- Reset Password Button -->
            <button type="button" onclick="document.getElementById('resetPasswordModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-key"></i>
                <span>إعادة تعيين كلمة المرور</span>
            </button>
        </div>
    </div>

    <!-- Alert for Rejection / Suspension Reasons -->
    @if($investor->status === 'rejected' && $investor->rejection_reason)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <strong>سبب الرفض المسجل:</strong> {{ $investor->rejection_reason }}
        </div>
    @endif

    @if($investor->status === 'suspended' && $investor->suspension_reason)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
            <strong>سبب الإيقاف المؤقت (بتاريخ {{ $investor->suspension_date?->format('Y-m-d') }}):</strong>
            <p class="mt-1">{{ $investor->suspension_reason }}</p>
        </div>
    @endif

    <!-- Admin Approval & Visibility Override Card (Special Control) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl {{ $investor->admin_approved_override ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-sm text-slate-900">موافقة الإدارة على إظهار الشقق واستقبال الحجوزات</h3>
                    @if($investor->admin_approved_override)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">موافقة استثنائية مفعلة</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">تخضع للحالة والاشتراك</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($investor->admin_approved_override)
                        <strong class="text-indigo-600 font-semibold">استثناء إداري مفعّل:</strong> تظهر شقق هذا المستثمر للنزلاء في الموقع وتستقبل الحجوزات حتى لو كان حسابه موقوفاً أو انتهى اشتراكه الشهري.
                    @else
                        @if($investor->isSuspended())
                            <span class="text-rose-600 font-bold">⚠️ المستثمر موقوف:</span> شققه مخفية حالياً عن النزلاء. يمكنك منحه موافقة استثنائية لعرضها.
                        @elseif(!$investor->hasActiveSubscription())
                            <span class="text-amber-600 font-bold">⚠️ الاشتراك الشهري منتهٍ:</span> شققه مخفية تلقائياً حتى تجديد الاشتراك، إلا لو وافقت الإدارة.
                        @else
                            <span class="text-emerald-600 font-semibold">حساب المستثمر سارٍ واشتراكه نشط، وتظهر شققه المتاحة طبيعياً.</span>
                        @endif
                    @endif
                </p>
            </div>
        </div>

        <form action="{{ route('admin.investors.toggle-admin-approval', $investor->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2.5 {{ $investor->admin_approved_override ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white' }} font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid {{ $investor->admin_approved_override ? 'fa-ban' : 'fa-check-double' }}"></i>
                <span>{{ $investor->admin_approved_override ? 'إلغاء موافقة الإدارة الاستثنائية' : 'منح موافقة الإدارة على الظهور' }}</span>
            </button>
        </form>
    </div>

    <!-- CRITICAL REQUIREMENT (Section 4): Confirmed bookings during suspension / active -->
    @if($confirmedBookings->isNotEmpty())
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-emerald-200">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-sm text-slate-900">الحجوزات القادمة والمؤكدة لهذا المستثمر (لا يتم إلغاؤها عند الإيقاف)</h3>
                </div>
                <span class="text-xs font-bold text-emerald-700">{{ $confirmedBookings->count() }} حجز قائم</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                        <tr>
                            <th class="p-2.5">رقم الحجز</th>
                            <th class="p-2.5">الشقة</th>
                            <th class="p-2.5">العميل</th>
                            <th class="p-2.5">تاريخ الدخول</th>
                            <th class="p-2.5">تاريخ الخروج</th>
                            <th class="p-2.5">المبلغ</th>
                            <th class="p-2.5">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($confirmedBookings as $cb)
                            <tr>
                                <td class="p-2.5 font-mono font-bold text-amber-700">
                                    <a href="{{ route('admin.bookings.show', $cb->id) }}" class="hover:underline">{{ $cb->reference_number }}</a>
                                </td>
                                <td class="p-2.5">شقة {{ $cb->apartment->apartment_number }} (الدور {{ $cb->apartment->floor_number }})</td>
                                <td class="p-2.5">{{ $cb->customer_name }} ({{ $cb->customer_phone }})</td>
                                <td class="p-2.5">{{ $cb->checkin_date->format('Y-m-d') }}</td>
                                <td class="p-2.5">{{ $cb->checkout_date->format('Y-m-d') }}</td>
                                <td class="p-2.5 font-bold text-emerald-700">{{ number_format($cb->total_amount, 2) }} ر.س</td>
                                <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">مؤكد</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Profile & Verification Documents Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Personal & Contact Info -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-id-card text-amber-500"></i>
                <span>بيانات الهوية والاتصال</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">الاسم الكامل:</span>
                    <span class="font-bold text-slate-800">{{ $investor->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الهوية / الإقامة:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $investor->id_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الجوال:</span>
                    <span class="font-bold text-slate-800" dir="ltr">{{ $investor->phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم واتساب:</span>
                    <span class="font-bold text-emerald-700" dir="ltr">{{ $investor->whatsapp }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">البريد الإلكتروني:</span>
                    <span class="font-bold text-slate-800" dir="ltr">{{ $investor->user->email }}</span>
                </div>
            </div>

            <!-- ID Document View -->
            <div class="pt-3 border-t">
                <span class="text-xs font-bold text-slate-700 block mb-2">وثيقة الهوية المرفوعة:</span>
                @php
                    $idDocUrl = $investor->id_image ? asset('storage/' . $investor->id_image) : asset('images/id-default.svg');
                @endphp
                <div class="space-y-2">
                    <a href="{{ $idDocUrl }}" target="_blank" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between hover:bg-amber-50 transition text-xs">
                        <span class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-id-card text-amber-500 text-base"></i>
                            <span>معاينة وثيقة الهوية (نافذة كاملة)</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-slate-400"></i>
                    </a>
                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-900 h-36">
                        <img src="{{ $idDocUrl }}" alt="وثيقة الهوية" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='{{ asset('images/id-default.svg') }}';">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tourism License Details -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-stamp text-amber-500"></i>
                <span>بيانات الترخيص السياحي</span>
            </h3>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الترخيص:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $investor->license_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">اسم المنشأة بالترخيص:</span>
                    <span class="font-bold text-slate-800">{{ $investor->license_name }}</span>
                </div>
            </div>

            <!-- License Document View -->
            <div class="pt-3 border-t">
                <span class="text-xs font-bold text-slate-700 block mb-2">وثيقة الترخيص السياحي:</span>
                @php
                    $licDocUrl = $investor->license_image ? asset('storage/' . $investor->license_image) : asset('images/license-default.svg');
                @endphp
                <div class="space-y-2">
                    <a href="{{ $licDocUrl }}" target="_blank" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between hover:bg-amber-50 transition text-xs">
                        <span class="font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-certificate text-emerald-500 text-base"></i>
                            <span>معاينة وثيقة الترخيص (نافذة كاملة)</span>
                        </span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-slate-400"></i>
                    </a>
                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-900 h-40">
                        <img src="{{ $licDocUrl }}" alt="وثيقة الترخيص" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='{{ asset('images/license-default.svg') }}';">
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-[11px] text-blue-900 leading-relaxed">
                <i class="fa-solid fa-shield-check text-blue-600 ml-1"></i>
                تم التحقق من ملاءمة الترخيص للنشاط والوحدة وفق الأنظمة المعمول بها.
            </div>
        </div>

        <!-- Financial & Wallet Summary -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b pb-2 flex items-center gap-2">
                <i class="fa-solid fa-wallet text-amber-500"></i>
                <span>الوضع المالي والمحفظة</span>
            </h3>

            <div class="p-4 rounded-xl bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-slate-400 block">رصيد المحفظة الحالي:</span>
                    <span class="text-2xl font-black text-amber-400">{{ number_format($investor->wallet?->balance ?? 0, 2) }}</span>
                    <span class="text-xs font-bold text-slate-300">ريال</span>
                </div>
                <button type="button" onclick="document.getElementById('addBalanceModal').classList.remove('hidden')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg text-xs font-bold transition">
                    + شحن
                </button>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">البنك المستلم:</span>
                    <span class="font-bold text-slate-800">{{ $investor->bank_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">اسم صاحب الحساب:</span>
                    <span class="font-bold text-slate-800">{{ $investor->bank_account_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">رقم الآيبان:</span>
                    <span class="font-mono text-slate-800" dir="ltr">{{ $investor->iban }}</span>
                </div>
            </div>

            <!-- Active Subscription -->
            <div class="pt-3 border-t text-xs">
                <span class="text-slate-500 block mb-1">الاشتراك الشهري:</span>
                @if($investor->activeSubscription)
                    <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex justify-between items-center">
                        <span class="font-bold">نشط حتى {{ $investor->activeSubscription->end_date->format('Y-m-d') }}</span>
                        <span class="text-[10px] bg-emerald-200 px-2 py-0.5 rounded-full font-bold">{{ $investor->activeSubscription->daysRemaining() }} يوم متبقي</span>
                    </div>
                @else
                    <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 font-bold">
                        منتهي أو غير نشط
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Investor Apartments List -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm text-slate-900">شقق هذا المستثمر ({{ $investor->apartments->count() }})</h3>
            <a href="{{ route('admin.apartments.create') }}" class="text-xs font-bold text-amber-600 hover:underline">
                + إضافة شقة جديدة للمستثمر
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($investor->apartments as $apt)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="font-black text-slate-900">شقة {{ $apt->apartment_number }}</div>
                        <div class="text-[11px] text-slate-500">الدور {{ $apt->floor_number }} • {{ number_format($apt->daily_price, 0) }} ريال/ليلة</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $apt->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $apt->is_active ? 'نشطة' : 'معطلة' }}
                        </span>
                        <a href="{{ route('admin.apartments.show', $apt->id) }}" class="p-1.5 bg-white text-slate-700 rounded-lg shadow-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Wallet Transactions History -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-900 mb-3">كشف حساب المحفظة والخصومات (آخر 10 عمليات)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b">
                    <tr>
                        <th class="p-2.5">التاريخ</th>
                        <th class="p-2.5">النوع</th>
                        <th class="p-2.5">المبلغ</th>
                        <th class="p-2.5">الوصف</th>
                        <th class="p-2.5">الرصيد بعد العملية</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($investor->wallet?->transactions ?? [] as $tr)
                        <tr>
                            <td class="p-2.5 text-slate-500">{{ $tr->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($tr->type === 'deposit') bg-emerald-100 text-emerald-800
                                    @elseif($tr->type === 'deduction') bg-rose-100 text-rose-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $tr->type_label }}
                                </span>
                            </td>
                            <td class="p-2.5 font-bold {{ $tr->type === 'deposit' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $tr->type === 'deposit' ? '+' : '-' }}{{ number_format($tr->amount, 2) }} ر.س
                            </td>
                            <td class="p-2.5 text-slate-800">{{ $tr->description }}</td>
                            <td class="p-2.5 font-bold text-slate-700">{{ number_format($tr->balance_after, 2) }} ر.س</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-4 text-center text-slate-400">لا توجد حركات مالية مسجلة بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Reject Investor -->
<div id="rejectModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-rose-700">رفض طلب المستثمر</h3>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.investors.reject', $investor->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الرفض (سيصل للمستثمر في رسالة واتساب) *</label>
                <textarea name="rejection_reason" required rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500" placeholder="مثال: صورة الترخيص غير واضحة / رقم الهوية غير مطابق..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Suspend Investor -->
<div id="suspendModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-amber-700">إيقاف المستثمر مؤقتاً</h3>
            <button onclick="document.getElementById('suspendModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.investors.suspend', $investor->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">سبب الإيقاف المؤقت *</label>
                <textarea name="suspension_reason" required rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500" placeholder="مثال: انتهاء الترخيص السياحي / عدم سداد رسوم الحجوزات..."></textarea>
            </div>
            <p class="text-[11px] text-slate-500">
                ملاحظة: سيتم إخفاء الشقق عن استقبال الحجوزات الجديدة، مع بقاء الحجوزات المؤكدة السابقة قائمة لمعالجتها إدارياً.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('suspendModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold">تأكيد الإيقاف</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Balance -->
<div id="addBalanceModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-slate-900">شحن رصيد محفظة المستثمر</h3>
            <button onclick="document.getElementById('addBalanceModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.investors.add-balance', $investor->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">المبلغ المراد شحنه (ريال) *</label>
                <input type="number" name="amount" required min="1" step="0.01" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500" placeholder="500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">البيان / مرجع الحوالة *</label>
                <input type="text" name="notes" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500" placeholder="شحن رصيد مقابل حوالة بنكية #1234">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addBalanceModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">إيداع في المحفظة</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reset Investor Password -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-indigo-700 flex items-center gap-2">
                <i class="fa-solid fa-key"></i>
                <span>إعادة تعيين كلمة مرور المستثمر</span>
            </h3>
            <button onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.investors.reset-password', $investor->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs">
                <div><strong>المستثمر:</strong> {{ $investor->full_name }}</div>
                <div><strong>البريد المسجل:</strong> {{ $investor->user?->email }}</div>
                <div><strong>جوال واتساب:</strong> {{ $investor->whatsapp }}</div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">كلمة المرور الجديدة * (8 خانات على الأقل)</label>
                <input type="text" name="new_password" required minlength="8" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 font-mono text-left" dir="ltr" placeholder="مثال: Dark@2026!">
                <span class="text-[11px] text-slate-400 mt-1 block">سيتم تحديث كلمة المرور وإرسال إشعار فوري لرسائل واتساب الخاصة بالمستثمر ببياناته الجديدة.</span>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold">حفظ وإرسال للمستثمر</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'سجل إشعارات واتساب')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل وتتبع إشعارات واتساب</h1>
            <p class="text-xs text-slate-500 mt-1">تتبع الرسائل التلقائية للنظام وإرسالها المباشر لأطراف الحجز والمستثمرين</p>
        </div>
    </div>

    <!-- Explanatory Guide Box (إجابة وتوضيح شامل للمشرف) -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-white rounded-2xl border-2 border-emerald-300 p-5 shadow-sm space-y-3">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div class="flex-1 space-y-2">
                <h3 class="font-black text-sm text-emerald-950 flex items-center gap-2">
                    <span>📌 كيف تعمل إشعارات واتساب هنا؟ (توضيح تقني صريح):</span>
                </h3>
                <p class="text-xs text-slate-700 leading-relaxed">
                    يقوم هذا السجل <strong>بتوليد وصياغة كل إشعار آلياً</strong> (عند تأكيد حجز، أو طلب معاينة، أو توثيق عقد، أو تحويل مالي) وتخزينه في النظام لضمان عدم ضياع أي حركة.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 text-xs">
                    <div class="bg-white/90 p-3 rounded-xl border border-emerald-200">
                        <strong class="text-emerald-900 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-mobile-screen-button text-emerald-600"></i>
                            <span>1. الإرسال الفعلي المجاني (مفعل حالياً):</span>
                        </strong>
                        <p class="text-slate-600 text-[11px] leading-normal">
                            بجانب كل رسالة في الجدول أدناه، يوجد زر أخضر <strong>"فتح وإرسال عبر واتساب"</strong>. بالضغط عليه يفتح محادثة واتساب الرسمية مع المستلم مع تعبئة نص الرسالة فوراً لتضغط إرسال بدون تكاليف اشتراكات خارجية.
                        </p>
                    </div>

                    <div class="bg-white/90 p-3 rounded-xl border border-emerald-200">
                        <strong class="text-slate-900 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-robot text-teal-600"></i>
                            <span>2. الإرسال الآلي الصامت بالخلفية (API Gateway):</span>
                        </strong>
                        <p class="text-slate-600 text-[11px] leading-normal">
                            لتصل الرسائل تلقائياً إلى هواتفهم في الخلفية دون أي تدخل بشري، يتطلب ذلك ربط بوابة واتساب سحابية معتمدة (مثل Meta Cloud API أو UltraMsg أو Twilio) ووضع مفتاح الـ API في إعدادات النظام.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">الوقت</th>
                        <th class="p-3.5">المستلم والجهة</th>
                        <th class="p-3.5">نص الرسالة المنشأ آلياً</th>
                        <th class="p-3.5">حالة السجل</th>
                        <th class="p-3.5 text-center">الإرسال المباشر</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notifications as $notif)
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $notif->recipient_phone);
                            if (str_starts_with($cleanPhone, '05')) {
                                $cleanPhone = '966' . substr($cleanPhone, 1);
                            }
                            $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($notif->message);
                        @endphp
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="p-3.5 text-slate-500 font-mono text-[11px]">{{ $notif->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-900" dir="ltr">{{ $notif->recipient_phone }}</div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($notif->recipient_type === 'admin') bg-purple-100 text-purple-800
                                    @elseif($notif->recipient_type === 'investor') bg-blue-100 text-blue-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ $notif->recipient_type === 'admin' ? 'إدارة المنصة' : ($notif->recipient_type === 'investor' ? 'مستثمر' : 'نزيل / عميل') }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-800 max-w-md whitespace-pre-line text-xs font-sans bg-slate-50/60 rounded-lg p-2.5 border border-slate-100">{{ $notif->message }}</td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if($notif->status === 'sent')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        <span>جاهز وموثق بالسجل</span>
                                    </span>
                                @elseif($notif->status === 'failed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>فشل</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">قيد المعالجة</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <a href="{{ $waUrl }}" target="_blank"
                                   class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>فتح وإرسال عبر واتساب</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">لا توجد إشعارات مسجلة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
    </div>

</div>
@endsection

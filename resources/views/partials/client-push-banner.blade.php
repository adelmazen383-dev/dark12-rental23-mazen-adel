<!-- Client Instant Push Notification Opt-in Banner (iPhone PWA + Android + Desktop) -->
<div class="js-client-push-card rounded-2xl overflow-hidden shadow-xs animate-fade-in">
    <!-- Prompt to enable (Android / Desktop / iPhone PWA standalone) -->
    <div class="js-push-enable-wrap hidden bg-gradient-to-l from-amber-50 via-white to-amber-50/70 border border-amber-300 rounded-2xl p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-base flex-shrink-0 shadow-sm">
                <i class="fa-solid fa-bell bell-ring-anim"></i>
            </div>
            <div>
                <h4 class="font-black text-xs sm:text-sm text-slate-900">{{ $pushTitle ?? 'فعّل الإشعارات الفورية على جوالك' }}</h4>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                    {{ $pushSubtitle ?? 'ليصلك رد المالك وتأكيد الحجز وكود الاستقبال في نفس اللحظة حتى لو أغلقت الموقع.' }}
                </p>
            </div>
        </div>
        <button type="button" onclick="DarkClientPush.enable(true)"
            class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs transition shadow-sm flex items-center justify-center gap-2 flex-shrink-0 btn-press btn-shimmer">
            <i class="fa-solid fa-bolt"></i>
            <span>تفعيل الإشعارات الآن</span>
        </button>
    </div>

    <!-- iPhone Safari Guide (shown automatically when on iOS Safari before Add to Home Screen) -->
    <div class="js-ios-hint hidden bg-gradient-to-l from-amber-50 via-white to-sky-50 border border-amber-300 rounded-2xl p-3.5 sm:p-4 flex items-start gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center text-base flex-shrink-0 shadow-xs">
            <i class="fa-brands fa-apple"></i>
        </div>
        <div class="text-[11px] text-slate-700 leading-relaxed space-y-1">
            <div class="font-black text-xs text-slate-900">لتصلك الإشعارات على الآيفون (حتى والموقع مغلق):</div>
            <p>
                في <strong>Safari</strong> اضغط زر <strong>المشاركة</strong> <i class="fa-solid fa-arrow-up-from-bracket text-sky-600 mx-0.5"></i> ثم اختر <strong>«إضافة إلى الشاشة الرئيسية»</strong>، وبعدها افتح تطبيق <strong>«دارك»</strong> من شاشتك واضغط تفعيل الإشعارات.
            </p>
        </div>
    </div>

    <!-- Active Confirmation & Quick Test -->
    <div class="js-push-active-wrap hidden bg-emerald-50 border border-emerald-200 rounded-2xl px-3.5 py-2.5 flex items-center justify-between gap-2 text-xs">
        <div class="flex items-center gap-2 text-emerald-800 font-bold">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>الإشعارات الفورية مفعلة — ستصلك الردود والتحديثات لحظياً</span>
        </div>
        <button type="button" onclick="DarkClientPush.test()" class="px-2.5 py-1 rounded-lg bg-white hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-[11px] transition flex-shrink-0 btn-press">
            اختبار
        </button>
    </div>
</div>

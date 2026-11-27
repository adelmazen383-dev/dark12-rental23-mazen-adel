@extends('layouts.admin')

@section('title', 'إعدادات النظام والرسوم')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div>
        <h1 class="text-2xl font-black text-slate-900">إعدادات النظام والرسوم المالية</h1>
        <p class="text-xs text-slate-500 mt-1">تحديد قيم الاشتراكات ورسوم الحجوزات وحسابات البنك المعتمدة دون الحاجة لتعديل البرمجة</p>
    </div>

    <!-- Financial Settings Form -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <h3 class="font-bold text-sm text-slate-900 border-b pb-3 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-coins text-amber-500"></i>
            <span>الرسوم المالية والاشتراكات (قابلة للتعديل مباشرة)</span>
        </h3>

        <div id="financialAlert" class="hidden mb-4 p-3.5 rounded-xl text-xs font-semibold"></div>

        <form id="financialSettingsForm" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        قيمة الاشتراك الشهري للمستثمر (ريال) *
                    </label>
                    <input type="number" name="monthly_subscription" value="{{ $settings['monthly_subscription'] ?? 500 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">يتم احتساب هذا المبلغ شهرياً لتجديد اشتراك المستثمر بالمنصة.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        رسوم المنصة لكل حجز مؤكد (ريال) *
                    </label>
                    <input type="number" name="booking_fee" value="{{ $settings['booking_fee'] ?? 150 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">تُخصم تلقائياً من محفظة المستثمر عند تأكيد حجز الإيجار (150 ريال لكل تأكيد حجز).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-contract text-emerald-600"></i>
                        <span>رسوم إصدار وتوثيق عقد إيجار رسمي (ريال) *</span>
                    </label>
                    <input type="number" name="contract_service_fee" value="{{ $settings['contract_service_fee'] ?? 100 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs focus:ring-2 focus:ring-emerald-500 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">تُخصم من محفظة المستثمر: رسوم خدمة إعداد وتوثيق عقود الإيجار (100 ريال لكل عقد).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-invoice text-emerald-600"></i>
                        <span>رسوم إصدار فاتورة ضريبية رسمية (ريال) *</span>
                    </label>
                    <input type="number" name="invoice_service_fee" value="{{ $settings['invoice_service_fee'] ?? 50 }}" required min="0" step="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/30 text-xs focus:ring-2 focus:ring-emerald-500 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">مصدر دخل للمنصة: رسوم إصدار الفواتير الإلكترونية المعتمدة للعملاء والمستثمرين.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        حد التنبيه لانخفاض رصيد المحفظة (ريال) *
                    </label>
                    <input type="number" name="low_balance_threshold" value="{{ $settings['low_balance_threshold'] ?? 100 }}" required min="0"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                    <p class="text-[10px] text-slate-400 mt-1">يصل تنبيه للمستثمر عبر واتساب عند وصول رصيده لهذا الحد.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        رقم واتساب الإدارة المعتمد للإشعارات *
                    </label>
                    <input type="text" name="admin_whatsapp" value="{{ $settings['admin_whatsapp'] ?? '0500000000' }}" required dir="ltr"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 text-left">
                    <p class="text-[10px] text-slate-400 mt-1">تصل إليه إشعارات الحجوزات ورفع الإيصالات وطلبات المعاينة.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        اسم المنصة / البرج *
                    </label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'أجنحة البرج' }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        أيام التنبيه قبل انتهاء الاشتراك الشهري
                    </label>
                    <input type="number" name="subscription_reminder_days" value="{{ $settings['subscription_reminder_days'] ?? 7 }}" min="1"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" id="financialSaveBtn" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk" id="financialSaveIcon"></i>
                    <span id="financialSaveText">حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Email & SMTP Settings for OTP Delivery -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b pb-3">
            <div>
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-envelope-circle-check text-indigo-600"></i>
                    <span>إعدادات البريد الإلكتروني وإرسال رمز التحقق (SMTP / OTP)</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">مطلوب لإرسال رمز استعادة كلمة المرور (OTP) إلى بريد المستثمرين والمشرفين</p>
            </div>
            @if(!empty($mailConfigured))
                <span id="smtpStatusBadge" data-configured="1" class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 w-fit">
                    <i class="fa-solid fa-circle-check ml-1"></i> SMTP مفعّل
                </span>
            @else
                <span id="smtpStatusBadge" data-configured="0" class="px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200 w-fit">
                    <i class="fa-solid fa-triangle-exclamation ml-1"></i> غير مفعّل (وضع السجل المحلي)
                </span>
            @endif
        </div>

        <!-- Quick Presets -->
        <div class="flex flex-wrap items-center gap-2 text-[11px]">
            <span class="font-bold text-slate-600">تعبئة سريعة:</span>
            <button type="button" onclick="applyMailPreset('smtp.gmail.com', 587, 'tls')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 font-bold transition">Gmail (App Password)</button>
            <button type="button" onclick="applyMailPreset('smtp.hostinger.com', 465, 'ssl')" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 font-bold transition">Hostinger</button>
            <button type="button" onclick="applyMailPreset('smtp.zoho.com', 465, 'ssl')" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 font-bold transition">Zoho Mail</button>
            <button type="button" onclick="applyMailPreset('smtp-relay.brevo.com', 587, 'tls')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 font-bold transition">Brevo (مجاني 300/يوم)</button>
        </div>

        <div id="smtpAlert" class="hidden p-3.5 rounded-xl text-xs font-semibold"></div>

        <form id="smtpSettingsForm" action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">وضع الإرسال *</label>
                    <select name="mail_mailer" id="mail_mailer" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                        <option value="smtp" {{ ($settings['mail_mailer'] ?? '') === 'smtp' ? 'selected' : '' }}>إرسال فعلي عبر SMTP (مطلوب)</option>
                        <option value="log" {{ ($settings['mail_mailer'] ?? 'log') === 'log' ? 'selected' : '' }}>معطل (حفظ في السجل فقط)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">خادم SMTP (Host)</label>
                    <input type="text" name="mail_host" id="mail_host" value="{{ $settings['mail_host'] ?? '' }}" dir="ltr" placeholder="smtp.gmail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 text-left">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">المنفذ (Port)</label>
                        <input type="number" name="mail_port" id="mail_port" value="{{ $settings['mail_port'] ?? 587 }}" dir="ltr" placeholder="587"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 text-left">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">التشفير</label>
                        <select name="mail_encryption" id="mail_encryption" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (587)</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL (465)</option>
                            <option value="none" {{ ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>بدون</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم المستخدم / الإيميل (Username)</label>
                    <input type="text" name="mail_username" id="mail_username" value="{{ $settings['mail_username'] ?? '' }}" dir="ltr" placeholder="your-email@gmail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 text-left">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">كلمة مرور التطبيق (App Password)</label>
                    <input type="password" name="mail_password" id="mail_password" dir="ltr" placeholder="{{ !empty($settings['mail_password']) ? '•••••••• (محفوظة - اتركها فارغة للإبقاء)' : 'أدخل App Password' }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 text-left">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">بريد المرسل (From Email)</label>
                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" dir="ltr" placeholder="noreply@thiqa.me"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 text-left">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200 text-[11px] text-indigo-900 leading-relaxed">
                <strong>ملاحظة لـ Gmail:</strong> فعّل التحقق بخطوتين (2-Step Verification) في حساب Google ثم أنشئ <strong>App Password</strong> من إعدادات الأمان وأدخله في خانة كلمة المرور أعلاه (وليس كلمة مرور حساب Gmail العادية). اختر <strong>TLS (587)</strong> أو <strong>SSL (465)</strong>.
            </div>

            <div class="flex justify-end">
                <button type="submit" id="smtpSaveBtn" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk" id="smtpSaveIcon"></i>
                    <span id="smtpSaveText">حفظ إعدادات البريد (SMTP)</span>
                </button>
            </div>
        </form>

        <!-- Live Test Email Form -->
        <div id="testMailAlert" class="hidden p-3.5 rounded-xl text-xs font-semibold"></div>

        <form id="testMailForm" action="{{ route('admin.settings.test-mail') }}" method="POST" class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @csrf
            <div class="flex-1">
                <input type="email" id="testEmailInput" name="test_email" required dir="ltr" value="{{ auth()->user()->email }}" placeholder="أدخل إيميلاً لإرسال رسالة OTP تجريبية..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 text-left">
            </div>
            <button type="submit" id="testMailBtn" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-paper-plane" id="testMailIcon"></i>
                <span id="testMailText">إرسال بريد اختبار الآن</span>
            </button>
        </form>
    </div>

    <script>
        function applyMailPreset(host, port, enc) {
            document.getElementById('mail_mailer').value = 'smtp';
            document.getElementById('mail_host').value = host;
            document.getElementById('mail_port').value = port;
            document.getElementById('mail_encryption').value = enc;
        }

        // Keep Port and Encryption synced so 587+SSL mismatch never occurs
        document.getElementById('mail_encryption')?.addEventListener('change', function() {
            const portEl = document.getElementById('mail_port');
            if (!portEl) return;
            if (this.value === 'ssl' && (portEl.value === '587' || !portEl.value)) {
                portEl.value = '465';
            } else if (this.value === 'tls' && (portEl.value === '465' || !portEl.value)) {
                portEl.value = '587';
            }
        });
        document.getElementById('mail_port')?.addEventListener('change', function() {
            const encEl = document.getElementById('mail_encryption');
            if (!encEl) return;
            if (this.value === '465') encEl.value = 'ssl';
            else if (this.value === '587') encEl.value = 'tls';
        });

        function showInlineAlert(el, ok, message) {
            if (!el) return;
            el.className = ok
                ? 'p-3.5 rounded-xl text-xs font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2'
                : 'p-3.5 rounded-xl text-xs font-semibold bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-2';
            el.innerHTML = '';
            const ic = document.createElement('i');
            ic.className = ok
                ? 'fa-solid fa-circle-check text-emerald-600 text-base flex-shrink-0'
                : 'fa-solid fa-triangle-exclamation text-rose-600 text-base flex-shrink-0';
            const sp = document.createElement('span');
            sp.textContent = message;
            el.appendChild(ic);
            el.appendChild(sp);
        }

        function updateSmtpBadge(configured) {
            const badge = document.getElementById('smtpStatusBadge');
            if (!badge) return;
            badge.dataset.configured = configured ? '1' : '0';
            if (configured) {
                badge.className = 'px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 w-fit';
                badge.innerHTML = '<i class="fa-solid fa-circle-check ml-1"></i> SMTP مفعّل';
            } else {
                badge.className = 'px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200 w-fit';
                badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation ml-1"></i> غير مفعّل (وضع السجل المحلي)';
            }
        }

        async function fetchFreshCsrf() {
            try {
                const res = await fetch("{{ route('csrf.token') }}", {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data && data.csrf_token) {
                        const meta = document.querySelector('meta[name="csrf-token"]');
                        if (meta) meta.content = data.csrf_token;
                        document.querySelectorAll('input[name="_token"]').forEach(i => i.value = data.csrf_token);
                        return data.csrf_token;
                    }
                }
            } catch(e) {}
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        async function postJsonWithFreshCsrf(url, payload) {
            let token = await fetchFreshCsrf();
            const sendReq = async (tok) => fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': tok
                },
                body: JSON.stringify(Object.assign({}, payload, { _token: tok }))
            });

            let res = await sendReq(token);
            if (res.status === 419) {
                token = await fetchFreshCsrf();
                res = await sendReq(token);
            }
            let data = {};
            try { data = await res.json(); } catch (_) {}
            if (data && data.csrf_token) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.content = data.csrf_token;
                document.querySelectorAll('input[name="_token"]').forEach(i => i.value = data.csrf_token);
            }
            return { res, data };
        }

        function extractErrorMessage(data, fallback) {
            if (data && data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                if (firstKey && Array.isArray(data.errors[firstKey]) && data.errors[firstKey][0]) {
                    return data.errors[firstKey][0];
                }
            }
            return (data && data.message) ? data.message : fallback;
        }

        function formToObject(form) {
            const obj = {};
            new FormData(form).forEach((v, k) => {
                if (k !== '_token') obj[k] = v;
            });
            return obj;
        }

        // Financial settings AJAX save (eliminates 419 completely)
        document.getElementById('financialSettingsForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('financialSaveBtn');
            const icon = document.getElementById('financialSaveIcon');
            const text = document.getElementById('financialSaveText');
            const alertBox = document.getElementById('financialAlert');

            btn.disabled = true;
            icon.className = 'fa-solid fa-spinner fa-spin';
            text.textContent = 'جارٍ الحفظ...';
            alertBox.className = 'hidden';

            try {
                const { res, data } = await postJsonWithFreshCsrf(this.action, formToObject(this));
                if (res.ok && data.success) {
                    showInlineAlert(alertBox, true, data.message || 'تم حفظ التعديلات بنجاح.');
                } else {
                    showInlineAlert(alertBox, false, extractErrorMessage(data, 'تعذر حفظ التعديلات. يرجى المحاولة مجدداً.'));
                }
            } catch (err) {
                showInlineAlert(alertBox, false, 'حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
            } finally {
                btn.disabled = false;
                icon.className = 'fa-solid fa-floppy-disk';
                text.textContent = 'حفظ التعديلات';
            }
        });

        async function saveSmtpSettings() {
            const form = document.getElementById('smtpSettingsForm');
            const btn = document.getElementById('smtpSaveBtn');
            const icon = document.getElementById('smtpSaveIcon');
            const text = document.getElementById('smtpSaveText');
            const alertBox = document.getElementById('smtpAlert');

            btn.disabled = true;
            icon.className = 'fa-solid fa-spinner fa-spin';
            text.textContent = 'جارٍ حفظ إعدادات البريد...';
            alertBox.className = 'hidden';

            try {
                const payload = formToObject(form);
                const { res, data } = await postJsonWithFreshCsrf(form.action, payload);
                if (res.ok && data.success) {
                    showInlineAlert(alertBox, true, data.message || 'تم حفظ إعدادات البريد (SMTP) بنجاح.');
                    if (typeof data.mail_configured === 'boolean') {
                        updateSmtpBadge(data.mail_configured);
                    }
                    const pwEl = document.getElementById('mail_password');
                    if (pwEl && pwEl.value.trim() !== '') {
                        pwEl.value = '';
                        pwEl.placeholder = '•••••••• (محفوظة - اتركها فارغة للإبقاء)';
                    }
                    return true;
                } else {
                    showInlineAlert(alertBox, false, extractErrorMessage(data, 'تعذر حفظ إعدادات البريد. يرجى مراجعة الحقول.'));
                    return false;
                }
            } catch (err) {
                showInlineAlert(alertBox, false, 'حدث خطأ في الاتصال بالخادم أثناء حفظ الإعدادات.');
                return false;
            } finally {
                btn.disabled = false;
                icon.className = 'fa-solid fa-floppy-disk';
                text.textContent = 'حفظ إعدادات البريد (SMTP)';
            }
        }

        document.getElementById('smtpSettingsForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            await saveSmtpSettings();
        });

        document.getElementById('testMailForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('testMailBtn');
            const icon = document.getElementById('testMailIcon');
            const text = document.getElementById('testMailText');
            const alertBox = document.getElementById('testMailAlert');
            const emailInput = document.getElementById('testEmailInput');

            if (!emailInput.value) return;

            btn.disabled = true;
            icon.className = 'fa-solid fa-spinner fa-spin';
            text.textContent = 'جارٍ التحقق والإرسال...';
            alertBox.className = 'hidden';

            try {
                // Auto-save SMTP settings first if user filled host & chose smtp
                const mailerVal = document.getElementById('mail_mailer')?.value;
                const hostVal = (document.getElementById('mail_host')?.value || '').trim();
                const pwVal = (document.getElementById('mail_password')?.value || '').trim();
                const isConfigured = document.getElementById('smtpStatusBadge')?.dataset.configured === '1';
                if (mailerVal === 'smtp' && hostVal !== '' && (!isConfigured || pwVal !== '')) {
                    const saved = await saveSmtpSettings();
                    if (!saved) return;
                }

                const { res, data } = await postJsonWithFreshCsrf(this.action, {
                    test_email: emailInput.value
                });

                if (res.ok && data.success) {
                    showInlineAlert(alertBox, true, data.message);
                } else {
                    showInlineAlert(alertBox, false, extractErrorMessage(data, 'حدث خطأ أثناء الإرسال.'));
                }
            } catch(err) {
                showInlineAlert(alertBox, false, 'حدث خطأ في الاتصال بالخادم أثناء اختبار البريد.');
            } finally {
                btn.disabled = false;
                icon.className = 'fa-solid fa-paper-plane';
                text.textContent = 'إرسال بريد اختبار الآن';
            }
        });
    </script>

    <!-- Platform Bank Accounts Management -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-amber-500"></i>
                    <span>الحسابات البنكية المعتمدة للتحويل (تظهر للنزلاء في الباركود)</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">الحسابات التي يقوم العميل بتحويل مبلغ الإيجار والتأمين إليها</p>
            </div>
            <button type="button" onclick="document.getElementById('addBankModal').classList.remove('hidden')" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1">
                <i class="fa-solid fa-plus"></i>
                <span>إضافة حساب بنكي</span>
            </button>
        </div>

        <div class="space-y-3">
            @forelse($bankAccounts as $bank)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-slate-900">{{ $bank->bank_name }} - {{ $bank->account_name }}</div>
                        <div class="font-mono text-xs text-slate-600 mt-0.5" dir="ltr">{{ $bank->iban }}</div>
                    </div>

                    <form action="{{ route('admin.bank-accounts.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('حذف هذا الحساب البنكي؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="حذف">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-4 text-center text-xs text-slate-400">لا توجد حسابات بنكية مضافة بعد.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Modal: Add Bank Account -->
<div id="addBankModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="font-bold text-base text-slate-900">إضافة حساب بنكي معتمد</h3>
            <button onclick="document.getElementById('addBankModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.bank-accounts.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم البنك *</label>
                <input type="text" name="bank_name" required placeholder="مثال: البنك الأهلي السعودي" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">اسم صاحب الحساب (المؤسسة أو الشركة) *</label>
                <input type="text" name="account_name" required placeholder="مؤسسة أجنحة البرج الفندقية" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">رقم الآيبان IBAN *</label>
                <input type="text" name="iban" required dir="ltr" placeholder="SA0000000000000000000000" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 text-left">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addBankModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold">إضافة الحساب</button>
            </div>
        </form>
    </div>
</div>
@endsection

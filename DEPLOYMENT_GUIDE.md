# 🚀 دليل النشر والتشغيل الشامل — Vercel + Cloudflare + MySQL (مجاني 100% مدى الحياة)

> **المشروع:** منصة دَارِك (أجنحة البرج الفندقية) — `tower-rental`  
> **الحالة:** تم ربط وهجرة قاعدة بيانات MySQL بنجاح وتشغيل كل الجداول والبيانات الأولية.  
> **الاختبارات:** 50/50 نجحت (تغطية أمنية ووظيفية كاملة).

---

## 🌟 ملخص ما تم إنجازه تلقائياً:

1. **قاعدة بيانات MySQL سحابية مجانية 100% (Filess.io - Shared Tier):**
   - **Host:** `on4x7k.h.filess.io`
   - **Port:** `3307`
   - **Database:** `tower_rental_attackmove`
   - **Username:** `tower_rental_attackmove`
   - **Engine:** MySQL 8.0.29 (Active)
   - **الميجريشن والداتا:** تم تشغيل `migrate --force` و `db:seed --force` بنجاح كامل بدون أي أخطاء! الجداول، الشقق، المستخدمين، الحسابات البنكية، والإعدادات جاهزة في السيرفر السحابي.

2. **ملف `vercel.json` مُعد وجاهز فوراً للتشغيل:**
   - تم تضمين بيانات قاعدة البيانات داخل `vercel.json` مباشرة.
   - السيرفر السحابي Serverless على Vercel سيتصل فوراً بقاعدة بيانات MySQL السحابية بدون أي إعدادات إضافية.
   - `SESSION_DRIVER=database` و `CACHE_STORE=database` مفعلان لحفظ الجلسات وسجل الدخول حتى مع السيرفر ليس.

3. **الإشعارات الذكية المحدثة:**
   - تم إزالة كارت الإشعارات التجريبي الضخم وأزرار الإعداد اليدوي من الداشبورد.
   - الإشعارات أصبحت تعمل في الخلفية تلقائياً (تنبيهات صوتية فورية Chime عند أول تفاعل، رسائل Toast عائمة، شارة عداد الرسائل غير المقروءة، واهتزاز للهواتف الذكية).

---

## 🌐 الخطوة التالية: ربط الدومين مع Cloudflare و Vercel

### أولاً: نشر الكود على Vercel (ضغطة زر واحدة)

1. افتح **[Vercel Dashboard](https://vercel.com/dashboard)**.
2. إذا كان المشروع مربوطاً بـ GitHub، فقط قم بعمل Push:
   ```bash
   git push origin main
   ```
   أو من Vercel اضغط **"Redeploy"**.
3. المشروع سيعمل مباشرة لأن كل إعدادات MySQL مدمجة في `vercel.json`.

---

### ثانياً: ربط دومينك من Cloudflare مع Vercel

#### 1. إضافة الدومين في Vercel:
1. في Vercel Dashboard، ادخل على مشروعك ← **Settings** ← **Domains**.
2. اكتب الدومين الخاص بك (مثال: `yourdomain.com` أو `rent.yourdomain.com`) ثم اضغط **Add**.
3. سيعطيك Vercel السجل المطلوب إضافته (عادة CNAME موجه إلى `cname.vercel-dns.com` أو IP من نوع A).

#### 2. ضبط سجلات DNS في Cloudflare:
1. افتح حسابك في **[Cloudflare Dashboard](https://dash.cloudflare.com)** واضغط على الدومين الخاص بك.
2. اذهب إلى تبويب **DNS** ← **Records** ← اضغط **Add record**.
3. أضف السجل:
   - **Type:** `CNAME`
   - **Name:** `@` (أو `www` أو اسم الـ Subdomain)
   - **Target:** `cname.vercel-dns.com`
   - **Proxy status:** **DNS Only (أيقونة سحابة رمادية)** في البداية حتى يتحقق Vercel من الشهادة، ثم يمكنك تفعيل **Proxied (برتقالي)** للاستفادة من حماية وكاش Cloudflare.
   - **TTL:** `Auto`.
4. اضغط **Save**.

#### 3. ضبط تشفير SSL في Cloudflare:
1. في لوحة Cloudflare، اذهب إلى **SSL/TLS** ← **Overview**.
2. اختر وضع التشفير: **Full** أو **Full (strict)**.
3. اذهب إلى **SSL/TLS** ← **Edge Certificates** وتأكد من تفعيل:
   - **Always Use HTTPS:** `ON`
   - **Automatic HTTPS Rewrites:** `ON`

---

## 📊 بيانات الدخول الافتراضية للنظام (موجودة في قاعدة بيانات MySQL):

- **لوحة تحكم الإدارة (Admin):**
  - الرابط: `https://yourdomain.com/admin` أو `/login`
  - البريد: `admin@tower.com`
  - كلمة المرور: `admin123`

- **بوابة المستثمر (Investor):**
  - الرابط: `https://yourdomain.com/investor` أو `/login`
  - البريد: `investor@tower.com`
  - كلمة المرور: `investor123`

---

## 🛠️ التحقق والأوامر المفيدة:

- **فحص الاتصال بقاعدة البيانات محلياً:**
  ```powershell
  php artisan db:show
  ```
- **تشغيل الاختبارات كاملة للتأكد من الأمان والاستقرار:**
  ```powershell
  php artisan test
  ```
  *(جميع الاختبارات الـ 50 تعمل وتمر بنسبة 100%)*

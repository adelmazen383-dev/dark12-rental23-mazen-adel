# 🚀 دليل النشر الكامل — Vercel + Cloudflare + PlanetScale (MySQL)

> **المشروع:** منصة دَارِك — tower-rental  
> **آخر تحديث:** نوفمبر 2026

---

## الخطوة 1: إعداد قاعدة البيانات على PlanetScale

### 1-أ. إنشاء الحساب والقاعدة
1. اذهب إلى **[planetscale.com](https://planetscale.com)** وأنشئ حساباً مجانياً
2. اضغط **"Create a database"**
3. اختر اسماً مثل `tower-rental`
4. اختر Region قريبة من مستخدميك (مثلاً `ap-southeast-1` للخليج)
5. انتظر حتى تصبح الحالة **Ready**

### 1-ب. الحصول على بيانات الاتصال
1. افتح قاعدة البيانات ← اضغط **"Connect"**
2. اختر **"Laravel"** من القائمة
3. ستظهر لك هذه القيم — احتفظ بها:

```
DB_HOST=aws.connect.psdb.cloud
DB_DATABASE=tower-rental
DB_USERNAME=xxxxxxxxxxxx
DB_PASSWORD=pscale_pw_xxxxxxxxxxxx
```

> **ملاحظة PlanetScale:** لا تدعم Foreign Key Constraints افتراضياً.
> إذا ظهر خطأ migration، شغّل هذا الأمر:
> ```
> php artisan migrate --force
> ```

---

## الخطوة 2: نشر المشروع على Vercel

### 2-أ. رفع الكود
```bash
# في مجلد المشروع
git add -A
git commit -m "deploy: mysql + vercel + cloudflare ready"
git push origin main
```

### 2-ب. ربط Vercel بـ GitHub
1. اذهب إلى **[vercel.com](https://vercel.com)** وسجّل دخول
2. اضغط **"Add New Project"** ← اختر الـ repo من GitHub
3. **Framework Preset:** اتركه على "Other"
4. اضغط **Deploy** — سيفشل في البداية لأن متغيرات البيئة ناقصة، هذا طبيعي

### 2-ج. إضافة Environment Variables السرية
اذهب إلى: **Vercel Dashboard → مشروعك → Settings → Environment Variables**

أضف هذه المتغيرات (قيمها من PlanetScale):

| المتغير | القيمة |
|---------|--------|
| `DB_HOST` | `aws.connect.psdb.cloud` |
| `DB_DATABASE` | اسم قاعدتك |
| `DB_USERNAME` | اسم المستخدم من PlanetScale |
| `DB_PASSWORD` | كلمة المرور من PlanetScale |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` |
| `VAPID_PUBLIC_KEY` | مفتاحك العام للإشعارات |
| `VAPID_PRIVATE_KEY` | مفتاحك الخاص للإشعارات |

> **تلميح:** اختر **"All Environments"** لكل متغير حتى يعمل في Production وPreview

### 2-د. تشغيل الـ Migrations
بعد إضافة المتغيرات:

```bash
# محلياً — اسحب المتغيرات من Vercel
vercel env pull .env.production

# شغّل الـ migrations على PlanetScale
php artisan migrate --force --env=production

# ثم أعد الـ deploy
vercel --prod
```

---

## الخطوة 3: ربط دومين Cloudflare

### 3-أ. إضافة الدومين في Vercel
1. اذهب إلى **Vercel Dashboard → مشروعك → Settings → Domains**
2. أدخل دومينك مثلاً: `dark.sa` أو `tower-rental.com`
3. اضغط **Add**
4. ستظهر لك Vercel سجلات DNS — احتفظ بها

### 3-ب. إعداد Cloudflare DNS
1. سجّل دخول إلى **[cloudflare.com](https://cloudflare.com)**
2. اختر دومينك
3. اذهب إلى **DNS → Records**
4. أضف السجل التالي:

| النوع | الاسم | القيمة |
|-------|-------|--------|
| `CNAME` | `@` (أو `www`) | `cname.vercel-dns.com` |

> **مهم:** اضغط على أيقونة البرتقالي (Proxied ☁️) لتحويله إلى **DNS only (رمادي ⚡)**  
> حتى تعمل شهادة SSL الخاصة بـ Vercel بشكل صحيح

### 3-ج. إعدادات Cloudflare المطلوبة

**في SSL/TLS:**
- اذهب إلى **SSL/TLS → Overview**
- اختر **"Full (strict)"**

**في Security:**
- اذهب إلى **Security → Settings**
- اضبط **Security Level** على **Medium**
- فعّل **Bot Fight Mode**

**في Speed:**
- اذهب إلى **Speed → Optimization**
- فعّل **Auto Minify** (HTML, CSS, JS)

### 3-د. تحديث APP_URL في Vercel
بعد ربط الدومين، حدّث في **Vercel → Environment Variables**:

```
APP_URL=https://yourdomain.com
```

وفي `vercel.json`:
```json
"APP_URL": "https://yourdomain.com"
```

---

## الخطوة 4: التحقق من عمل كل شيء

### فحص سريع
```bash
# 1. التحقق من الاتصال بقاعدة البيانات
curl https://yourdomain.com/admin
# يجب أن يعيد توجيهك إلى صفحة تسجيل الدخول

# 2. التحقق من HTTPS
curl -I https://yourdomain.com
# يجب أن تظهر: HTTP/2 200

# 3. التحقق من Security Headers
curl -I https://yourdomain.com | grep -E "x-frame|x-content|strict-transport"
```

---

## ملخص البنية النهائية

```
المستخدمون
    ↓
Cloudflare (CDN + حماية DDoS + SSL)
    ↓
Vercel (Serverless PHP — Laravel)
    ↓
PlanetScale (MySQL Serverless — قاعدة البيانات)
```

---

## 🔑 متغيرات البيئة الكاملة المطلوبة في Vercel

```env
# App
APP_NAME="منصة دارك لتنظيم الإيجار"
APP_ENV=production
APP_KEY=base64:JHzMD+YRgSK64LcUfln48XZc5QN7acCQafvQa4pCQyo=
APP_DEBUG=false
APP_URL=https://yourdomain.com
FORCE_HTTPS=true

# Database (PlanetScale)
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=tower-rental
DB_USERNAME=YOUR_USERNAME
DB_PASSWORD=YOUR_PASSWORD
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Push Notifications (VAPID)
VAPID_PUBLIC_KEY=YOUR_VAPID_PUBLIC_KEY
VAPID_PRIVATE_KEY=YOUR_VAPID_PRIVATE_KEY
```

---

> [!TIP]
> بعد أول deploy ناجح، يمكنك تفعيل **Vercel + Cloudflare Integration** من marketplace  
> لتحديث DNS تلقائياً عند كل deploy جديد.

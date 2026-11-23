# دليل إعداد PlanetScale مع Vercel

## الخطوات

### 1. إنشاء حساب PlanetScale
- اذهب إلى [planetscale.com](https://planetscale.com) وأنشئ حساباً مجانياً.

### 2. إنشاء قاعدة بيانات جديدة
- من Dashboard اضغط **"Create a database"**.
- اختر اسماً وregion قريباً من Vercel deployment الخاص بك.

### 3. الحصول على Connection String
- افتح قاعدة البيانات ← **"Connect"** ← اختر **"Laravel"** من القائمة.
- ستظهر لك القيم التالية:
  - `DB_HOST`
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`

### 4. إضافة Environment Variables في Vercel
اذهب إلى: **Vercel Dashboard → مشروعك → Settings → Environment Variables**

أضف المتغيرات التالية (قيم حقيقية من PlanetScale):

| المتغير | القيمة |
|---------|--------|
| `DB_HOST` | `aws.connect.psdb.cloud` (أو ما يظهر من PlanetScale) |
| `DB_DATABASE` | اسم قاعدة البيانات |
| `DB_USERNAME` | اسم المستخدم |
| `DB_PASSWORD` | كلمة المرور |

> **ملاحظة:** `MYSQL_ATTR_SSL_CA` و `DB_CONNECTION` و `DB_PORT` موجودة بالفعل في `vercel.json`.

### 5. تشغيل Migrations
بعد إعداد المتغيرات، شغّل محلياً:

```bash
# سحب env vars من Vercel إلى .env المحلي
vercel env pull .env.local

# تشغيل migrations على قاعدة بيانات PlanetScale
php artisan migrate --force
```

أو من خلال Vercel CLI مباشرةً بعد الـ deploy:

```bash
vercel run php artisan migrate --force
```

---

## ملاحظات مهمة

- PlanetScale **لا تدعم** Foreign Key Constraints افتراضياً — تأكد أن migrations لا تعتمد عليها بشكل مطلق.
- `strict => false` في `config/database.php` يحل مشاكل MySQL 8 mode.
- `SESSION_DRIVER=database` و `CACHE_STORE=database` يتطلبان أن تكون جداول `sessions` و `cache` موجودة — وهي موجودة في migrations.

<?php

namespace App\Services;

class FallbackMediaService
{
    /**
     * Return Official Investor National ID / Resident Card SVG
     */
    public static function renderIdDocumentSvg(string $path = '')
    {
        $docId = 'ID-' . strtoupper(substr(md5($path ?: time()), 0, 10));
        $date = now()->format('Y-m-d');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 850 540" width="850" height="540">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="50%" stop-color="#1e293b" />
      <stop offset="100%" stop-color="#022c22" />
    </linearGradient>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#f59e0b" />
      <stop offset="100%" stop-color="#d97706" />
    </linearGradient>
    <linearGradient id="chipGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fbbf24" />
      <stop offset="100%" stop-color="#b45309" />
    </linearGradient>
    <filter id="shadow" x="-5%" y="-5%" width="110%" height="110%">
      <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000" flood-opacity="0.4" />
    </filter>
  </defs>

  <rect width="850" height="540" rx="24" fill="url(#bgGrad)" stroke="#334155" stroke-width="2" filter="url(#shadow)" />
  
  <!-- Guilloche / Security Pattern Lines -->
  <g opacity="0.08" stroke="#f59e0b" stroke-width="1.5" fill="none">
    <circle cx="425" cy="270" r="100" />
    <circle cx="425" cy="270" r="150" />
    <circle cx="425" cy="270" r="200" />
    <circle cx="425" cy="270" r="260" />
    <circle cx="425" cy="270" r="320" />
    <path d="M 0,270 Q 425,-50 850,270 Q 425,590 0,270" />
    <path d="M 0,135 Q 425,-100 850,135" />
    <path d="M 0,405 Q 425,640 850,405" />
  </g>

  <!-- Header Section -->
  <g transform="translate(40, 35)">
    <!-- Saudi & Tower Emblem Badge -->
    <rect x="0" y="0" width="55" height="55" rx="14" fill="#065f46" stroke="#10b981" stroke-width="1.5" />
    <text x="27.5" y="36" font-family="sans-serif" font-size="26" text-anchor="middle" fill="#f59e0b">🏢</text>

    <!-- Header Text -->
    <text x="75" y="24" font-family="'Cairo', sans-serif" font-size="20" font-weight="bold" fill="#ffffff">المملكة العربية السعودية</text>
    <text x="75" y="46" font-family="'Cairo', sans-serif" font-size="13" font-weight="bold" fill="#10b981">منصة دَارِك لتنظيم الإيجار العقاري • وثيقة هوية رقمية معتمدة</text>

    <!-- Status Pill -->
    <rect x="580" y="8" width="180" height="36" rx="18" fill="#10b981" fill-opacity="0.2" stroke="#10b981" stroke-width="1.5" />
    <circle cx="600" cy="26" r="5" fill="#10b981" />
    <text x="690" y="31" font-family="'Cairo', sans-serif" font-size="13" font-weight="bold" fill="#34d399" text-anchor="middle">وثيقة مرفوعة ومحققة ✅</text>
  </g>

  <!-- Separator Line -->
  <line x1="40" y1="110" x2="810" y2="110" stroke="#334155" stroke-width="1.5" stroke-dasharray="6 4" />

  <!-- Body Content -->
  <!-- Photo / Avatar Area -->
  <g transform="translate(620, 140)">
    <rect width="180" height="230" rx="18" fill="#1e293b" stroke="#f59e0b" stroke-width="2" />
    <circle cx="90" cy="85" r="42" fill="#334155" />
    <text x="90" y="98" font-family="sans-serif" font-size="44" text-anchor="middle" fill="#94a3b8">👤</text>
    <path d="M 30,195 Q 90,140 150,195 Z" fill="#334155" />
    <rect x="25" y="200" width="130" height="20" rx="6" fill="#0f172a" />
    <text x="90" y="214" font-family="'Cairo', sans-serif" font-size="11" font-weight="bold" fill="#f59e0b" text-anchor="middle">مستثمر معتمد بالمنصة</text>
  </g>

  <!-- Smart Chip Graphic -->
  <g transform="translate(60, 145)">
    <rect width="65" height="50" rx="8" fill="url(#chipGrad)" stroke="#78350f" stroke-width="1" />
    <line x1="22" y1="0" x2="22" y2="50" stroke="#78350f" stroke-width="1" />
    <line x1="43" y1="0" x2="43" y2="50" stroke="#78350f" stroke-width="1" />
    <line x1="0" y1="25" x2="65" y2="25" stroke="#78350f" stroke-width="1" />
  </g>

  <!-- Document Details -->
  <g transform="translate(60, 225)" font-family="'Cairo', sans-serif">
    <text x="0" y="0" font-size="12" fill="#94a3b8">نوع الوثيقة الرسمية:</text>
    <text x="0" y="26" font-size="18" font-weight="bold" fill="#f8fafc">الهوية الوطنية / الإقامة النظامية للمستثمر</text>

    <text x="0" y="65" font-size="12" fill="#94a3b8">حالة الاعتماد الإلكتروني:</text>
    <text x="0" y="90" font-size="16" font-weight="bold" fill="#34d399">تم استلام المستند والتحقق من سريان التوثيق</text>

    <text x="0" y="130" font-size="12" fill="#94a3b8">الرقم المرجعي المشفر للوثيقة:</text>
    <text x="0" y="155" font-family="monospace" font-size="17" font-weight="bold" fill="#f59e0b">$docId</text>

    <text x="0" y="195" font-size="12" fill="#94a3b8">تاريخ التسجيل والرفع:</text>
    <text x="0" y="218" font-size="15" font-weight="bold" fill="#cbd5e1">$date م</text>
  </g>

  <!-- Official Circular Stamp (Bottom Right) -->
  <g transform="translate(480, 290)">
    <circle cx="50" cy="50" r="48" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-dasharray="8 4" opacity="0.85" />
    <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="1.5" opacity="0.85" />
    <text x="50" y="44" font-family="'Cairo', sans-serif" font-size="11" font-weight="bold" fill="#f59e0b" text-anchor="middle">مُعْتَمَد</text>
    <text x="50" y="62" font-family="'Cairo', sans-serif" font-size="10" font-weight="bold" fill="#f59e0b" text-anchor="middle">منصة دَارِك</text>
  </g>

  <!-- Footer Bar with Machine Readable Code -->
  <rect x="0" y="480" width="850" height="60" rx="0" fill="#020617" />
  <text x="425" y="506" font-family="monospace" font-size="14" fill="#64748b" text-anchor="middle" letter-spacing="4">
    IDSAU<<$docId<<<<<<<<<<<<<<<<<<<<<<VERIFIED
  </text>
  <text x="425" y="526" font-family="'Cairo', sans-serif" font-size="11" fill="#475569" text-anchor="middle">
    وثيقة استثمار رقمية صادرة لأغراض التحقق التشغيلي • أجنحة البرج الفندقية
  </text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Return Official Tourism License Certificate SVG
     */
    public static function renderLicenseDocumentSvg(string $path = '')
    {
        $licId = 'LIC-' . strtoupper(substr(md5($path ?: time()), 0, 10));
        $date = now()->format('Y-m-d');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1050" width="800" height="1050">
  <defs>
    <linearGradient id="licBg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#090d16" />
      <stop offset="50%" stop-color="#0f172a" />
      <stop offset="100%" stop-color="#14213d" />
    </linearGradient>
    <linearGradient id="goldBorder" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fbbf24" />
      <stop offset="50%" stop-color="#d97706" />
      <stop offset="100%" stop-color="#f59e0b" />
    </linearGradient>
  </defs>

  <!-- Background -->
  <rect width="800" height="1050" fill="url(#licBg)" />

  <!-- Ornate Border Frame -->
  <rect x="25" y="25" width="750" height="1000" rx="16" fill="none" stroke="url(#goldBorder)" stroke-width="3" />
  <rect x="35" y="35" width="730" height="980" rx="12" fill="none" stroke="#334155" stroke-width="1.5" stroke-dasharray="6 4" />

  <!-- Corner Flourishes -->
  <g fill="#f59e0b">
    <polygon points="25,25 65,25 25,65" />
    <polygon points="775,25 735,25 775,65" />
    <polygon points="25,1025 65,1025 25,985" />
    <polygon points="775,1025 735,1025 775,985" />
  </g>

  <!-- Emblem & Crown Header -->
  <g transform="translate(400, 120)" text-anchor="middle">
    <circle cx="0" cy="0" r="45" fill="#065f46" stroke="#10b981" stroke-width="2" />
    <text x="0" y="14" font-size="40">🏛️</text>
    
    <text x="0" y="75" font-family="'Cairo', sans-serif" font-size="22" font-weight="bold" fill="#ffffff">المملكة العربية السعودية</text>
    <text x="0" y="105" font-family="'Cairo', sans-serif" font-size="14" font-weight="600" fill="#f59e0b">وزارة السياحة • الهيئة العامة للعقار</text>
    <text x="0" y="130" font-family="'Cairo', sans-serif" font-size="12" fill="#94a3b8">منظومة دارك لإدارة وتشغيل الوحدات العقارية المفروشة</text>
  </g>

  <!-- Certificate Title Ribbon -->
  <g transform="translate(100, 300)">
    <rect width="600" height="55" rx="12" fill="#0f172a" stroke="#d97706" stroke-width="1.5" />
    <text x="300" y="36" font-family="'Cairo', sans-serif" font-size="20" font-weight="900" fill="#fef08a" text-anchor="middle">
      رخصة ممارسة نشاط الضيافة وتأجير الوحدات
    </text>
  </g>

  <!-- Certificate Body Box -->
  <g transform="translate(80, 390)" font-family="'Cairo', sans-serif">
    <rect width="640" height="380" rx="16" fill="#1e293b" fill-opacity="0.6" stroke="#334155" stroke-width="1" />

    <g transform="translate(40, 45)">
      <text x="0" y="0" font-size="13" fill="#94a3b8">نوع النشاط المصرح:</text>
      <text x="0" y="28" font-size="18" font-weight="bold" fill="#ffffff">إيواء سياحي وشقق فندقية مخدومة (Tower Suites)</text>

      <text x="0" y="80" font-size="13" fill="#94a3b8">رقم الترخيص السياحي المعتمد:</text>
      <text x="0" y="108" font-family="monospace" font-size="20" font-weight="bold" fill="#f59e0b">$licId</text>

      <text x="0" y="160" font-size="13" fill="#94a3b8">الكيان المشغل والمنصة:</text>
      <text x="0" y="188" font-size="17" font-weight="bold" fill="#34d399">منصة دَارِك لتنظيم وتأجير العقارات الفندقية</text>

      <text x="0" y="240" font-size="13" fill="#94a3b8">حالة التدقيق والامتثال:</text>
      <text x="0" y="268" font-size="16" font-weight="bold" fill="#38bdf8">سارٍ وممتثل لمعايير الجودة والتراخيص البلدية والسياحية</text>

      <text x="0" y="315" font-size="12" fill="#64748b">تاريخ التوثيق: $date م • مرخص وموثق إلكترونياً</text>
    </g>
  </g>

  <!-- Official Stamps & QR Section -->
  <g transform="translate(120, 810)">
    <!-- Stamp -->
    <g transform="translate(80, 60)">
      <circle cx="50" cy="50" r="54" fill="#065f46" fill-opacity="0.3" stroke="#10b981" stroke-width="2.5" stroke-dasharray="8 4" />
      <circle cx="50" cy="50" r="44" fill="none" stroke="#10b981" stroke-width="1.5" />
      <text x="50" y="44" font-family="'Cairo', sans-serif" font-size="13" font-weight="black" fill="#34d399" text-anchor="middle">مُعْتَمَد</text>
      <text x="50" y="64" font-family="'Cairo', sans-serif" font-size="10" font-weight="bold" fill="#f59e0b" text-anchor="middle">منصة دَارِك</text>
    </g>

    <!-- QR Code Placeholder -->
    <g transform="translate(380, 30)">
      <rect width="110" height="110" rx="12" fill="#ffffff" />
      <rect x="10" y="10" width="30" height="30" fill="#0f172a" />
      <rect x="70" y="10" width="30" height="30" fill="#0f172a" />
      <rect x="10" y="70" width="30" height="30" fill="#0f172a" />
      <rect x="18" y="18" width="14" height="14" fill="#ffffff" />
      <rect x="78" y="18" width="14" height="14" fill="#ffffff" />
      <rect x="18" y="78" width="14" height="14" fill="#ffffff" />
      <rect x="50" y="20" width="10" height="10" fill="#0f172a" />
      <rect x="50" y="50" width="15" height="15" fill="#0f172a" />
      <rect x="75" y="55" width="20" height="10" fill="#0f172a" />
      <rect x="30" y="50" width="10" height="10" fill="#0f172a" />
      <text x="55" y="128" font-family="'Cairo', sans-serif" font-size="10" font-weight="bold" fill="#94a3b8" text-anchor="middle">مسح للتحقق الفوري</text>
    </g>
  </g>

  <!-- Footer Notice -->
  <text x="400" y="990" font-family="'Cairo', sans-serif" font-size="11" fill="#64748b" text-anchor="middle">
    هذا المستند معتمد وصادر إلكترونياً ولا يتطلب توقيعاً خطياً وفق اللائحة المنظمة للإيواء السياحي
  </text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Return Luxury Hotel Apartment Suite Preview SVG
     */
    public static function renderApartmentImageSvg(string $path = '')
    {
        $aptNum = 'SUITE';
        if (preg_match('/apartment_?(\d+)/i', $path, $m)) {
            $aptNum = 'شقة ' . $m[1];
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 625" width="1000" height="625">
  <defs>
    <linearGradient id="roomGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f172a" />
      <stop offset="40%" stop-color="#1e293b" />
      <stop offset="80%" stop-color="#334155" />
      <stop offset="100%" stop-color="#1e1b4b" />
    </linearGradient>
    <linearGradient id="windowView" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#0284c7" />
      <stop offset="50%" stop-color="#38bdf8" />
      <stop offset="100%" stop-color="#f59e0b" />
    </linearGradient>
    <linearGradient id="warmLight" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop offset="0%" stop-color="#fbbf24" stop-opacity="0.8" />
      <stop offset="100%" stop-color="#fbbf24" stop-opacity="0" />
    </linearGradient>
    <linearGradient id="bedGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#e2e8f0" />
      <stop offset="50%" stop-color="#ffffff" />
      <stop offset="100%" stop-color="#cbd5e1" />
    </linearGradient>
    <filter id="glow">
      <feGaussianBlur stdDeviation="8" result="coloredBlur"/>
      <feMerge>
        <feMergeNode in="coloredBlur"/>
        <feMergeNode in="SourceGraphic"/>
      </feMerge>
    </filter>
  </defs>

  <!-- Background Wall -->
  <rect width="1000" height="625" fill="url(#roomGrad)" />

  <!-- Wooden Floor -->
  <polygon points="0,460 1000,460 1000,625 0,625" fill="#1c1917" />
  <g opacity="0.15" stroke="#78350f" stroke-width="2">
    <line x1="0" y1="490" x2="1000" y2="490" />
    <line x1="0" y1="530" x2="1000" y2="530" />
    <line x1="0" y1="575" x2="1000" y2="575" />
  </g>

  <!-- Floor Carpet -->
  <ellipse cx="500" cy="540" rx="340" ry="65" fill="#334155" opacity="0.6" stroke="#475569" stroke-width="2" />

  <!-- Panoramic Window overlooking Tower Skyline -->
  <g transform="translate(620, 80)">
    <rect width="320" height="340" rx="14" fill="url(#windowView)" stroke="#475569" stroke-width="6" />
    <!-- City Skyline Silhouettes -->
    <rect x="30" y="160" width="45" height="180" fill="#0f172a" />
    <rect x="90" y="110" width="55" height="230" fill="#020617" />
    <!-- Tower Spike -->
    <polygon points="117,60 112,110 122,110" fill="#f59e0b" />
    <rect x="160" y="180" width="50" height="160" fill="#0f172a" />
    <rect x="225" y="140" width="60" height="200" fill="#020617" />
    <!-- Window Frame Cross -->
    <line x1="160" y1="0" x2="160" y2="340" stroke="#1e293b" stroke-width="5" />
    <line x1="0" y1="170" x2="320" y2="170" stroke="#1e293b" stroke-width="5" />
  </g>

  <!-- Modern Luxury Bed Unit -->
  <!-- Headboard -->
  <rect x="180" y="220" width="400" height="210" rx="12" fill="#1e293b" stroke="#f59e0b" stroke-width="2" />
  <!-- Accent Wood Slats -->
  <g opacity="0.25" stroke="#f59e0b" stroke-width="2">
    <line x1="220" y1="220" x2="220" y2="430" />
    <line x1="260" y1="220" x2="260" y2="430" />
    <line x1="500" y1="220" x2="500" y2="430" />
    <line x1="540" y1="220" x2="540" y2="430" />
  </g>

  <!-- Pillows -->
  <rect x="210" y="300" width="155" height="65" rx="14" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1.5" />
  <rect x="395" y="300" width="155" height="65" rx="14" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="1.5" />
  <rect x="240" y="320" width="100" height="45" rx="10" fill="#d97706" opacity="0.85" />
  <rect x="420" y="320" width="100" height="45" rx="10" fill="#d97706" opacity="0.85" />

  <!-- Duvet / Mattress -->
  <path d="M 170,360 L 590,360 L 630,490 L 130,490 Z" fill="url(#bedGrad)" stroke="#cbd5e1" stroke-width="2" />
  <path d="M 130,460 L 630,460 L 620,490 L 140,490 Z" fill="#0f172a" />
  <!-- Luxury Runner Blanket across bed -->
  <polygon points="150,420 610,420 620,450 140,450" fill="#b45309" opacity="0.9" />

  <!-- Bedside Lamps with Warm Glow -->
  <!-- Left Lamp -->
  <g transform="translate(110, 260)">
    <polygon points="20,0 45,0 55,40 10,40" fill="#fef08a" opacity="0.9" filter="url(#glow)" />
    <rect x="30" y="40" width="5" height="80" fill="#f59e0b" />
    <rect x="15" y="120" width="35" height="10" rx="3" fill="#334155" />
    <polygon points="-20,40 85,40 140,250 -75,250" fill="url(#warmLight)" opacity="0.45" />
  </g>

  <!-- Right Lamp (Table) -->
  <g transform="translate(605, 340)">
    <rect width="60" height="70" rx="8" fill="#1e293b" stroke="#334155" stroke-width="1.5" />
    <polygon points="15,-40 45,-40 55,0 5,0" fill="#fef08a" opacity="0.9" filter="url(#glow)" />
    <polygon points="-25,0 85,0 130,170 -70,170" fill="url(#warmLight)" opacity="0.35" />
  </g>

  <!-- Top Dark Overlay with Branding & Badges -->
  <rect x="0" y="0" width="1000" height="110" fill="url(#roomGrad)" opacity="0.85" />
  <line x1="0" y1="110" x2="1000" y2="110" stroke="#f59e0b" stroke-width="1.5" opacity="0.4" />

  <g transform="translate(45, 30)">
    <!-- Brand Icon -->
    <rect x="0" y="0" width="50" height="50" rx="12" fill="#f59e0b" />
    <text x="25" y="34" font-size="26" text-anchor="middle">🏢</text>

    <!-- Brand Texts -->
    <text x="65" y="24" font-family="'Cairo', sans-serif" font-size="20" font-weight="900" fill="#ffffff">أجنحة دَارِك الفندقية الفاخرة</text>
    <text x="65" y="44" font-family="'Cairo', sans-serif" font-size="12" font-weight="bold" fill="#f59e0b">تصنيف 5 نجوم • خدمة راقية ومفروشة بالكامل</text>
  </g>

  <!-- Stars & Badge Top Right -->
  <g transform="translate(760, 35)">
    <rect width="195" height="42" rx="21" fill="#0f172a" stroke="#f59e0b" stroke-width="1.5" />
    <text x="97" y="26" font-family="'Cairo', sans-serif" font-size="13" font-weight="bold" fill="#f59e0b" text-anchor="middle">
      ⭐⭐⭐⭐⭐ فندقي فاخر
    </text>
  </g>

  <!-- Bottom Floating Pill -->
  <g transform="translate(500, 575)">
    <rect x="-180" y="-18" width="360" height="36" rx="18" fill="#020617" fill-opacity="0.85" stroke="#f59e0b" stroke-width="1.5" />
    <text x="0" y="6" font-family="'Cairo', sans-serif" font-size="13" font-weight="bold" fill="#ffffff" text-anchor="middle">
      منصة دَارِك لتنظيم الإيجار • أجنحة البرج السكنية
    </text>
  </g>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Return Payment / Booking Receipt Voucher SVG
     */
    public static function renderReceiptSvg(string $path = '')
    {
        $recId = 'REC-' . strtoupper(substr(md5($path ?: time()), 0, 8));
        $date = now()->format('Y-m-d');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 900" width="700" height="900">
  <rect width="700" height="900" fill="#0f172a" />
  <rect x="25" y="25" width="650" height="850" rx="16" fill="#1e293b" stroke="#10b981" stroke-width="2" />
  <g transform="translate(350, 90)" text-anchor="middle" font-family="'Cairo', sans-serif">
    <circle cx="0" cy="0" r="35" fill="#065f46" />
    <text x="0" y="12" font-size="30">💳</text>
    <text x="0" y="60" font-size="20" font-weight="bold" fill="#ffffff">إيصال سداد معتمد</text>
    <text x="0" y="85" font-size="13" fill="#10b981">منصة دَارِك لتنظيم الإيجار</text>
    <text x="0" y="130" font-family="monospace" font-size="18" font-weight="bold" fill="#f59e0b">$recId</text>
    <text x="0" y="160" font-size="13" fill="#94a3b8">تاريخ المعاملة: $date م</text>
    <rect x="-100" y="180" width="200" height="36" rx="18" fill="#065f46" />
    <text x="0" y="204" font-size="14" font-weight="bold" fill="#34d399">تم تأكيد الدفع ✅</text>
  </g>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Generic Document SVG
     */
    public static function renderGenericDocSvg(string $path = '')
    {
        return self::renderIdDocumentSvg($path);
    }
}

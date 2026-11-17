<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق - منصة دارك</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1e293b;
            direction: rtl;
            text-align: right;
        }
        .container {
            max-width: 520px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 28px 24px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20px;
            margin: 0;
            font-weight: 800;
        }
        .header p {
            color: #fbbf24;
            font-size: 12px;
            margin: 6px 0 0;
            font-weight: 600;
        }
        .content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.7;
            color: #475569;
            margin-bottom: 24px;
        }
        .otp-box {
            background: #fffbeb;
            border: 2px dashed #f59e0b;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-title {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 900;
            color: #b45309;
            letter-spacing: 10px;
            margin: 0;
            display: inline-block;
            direction: ltr;
        }
        .timer-notice {
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
        }
        .warning-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 20px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>منصة دَارِك لتنظيم الإيجار</h1>
            <p>إدارة وتشغيل العقارات بأعلى درجات الأمان</p>
        </div>

        <!-- Body -->
        <div class="content">
            <div class="greeting">مرحباً {{ $name }}،</div>
            <p class="text">
                تلقينا طلباً لإعادة تعيين كلمة المرور لحسابك المسجل في منصة دارك. استخدم رمز التحقق (OTP) أدناه لإكمال العملية:
            </p>

            <div class="otp-box">
                <div class="otp-title">رمز التحقق لمرة واحدة (OTP)</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="timer-notice">⏱️ هذا الرمز صالح لمدة <strong>15 دقيقة</strong> فقط.</div>
            </div>

            <div class="warning-box">
                🔒 <strong>تنبيه أمني:</strong> لا تشارك هذا الرمز مع أي شخص. فريق منصة دارك لن يطلب منك هذا الرمز أبداً. إذا لم تكن أنت من طلب إعادة التعيين، يمكنك تجاهل هذه الرسالة بأمان وسيبقى حسابك محمياً.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} لمنصة دَارِك لتنظيم الإيجار.<br>
            هذه الرسالة آلية، يرجى عدم الرد عليها مباشرة.
        </div>
    </div>
</body>
</html>

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values', 'ip_address'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user() { return $this->belongsTo(User::class); }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'investor_requested_contract' => 'طلب إصدار وتوثيق عقد إيجار',
            'wallet_balance_added' => 'شحن رصيد وإيداع بمحفظة المستثمر',
            'booking_created' => 'إنشاء طلب حجز جديد',
            'booking_confirmed' => 'تأكيد الحجز وخصم الرسوم وتوليد كود الاستقبال',
            'receipt_uploaded' => 'رفع إيصال تحويل بنكي للحجز',
            'investor_approved' => 'اعتماد وتفعيل حساب المستثمر',
            'investor_suspended' => 'إيقاف حساب المستثمر وتعليق نشاطه',
            'investor_admin_approval_toggled' => 'تعديل استثناء الموافقة الإدارية للمستثمر',
            'apartment_admin_approval_toggled' => 'تعديل استثناء العرض للشقة',
            'apartment_homepage_featured_toggled' => 'تغيير تثبيت الشقة بالصفحة الرئيسية',
            'investor_profile_updated' => 'تحديث بيانات الحساب والملف الشخصي',
            'conversation_status_toggled' => 'تغيير حالة المحادثة (فتح / إغلاق)',
            'inspection_requested' => 'طلب معاينة شقة جديدة',
            'bypass_attempt_detected' => '🚨 رصد محاولة تحايل وتجاوز عمولة بالشات',
            'reception_checkin' => '🔑 تسجيل دخول نزيل بالاستقبال وتسليم المفتاح',
            'reception_checkout' => 'تسجيل خروج نزيل ومغادرة الشقة',
            'unregistered_deal_recorded' => '🚨 تسجيل صفقة غير معتمدة وفرض غرامة',
            'user_login_success' => '✅ تسجيل دخول ناجح',
            'security_failed_login' => '🔒 محاولة تسجيل دخول فاشلة',
            'security_brute_force_blocked' => '🛡️ حظر محاولات تخمين متكررة (Rate Limit)',
            'security_unauthorized_admin_access' => '⛔ محاولة وصول غير مصرح لصفحات الإدارة',
            'security_path_traversal_blocked' => '🚫 محاولة استعراض مسار محظور (Path Traversal)',
            'security_forbidden_extension_blocked' => '🛑 محاولة طلب ملف بامتداد محظور',
            'security_otp_rate_limited' => '⚠️ تجاوز حد طلب رموز التحقق OTP',
            'security_invalid_otp_attempt' => '❌ إدخال رمز تحقق خاطئ (OTP)',
            'password_reset_via_email_otp' => '🔑 إعادة تعيين كلمة المرور عبر البريد',
            default => str_replace('_', ' ', $this->action),
        };
    }

    public function getActionBadgeColorAttribute(): string
    {
        return match ($this->action) {
            'investor_approved', 'booking_confirmed', 'reception_checkin', 'user_login_success', 'password_reset_via_email_otp' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'wallet_balance_added' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'investor_requested_contract' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'bypass_attempt_detected', 'unregistered_deal_recorded', 'investor_suspended',
            'security_failed_login', 'security_brute_force_blocked', 'security_unauthorized_admin_access',
            'security_path_traversal_blocked', 'security_forbidden_extension_blocked' => 'bg-rose-100 text-rose-800 border-rose-300',
            'receipt_uploaded', 'inspection_requested', 'security_otp_rate_limited', 'security_invalid_otp_attempt' => 'bg-amber-100 text-amber-800 border-amber-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getModelLabelAttribute(): string
    {
        if (!$this->model_type) return '—';
        $basename = class_basename($this->model_type);
        $typeNames = [
            'Apartment' => 'شقة',
            'Booking' => 'حجز',
            'Investor' => 'مستثمر',
            'RentalContract' => 'عقد إيجار',
            'Conversation' => 'محادثة شات',
            'Wallet' => 'محفظة مالية',
            'InspectionRequest' => 'طلب معاينة',
            'User' => 'مستخدم',
        ];
        return ($typeNames[$basename] ?? $basename) . ' #' . $this->model_id;
    }
}

<?php
namespace App\Services;

use App\Models\NotificationLog;

class NotificationService
{
    /**
     * Send a WhatsApp notification (placeholder - integrate with actual WhatsApp API)
     */
    public static function sendWhatsApp(string $phone, string $message, string $recipientType = 'customer', ?string $relatedType = null, ?int $relatedId = null): NotificationLog
    {
        $log = NotificationLog::create([
            'type' => 'whatsapp',
            'recipient_phone' => $phone,
            'recipient_type' => $recipientType,
            'message' => $message,
            'status' => 'pending',
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);

        try {
            // TODO: Integrate with WhatsApp Business API (e.g., Twilio, WATI, UltraMsg)
            // Example with UltraMsg:
            // $response = Http::post("https://api.ultramsg.com/{instance}/messages/chat", [
            //     'token' => config('services.whatsapp.token'),
            //     'to' => $phone,
            //     'body' => $message,
            // ]);

            // For now, mark as sent (simulated)
            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $log->retry_count + 1,
            ]);
        }

        return $log;
    }

    /**
     * Send booking confirmation notification
     */
    public static function notifyBookingConfirmed($booking): void
    {
        $message = "✅ تأكيد حجز\n";
        $message .= "رقم الحجز: {$booking->reference_number}\n";
        $message .= "الشقة: {$booking->apartment->apartment_number} - الدور {$booking->apartment->floor_number}\n";
        $message .= "تاريخ الدخول: {$booking->checkin_date->format('Y-m-d')}\n";
        $message .= "تاريخ الخروج: {$booking->checkout_date->format('Y-m-d')}\n";
        $message .= "المبلغ: {$booking->total_amount} ريال";

        // Notify customer
        self::sendWhatsApp($booking->customer_phone, $message, 'customer', 'booking', $booking->id);

        // Notify investor
        $investor = $booking->apartment->investor;
        if ($investor) {
            self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'booking', $booking->id);
        }

        // Notify admin
        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'booking', $booking->id);
        }
    }

    /**
     * Send receipt uploaded notification
     */
    public static function notifyReceiptUploaded($booking): void
    {
        $message = "📄 إيصال تحويل جديد\n";
        $message .= "رقم الحجز: {$booking->reference_number}\n";
        $message .= "الشقة: {$booking->apartment->apartment_number}\n";
        $message .= "العميل: {$booking->customer_name}\n";
        $message .= "الإقامة: {$booking->checkin_date->format('Y-m-d')} إلى {$booking->checkout_date->format('Y-m-d')}\n";
        $message .= "المبلغ: {$booking->total_amount} ريال\n";
        $message .= "الحالة: بانتظار التحقق من الدفع";

        $investor = $booking->apartment->investor;
        if ($investor) {
            self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'booking', $booking->id);
        }

        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'booking', $booking->id);
        }
    }

    /**
     * Send inspection request notification
     */
    public static function notifyInspectionRequest($inspection): void
    {
        $message = "👁 طلب معاينة جديد\n";
        $message .= "الشقة: {$inspection->apartment->apartment_number} - الدور {$inspection->apartment->floor_number}\n";
        $message .= "العميل: {$inspection->customer_name}\n";
        $message .= "الجوال: {$inspection->customer_phone}";

        $investor = $inspection->apartment->investor;
        if ($investor) {
            self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'inspection', $inspection->id);
        }

        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'inspection', $inspection->id);
        }
    }
}

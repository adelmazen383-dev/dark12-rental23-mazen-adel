<?php

namespace App\Services;

use App\Models\NotificationLog;

class NotificationService
{
    /**
     * Send a WhatsApp notification (records log for direct WhatsApp link or API gateway).
     */
    public static function sendWhatsApp(?string $phone, string $message, string $recipientType = 'customer', ?string $relatedType = null, ?int $relatedId = null): NotificationLog
    {
        $log = NotificationLog::create([
            'type' => 'whatsapp',
            'recipient_phone' => $phone ?: '',
            'recipient_type' => $recipientType,
            'message' => $message,
            'status' => 'pending',
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);

        try {
            // TODO: Integrate with WhatsApp Business API (e.g., Twilio, WATI, UltraMsg)
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
     * Notify investor and admin immediately when a client creates a new booking request.
     */
    public static function notifyNewBooking($booking): void
    {
        $booking->loadMissing('apartment.investor');
        $apartment = $booking->apartment;
        $investor = $apartment?->investor;

        $message = "🎉 طلب حجز جديد\n";
        $message .= "رقم الحجز: {$booking->reference_number}\n";
        $message .= "الشقة: {$apartment->apartment_number} - الدور {$apartment->floor_number}\n";
        $message .= "العميل: {$booking->customer_name} ({$booking->customer_phone})\n";
        $message .= "الإقامة: {$booking->checkin_date->format('Y-m-d')} إلى {$booking->checkout_date->format('Y-m-d')}\n";
        $message .= "المبلغ الإجمالي: " . number_format((float) $booking->total_amount, 0) . " ريال\n";
        $message .= "الحالة: بانتظار التحويل البنكي";

        if ($investor) {
            if ($investor->whatsapp) {
                self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'booking', $booking->id);
            }

            WebPushService::sendToInvestor(
                $investor,
                "🎉 عميل جديد يريد استئجار شقة {$apartment->apartment_number}!",
                "العميل: {$booking->customer_name} ({$booking->customer_phone}) | من {$booking->checkin_date->format('Y-m-d')} إلى {$booking->checkout_date->format('Y-m-d')} | " . number_format((float) $booking->total_amount, 0) . " ريال",
                route('investor.bookings.show', $booking->id),
                'booking-new-' . $booking->id
            );
        }

        WebPushService::sendToAdmins(
            "🔔 طلب حجز جديد #{$booking->reference_number}",
            "شقة {$apartment->apartment_number} | {$booking->customer_name} | " . number_format((float) $booking->total_amount, 0) . " ريال",
            route('admin.bookings.show', $booking->id),
            'admin-booking-new-' . $booking->id
        );

        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'booking', $booking->id);
        }
    }

    /**
     * Send booking confirmation notification to BOTH customer and investor instantly.
     */
    public static function notifyBookingConfirmed($booking): void
    {
        $booking->loadMissing('apartment.investor');
        $apartment = $booking->apartment;
        $investor = $apartment?->investor;

        $message = "✅ تأكيد حجز\n";
        $message .= "رقم الحجز: {$booking->reference_number}\n";
        $message .= "الشقة: {$apartment->apartment_number} - الدور {$apartment->floor_number}\n";
        $message .= "تاريخ الدخول: {$booking->checkin_date->format('Y-m-d')}\n";
        $message .= "تاريخ الخروج: {$booking->checkout_date->format('Y-m-d')}\n";
        $message .= "المبلغ: {$booking->total_amount} ريال";

        // Notify customer (WhatsApp log + instant background Web Push)
        self::sendWhatsApp($booking->customer_phone, $message, 'customer', 'booking', $booking->id);

        WebPushService::sendToClientByBooking(
            $booking,
            "✅ تم تأكيد حجزك رقم #{$booking->reference_number}!",
            "شقة {$apartment->apartment_number} (الدور {$apartment->floor_number}) | كود استلام المفتاح بالاستقبال: {$booking->reception_code}",
            route('client.booking.confirmation', $booking->reference_number),
            'client-booking-confirmed-' . $booking->id
        );

        // Notify investor (WhatsApp log + instant background Web Push)
        if ($investor) {
            if ($investor->whatsapp) {
                self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'booking', $booking->id);
            }

            WebPushService::sendToInvestor(
                $investor,
                "✅ تم تأكيد حجز شقتك رقم {$apartment->apartment_number}",
                "حجز #{$booking->reference_number} | {$booking->customer_name} | {$booking->checkin_date->format('Y-m-d')} إلى {$booking->checkout_date->format('Y-m-d')}",
                route('investor.bookings.show', $booking->id),
                'booking-confirmed-' . $booking->id
            );
        }

        // Notify admin
        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'booking', $booking->id);
        }
    }

    /**
     * Send receipt uploaded notification to investor and admin.
     */
    public static function notifyReceiptUploaded($booking): void
    {
        $booking->loadMissing('apartment.investor');
        $apartment = $booking->apartment;
        $investor = $apartment?->investor;

        $message = "📄 إيصال تحويل جديد\n";
        $message .= "رقم الحجز: {$booking->reference_number}\n";
        $message .= "الشقة: {$apartment->apartment_number}\n";
        $message .= "العميل: {$booking->customer_name}\n";
        $message .= "الإقامة: {$booking->checkin_date->format('Y-m-d')} إلى {$booking->checkout_date->format('Y-m-d')}\n";
        $message .= "المبلغ: {$booking->total_amount} ريال\n";
        $message .= "الحالة: بانتظار التحقق من الدفع";

        if ($investor) {
            if ($investor->whatsapp) {
                self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'booking', $booking->id);
            }

            WebPushService::sendToInvestor(
                $investor,
                "📄 العميل رفع إيصال الدفع - شقة {$apartment->apartment_number}",
                "حجز #{$booking->reference_number} | {$booking->customer_name} | " . number_format((float) $booking->total_amount, 0) . " ريال (بانتظار مراجعة الإدارة)",
                route('investor.bookings.show', $booking->id),
                'booking-receipt-' . $booking->id
            );
        }

        WebPushService::sendToAdmins(
            "📄 إيصال تحويل بانتظار المراجعة #{$booking->reference_number}",
            "شقة {$apartment->apartment_number} | {$booking->customer_name} | " . number_format((float) $booking->total_amount, 0) . " ريال",
            route('admin.bookings.show', $booking->id),
            'admin-receipt-' . $booking->id
        );

        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'booking', $booking->id);
        }
    }

    /**
     * Send inspection request notification.
     */
    public static function notifyInspectionRequest($inspection): void
    {
        $inspection->loadMissing('apartment.investor');
        $apartment = $inspection->apartment;
        $investor = $apartment?->investor;

        $message = "👁 طلب معاينة جديد\n";
        $message .= "الشقة: {$apartment->apartment_number} - الدور {$apartment->floor_number}\n";
        $message .= "العميل: {$inspection->customer_name}\n";
        $message .= "الجوال: {$inspection->customer_phone}";

        if ($investor) {
            if ($investor->whatsapp) {
                self::sendWhatsApp($investor->whatsapp, $message, 'investor', 'inspection', $inspection->id);
            }

            WebPushService::sendToInvestor(
                $investor,
                "👁 عميل يطلب معاينة شقتك رقم {$apartment->apartment_number}",
                "العميل: {$inspection->customer_name} | الجوال: {$inspection->customer_phone}" . ($inspection->notes ? " | {$inspection->notes}" : ''),
                route('investor.dashboard'),
                'inspection-' . $inspection->id
            );
        }

        WebPushService::sendToAdmins(
            "👁 طلب معاينة جديد - شقة {$apartment->apartment_number}",
            "العميل: {$inspection->customer_name} ({$inspection->customer_phone})",
            route('admin.inspections.index'),
            'admin-inspection-' . $inspection->id
        );

        $adminPhone = \App\Models\Setting::getValue('admin_whatsapp', '');
        if ($adminPhone) {
            self::sendWhatsApp($adminPhone, $message, 'admin', 'inspection', $inspection->id);
        }
    }
}

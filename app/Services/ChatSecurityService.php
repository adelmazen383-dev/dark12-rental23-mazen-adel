<?php

namespace App\Services;

use App\Models\{Conversation, Message};

class ChatSecurityService
{
    /**
     * Common evasion keywords used by parties trying to circumvent platform commission.
     */
    protected static array $suspiciousKeywords = [
        'واتس', 'واتساب', 'whats', 'whatsapp',
        'رقمي', 'جوالي', 'فون', 'موبايلي', 'تليفون', 'اتصل بي', 'كلمني',
        'كاش', 'بره', 'برة', 'خارج المنصة', 'خارج الموقع', 'وفر العمولة', 'نوفر العمولة', 'توفير العمولة',
        'من ورى', 'من ورا', 'ايبان', 'iban', 'تحويل شخصي', 'حسابي البنكي', 'نتفق بره'
    ];

    /**
     * Check if a message contains bypass attempts (phone numbers or circumvention keywords).
     * Returns the detected trigger description, or null if clean.
     */
    public static function detectBypassAttempt(string $text): ?string
    {
        // 1. Check for phone numbers (Arabic or English digits, with or without spaces/symbols)
        $normalizedText = self::normalizeArabicDigits($text);
        
        // Remove spaces and dashes inside digit sequences to catch separated numbers: 0 5 0 1 2 3 4 5 6 7
        $digitsOnlySequence = preg_replace('/(?<=\d)[\s\-\.](?=\d)/', '', $normalizedText);

        // Saudi Mobile / Standard Phone Patterns: 05XXXXXXXX or +9665XXXXXXXX or general 9-12 digits sequence
        if (preg_match('/(05\d{8}|(\+?9665)\d{8}|\b\d{9,12}\b)/', $digitsOnlySequence, $matches)) {
            return "محاولة تبادل رقم هاتف أو وسيلة اتصال خارجية ({$matches[0]})";
        }

        // 2. Check for suspicious circumvention keywords
        $lowerText = mb_strtolower($text, 'UTF-8');
        foreach (self::$suspiciousKeywords as $keyword) {
            if (mb_strpos($lowerText, $keyword) !== false) {
                return "محاولة تواصل خارجي أو التهرب من عمولة المنصة باستخدام عبارة '{$keyword}'";
            }
        }

        return null;
    }

    /**
     * Mask detected phone numbers in text to prevent direct circumvention.
     */
    public static function maskPhoneNumbers(string $text): string
    {
        $normalized = self::normalizeArabicDigits($text);
        $masked = preg_replace('/(05\d{8}|(\+?9665)\d{8}|\b0\d{8,11}\b)/', '[رقم هاتف محجوب لسلامة المعاملة]', $normalized);
        return $masked;
    }

    /**
     * Apply security check on sent message.
     * Flags conversation and posts security reminder if needed.
     */
    public static function inspectAndGuard(Conversation $conversation, string $rawMessage, string $senderType): array
    {
        $bypassReason = self::detectBypassAttempt($rawMessage);

        if ($bypassReason) {
            $isFirstFlag = !$conversation->has_bypass_attempt;

            $conversation->update([
                'has_bypass_attempt' => true,
                'bypass_attempt_reason' => $bypassReason,
            ]);

            AuditService::log('chat_bypass_attempt_flagged', $conversation, null, [
                'sender_type' => $senderType,
                'reason' => $bypassReason,
                'message_snippet' => mb_substr($rawMessage, 0, 100),
            ]);

            // If first attempt, insert an official system cautionary notice in the conversation
            if ($isFirstFlag) {
                $conversation->messages()->create([
                    'sender_type' => 'admin',
                    'sender_name' => 'إدارة البرج والأمن الفندقي',
                    'message' => "⚠️ تنبيه أمني وإرشادي من إدارة البرج:\nنحيطكم علماً بأن إتمام الحجز عبر المنصة هو الشرط الأساسي والوحيد لاستلام كارت الدخول والمفتاح من موظف الاستقبال، وضمان وديعة التأمين والعقد القانوني الموحد.\nأي اتفاق خارجي يسقط حق النزيل في دخول البرج ويوقع مخالفة تشغيلية على المالك.",
                    'is_read_by_customer' => false,
                    'is_read_by_investor' => false,
                    'is_read_by_admin' => true,
                ]);
            }

            return [
                'flagged' => true,
                'reason' => $bypassReason,
                'cleaned_message' => self::maskPhoneNumbers($rawMessage),
            ];
        }

        return [
            'flagged' => false,
            'reason' => null,
            'cleaned_message' => $rawMessage,
        ];
    }

    /**
     * Convert Eastern Arabic numerals (٠-٩) to Western (0-9)
     */
    protected static function normalizeArabicDigits(string $str): string
    {
        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($arabic, $latin, $str);
    }
}

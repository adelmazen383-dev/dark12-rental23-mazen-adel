<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MailConfigService
{
    /**
     * Apply dynamic mail/SMTP settings saved in Admin Settings (if configured),
     * while preserving Laravel testing environment ('array' mailer).
     */
    public static function apply(): void
    {
        if (app()->environment('testing') || config('mail.default') === 'array') {
            return;
        }

        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $mailer = Setting::getValue('mail_mailer');
            $host = trim((string) Setting::getValue('mail_host', ''));
            $port = (int) Setting::getValue('mail_port', 587);
            $encryption = strtolower(trim((string) Setting::getValue('mail_encryption', 'tls')));
            $username = trim((string) Setting::getValue('mail_username', ''));
            $rawPassword = (string) Setting::getValue('mail_password', '');
            $fromAddress = trim((string) Setting::getValue('mail_from_address', ''));
            $fromName = trim((string) Setting::getValue('mail_from_name', ''));

            if ($rawPassword !== '') {
                try {
                    $password = Crypt::decryptString($rawPassword);
                } catch (\Throwable) {
                    $password = $rawPassword;
                }
            } else {
                $password = '';
            }

            // Only override .env config if Admin actually configured SMTP in database settings
            if ($mailer === 'smtp' && $host !== '') {
                $scheme = ($encryption === 'ssl' || $port === 465) ? 'smtps' : 'smtp';

                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.scheme' => $scheme,
                    'mail.mailers.smtp.host' => $host,
                    'mail.mailers.smtp.port' => $port ?: 587,
                    'mail.mailers.smtp.username' => $username ?: config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $password ?: config('mail.mailers.smtp.password'),
                    'mail.mailers.smtp.timeout' => 12,
                ]);
            } elseif (config('mail.default') === 'smtp') {
                // Ensure .env SMTP port 465 gets smtps scheme automatically if MAIL_SCHEME wasn't set
                $envPort = (int) config('mail.mailers.smtp.port', 587);
                $envScheme = config('mail.mailers.smtp.scheme');
                if (!$envScheme && $envPort === 465) {
                    config(['mail.mailers.smtp.scheme' => 'smtps']);
                }
                if (!config('mail.mailers.smtp.timeout')) {
                    config(['mail.mailers.smtp.timeout' => 12]);
                }
            }

            // Ensure valid From address (avoid hello@example.com being rejected by Gmail/SMTP relays)
            $effectiveFrom = $fromAddress;
            if ($effectiveFrom === '' || $effectiveFrom === 'hello@example.com') {
                $effectiveUser = config('mail.mailers.smtp.username');
                if (is_string($effectiveUser) && filter_var($effectiveUser, FILTER_VALIDATE_EMAIL)) {
                    $effectiveFrom = $effectiveUser;
                }
            }

            if ($effectiveFrom !== '' && filter_var($effectiveFrom, FILTER_VALIDATE_EMAIL)) {
                config(['mail.from.address' => $effectiveFrom]);
            }
            if ($fromName !== '') {
                config(['mail.from.name' => $fromName]);
            }
        } catch (\Throwable $e) {
            Log::warning('MailConfigService apply skipped: ' . $e->getMessage());
        }
    }

    /**
     * Check whether real outbound email delivery is actually configured (not 'log' or placeholder 127.0.0.1).
     */
    public static function isRealDeliveryConfigured(): bool
    {
        $driver = (string) config('mail.default', 'log');

        if (in_array($driver, ['log', 'array', ''], true)) {
            return false;
        }

        if ($driver === 'smtp') {
            $host = trim((string) config('mail.mailers.smtp.host', ''));
            $port = (int) config('mail.mailers.smtp.port', 2525);
            if ($host === '' || ($host === '127.0.0.1' && $port === 2525 && !config('mail.mailers.smtp.username'))) {
                return false;
            }
        }

        return true;
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $name;

    public function __construct(string $otp, string $name = 'المستخدم')
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('رمز التحقق لإعادة تعيين كلمة المرور - منصة دارك')
                    ->view('emails.password-reset-otp')
                    ->text('emails.password-reset-otp-text');
    }
}

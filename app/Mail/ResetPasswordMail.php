<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $name,
        public int $expireMinutes = 60,
    ) {}

    public function build()
    {
        return $this->subject('Reset Your Password - Taddabur')
            ->view('mails.reset-password')
            ->text('mails.reset-password-text');
    }
}

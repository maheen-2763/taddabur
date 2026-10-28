<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $verificationUrl,
        public string $name,
    ) {}

    public function build()
    {
        return $this->subject('Verify Your Email - Taddabur')
            ->view('mails.verify-email')
            ->text('mails.verify-email-text');
    }
}

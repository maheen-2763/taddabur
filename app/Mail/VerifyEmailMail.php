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
        public bool $isChange = false, // true = user ne profile se email badla hai
    ) {}

    public function build()
    {
        return $this->subject($this->isChange ? 'Confirm Your New Email - Taddabur' : 'Verify Your Email - Taddabur')
            ->view('mails.verify-email')
            ->text('mails.verify-email-text');
    }
}

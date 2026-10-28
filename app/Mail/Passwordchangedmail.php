<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $changedAt,
    ) {}

    public function build()
    {
        return $this->subject('Your Password Was Changed - Taddabur')
            ->view('mails.password-changed')
            ->text('mails.password-changed-text');
    }
}

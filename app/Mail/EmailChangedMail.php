<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Purane email address par bheji jati hai: "aapka email badla gaya". */
class EmailChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $maskedNewEmail;

    public function __construct(
        public string $name,
        string $newEmail,
        public string $changedAt,
    ) {
        $local = strstr($newEmail, '@', true) ?: $newEmail;
        $domain = substr(strstr($newEmail, '@') ?: '', 1);
        $this->maskedNewEmail = substr($local, 0, 2) . '***@' . $domain;
    }

    public function build()
    {
        return $this->subject('Your Email Address Was Changed - Taddabur')
            ->view('mails.email-changed')
            ->text('mails.email-changed-text');
    }
}

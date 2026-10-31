<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $deletedAt,
    ) {}

    public function build()
    {
        return $this->subject('Your Taddabur Account Was Deleted')
            ->view('mails.account-deleted')
            ->text('mails.account-deleted-text');
    }
}

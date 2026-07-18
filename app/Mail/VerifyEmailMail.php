<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verificationUrl;
    public string $name;

    public function __construct(string $verificationUrl, string $name)
    {
        $this->verificationUrl = $verificationUrl;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Verify Your Email - Taddabur')
            ->view('mails.verify-email')
            ->with([
                'verificationUrl' => $this->verificationUrl,
                'name' => $this->name,
            ]);
    }
}

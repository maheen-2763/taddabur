<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public function __construct(public User $user)
    {
        $this->name = $user->name;
    }

    public function build()
    {
        return $this->subject('Welcome to Taddabur')
            ->view('mails.welcome')
            ->text('mails.welcome-text');
    }
}

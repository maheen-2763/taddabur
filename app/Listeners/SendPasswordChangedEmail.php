<?php

namespace App\Listeners;

use App\Mail\PasswordChangedMail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendPasswordChangedEmail
{
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        try {
            Mail::to($user->email)->send(
                new PasswordChangedMail($user->name, now()->format('d M Y, H:i T'))
            );
        } catch (Throwable $e) {
            // Password pehle hi badal chuka hai; mail fail hone se user ko error nahi dikhna chahiye.
            report($e);
        }
    }
}

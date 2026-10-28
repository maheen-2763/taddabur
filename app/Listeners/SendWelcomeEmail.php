<?php

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendWelcomeEmail
{
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Atomic claim: sirf ek request welcome mail bhejegi.
        $claimed = User::where('id', $user->id)
            ->whereNull('welcome_email_sent_at')
            ->update(['welcome_email_sent_at' => now()]);

        if ($claimed === 0) {
            return;
        }

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (Throwable $e) {
            // Verification pura ho chuka hai; mail error se user ko 500 nahi dikhna chahiye.
            report($e);
            User::where('id', $user->id)->update(['welcome_email_sent_at' => null]);
        }
    }
}

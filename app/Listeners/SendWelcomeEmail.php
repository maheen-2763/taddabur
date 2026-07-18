<?php

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendWelcomeEmail
{
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Atomic: sirf tab update karega jab abhi tak NULL ho — 
        // isse do simultaneous requests mein sirf EK hi safal hoga
        $updated = User::where('id', $user->id)
            ->whereNull('welcome_email_sent_at')
            ->update(['welcome_email_sent_at' => now()]);

        // Agar update 0 rows pe hua, matlab dusri request already bhej chuki hai — skip karo
        if ($updated === 0) {
            return;
        }

        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}

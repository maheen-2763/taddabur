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

        Mail::to($user->email)->send(new WelcomeEmail($user));
    }
}

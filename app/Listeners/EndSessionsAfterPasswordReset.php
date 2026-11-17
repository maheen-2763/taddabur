<?php

namespace App\Listeners;

use App\Support\SingleSession;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;

final class EndSessionsAfterPasswordReset
{
    public function subscribe(Dispatcher $events): array
    {
        return [PasswordReset::class => 'onPasswordReset'];
    }

    // A reset means the old password may have been compromised: end every session on every device.
    public function onPasswordReset(PasswordReset $event): void
    {
        SingleSession::endOtherSessions($event->user);
        SingleSession::rotateRememberToken($event->user);
    }
}

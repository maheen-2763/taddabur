<?php

namespace App\Listeners;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;

final class LogAuthActivity
{
    public function __construct(private readonly RecordActivity $record) {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            Failed::class => 'onFailed',
            Lockout::class => 'onLockout',
            PasswordReset::class => 'onPasswordReset',
        ];
    }

    // Credentials / IP / email yahan kabhi read nahi hote.
    public function onFailed(Failed $event): void
    {
        $this->record->handle(ActivityEvent::LoginFailed);
    }

    public function onLockout(Lockout $event): void
    {
        $this->record->handle(ActivityEvent::Lockout);
    }

    public function onPasswordReset(PasswordReset $event): void
    {
        $this->record->handle(ActivityEvent::PasswordReset, (int) $event->user->getAuthIdentifier());
    }
}

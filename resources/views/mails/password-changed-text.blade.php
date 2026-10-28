Assalamu alaikum {!! \Illuminate\Support\Str::before($name, ' ') !!},

The password of your Taddabur account was changed on {{ $changedAt }}.

If this was you, no further action is needed.

If this was NOT you, reset your password right away:
{!! route('password.request') !!}

and write to us at {{ config('mail.from.address') }}.

Taddabur - Understand. Reflect. Grow.
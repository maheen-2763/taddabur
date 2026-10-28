Assalamu alaikum {!! \Illuminate\Support\Str::before($name, ' ') !!},

We received a request to reset the password of your Taddabur account.

Choose a new password:
{!! $resetUrl !!}

This link expires in {{ $expireMinutes }} minutes and can be used only once. If you did not request a password reset, no
action is needed. Your password will stay the same. Never share this link with anyone.

Taddabur - Understand. Reflect. Grow.
Need help? {{ config('mail.from.address') }}
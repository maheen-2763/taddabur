Assalamu alaikum {!! \Illuminate\Support\Str::before($name, ' ') !!},

@if ($isChange)
    You asked to change the email address of your Taddabur account to this address. Please confirm it to continue using your
    account.

    Confirm your new email:
@else
    Thank you for joining Taddabur. Please confirm your email address to activate your account.

    Verify your email:
@endif
{!! $verificationUrl !!}

This link expires in {{ config('auth.verification.expire', 60) }} minutes.@if ($isChange) If you did not request this
change, please write to us at {{ config('mail.from.address') }} right away.@else If you did not create a Taddabur
account, you can safely ignore this email.@endif


Taddabur - Understand. Reflect. Grow.
Need help? {{ config('mail.from.address') }}
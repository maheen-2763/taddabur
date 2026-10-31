@extends('mails.layout')
@section('title', $isChange ? 'Confirm your new email — Taddabur' : 'Verify your email — Taddabur')
@section('preheader', $isChange ? 'Confirm your new email address to keep using your Taddabur account.' : 'Confirm your email address to activate your Taddabur account.')
@section('heading', $isChange ? 'Confirm your new email' : 'Verify your email')

@section('content')
  <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
  @if ($isChange)
    <p style="margin:0 0 6px;">You asked to change the email address of your Taddabur account to this address. Please
      confirm it to continue using your account.</p>
  @else
    <p style="margin:0 0 6px;">Thank you for joining Taddabur. Please confirm your email address to activate your account
      and begin your journey of understanding and reflection.</p>
  @endif
  @include('mails.partials.button', ['url' => $verificationUrl, 'label' => $isChange ? 'Confirm New Email' : 'Verify Email Address'])
  @include('mails.partials.link-fallback', ['url' => $verificationUrl])
@endsection

@section('verse')
  @include('mails.partials.arabic-image', ['file' => 'ayah-4-82', 'alt' => 'أَفَلَا يَتَدَبَّرُونَ ٱلْقُرْءَانَ', 'width' => 242])
  <div style="font-size:14px;line-height:1.6;font-style:italic;color:#5c5846;margin-top:6px;">"Do they not reflect upon
    the Quran?"</div>
  <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9a6f1c;margin-top:4px;">An-Nisa 4:82</div>
@endsection

@section('footnote')
  @if ($isChange)
    This link expires in {{ config('auth.verification.expire', 60) }} minutes. If you did not request this change, please
    write to us at {{ config('mail.from.address') }} right away.
  @else
    This link expires in {{ config('auth.verification.expire', 60) }} minutes. If you did not create a Taddabur account, you
    can safely ignore this email.
  @endif
@endsection
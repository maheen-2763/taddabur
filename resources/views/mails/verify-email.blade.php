@extends('mails.layout')
@section('title', 'Verify your email — Taddabur')
@section('preheader', 'Confirm your email address to activate your Taddabur account.')
@section('heading', 'Verify your email')

@section('content')
  <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
  <p style="margin:0 0 6px;">Thank you for joining Taddabur. Please confirm your email address to activate your account
    and begin your journey of understanding and reflection.</p>
  @include('mails.partials.button', ['url' => $verificationUrl, 'label' => 'Verify Email Address'])
  @include('mails.partials.link-fallback', ['url' => $verificationUrl])
@endsection

@section('verse')
  @include('mails.partials.arabic-image', ['file' => 'ayah-20-114', 'alt' => 'وَقُل رَّبِّ زِدْنِى عِلْمًا', 'width' => 235])
  <div style="font-size:14px;line-height:1.6;font-style:italic;color:#5c5846;margin-top:6px;">"And say: My Lord, increase
    me in knowledge."</div>
  <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9a6f1c;margin-top:4px;">Ta-Ha 20:114</div>
@endsection

@section('footnote')
  This link expires in {{ config('auth.verification.expire', 60) }} minutes. If you did not create a Taddabur account, you
  can safely ignore this email.
@endsection
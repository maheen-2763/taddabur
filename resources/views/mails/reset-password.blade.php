@extends('mails.layout')
@section('title', 'Reset your password — Taddabur')
@section('preheader', 'Use this link to choose a new password for your Taddabur account.')
@section('heading', 'Reset your password')

@section('content')
    <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
    <p style="margin:0 0 6px;">We received a request to reset the password of your Taddabur account. Click the button below
        to choose a new one.</p>
    @include('mails.partials.button', ['url' => $resetUrl, 'label' => 'Reset Password'])
    @include('mails.partials.link-fallback', ['url' => $resetUrl])
@endsection

@section('verse')
    @include('mails.partials.arabic-image', ['file' => 'ayah-85-14', 'alt' => 'وَهُوَ ٱلْغَفُورُ ٱلْوَدُودُ', 'width' => 218])
    <div style="margin-top:8px;font-size:15px;line-height:1.7;font-style:italic;color:#14301f;">"And He is the Forgiving,
        the Affectionate."</div>
    <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9a6f1c;margin-top:6px;">Al-Buruj 85:14</div>
@endsection

@section('footnote')
    This link expires in {{ $expireMinutes }} minutes and can be used only once. If you did not request a password reset, no
    action is needed. Your password will stay the same. Never share this link with anyone.
@endsection
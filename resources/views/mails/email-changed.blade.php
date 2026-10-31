@extends('mails.layout')
@section('title', 'Your email address was changed — Taddabur')
@section('preheader', 'The email address of your Taddabur account was just changed.')
@section('heading', 'Your email address was changed')

@section('content')
    <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
    <p style="margin:0 0 14px;">The email address of your Taddabur account was changed on <strong>{{ $changedAt }}</strong>.
        The new address is <strong>{{ $maskedNewEmail }}</strong>.</p>
    <p style="margin:0 0 6px;">If this was you, no further action is needed.</p>
    <p style="margin:0 0 6px;"><strong>If this was not you</strong>, please write to us right away so we can help you secure
        your account.</p>
    @include('mails.partials.button', ['url' => 'mailto:' . config('mail.from.address') . '?subject=' . rawurlencode('My Taddabur email was changed'), 'label' => 'Contact Support'])
@endsection

@section('footnote')
    This is a security notice sent to your previous email address. Your account now uses the new address, so password reset
    links will go there.
@endsection
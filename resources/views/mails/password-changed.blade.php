@extends('mails.layout')
@section('title', 'Your password was changed — Taddabur')
@section('preheader', 'The password of your Taddabur account was just changed.')
@section('heading', 'Your password was changed')

@section('content')
    <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
    <p style="margin:0 0 14px;">The password of your Taddabur account was changed on <strong>{{ $changedAt }}</strong>.</p>
    <p style="margin:0 0 6px;">If this was you, no further action is needed.</p>
    <p style="margin:0 0 6px;"><strong>If this was not you</strong>, reset your password right away and write to us.</p>
    @include('mails.partials.button', ['url' => route('password.request'), 'label' => 'Reset My Password'])
@endsection

@section('footnote')
    This is a security notice. You are receiving it because the password of your Taddabur account was changed. For help,
    simply reply to this email.
@endsection
@extends('mails.layout')
@section('title', 'Your account was deleted — Taddabur')
@section('preheader', 'Your Taddabur account and personal data have been deleted.')
@section('heading', 'Your account was deleted')

@section('content')
    <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
    <p style="margin:0 0 14px;">Your Taddabur account was deleted on <strong>{{ $deletedAt }}</strong>. Your reading
        progress, bookmarks, notes and preferences have been removed from our system.</p>
    <p style="margin:0 0 14px;">Donations, if you made any, are recorded separately by the name you entered and are not
        linked to your account.</p>
    <p style="margin:0 0 6px;">If you did not do this, please write to us at <a
            href="mailto:{{ config('mail.from.address') }}" style="color:#9a6f1c;">{{ config('mail.from.address') }}</a>.
    </p>
    <p style="margin:14px 0 6px;">You are always welcome to return. JazakAllahu khair for being part of Taddabur.</p>
@endsection

@section('footnote')
    This is a confirmation of an action on your account. We will not send you any further emails unless you create a new
    account.
@endsection
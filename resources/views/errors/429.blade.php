@php
    $retry = isset($exception) && method_exists($exception, 'getHeaders')
        ? (int) ($exception->getHeaders()['Retry-After'] ?? 0)
        : 0;
@endphp
@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Please slow down a little')
@section('message', 'We received too many requests from you in a short time. This protects your account and our service.')

@section('extra')
    @if ($retry > 0)
        <p class="msg" id="wait" role="status">You can try again in <strong id="secs">{{ $retry }}</strong> seconds.</p>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var s = {{ $retry }};
                var secs = document.getElementById('secs');
                var wait = document.getElementById('wait');
                var retry = document.getElementById('retry');
                var timer = setInterval(function () {
                    s--;
                    if (s <= 0) {
                        clearInterval(timer);
                        wait.hidden = true;
                        retry.hidden = false;
                        return;
                    }
                    secs.textContent = s;
                }, 1000);
            });
        </script>
    @endif
@endsection

@section('actions')
    <a class="btn" id="retry" href="{{ url()->previous('/') }}" @if ($retry > 0) hidden @endif>Try again</a>
    <a class="btn btn-ghost" href="/">Return Home</a>
@endsection
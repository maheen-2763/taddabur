@extends('layouts.guest')

@section('title', 'Verify Email — Taddabur')

@push('styles')
    <style>
        .auth-section {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .auth-pattern {
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z' fill='none' stroke='rgba(201,150,58,0.07)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        .auth-watermark {
            position: fixed;
            top: 55%;
            right: 3%;
            transform: translateY(-50%);
            font-family: var(--font-arabic);
            font-size: clamp(8rem, 13vw, 14rem);
            line-height: 1;
            color: rgba(201, 150, 58, 0.12);
            pointer-events: none;
            z-index: 0;
            user-select: none;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .auth-bismillah {
            font-family: var(--font-arabic);
            font-size: 1.4rem;
            color: var(--gold-light);
            opacity: 0.85;
        }

        .auth-tagline {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            letter-spacing: 0.04em;
        }

        .auth-card {
            position: relative;
            background: var(--cream);
            border: 1px solid rgba(201, 150, 58, 0.25);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }

        .auth-card::before,
        .auth-card::after {
            content: '';
            position: absolute;
            width: 28px;
            height: 28px;
            border: 2px solid var(--gold);
            opacity: 0.55;
            pointer-events: none;
            z-index: 2;
        }

        .auth-card::before {
            top: -1px;
            left: -1px;
            border-right: none;
            border-bottom: none;
        }

        .auth-card::after {
            bottom: -1px;
            right: -1px;
            border-left: none;
            border-top: none;
        }

        .auth-card .form-control {
            border-radius: var(--radius);
            border-color: var(--border);
            background: #fff;
            color: var(--ink);
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-card .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 150, 58, 0.15);
        }

        .auth-card .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
        }

        .auth-card .input-group>.form-control:not(:last-child) {
            border-radius: var(--radius) 0 0 var(--radius);
        }

        .auth-card .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--ink);
            margin-bottom: 0.35rem;
        }

        .btn-toggle-pass {
            border: 1px solid var(--border);
            border-left: none;
            background: #fff;
            color: var(--ink-soft);
        }

        .btn-toggle-pass:hover {
            color: var(--gold-dark);
            background: #fff;
        }

        .btn-toggle-pass:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: -2px;
        }

        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        .link-gold {
            color: var(--gold-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .link-gold:hover {
            color: var(--gold);
        }

        .icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(201, 150, 58, 0.1);
            border: 1.5px solid rgba(201, 150, 58, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .alert-brand-success {
            background: rgba(27, 94, 59, 0.1);
            border: 1px solid var(--emerald-light) !important;
            color: var(--emerald);
        }

        .alert-brand-error {
            background: rgba(220, 53, 69, 0.08);
            color: #842029;
        }

        .btn-gold.btn {
            position: relative;
        }

        .btn-gold .tasbih-loader {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-gold.is-loading .tasbih-loader {
            display: flex;
        }

        .btn-gold.is-loading .btn-label {
            visibility: hidden;
        }

        .btn-gold.is-loading:disabled {
            opacity: 1;
        }

        .bead {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.25;
            animation: beadPulse 1.2s ease-in-out infinite;
        }

        .bead:nth-child(1) {
            animation-delay: 0s;
        }

        .bead:nth-child(2) {
            animation-delay: 0.15s;
        }

        .bead:nth-child(3) {
            animation-delay: 0.30s;
        }

        .bead:nth-child(4) {
            animation-delay: 0.45s;
        }

        .bead:nth-child(5) {
            animation-delay: 0.60s;
        }

        .bead:nth-child(6) {
            animation-delay: 0.75s;
        }

        .bead:nth-child(7) {
            animation-delay: 0.90s;
        }

        @keyframes beadPulse {

            0%,
            100% {
                opacity: 0.2;
                transform: scale(0.85);
            }

            50% {
                opacity: 1;
                transform: scale(1.15);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .bead {
                animation: none;
                opacity: 0.6;
            }
        }

        .ayah-footer {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.82rem;
            line-height: 1.7;
        }

        .ayah-footer-ref {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.75rem;
        }

        .icon-circle.pulse {
            width: 72px;
            height: 72px;
            animation: iconPulse 2.5s ease-in-out infinite;
        }

        @keyframes iconPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(201, 150, 58, 0.2);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(201, 150, 58, 0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .icon-circle.pulse {
                animation: none;
            }
        }

        .steps-box {
            background: rgba(201, 150, 58, 0.05);
            border-radius: var(--radius);
            padding: 0.5rem 1.25rem;
        }

        .step-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
            color: var(--ink-soft);
        }

        .step-row:last-child {
            border-bottom: none;
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(201, 150, 58, 0.12);
            border: 1px solid rgba(201, 150, 58, 0.3);
            color: var(--gold-dark);
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .btn-signout {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--ink-soft);
            border-radius: var(--radius);
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            width: 100%;
            transition: border-color 0.2s, color 0.2s;
        }

        .btn-signout:hover {
            border-color: var(--gold);
            color: var(--ink);
        }

        /* Mobile */
        @media (max-width: 991.98px) {
            .auth-watermark {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .auth-section {
                padding: 1.5rem 1rem;
            }

            .auth-brand {
                margin-bottom: 1rem !important;
            }

            .auth-bismillah {
                font-size: 1.2rem;
                margin-bottom: 0.5rem !important;
            }

            .auth-head {
                margin-bottom: 1rem !important;
            }

            .icon-circle {
                width: 56px;
                height: 56px;
            }

            .auth-card form .mb-3 {
                margin-bottom: 0.85rem !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="auth-pattern" aria-hidden="true"></div>

    <section class="auth-section">
        <div class="auth-wrapper">

            <div class="text-center mb-4 auth-brand">
                <div class="auth-bismillah mb-3" lang="ar" dir="rtl">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-1" aria-label="Taddabur home">
                    @include('components.logo', ['variant' => 'stacked', 'height' => 40])
                </a>
                <p class="auth-tagline mt-2 mb-0">Understand. Reflect. Grow.</p>
            </div>

            <div class="auth-card p-3 p-sm-4 p-md-5 text-center">

                <div class="icon-circle pulse mx-auto mb-4">
                    <i class="bi bi-envelope-check" style="font-size:1.8rem; color:var(--gold)" aria-hidden="true"></i>
                </div>

                <h1 class="h4 heading-font mb-2" style="color:var(--ink)">Check your inbox</h1>
                <p class="text-muted small mb-4">
                    We sent a verification link to your email address.
                    Click it to activate your account and begin your journey with Taddabur.
                </p>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-brand-success border-0 rounded-3 small py-2 mb-4 text-start" role="status">
                        <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                @error('email')
                    <div class="alert alert-brand-error border-0 rounded-3 small py-2 mb-4 text-start" role="alert">
                        {{ $message }}
                    </div>
                @enderror

                <div class="text-start mb-4 steps-box">
                    <div class="step-row">
                        <span class="step-num">1</span>
                        <span>Open your email inbox</span>
                    </div>
                    <div class="step-row">
                        <span class="step-num">2</span>
                        <span>Find the email from <strong>Taddabur</strong> — check spam if not there</span>
                    </div>
                    <div class="step-row">
                        <span class="step-num">3</span>
                        <span>Click <strong>"Verify Email Address"</strong> to activate your account</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('verification.send') }}" id="resend-form">
                    @csrf
                    <button type="submit" class="btn-gold btn w-100" id="resend-btn" @disabled($resendWait > 0)> <span
                            class="btn-label"><i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i> Resend Verification
                            Email</span>
                        <span class="tasbih-loader" aria-hidden="true">
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span>
                        </span>
                    </button>
                </form>
                <p id="resend-wait" class="text-muted small mt-2 mb-0" role="status" @if ($resendWait <= 0) hidden @endif>
                    You can request another email in <strong id="resend-secs">{{ $resendWait }}</strong> seconds.
                </p>
                <p class="text-muted small mt-2 mb-0">The link expires in {{ config('auth.verification.expire', 60) }}
                    minutes.</p>

                <hr style="border-color:var(--border); margin: 1.25rem 0;">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-signout">
                        <i class="bi bi-box-arrow-left me-1" aria-hidden="true"></i>
                        Sign out and use a different account
                    </button>
                </form>
            </div>

            <div class="text-center mt-4 px-3">
                <p class="ayah-footer fst-italic mb-1">"Allah does not burden a soul beyond that it can bear."</p>
                <p class="ayah-footer-ref">— Al-Baqarah 2:286</p>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resend-form');
            const btn = document.getElementById('resend-btn');
            let submitting = false;

            form.addEventListener('submit', (e) => {
                if (submitting) {
                    e.preventDefault();
                    return;
                }
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                    return;
                }
                submitting = true;
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
                btn.disabled = true;
            });

            window.addEventListener('pageshow', (e) => { if (e.persisted) window.location.reload(); });
            const wait = {{ (int) $resendWait }};
            if (wait > 0) {
                const waitMsg = document.getElementById('resend-wait');
                const secsEl = document.getElementById('resend-secs');
                const end = Date.now() + wait * 1000;
                const timer = setInterval(() => {
                    const left = Math.ceil((end - Date.now()) / 1000);
                    if (left <= 0) { clearInterval(timer); btn.disabled = false; waitMsg.hidden = true; return; }
                    secsEl.textContent = left;
                }, 250);
            }

        });
    </script>
@endpush
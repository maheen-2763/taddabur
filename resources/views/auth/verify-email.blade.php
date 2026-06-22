@extends('layouts.guest')

@section('title', 'Verify Email — Taddabur')

@push('styles')
    <style>
        .auth-section {
            min-height: 100vh;
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
            animation: patternDrift 60s linear infinite;
        }

        .auth-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: var(--font-arabic);
            font-size: clamp(9rem, 16vw, 16rem);
            line-height: 1;
            color: rgba(201, 150, 58, 0.05);
            pointer-events: none;
            z-index: 0;
            user-select: none;
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
            mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
        }

        @keyframes patternDrift {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 120px 60px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .auth-pattern {
                animation: none;
            }
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .auth-bismillah {
            font-family: var(--font-arabic);
            font-size: 1.3rem;
            color: var(--gold-light);
            opacity: 0.85;
        }

        .auth-tagline {
            color: rgba(255, 255, 255, 0.6);
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

        /* Animated envelope icon */
        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(201, 150, 58, 0.1);
            border: 1.5px solid rgba(201, 150, 58, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            .icon-circle {
                animation: none;
            }
        }

        /* Inbox steps */
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

        .btn-emerald-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--muted);
            border-radius: var(--radius);
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-emerald-outline:hover {
            border-color: var(--border);
            color: var(--ink-soft);
        }

        .ayah-footer {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.82rem;
            line-height: 1.7;
        }

        .ayah-footer-ref {
            color: rgba(255, 255, 255, 0.35);
            font-size: 0.75rem;
        }
    </style>
@endpush

@section('content')
    <div class="auth-pattern" aria-hidden="true"></div>
    <div class="auth-watermark" aria-hidden="true">ﷲ</div>

    <section class="auth-section">
        <div class="auth-wrapper">

            <div class="text-center mb-4">
                <div class="auth-bismillah mb-3" lang="ar" dir="rtl">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-1">
                    @include('components.logo', ['variant' => 'stacked', 'height' => 48])
                </a>
                <p class="auth-tagline mt-2 mb-0">Reflect. Understand. Grow.</p>
            </div>

            <div class="auth-card p-4 p-md-5 text-center">

                {{-- Animated envelope --}}
                <div class="icon-circle mx-auto mb-4">
                    <i class="bi bi-envelope-check" style="font-size:1.8rem; color:var(--gold)"></i>
                </div>

                <h4 class="heading-font mb-2" style="color:var(--ink)">Check your inbox</h4>
                <p class="text-muted small mb-4">
                    We sent a verification link to your email address.
                    Click it to activate your account and begin your journey with Taddabur.
                </p>

                {{-- Resend success --}}
                @if (session('status') === 'verification-link-sent')
                    <div class="alert border-0 rounded-3 small py-3 mb-4 text-start"
                        style="background:rgba(27,94,59,0.1); border:1px solid var(--emerald-light)!important; color:var(--emerald);">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                {{-- What to do steps --}}
                <div class="text-start mb-4"
                    style="background:rgba(201,150,58,0.05); border-radius:var(--radius); padding:1rem 1.25rem;">
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

                {{-- Resend --}}
                <form method="POST" action="{{ route('verification.send') }}" id="resend-form">
                    @csrf
                    <button type="submit" class="btn-gold btn w-100 mb-2" id="resend-btn">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend Verification Email
                    </button>
                    <div class="tasbih-loader mt-1" id="tasbih-loader" aria-label="Sending…"
                        style="display:none; justify-content:center; gap:6px; padding:0.5rem 0;">
                        @for ($i = 0; $i < 7; $i++)
                            <div
                                style="width:10px;height:10px;border-radius:50%;background:var(--gold-dark);
                                    opacity:0.25;animation:beadPulse 1.2s ease-in-out infinite;
                                    animation-delay:{{ $i * 0.15 }}s">
                            </div>
                        @endfor
                    </div>
                </form>

                <hr style="border-color:var(--border); margin: 1.25rem 0;">

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-emerald-outline">
                        <i class="bi bi-box-arrow-left me-1"></i>
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
    <style>
        @keyframes beadPulse {

            0%,
            100% {
                opacity: 0.2;
                transform: scale(0.85);
            }

            50% {
                opacity: 1;
                transform: scale(1.15);
                background: var(--gold);
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resend-form');
            const btn = document.getElementById('resend-btn');
            const loader = document.getElementById('tasbih-loader');
            if (form) {
                form.addEventListener('submit', () => {
                    btn.style.display = 'none';
                    loader.style.display = 'flex';
                });
            }
        });
    </script>
@endpush

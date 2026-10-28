@extends('layouts.guest')

@section('title', 'Reset Password — Taddabur')

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

        .pw-hint-btn {
            position: relative;
            width: 20px;
            height: 20px;
            padding: 0;
            border-radius: 50%;
            border: 1.5px solid var(--gold-dark);
            background: transparent;
            color: var(--gold-dark);
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .pw-hint-btn::after {
            content: '';
            position: absolute;
            inset: -10px;
        }

        .pw-hint-btn:hover,
        .pw-hint-btn[aria-expanded="true"] {
            background: var(--gold-dark);
            color: #fff;
        }

        .pw-hint-btn:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
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

            <div class="auth-card p-3 p-sm-4 p-md-5">

                <div class="text-center mb-4 auth-head">
                    <div class="icon-circle mx-auto mb-3">
                        <i class="bi bi-shield-lock fs-4" style="color:var(--gold)" aria-hidden="true"></i>
                    </div>
                    <h1 class="h4 heading-font mb-1" style="color:var(--ink)">Set new password</h1>
                    <p class="text-muted small mb-0">Choose a strong password for your account.</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" id="reset-form" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required
                            maxlength="255" autocomplete="username" autocapitalize="off" spellcheck="false"
                            inputmode="email">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            <a href="{{ route('password.request') }}" class="link-gold small d-inline-block mt-1">Request a new
                                reset link</a>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <label for="password" class="form-label mb-0">New Password</label>
                            <button type="button" class="pw-hint-btn" id="pw-hint-btn" aria-label="Password requirements"
                                aria-expanded="false" aria-controls="password-help">!</button>
                        </div>
                        <div class="input-group">
                            <input id="password" type="password" name="password" minlength="8"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Create a new password" required autofocus autocomplete="new-password"
                                aria-describedby="password-help">
                            <button class="btn btn-toggle-pass" type="button" data-toggle-password="password"
                                aria-label="Show password" aria-controls="password" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-help" class="form-text small" hidden>At least 8 characters, with letters and
                            numbers.</div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat new password" required minlength="8"
                                autocomplete="new-password">
                            <button class="btn btn-toggle-pass" type="button" data-toggle-password="password_confirmation"
                                aria-label="Show password" aria-controls="password_confirmation" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-gold btn w-100" id="reset-btn">
                        <span class="btn-label"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Reset Password</span>
                        <span class="tasbih-loader" aria-hidden="true">
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span>
                        </span>
                    </button>
                </form>
            </div>

            <div class="text-center mt-4 px-3">
                <p class="ayah-footer fst-italic mb-1">"Whoever relies upon Allah — then He is sufficient for him."</p>
                <p class="ayah-footer-ref">— At-Talaq 65:3</p>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('reset-form');
            const btn = document.getElementById('reset-btn');
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
                const p = document.getElementById('password').value;
                const c = document.getElementById('password_confirmation');
                if (p !== c.value) {
                    e.preventDefault();
                    c.setCustomValidity('Passwords do not match.');
                    c.reportValidity();
                    c.addEventListener('input', () => c.setCustomValidity(''), { once: true });
                    return;
                }
                submitting = true;
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
                btn.disabled = true;
            });

            window.addEventListener('pageshow', (e) => {
                if (e.persisted) {
                    submitting = false;
                    btn.disabled = false;
                    btn.classList.remove('is-loading');
                    btn.removeAttribute('aria-busy');
                }
            });

            document.querySelectorAll('[data-toggle-password]').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const input = document.getElementById(toggle.dataset.togglePassword);
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    toggle.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
                    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                    toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                });
            });

            const hintBtn = document.getElementById('pw-hint-btn');
            const hint = document.getElementById('password-help');
            hintBtn.addEventListener('click', () => {
                hint.hidden = !hint.hidden;
                hintBtn.setAttribute('aria-expanded', hint.hidden ? 'false' : 'true');
            });
        });
    </script>
@endpush
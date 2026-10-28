@extends('layouts.guest')

@section('title', 'Create Account — Taddabur')

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
            color: rgba(201, 150, 58, 0.16);
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

        /* Form */
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

        .auth-card .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .auth-card .form-check-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 150, 58, 0.15);
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

        .alert-brand-error {
            background: rgba(220, 53, 69, 0.08);
            color: #842029;
        }

        /* Honeypot — screen par nahi dikhta */
        .hp-field {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        /* Submit button + tasbih loader (button ke andar) */
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
            color: var(--gold-light);
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

        /* Verse — Ta-Ha 20:114 */
        .register-verse {
            background: linear-gradient(135deg, rgba(201, 150, 58, 0.08) 0%, rgba(27, 94, 59, 0.06) 100%);
            border-right: 3px solid var(--gold);
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 0.75rem 1.1rem;
            margin-bottom: 1.1rem;
        }

        .register-verse-ar {
            font-family: var(--font-arabic);
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--ink);
            text-align: right;
            direction: rtl;
            margin-bottom: 0.1rem;
        }

        .register-verse-en {
            font-size: 0.8rem;
            color: var(--muted);
            font-style: italic;
            text-align: right;
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

            .auth-card>p.text-muted {
                margin-bottom: 1rem !important;
            }

            .auth-card form .mb-3 {
                margin-bottom: 0.85rem !important;
            }

            .register-verse {
                padding: 0.6rem 0.9rem;
                margin-bottom: 0.9rem;
            }

            .register-verse-ar {
                font-size: 1.05rem;
                line-height: 1.6;
            }
        }

        /* Mobile */
        .pw-hint-btn {
            position: relative;
            width: 17px;
            height: 17px;
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
            /* mobile par tap karna aasan */
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
    </style>
@endpush

@section('content')

    <div class="auth-pattern" aria-hidden="true"></div>
    <div class="auth-watermark" aria-hidden="true">اقْرَأْ</div>

    <section class="auth-section">
        <div class="auth-wrapper">

            {{-- Brand header --}}
            <div class="text-center mb-4 auth-brand">
                <div class="auth-bismillah mb-3" lang="ar" dir="rtl">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>

                <a href="{{ url('/') }}" class="text-decoration-none d-inline-block mb-1" aria-label="Taddabur home">
                    @include('components.logo', ['variant' => 'stacked', 'height' => 40])
                </a>

            </div>

            {{-- Card --}}
            <div class="auth-card p-3 p-sm-4 p-md-5">

                <h1 class="h4 heading-font mb-1" style="color:var(--ink)">Create your account</h1>
                <p class="text-muted small mb-4">Free forever. Start reflecting on the Quran today.</p>

                <div class="register-verse">
                    <div class="register-verse-ar" lang="ar" dir="rtl">وَقُل رَّبِّ زِدْنِى عِلْمًا</div>
                    <div class="register-verse-en">"And say: My Lord, increase me in knowledge." — Ta-Ha 20:114</div>
                </div>

                @error('hp_trap')
                    <div class="alert alert-brand-error border-0 rounded-3 small py-2 mb-3" role="alert">
                        Something went wrong. Please reload the page and try again.
                    </div>
                @enderror

                <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
                    @csrf

                    {{-- Honeypot --}}
                    <div class="hp-field" aria-hidden="true">
                        <label for="hp_trap">Leave this field empty</label>
                        <input id="hp_trap" type="text" name="hp_trap" value="" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Mohammed Ahmed"
                            required maxlength="255" autocomplete="name" autocapitalize="words">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required
                            maxlength="255" autocomplete="username" autocapitalize="off" spellcheck="false"
                            inputmode="email">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            <button type="button" class="pw-hint-btn" id="pw-hint-btn" aria-label="Password requirements"
                                aria-expanded="false" aria-controls="password-help">!</button>
                        </div>
                        <div class="input-group">
                            <input id="password" type="password" name="password" minlength="8"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Create a password"
                                required autocomplete="new-password" aria-describedby="password-help">
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

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat your password" required minlength="8"
                                autocomplete="new-password">
                            <button class="btn btn-toggle-pass" type="button" data-toggle-password="password_confirmation"
                                aria-label="Show password" aria-controls="password_confirmation" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input id="age_confirmed" type="checkbox" name="age_confirmed" value="1"
                                class="form-check-input @error('age_confirmed') is-invalid @enderror" required
                                @checked(old('age_confirmed'))>
                            <label for="age_confirmed" class="form-check-label small text-muted">
                                I am 18 years or older and agree to the
                                <a href="{{ route('terms') }}" class="link-gold" target="_blank" rel="noopener">Terms</a>
                                and
                                <a href="{{ route('privacy') }}" class="link-gold" target="_blank" rel="noopener">Privacy
                                    Policy</a>.
                            </label>
                        </div>
                        @error('age_confirmed')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-gold btn w-100" id="register-btn">
                        <span class="btn-label">Create Account <i class="bi bi-arrow-right ms-1"
                                aria-hidden="true"></i></span>
                        <span class="tasbih-loader" aria-hidden="true">
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span>
                        </span>
                    </button>

                    <div class="text-center small text-muted mt-3">
                        Already have an account?
                        <a href="{{ route('login') }}" class="link-gold">Sign in</a>
                    </div>
                </form>
            </div>


        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('register-form');
            const btn = document.getElementById('register-btn');
            let submitting = false;

            const resetButton = () => {
                submitting = false;
                btn.disabled = false;
                btn.classList.remove('is-loading');
                btn.removeAttribute('aria-busy');
            };

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
                const c = document.getElementById('password_confirmation').value;
                if (p !== c) {
                    e.preventDefault();
                    const conf = document.getElementById('password_confirmation');
                    conf.setCustomValidity('Passwords do not match.');
                    conf.reportValidity();
                    conf.addEventListener('input', () => conf.setCustomValidity(''), { once: true });
                    return;
                }
                submitting = true;
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
                btn.disabled = true;
            });

            // Back button se aane par button wapas theek ho
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) resetButton();
            });

            // Password show/hide
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

            // Password hint (! button)
            const hintBtn = document.getElementById('pw-hint-btn');
            const hint = document.getElementById('password-help');
            hintBtn.addEventListener('click', () => {
                hint.hidden = !hint.hidden;
                hintBtn.setAttribute('aria-expanded', hint.hidden ? 'false' : 'true');
            });

            // Server error ke baad pehli galat field par focus
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        });
    </script>
@endpush
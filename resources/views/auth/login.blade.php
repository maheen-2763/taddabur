@extends('layouts.guest')

@section('title', 'Sign In — Taddabur')

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

        .auth-card .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .auth-card .form-check-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 150, 58, 0.15);
        }

        .link-gold {
            color: var(--gold-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .link-gold:hover {
            color: var(--gold);
        }

        .alert-brand-success {
            background: rgba(27, 94, 59, 0.1);
            border: 1px solid var(--emerald-light) !important;
            color: var(--emerald);
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

        .bead:nth-child(1) { animation-delay: 0s; }
        .bead:nth-child(2) { animation-delay: 0.15s; }
        .bead:nth-child(3) { animation-delay: 0.30s; }
        .bead:nth-child(4) { animation-delay: 0.45s; }
        .bead:nth-child(5) { animation-delay: 0.60s; }
        .bead:nth-child(6) { animation-delay: 0.75s; }
        .bead:nth-child(7) { animation-delay: 0.90s; }

        @keyframes beadPulse {
            0%, 100% { opacity: 0.2; transform: scale(0.85); }
            50% { opacity: 1; transform: scale(1.15); }
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

                <p class="auth-tagline mt-2 mb-1">Understand. Reflect. Grow.</p>
            </div>

            {{-- Card --}}
            <div class="auth-card p-3 p-sm-4 p-md-5">

                <h1 class="h4 heading-font mb-1" style="color:var(--ink)">Welcome back</h1>
                <p class="text-muted small mb-4">Sign in to continue your journey.</p>

                @if (session('status'))
                    <div class="alert alert-brand-success border-0 rounded-3 small py-2 mb-3" role="status">
                        <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com"
                            required maxlength="255" autocomplete="username" autocapitalize="off" spellcheck="false"
                            inputmode="email" @if (!$errors->has('password') || $errors->has('email')) autofocus @endif>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($lockSeconds > 0)
    <div id="lock-msg" class="invalid-feedback d-block" role="status">
        Try again in <strong id="lock-secs">{{ $lockSeconds }}</strong> seconds.
    </div>
@endif
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="link-gold small">Forgot password?</a>
                            @endif
                        </div>
                        <div class="input-group">
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Enter your password" required autocomplete="current-password"
                                @if ($errors->has('password') && !$errors->has('email')) autofocus @endif>
                            <button class="btn btn-toggle-pass" type="button" data-toggle-password="password"
                                aria-label="Show password" aria-controls="password" aria-pressed="false">
                                <i class="bi bi-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                            <label for="remember_me" class="form-check-label small text-muted">Keep me signed in</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-gold btn w-100" id="login-btn">
                        <span class="btn-label">Sign In <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></span>
                        <span class="tasbih-loader" aria-hidden="true">
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span><span class="bead"></span><span class="bead"></span>
                            <span class="bead"></span>
                        </span>
                    </button>

                    <div class="text-center small text-muted mt-3">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="link-gold">Create one</a>
                    </div>
                </form>
            </div>

            {{-- Verse of the Day --}}
            <div class="text-center mt-4 px-3">
                <p class="ayah-footer fst-italic mb-1" id="votd-text"></p>
                <p class="ayah-footer-ref" id="votd-ref"></p>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* Verse of the Day (din bhar same verse) */
            const verses = [
                { text: '"Indeed, in the remembrance of Allah do hearts find rest."', ref: '— Ar-Ra\'d 13:28' },
                { text: '"So remember Me; I will remember you."', ref: '— Al-Baqarah 2:152' },
                { text: '"And He is with you wherever you are."', ref: '— Al-Hadid 57:4' },
                { text: '"Verily, with hardship comes ease."', ref: '— Ash-Sharh 94:6' },
                { text: '"Allah does not burden a soul beyond that it can bear."', ref: '— Al-Baqarah 2:286' },
                { text: '"And seek help through patience and prayer."', ref: '— Al-Baqarah 2:45' },
                { text: '"He knows what is within the heavens and earth."', ref: '— Al-Ma\'idah 5:97' },
                { text: '"And your Lord is going to give you, and you will be satisfied."', ref: '— Ad-Duha 93:5' },
                { text: '"Whoever relies upon Allah — then He is sufficient for him."', ref: '— At-Talaq 65:3' },
                { text: '"Read! In the name of your Lord who created."', ref: '— Al-\'Alaq 96:1' },
                { text: '"Do they not reflect upon the Quran?"', ref: '— An-Nisa 4:82' },
                { text: '"This is the Book about which there is no doubt, a guidance for those conscious of Allah."', ref: '— Al-Baqarah 2:2' },
                { text: '"And We have certainly made the Quran easy to remember."', ref: '— Al-Qamar 54:17' },
                { text: '"The most beloved of deeds to Allah are those done consistently, even if small."', ref: '— Hadith, Bukhari' },
            ];

            const v = verses[Math.floor(Date.now() / 86400000) % verses.length];
            document.getElementById('votd-text').textContent = v.text;
            document.getElementById('votd-ref').textContent = v.ref;

            /* Submit: double-click rok do + loader */
            const form = document.getElementById('login-form');
            const btn = document.getElementById('login-btn');
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

const lockSecs = {{ (int) $lockSeconds }};
const lockEmail = @json($lockEmail);
if (lockSecs > 0) {
    const lockMsg = document.getElementById('lock-msg');
    const secsEl = document.getElementById('lock-secs');
    const emailInput = document.getElementById('email');
    const end = Date.now() + lockSecs * 1000;
    let timer;
    const unlock = () => { clearInterval(timer); btn.disabled = false; lockMsg.remove(); };
    btn.disabled = true;
    timer = setInterval(() => {
        const left = Math.ceil((end - Date.now()) / 1000);
        if (left <= 0) { unlock(); return; }
        secsEl.textContent = left;
    }, 250);
    emailInput.addEventListener('input', () => {
        if (emailInput.value.trim().toLowerCase() !== lockEmail) unlock();
    });
}

            /* Password show/hide */
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
        });
    </script>
@endpush
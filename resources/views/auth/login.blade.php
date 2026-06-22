@extends('layouts.guest')

@section('title', 'Sign In — Taddabur')

@push('styles')
    <style>
        /* ================================================
               AUTH BACKGROUND
            ================================================ */
        .auth-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            position: relative;
            overflow: hidden;
        }

        /* Geometric pattern — drifts slowly */
        .auth-pattern {
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z' fill='none' stroke='rgba(201,150,58,0.07)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
            animation: patternDrift 60s linear infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .auth-pattern {
                animation: none;
            }
        }

        @keyframes patternDrift {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 120px 60px;
            }
        }

        /* Allah watermark */
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

        /* ================================================
               WRAPPER
            ================================================ */
        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        /* ================================================
               BRAND HEADER
            ================================================ */
        .auth-bismillah {
            font-family: var(--font-arabic);
            font-size: 1.4rem;
            color: var(--gold-light);
            opacity: 0.85;
        }

        /* Custom crescent+lantern SVG mark */
        .auth-logo-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .auth-logo-mark svg {
            flex-shrink: 0;
        }

        .auth-brand-text {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            color: var(--gold-light);
            letter-spacing: 0.04em;
        }

        .auth-tagline {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            letter-spacing: 0.04em;
        }

        /* Hijri date pill */
        .hijri-date {
            display: inline-block;
            font-family: var(--font-arabic);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.55);
            margin-top: 6px;
            letter-spacing: 0.02em;
            direction: rtl;
        }

        /* ================================================
               CARD
            ================================================ */
        .auth-card {
            position: relative;
            background: var(--cream);
            border: 1px solid rgba(201, 150, 58, 0.25);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }

        /* Manuscript corner brackets */
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

        /* ================================================
               FORM ELEMENTS
            ================================================ */
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

        .auth-card .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .auth-card .form-check-input:focus {
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

        /* ================================================
               TASBIH LOADING STATE
            ================================================ */
        .tasbih-loader {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0.6rem 0;
        }

        .tasbih-loader.active {
            display: flex;
        }

        .bead {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--gold-dark);
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
                background: var(--gold);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .bead {
                animation: none;
                opacity: 0.6;
            }
        }

        /* ================================================
               FOOTER VERSE
            ================================================ */
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

    {{-- Ambient layers --}}
    <div class="auth-pattern" aria-hidden="true"></div>
    <div class="auth-watermark" aria-hidden="true">ﷲ</div>

    <section class="auth-section">
        <div class="auth-wrapper">

            {{-- ── Brand header ── --}}
            <div class="text-center mb-4">

                <div class="auth-bismillah mb-2" lang="ar" dir="rtl">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>

                {{-- Custom crescent + lantern mark --}}
                <a href="{{ url('/') }}" class="auth-logo-mark justify-content-center mb-1">
                    <svg width="38" height="38" viewBox="0 0 38 38" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        {{-- Crescent --}}
                        <path
                            d="M22 6C16.477 6 12 10.477 12 16C12 21.523 16.477 26 22 26C23.91 26 25.694 25.457 27.2 24.52C25.434 25.468 23.41 26 21.25 26C14.484 26 9 20.516 9 13.75C9 9.178 11.454 5.176 15.13 3C13.213 3.946 11.535 5.327 10.222 7C8.826 8.78 8 11.04 8 13.5C8 19.85 13.15 25 19.5 25C22.18 25 24.637 24.05 26.55 22.46C24.694 24.4 22.49 26 19.5 26C13.149 26 8 20.851 8 14.5C8 8.149 13.149 3 19.5 3C20.35 3 21.187 3.09 22 3.26V6Z"
                            fill="none" />
                        <path
                            d="M23 5C17.477 5 13 9.477 13 15C13 20.523 17.477 25 23 25C24.476 25 25.876 24.68 27.13 24.1C25.1 25.289 22.72 26 20.16 26C12.96 26 7.16 20.2 7.16 13C7.16 5.8 12.96 0 20.16 0C21.84 0 23.45 0.31 24.93 0.87C23.74 2.13 23 3.98 23 6V5Z"
                            fill="#E8BE6D" opacity="0.9" />
                        {{-- Lantern body --}}
                        <rect x="16" y="18" width="6" height="10" rx="1.5" fill="#C9963A" opacity="0.85" />
                        {{-- Lantern top --}}
                        <path d="M15 18 L19 14 L23 18 Z" fill="#E8BE6D" opacity="0.9" />
                        {{-- Lantern bottom --}}
                        <rect x="15.5" y="28" width="7" height="2" rx="1" fill="#C9963A" opacity="0.7" />
                        {{-- Lantern handle --}}
                        <line x1="19" y1="14" x2="19" y2="12" stroke="#E8BE6D"
                            stroke-width="1.2" opacity="0.7" />
                        {{-- Lantern glow --}}
                        <rect x="17.5" y="20" width="3" height="5" rx="1" fill="#FAF6EE" opacity="0.5" />
                        {{-- Star --}}
                        <path d="M30 4 L30.9 6.9 L34 7 L31.7 9 L32.5 12 L30 10.4 L27.5 12 L28.3 9 L26 7 L29.1 6.9 Z"
                            fill="#E8BE6D" opacity="0.75" />
                    </svg>
                    <span class="auth-brand-text">Taddabur</span>
                </a>

                <p class="auth-tagline mt-1 mb-1">Reflect. Understand. Grow.</p>

                {{-- Hijri date — rendered by JS --}}
                <div class="hijri-date" id="hijri-date" lang="ar" dir="rtl" aria-label="Today's Hijri date"></div>

            </div>

            {{-- ── Card ── --}}
            <div class="auth-card p-4 p-md-5">

                <h4 class="heading-font mb-1" style="color:var(--ink)">Welcome back</h4>
                <p class="text-muted small mb-4">Sign in to continue your journey.</p>

                @if (session('status'))
                    <div class="alert alert-islamic-success small py-2 mb-3">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required
                            autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                                placeholder="Enter your password" required autocomplete="current-password">
                            <button class="btn btn-toggle-pass" type="button" onclick="togglePassword('password', this)"
                                tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                            <label for="remember_me" class="form-check-label small text-muted">Keep me signed in</label>
                        </div>
                    </div>

                    {{-- Submit button  --}}
                    <button type="submit" class="btn-gold btn w-100 mb-1" id="login-btn">
                        Sign In <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    {{-- Tasbih loader — hidden until submit --}}
                    <div class="tasbih-loader" id="tasbih-loader" aria-label="Signing in…">
                        <div class="bead"></div>
                        <div class="bead"></div>
                        <div class="bead"></div>
                        <div class="bead"></div>
                        <div class="bead"></div>
                        <div class="bead"></div>
                        <div class="bead"></div>
                    </div>

                    <div class="text-center small text-muted mt-3">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="link-gold">Create one</a>
                    </div>
                </form>
            </div>

            {{-- ── Verse of the Day ── --}}
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

            /* ── 1. Hijri date ── */
            try {
                const hijri = new Intl.DateTimeFormat('ar-SA-u-ca-islamic', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }).format(new Date());
                document.getElementById('hijri-date').textContent = hijri;
            } catch (e) {}

            /* ── 2. Verse of the Day (deterministic — same verse all day) ── */
            const verses = [{
                    text: '"Indeed, in the remembrance of Allah do hearts find rest."',
                    ref: '— Ar-Ra\'d 13:28'
                },
                {
                    text: '"So remember Me; I will remember you."',
                    ref: '— Al-Baqarah 2:152'
                },
                {
                    text: '"And He is with you wherever you are."',
                    ref: '— Al-Hadid 57:4'
                },
                {
                    text: '"Verily, with hardship comes ease."',
                    ref: '— Ash-Sharh 94:6'
                },
                {
                    text: '"Allah does not burden a soul beyond that it can bear."',
                    ref: '— Al-Baqarah 2:286'
                },
                {
                    text: '"And seek help through patience and prayer."',
                    ref: '— Al-Baqarah 2:45'
                },
                {
                    text: '"He knows what is within the heavens and earth."',
                    ref: '— Al-Ma\'idah 5:97'
                },
                {
                    text: '"And your Lord is going to give you, and you will be satisfied."',
                    ref: '— Ad-Duha 93:5'
                },
                {
                    text: '"Whoever relies upon Allah — then He is sufficient for him."',
                    ref: '— At-Talaq 65:3'
                },
                {
                    text: '"Read! In the name of your Lord who created."',
                    ref: '— Al-\'Alaq 96:1'
                },
                {
                    text: '"Do they not reflect upon the Quran?"',
                    ref: '— An-Nisa 4:82'
                },
                {
                    text: '"This is the Book about which there is no doubt, a guidance for those conscious of Allah."',
                    ref: '— Al-Baqarah 2:2'
                },
                {
                    text: '"And We have certainly made the Quran easy to remember."',
                    ref: '— Al-Qamar 54:17'
                },
                {
                    text: '"The most beloved of deeds to Allah are those done consistently, even if small."',
                    ref: '— Hadith, Bukhari'
                },
            ];

            const dayIndex = Math.floor(Date.now() / 86400000) % verses.length;
            const v = verses[dayIndex];
            document.getElementById('votd-text').textContent = v.text;
            document.getElementById('votd-ref').textContent = v.ref;

            /* ── 3. Tasbih loading state on submit ── */
            const form = document.getElementById('login-form');
            const btn = document.getElementById('login-btn');
            const loader = document.getElementById('tasbih-loader');

            if (form) {
                form.addEventListener('submit', () => {
                    btn.style.display = 'none';
                    loader.classList.add('active');
                });
            }

            /* ── 4. Password toggle ── */
            window.togglePassword = (fieldId, el) => {
                const input = document.getElementById(fieldId);
                const icon = el.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'bi bi-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'bi bi-eye';
                }
            };

        });
    </script>
@endpush

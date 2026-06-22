@extends('layouts.guest')

@section('title', 'Reset Password — Taddabur')

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

        .auth-card .form-control {
            border-radius: var(--radius);
            border-color: var(--border);
            background: #fff;
            color: var(--ink);
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
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

            <div class="auth-card p-4 p-md-5">

                <div class="text-center mb-4">
                    <div class="icon-circle mx-auto mb-3">
                        <i class="bi bi-shield-lock fs-4" style="color:var(--gold)"></i>
                    </div>
                    <h4 class="heading-font mb-1" style="color:var(--ink)">Set new password</h4>
                    <p class="text-muted small mb-0">Choose a strong password for your account.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" id="reset-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required
                            autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters"
                                required autocomplete="new-password">
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
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat new password" required autocomplete="new-password">
                            <button class="btn btn-toggle-pass" type="button"
                                onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-gold btn w-100 mb-1" id="reset-btn">
                        <i class="bi bi-check2 me-1"></i> Reset Password
                    </button>

                    <div class="tasbih-loader mt-2" id="tasbih-loader" aria-label="Resetting…"
                        style="display:none; justify-content:center; gap:6px; padding:0.6rem 0;">
                        @for ($i = 0; $i < 7; $i++)
                            <div
                                style="width:10px;height:10px;border-radius:50%;background:var(--gold-dark);
                                    opacity:0.25;animation:beadPulse 1.2s ease-in-out infinite;
                                    animation-delay:{{ $i * 0.15 }}s">
                            </div>
                        @endfor
                    </div>
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
            const form = document.getElementById('reset-form');
            const btn = document.getElementById('reset-btn');
            const loader = document.getElementById('tasbih-loader');
            if (form) {
                form.addEventListener('submit', () => {
                    btn.style.display = 'none';
                    loader.style.display = 'flex';
                });
            }
            window.togglePassword = (id, el) => {
                const input = document.getElementById(id);
                const icon = el.querySelector('i');
                input.type = input.type === 'password' ? 'text' : 'password';
                icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            };
        });
    </script>
@endpush

@extends('layouts.guest')

@section('title', 'Create Account — Taddabur')

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

        .auth-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z' fill='none' stroke='rgba(201,150,58,0.07)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
        }

        .auth-section::after {
            content: 'ﷲ';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: var(--font-arabic);
            font-size: clamp(9rem, 16vw, 16rem);
            line-height: 1;
            color: rgba(201, 150, 58, 0.05);
            pointer-events: none;
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
            mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .auth-brand {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            color: var(--gold-light);
            letter-spacing: 0.03em;
        }

        .auth-bismillah {
            font-family: var(--font-arabic);
            font-size: 1.4rem;
            color: var(--gold-light);
            opacity: 0.85;
        }

        .auth-tagline {
            color: rgba(255, 255, 255, 0.65);
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

        .link-gold {
            color: var(--gold-dark);
            font-weight: 600;
            text-decoration: none;
        }

        .link-gold:hover {
            color: var(--gold);
        }

        .auth-footer-note {
            color: rgba(255, 255, 255, 0.55);
        }

        .auth-footer-note a {
            color: var(--gold-light);
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <section class="auth-section">
        <div class="auth-wrapper">

            <div class="text-center mb-4">
                <div class="auth-bismillah mb-1" lang="ar" dir="rtl">بِسْمِ اللَّهِ</div>
                <a href="{{ route('register') }}" class="text-decoration-none">
                    <div class="auth-brand">🌙 Taddabur</div>
                </a>
                <p class="auth-tagline small mt-1">Begin your journey of reflection</p>
            </div>

            <div class="auth-card p-4 p-md-5">

                <h4 class="heading-font mb-1" style="color: var(--ink);">Create your account</h4>
                <p class="text-muted small mb-4">Join thousands reflecting on the Quran daily.</p>

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Mohammed Ahmed"
                            required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" required
                            autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
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

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat your password" required
                                autocomplete="new-password">
                            <button class="btn btn-toggle-pass" type="button"
                                onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-gold btn w-100 mb-3">
                        Create Account <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    {{-- Divider --}}
                    <div class="text-center small text-muted mt-3">
                        Already have an account?
                        <a href="{{ route('login') }}" class="link-gold">
                            Sign in
                        </a>
                    </div>

                </form>
            </div>

            {{-- Footer note --}}
            <p class="text-center auth-footer-note small mt-4">
                By registering, you agree to our
                <a href="#">Terms</a> &amp;
                <a href="#">Privacy Policy</a>.
            </p>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
@endpush

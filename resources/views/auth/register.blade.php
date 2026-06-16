<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Taddabur</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Amiri&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8faf8;
            min-height: 100vh;
        }

        .auth-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        .brand-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: #166534;
            letter-spacing: -0.02em;
        }

        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 1.4rem;
            color: #166534;
            opacity: 0.75;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.65rem 1rem;
            border-color: #dee2e6;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
        }

        .btn-register {
            background-color: #16a34a;
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s ease;
        }

        .btn-register:hover {
            background-color: #15803d;
        }

        .divider-text {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #374151;
            margin-bottom: 0.35rem;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center py-5">

    <div class="w-100" style="max-width: 460px; padding: 0 1rem;">

        {{-- Brand --}}
        <div class="text-center mb-4">
            <div class="bismillah mb-1">بِسْمِ اللَّهِ</div>
            <a href="{{ route('register') }}" class="text-decoration-none">
                <div class="brand-name">🌙 Taddabur</div>
            </a>
            <p class="text-muted small mt-1">Begin your journey of reflection</p>
        </div>

        {{-- Card --}}
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">

                <h4 class="fw-bold mb-1">Create your account</h4>
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
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com"
                            required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Min. 8 characters" required autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('password', this)" tabindex="-1">
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
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-register btn-success text-white w-100 mb-3">
                        Create Account <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    {{-- Divider --}}
                    <div class="text-center divider-text my-3">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-success fw-semibold text-decoration-none">
                            Sign in
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Footer note --}}
        <p class="text-center text-muted small mt-4">
            By registering, you agree to our
            <a href="#" class="text-success text-decoration-none">Terms</a> &amp;
            <a href="#" class="text-success text-decoration-none">Privacy Policy</a>.
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>

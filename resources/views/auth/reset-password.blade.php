<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Taddabur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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

        .btn-submit {
            background-color: #16a34a;
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #15803d;
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
            <a href="{{ route('home') }}" class="text-decoration-none">
                <div class="brand-name">🌙 Taddabur</div>
            </a>
            <p class="text-muted small mt-1">Reflect. Understand. Grow.</p>
        </div>

        {{-- Card --}}
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">

                {{-- Icon --}}
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center
                                bg-success bg-opacity-10 rounded-circle mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock text-success fs-4"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Set new password</h4>
                    <p class="text-muted small">Choose a strong password for your account.</p>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com"
                            required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
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
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat new password" required
                                autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit btn-success text-white w-100">
                        <i class="bi bi-check2 me-1"></i> Reset Password
                    </button>

                </form>
            </div>
        </div>

        <p class="text-center text-muted small mt-4 fst-italic px-3">
            "And whoever relies upon Allah — then He is sufficient for him." — Quran 65:3
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

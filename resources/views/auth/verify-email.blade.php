<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — Taddabur</title>

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

        .btn-submit {
            background-color: #16a34a;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: #15803d;
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background-color: rgba(22, 163, 74, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            <div class="card-body p-4 p-md-5 text-center">

                {{-- Icon --}}
                <div class="icon-circle mx-auto mb-4">
                    <i class="bi bi-envelope-check text-success" style="font-size: 1.8rem;"></i>
                </div>

                <h4 class="fw-bold mb-2">Check your inbox</h4>
                <p class="text-muted small mb-4">
                    We sent a verification link to your email address.
                    Please click it to activate your account and begin your journey with Taddabur.
                </p>

                {{-- Resend success --}}
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success border-0 rounded-3 small py-3 mb-4 text-start">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                {{-- Resend form --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-submit btn-success text-white w-100 mb-3">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend Verification Email
                    </button>
                </form>

                <hr class="my-3">

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted text-decoration-none small">
                        <i class="bi bi-box-arrow-left me-1"></i>
                        Sign out and use a different account
                    </button>
                </form>

            </div>
        </div>

        <p class="text-center text-muted small mt-4 fst-italic px-3">
            "Allah does not burden a soul beyond that it can bear." — Quran 2:286
        </p>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

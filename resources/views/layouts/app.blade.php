<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Taddabur — Quran & Prophet Stories')</title>
    <meta name="description" content="@yield('description', 'Read the Quran with tafsir and explore prophet stories in depth.')">

    {{-- Google Fonts: Amiri for Arabic, Lora for body, Cinzel for headings --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">


    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ================================================
           CSS VARIABLES — Islamic colour palette
        ================================================ */
        :root {
            --gold: #C9963A;
            --gold-light: #E8BE6D;
            --gold-dark: #9A6F2B;
            --emerald: #1B5E3B;
            --emerald-light: #2D8A59;
            --emerald-dark: #0D3D22;
            --cream: #FAF6EE;
            --cream-dark: #F0E8D8;
            --ink: #1A1A2E;
            --ink-soft: #3D3D5C;
            --muted: #6B7280;
            --border: #E5DDD0;
            --radius: 12px;
            --radius-lg: 20px;

            /* Font stacks */
            --font-heading: 'Cinzel', Georgia, serif;
            --font-body: 'Lora', Georgia, serif;
            --font-arabic: 'Amiri', 'Traditional Arabic', serif;
        }

        [data-bs-theme="dark"] {
            --cream: #0F1923;
            --cream-dark: #162030;
            --ink: #F0EAE0;
            --ink-soft: #C8C0B8;
            --border: #2A3A4A;
            --muted: #8899AA;
        }

        /* ================================================
           BASE STYLES
        ================================================ */
        body {
            font-family: var(--font-body);
            background-color: var(--cream);
            color: var(--ink);
            line-height: 1.75;
        }

        h1,
        h2,
        h3,
        h4,
        .heading-font {
            font-family: var(--font-heading);
            letter-spacing: 0.03em;
        }

        /* Arabic text — always right-to-left */
        .arabic {
            font-family: var(--font-arabic);
            font-size: 1.6rem;
            line-height: 2.4;
            direction: rtl;
            text-align: right;
            color: var(--ink);
        }

        .arabic-sm {
            font-family: var(--font-arabic);
            font-size: 1.2rem;
            line-height: 2.2;
            direction: rtl;
            text-align: right;
        }

        /* ================================================
           NAVBAR
        ================================================ */
        .navbar-islamic {
            background: var(--emerald-dark);
            border-bottom: 2px solid var(--gold-dark);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand-text {
            font-family: var(--font-heading);
            font-size: 1.3rem;
            color: var(--gold-light) !important;
            letter-spacing: 0.05em;
        }

        .navbar-brand-text span {
            color: #fff;
        }

        .nav-link-islamic {
            color: rgba(255, 255, 255, 0.85) !important;
            font-family: var(--font-body);
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .nav-link-islamic:hover,
        .nav-link-islamic.active {
            color: var(--gold-light) !important;
            background: rgba(255, 255, 255, 0.08);
        }

        /* ================================================
           PLAN BADGE
        ================================================ */
        .badge-free {
            background: #6B7280;
        }

        .badge-basic {
            background: var(--emerald-light);
        }

        .badge-premium {
            background: var(--gold);
            color: #1A1A2E;
        }

        /* ================================================
           CARDS
        ================================================ */
        .card-islamic {
            background: var(--cream-dark);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-islamic:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        /* ================================================
           GOLD DIVIDER
        ================================================ */
        .divider-gold {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 2rem 0;
        }

        /* ================================================
           BUTTONS
        ================================================ */
        .btn-gold {
            background: var(--gold);
            color: #1A1A2E;
            border: none;
            font-family: var(--font-heading);
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            padding: 0.6rem 1.5rem;
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .btn-gold:hover {
            background: var(--gold-light);
            color: #1A1A2E;
            transform: translateY(-1px);
        }

        .btn-emerald {
            background: var(--emerald);
            color: #fff;
            border: none;
            font-family: var(--font-heading);
            font-size: 0.85rem;
            padding: 0.6rem 1.5rem;
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .btn-emerald:hover {
            background: var(--emerald-light);
            color: #fff;
        }

        /* ================================================
           UPGRADE BANNER
        ================================================ */
        .upgrade-banner {
            background: linear-gradient(135deg, var(--emerald-dark), var(--emerald));
            border-left: 4px solid var(--gold);
            border-radius: var(--radius);
            padding: 1rem 1.5rem;
            color: #fff;
        }

        /* ================================================
           FOOTER
        ================================================ */
        .footer-islamic {
            background: var(--emerald-dark);
            border-top: 2px solid var(--gold-dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 2.5rem 0;
            font-size: 0.9rem;
        }

        .footer-islamic a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-islamic a:hover {
            color: var(--gold-light);
        }

        /* ================================================
           FLASH MESSAGES
        ================================================ */
        .alert-islamic-success {
            background: rgba(27, 94, 59, 0.1);
            border: 1px solid var(--emerald-light);
            color: var(--emerald);
            border-radius: var(--radius);
        }

        /* ================================================
           DARK MODE TOGGLE
        ================================================ */
        .theme-toggle {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            border-color: var(--gold);
            color: var(--gold-light);
        }

        /* ================================================
           SCROLLBAR (subtle)
        ================================================ */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--cream);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        html {
            scroll-behavior: smooth;
        }

        .feature-card {
            padding: 1.5rem;
            text-align: center;
            min-height: 280px;
        }

        .feature-card h5 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .lifetime-card {
            background: linear-gradient(135deg,
                    rgba(201, 150, 58, 0.08),
                    rgba(27, 94, 59, 0.08));
            border: 2px solid var(--gold);
            border-radius: var(--radius-lg);
        }

        .arabic-name {
            font-family: 'Amiri', serif;
            font-size: clamp(2rem, 5vw, 3rem);
            direction: rtl;
            color: var(--emerald);
            line-height: 1.6;
        }

        .arabic-title {
            font-family: 'Amiri', serif;
            font-size: 1.1rem;
            direction: rtl;
            color: var(--gold);
            line-height: 1.6;
            margin-top: 0.5rem;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
        }

        .timeline-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-period {
            background: rgba(27, 94, 59, .08);
            color: var(--emerald);
            padding: .35rem .8rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Unicode ayah end marker */
        .ayah-end-marker {
            font-family: var(--font-arabic);
            font-size: 1.1rem;
            color: var(--gold);
            margin-right: 0.5rem;
            direction: rtl;
            unicode-bidi: bidi-override;
        }
    </style>
    <script>
        document.documentElement.setAttribute(
            'data-bs-theme',
            localStorage.getItem('theme') || 'light'
        );
    </script>

    @stack('styles')
</head>

<body>

    {{-- NAVIGATION --}}
    @include('components.nav')


    {{-- FLASH MESSAGES --}}
    @if (session('success') || session('upgrade_message'))
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-islamic-success d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('upgrade_message'))
                <div class="upgrade-banner d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bi bi-star-fill me-2" style="color: var(--gold-light)"></i>
                        {{ session('upgrade_message') }}
                    </div>
                    <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn ms-3">
                        Upgrade Now
                    </a>
                </div>
            @endif
        </div>
    @endif
    @if ($errors->any())
        <div class="container mt-3">
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    {{-- Footer section only — replace your existing footer in layouts/app.blade.php --}}
    <footer class="footer-islamic mt-5">
        <div class="container">
            <div class="row">

                {{-- Brand column --}}
                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        @include('components.logo', ['height' => 32])
                    </div>
                    <p class="mb-0" style="font-size:0.85rem; color:rgba(255,255,255,0.65)">
                        Quran reading, Tafsir, and Prophet stories —<br>
                        for every Muslim, at every level.
                    </p>
                    <p class="mt-3 mb-0" style="font-size:0.78rem; color:rgba(255,255,255,0.35)">
                        <i class="bi bi-heart-fill me-1" style="color:var(--gold)"></i>
                        Built with love for the Ummah
                    </p>
                </div>

                {{-- Learn column --}}
                <div class="col-6 col-md-2 mb-3 offset-md-1">
                    <h6 class="text-white mb-3"
                        style="font-family:var(--font-heading); font-size:0.8rem; letter-spacing:0.06em;">LEARN</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.875rem;">
                        <li class="mb-2"><a href="{{ route('quran.index') }}">Quran</a></li>
                        <li class="mb-2"><a href="{{ route('stories.index') }}">Stories</a></li>
                        <li class="mb-2"><a href="{{ route('prophets.index') }}">Prophets</a></li>
                    </ul>
                </div>

                {{-- Account column --}}
                <div class="col-6 col-md-2 mb-3">
                    <h6 class="text-white mb-3"
                        style="font-family:var(--font-heading); font-size:0.8rem; letter-spacing:0.06em;">ACCOUNT</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.875rem;">
                        @auth
                            <li class="mb-2"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="mb-2"><a href="{{ route('profile.edit') }}">Profile</a></li>
                        @else
                            <li class="mb-2"><a href="{{ route('login') }}">Sign In</a></li>
                            <li class="mb-2"><a href="{{ route('register') }}">Register</a></li>
                        @endauth
                        <li class="mb-2"><a href="{{ route('pricing') }}">Pricing</a></li>
                    </ul>
                </div>

                {{-- Legal column --}}
                <div class="col-md-2 mb-3">
                    <h6 class="text-white mb-3"
                        style="font-family:var(--font-heading); font-size:0.8rem; letter-spacing:0.06em;">LEGAL</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.875rem;">
                        <li class="mb-2"><a href="#">Terms</a></li>
                        <li class="mb-2"><a href="#">Privacy</a></li>
                    </ul>
                </div>

            </div>

            {{-- Bottom bar --}}
            <div class="row mt-2 pt-3" style="border-top:1px solid rgba(201,150,58,0.15);">
                <div class="col-12 text-center">
                    <p class="mb-0" style="font-size:0.75rem; color:rgba(255,255,255,0.3);">
                        &copy; {{ now()->year }} Taddabur. All rights reserved.
                    </p>
                </div>
            </div>

        </div>
    </footer>
    @stack('scripts')
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dark mode toggle logic --}}
    <script>
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-bs-theme');
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        }

        function updateThemeIcon(theme) {
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = theme === 'dark' ?
                    'bi bi-sun-fill' :
                    'bi bi-moon-fill';
            }
        }

        // Set correct icon on load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            updateThemeIcon(savedTheme);
        });

        window.App = {
            csrfToken: "{{ csrf_token() }}"
        };
    </script>



</body>

</html>

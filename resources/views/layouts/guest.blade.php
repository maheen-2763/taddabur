<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Taddabur')</title>

    {{-- Google Fonts: same set as the main layout --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    {{-- Bootstrap 5 — same version as layouts.app --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Same variables as layouts.app — keep these two files in sync */
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

        body {
            font-family: var(--font-body);
            color: var(--ink);
            line-height: 1.75;
            /* Rich dark hero background — auth pages are the one guest context
               where the immersive treatment makes sense regardless of theme */
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0a2a18 100%);
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        h4,
        .heading-font {
            font-family: var(--font-heading);
            letter-spacing: 0.03em;
        }

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

        .alert-islamic-success {
            background: rgba(27, 94, 59, 0.1);
            border: 1px solid var(--emerald-light);
            color: var(--emerald);
            border-radius: var(--radius);
        }

        html {
            scroll-behavior: smooth;
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
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>

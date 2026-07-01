<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Islamic Scholars') — Taddabur</title>
    <meta name="description" content="@yield('description', 'Learn about the four great Imams and founders of Islamic jurisprudence.')">

    {{-- Same fonts as app.blade.php --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Scholars Module CSS --}}
    <link rel="stylesheet" href="{{ asset('css/scholars.css') }}">

    @stack('styles')

    <style>
        /* ================================================
           INHERIT ALL CSS VARIABLES FROM MAIN APP
           Copy your :root variables here so this layout
           is fully independent
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

        .arabic {
            font-family: var(--font-arabic);
            font-size: 1.6rem;
            line-height: 2.4;
            direction: rtl;
            text-align: right;
            color: var(--ink);
        }

        /* ================================================
           SCHOLARS SIDEBAR LAYOUT
        ================================================ */
        .scholars-wrapper {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 0;
            min-height: calc(100vh - 64px);
        }

        /* ---- Sidebar ---- */
        .scholars-sidebar {
            background: var(--emerald-dark);
            border-right: 1px solid rgba(201, 150, 58, 0.2);
            padding: 2rem 0;
            position: sticky;
            top: 64px;
            height: calc(100vh - 64px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--gold-dark) transparent;
        }

        .scholars-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .scholars-sidebar::-webkit-scrollbar-thumb {
            background: var(--gold-dark);
            border-radius: 2px;
        }

        .sidebar-section-label {
            font-family: var(--font-heading);
            font-size: 0.65rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(201, 150, 58, 0.6);
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
            margin-top: 1.5rem;
        }

        .sidebar-section-label:first-child {
            margin-top: 0;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-family: var(--font-body);
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav-link:hover {
            color: var(--gold-light);
            background: rgba(255, 255, 255, 0.05);
            border-left-color: var(--gold-dark);
        }

        .sidebar-nav-link.active {
            color: var(--gold-light);
            background: rgba(201, 150, 58, 0.08);
            border-left-color: var(--gold);
            font-weight: 500;
        }

        .sidebar-nav-link .arabic-sm {
            font-family: var(--font-arabic);
            font-size: 0.9rem;
            direction: rtl;
            color: rgba(201, 150, 58, 0.7);
            line-height: 1;
        }

        .sidebar-madhab-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            min-width: 8px;
        }

        /* Madhab color dots */
        .dot-hanafi {
            background: #5AC98A;
        }

        .dot-maliki {
            background: #60A5FA;
        }

        .dot-shafi_i {
            background: #F59E0B;
        }

        .dot-hanbali {
            background: #F87171;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(201, 150, 58, 0.15);
            margin: 1rem 1.5rem;
        }

        /* ---- Main Content ---- */
        .scholars-main {
            padding: 2.5rem;
            background: var(--cream);
            overflow-y: auto;
        }

        /* ================================================
           SCHOLARS NAVBAR (top breadcrumb bar)
        ================================================ */
        .scholars-topbar {
            background: var(--cream-dark);
            border-bottom: 1px solid var(--border);
            padding: 0.6rem 2.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: var(--muted);
            font-family: var(--font-body);
        }

        .scholars-topbar a {
            color: var(--emerald);
            text-decoration: none;
        }

        .scholars-topbar a:hover {
            color: var(--gold-dark);
        }

        .scholars-topbar .separator {
            color: var(--border);
        }

        /* ================================================
           RESPONSIVE — Mobile: sidebar collapses
        ================================================ */
        @media (max-width: 992px) {
            .scholars-wrapper {
                grid-template-columns: 1fr;
            }

            .scholars-sidebar {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 280px;
                height: 100vh;
                z-index: 1050;
            }

            .scholars-sidebar.open {
                display: block;
            }

            .scholars-main {
                padding: 1.5rem 1rem;
            }

            .scholars-topbar {
                padding: 0.6rem 1rem;
            }
        }

        /* ================================================
           NAVBAR — same as app.blade.php
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

        .footer-islamic {
            background: var(--emerald-dark);
            border-top: 2px solid var(--gold-dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 1.5rem 0;
            font-size: 0.85rem;
            text-align: center;
        }

        .footer-islamic a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
        }

        .footer-islamic a:hover {
            color: var(--gold-light);
        }

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
    </style>

    <script>
        document.documentElement.setAttribute(
            'data-bs-theme',
            localStorage.getItem('theme') || 'light'
        );
    </script>
</head>

<body>

    {{-- MAIN NAVBAR (same as app) --}}
    @include('components.nav')

    {{-- SCHOLARS TOPBAR BREADCRUMB --}}
    <div class="scholars-topbar">
        <a href="{{ route('home') }}">Home</a>
        <span class="separator">/</span>
        <a href="{{ route('scholars.index') }}">Scholars</a>
        @hasSection('breadcrumb')
            <span class="separator">/</span>
            @yield('breadcrumb')
        @endif
    </div>

    {{-- SCHOLARS LAYOUT: Sidebar + Main --}}
    <div class="scholars-wrapper">

        {{-- SIDEBAR --}}
        <aside class="scholars-sidebar" id="scholarsSidebar">
            <p class="sidebar-section-label">The Four Imams</p>

            {{-- Dynamically highlight active --}}
            @php
                $imams = [
                    [
                        'slug' => 'imam-abu-hanifa',
                        'name' => 'Abu Hanifa',
                        'arabic' => 'أبو حنيفة',
                        'madhab' => 'hanafi',
                    ],
                    ['slug' => 'imam-malik', 'name' => 'Imam Malik', 'arabic' => 'مالك بن أنس', 'madhab' => 'maliki'],
                    ['slug' => 'imam-al-shafii', 'name' => "Al-Shafi'i", 'arabic' => 'الشافعي', 'madhab' => 'shafi_i'],
                    [
                        'slug' => 'imam-ahmad-ibn-hanbal',
                        'name' => 'Ahmad ibn Hanbal',
                        'arabic' => 'أحمد بن حنبل',
                        'madhab' => 'hanbali',
                    ],
                ];
                $currentSlug = request()->route('scholar')?->slug ?? null;
            @endphp

            @foreach ($imams as $imam)
                <a href="{{ route('scholars.show', $imam['slug']) }}"
                    class="sidebar-nav-link {{ $currentSlug === $imam['slug'] ? 'active' : '' }}">
                    <span class="sidebar-madhab-dot dot-{{ $imam['madhab'] }}"></span>
                    <span class="flex-grow-1">
                        {{ $imam['name'] }}
                        <br>
                        <span class="arabic-sm">{{ $imam['arabic'] }}</span>
                    </span>
                </a>
            @endforeach

            <hr class="sidebar-divider">
            <p class="sidebar-section-label">Navigate</p>

            <a href="{{ route('scholars.index') }}"
                class="sidebar-nav-link {{ request()->routeIs('scholars.index') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap" style="color: var(--gold); font-size: 0.9rem;"></i>
                All Scholars
            </a>
        </aside>

        {{-- MOBILE SIDEBAR OVERLAY --}}
        <div class="d-lg-none" id="sidebarOverlay"
            style="display:none!important; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1049;"
            onclick="closeSidebar()">
        </div>

        {{-- MAIN CONTENT --}}
        <main class="scholars-main">

            {{-- Mobile sidebar toggle button --}}
            <button class="btn d-lg-none mb-3" onclick="openSidebar()"
                style="background: var(--emerald); color: #fff; font-size: 0.85rem; border-radius: var(--radius); padding: 0.5rem 1rem;">
                <i class="bi bi-list me-2"></i> All Scholars
            </button>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert mb-4"
                    style="background: rgba(27,94,59,0.1); border: 1px solid var(--emerald-light); color: var(--emerald); border-radius: var(--radius);">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

    {{-- SIMPLE FOOTER --}}
    <footer class="footer-islamic">
        <p class="mb-0">
            &copy; {{ now()->year }} Taddabur &nbsp;·&nbsp;
            <a href="{{ route('scholars.index') }}">Scholars</a> &nbsp;·&nbsp;
            <a href="{{ route('home') }}">Home</a>
        </p>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dark mode --}}
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
            if (icon) icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcon(localStorage.getItem('theme') || 'light');
        });
    </script>

    {{-- Mobile Sidebar --}}
    <script>
        function openSidebar() {
            document.getElementById('scholarsSidebar').classList.add('open');
            document.getElementById('sidebarOverlay').style.display = 'block';
        }

        function closeSidebar() {
            document.getElementById('scholarsSidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').style.display = 'none';
        }
    </script>

    @stack('scripts')
</body>

</html>

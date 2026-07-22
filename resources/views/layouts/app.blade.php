<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Taddabur — Quran & Prophet Stories')</title>
    <meta name="description" content="@yield('description', 'Read the Quran with tafsir and explore prophet stories in depth.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @stack('styles')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    {{-- ✅ Sirf EK toggleTheme() — file mein sirf yahi jagah defined --}}
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-bs-theme', next);
            try {
                localStorage.setItem('theme', next);
            } catch (e) {
                // localStorage blocked (private mode) — theme abhi ke liye kaam karega, refresh pe reset ho jayega
            }

            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.classList.toggle('bi-moon-fill', next === 'light');
                icon.classList.toggle('bi-sun-fill', next === 'dark');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const icon = document.getElementById('theme-icon');
            const current = document.documentElement.getAttribute('data-bs-theme');
            if (icon && current === 'dark') {
                icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        });
    </script>
</head>

<body>

    {{-- NAVIGATION --}}
    @include('components.nav')

    {{-- FLASH MESSAGES — content se pehle, taaki upar dikhein --}}
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

    {{-- ✅ MAIN CONTENT — sirf EK jagah render hota hai --}}
    @hasSection('sidebar')
        <div class="scholars-topbar">
            <a href="{{ route('home') }}">Home</a>
            <span class="separator">/</span>
            @yield('breadcrumb')
        </div>

        <div class="scholars-wrapper">
            <aside class="scholars-sidebar" id="scholarsSidebar">
                <div class="scholars-sidebar-inner">
                    @yield('sidebar')
                </div>
            </aside>
            <div class="d-lg-none" id="sidebarOverlay"
                style="display:none!important; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1049;"
                onclick="closeSidebar()">
            </div>
            <main class="scholars-main">
                @yield('content')
            </main>
        </div>
    @else
        <main>
            @yield('content')
        </main>
    @endif

    @include('partials.scroll-to-top')

    <div id="page-loader"
        class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        style="background: rgba(0,0,0,0.3); z-index: 9999;">
        <div class="tasbih-loader" id="tasbih-loader" aria-label="Loading…">
            <div class="bead"></div>
            <div class="bead"></div>
            <div class="bead"></div>
            <div class="bead"></div>
            <div class="bead"></div>
            <div class="bead"></div>
            <div class="bead"></div>
        </div>
    </div>

    {{-- ✅ Sirf EK footer --}}
    <footer class="footer-islamic mt-1">
        <div class="container-fluid px-3 px-md-5">
            <div class="row">

                <div class="col-md-4 mb-4">
                    <div class="mb-3">
                        <a class="navbar-brand text-decoration-none" href="{{ route('home') }}">
                            @include('components.logo', ['height' => 32])
                        </a>
                    </div>
                    <p class="mb-0" style="font-size:0.85rem; color:rgba(255,255,255,0.65)">
                        Quran reading, Tafsir, and Prophet stories and many more —<br>
                        for every Muslim, at every level.
                    </p>
                    <p class="mt-3 mb-0" style="font-size:0.78rem; color:rgba(255,255,255,0.35)">
                        <i class="bi bi-heart-fill me-1" style="color:var(--gold)"></i>
                        Built with love for the Ummah
                    </p>
                </div>

                <div class="col-6 col-md-2 mb-3 offset-md-1">
                    <h6 class="text-white mb-3"
                        style="font-family:var(--font-heading); font-size:0.8rem; letter-spacing:0.06em;">LEARN</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.875rem;">
                        <li class="mb-2"><a href="{{ route('quran.index') }}">Quran</a></li>
                        <li class="mb-2"><a href="{{ route('stories.index') }}">Stories</a></li>
                        <li class="mb-2"><a href="{{ route('prophets.index') }}">Prophets</a></li>
                    </ul>
                </div>

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

                <div class="col-6 col-md-2 mb-3">
                    <h6 class="text-white mb-3"
                        style="font-family:var(--font-heading); font-size:0.8rem; letter-spacing:0.06em;">About Us</h6>
                    <ul class="list-unstyled mb-0" style="font-size:0.875rem;">
                        <li class="mb-2"> <a href="{{ route('terms') }}">Terms</a></li>
                        <li class="mb-2"> <a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        <li class="mb-2"> <a href="{{ route('about') }}">Sources</a></li>
                    </ul>
                </div>

                {{-- Developer note trigger --}}
                <button class="dev-note-trigger" onclick="toggleDevNote()" aria-label="A note from the developer"
                    title="A note from the developer">
                    <i class="bi bi-feather"></i>
                </button>

                {{-- Developer note modal --}}
                <div class="dev-note-overlay" id="devNoteOverlay" onclick="toggleDevNote()"></div>
                <div class="dev-note-modal" id="devNoteModal">
                    <button class="dev-note-close" onclick="toggleDevNote()">&times;</button>

                    <div class="dev-note-content">
                        <p class="dev-note-arabic">بِسْمِ اللهِ الرَّحْمَٰنِ الرَّحِيمِ</p>

                        <p>
                            Taddabur is built by a single individual developer — a humble effort, not a company or a
                            team.
                            I'm continuing to improve it, both in content accuracy and in design, In Sha Allah.
                        </p>

                        <p>
                            May Allah accept this from me, and make it a means of benefit for anyone who uses it.
                            May He forgive every mistake made while building it, and have mercy on me and on the whole
                            Ummah.
                        </p>

                        <p>
                            May our Hereafter be as beautiful as He has promised us in the Qur'an. And may we be
                            reunited
                            with our Prophet ﷺ.
                        </p>

                        <p class="dev-note-signoff">
                            If this app has helped you in any way, a small dua for me and my family
                            would mean more than anything. — Ameen 🤲
                        </p>
                    </div>
                </div>

            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ✅ Sirf window.App yahan — toggleTheme duplicate hata diya --}}
    <script>
        window.App = {
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

</body>

</html>

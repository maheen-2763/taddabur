{{--
    Navigation Component — Taddabur
    Place this file at: resources/views/components/nav.blade.php
--}}
<nav class="navbar navbar-islamic navbar-expand-lg">
    <div class="container-fluid px-3 px-md-5">

        {{-- Logo --}}
        <a class="navbar-brand text-decoration-none" href="{{ route('home') }}">
            @include('components.logo', ['height' => 34])
        </a>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
            aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation"
            style="color:rgba(255,255,255,0.8)">
            <i class="bi bi-list" style="font-size:1.5rem;"></i>
        </button>

        {{-- Collapse wrapper — YEH MISSING THA, wapas add kiya --}}
        <div class="collapse navbar-collapse" id="navMain">

            {{-- Links --}}

            <ul class="navbar-nav mx-auto gap-1">

                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('quran.*') ? 'active' : '' }}"
                        {{ request()->routeIs('quran.*') ? 'aria-current=page' : '' }}
                        href="{{ route('quran.index') }}">
                        <i class="bi bi-book me-1"></i>Quran
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link-islamic nav-link dropdown-toggle {{ request()->routeIs(['stories.*', 'prophets.*', 'scholars.*', 'hadith.*', 'allah-names.*', 'prophet-names.*']) ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid me-1"></i>Features
                    </a>
                    <ul class="dropdown-menu nav-dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('stories.*') ? 'active' : '' }}"
                                href="{{ route('stories.index') }}">
                                <i class="bi bi-journal-text me-2"></i>Stories
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('prophets.*') ? 'active' : '' }}"
                                href="{{ route('prophets.index') }}">
                                <i class="bi bi-stars me-2"></i>Prophets
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('scholars.*') ? 'active' : '' }}"
                                href="{{ route('scholars.index') }}">
                                <i class="bi bi-mortarboard me-2"></i>Four Imams
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('hadith.*') ? 'active' : '' }}"
                                href="{{ route('hadith.index') }}">
                                <i class="bi bi-collection me-2"></i>Hadith
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('allah-names.*') ? 'active' : '' }}"
                                href="{{ route('allah-names.index') }}">
                                <i class="bi bi-moon-stars me-2"></i>99 Names of Allah
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('prophet-names.*') ? 'active' : '' }}"
                                href="{{ route('prophet-names.index') }}">
                                <i class="bi bi-award me-2"></i>Names of the Prophet ﷺ
                            </a>
                        </li>


                        <hr class="dropdown-divider">
                        <li><a class="dropdown-item {{ request()->routeIs('features.*') ? 'active' : '' }}"
                                href="{{ route('features.index') }}"><i class="bi bi-grid-3x3-gap-fill me-2"></i>App
                                Features</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}"
                                href="{{ route('about') }}"><i class="bi bi-info-circle me-2"></i>About</a></li>



                    </ul>
                </li>

            </ul>

            {{-- Right side --}}
            <div class="d-flex align-items-center gap-2">

                {{-- Dark mode toggle --}}
                <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode">
                    <i id="theme-icon" class="bi bi-moon-fill"></i>
                </button>

                @auth
                    <div class="dropdown">
                        <button class="dropdown-toggle nav-user-badge" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="nav-user-name">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu nav-dropdown-menu">
                            <li>
                                <a class="dropdown-item small" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item small text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link-islamic nav-link" style="font-size:0.85rem;">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="btn-gold btn btn-sm">
                        Get Started
                    </a>
                @endauth
            </div>

        </div> {{-- navMain close --}}

    </div>
</nav>

<script>
    // Mobile collapse menu link click hote hi band ho
    document.querySelectorAll('#navMain .nav-link:not(.dropdown-toggle), #navMain .dropdown-item').forEach(link => {
        link.addEventListener('click', () => {
            const navMain = document.getElementById('navMain');
            if (navMain.classList.contains('show')) {
                bootstrap.Collapse.getInstance(navMain)?.hide();
            }
        });
    });

    // Agar toggleTheme() load nahi hui, dev ko turant pata chale
    if (typeof toggleTheme !== 'function') {
        console.warn('toggleTheme() not loaded — check theme.js is included in this page.');
    }
</script>

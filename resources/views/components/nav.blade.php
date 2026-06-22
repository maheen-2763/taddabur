{{--
    Navigation Component — Taddabur
    Place this file at: resources/views/components/nav.blade.php
--}}
<nav class="navbar navbar-islamic navbar-expand-lg">
    <div class="container">

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

        {{-- Links --}}
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('quran.*') ? 'active' : '' }}"
                        href="{{ route('quran.index') }}">
                        <i class="bi bi-book me-1"></i>Quran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('stories.*') ? 'active' : '' }}"
                        href="{{ route('stories.index') }}">
                        <i class="bi bi-journal-text me-1"></i>Stories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('prophets.*') ? 'active' : '' }}"
                        href="{{ route('prophets.index') }}">
                        <i class="bi bi-stars me-1"></i>Prophets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link-islamic nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}"
                        href="{{ route('pricing') }}">
                        <i class="bi bi-gem me-1"></i>Pricing
                    </a>
                </li>
            </ul>

            {{-- Right side --}}
            <div class="d-flex align-items-center gap-2">

                {{-- Dark mode toggle --}}
                <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle dark mode">
                    <i id="theme-icon" class="bi bi-moon-fill"></i>
                </button>

                @auth
                    {{-- Plan badge --}}
                    @php $plan = auth()->user()->subscription_plan ?? 'free'; @endphp
                    <span class="badge badge-{{ $plan }} text-uppercase"
                        style="font-size:0.65rem; letter-spacing:0.05em;">
                        {{ ucfirst($plan) }}
                    </span>

                    <div class="dropdown">
                        <button class="btn btn-sm d-flex align-items-center gap-2 dropdown-toggle"
                            style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);
                                       color:#fff; border-radius:var(--radius); padding:0.35rem 0.85rem;"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span
                                style="font-size:0.85rem; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ auth()->user()->name }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-1"
                            style="border-color:var(--border); border-radius:var(--radius);">
                            <li>
                                <a class="dropdown-item small" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item small" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item small" href="{{ route('subscription.upgrade') }}">
                                    <i class="bi bi-star me-2" style="color:var(--gold)"></i>Upgrade
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
        </div>

    </div>
</nav>

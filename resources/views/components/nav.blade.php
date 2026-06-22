<nav class="navbar navbar-expand-lg navbar-islamic">
    <div class="container">



        {{-- Logo --}}
        <a class="navbar-brand navbar-brand-text" href="{{ url('#') }}">
            @include('components.logo')
        </a>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler border-0" aria-label="Toggle navigation" type="button" data-bs-toggle="collapse"
            data-bs-target="#navMain" style="color:white">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMain">

            {{-- Left links --}}
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-islamic {{ request()->routeIs('quran.*') ? 'active' : '' }}"
                        href="{{ route('quran.index') }}">
                        <i class="bi bi-book me-1"></i>Quran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-islamic {{ request()->routeIs('stories.*') ? 'active' : '' }}"
                        href="{{ route('stories.index') }}">
                        <i class="bi bi-journal-text me-1"></i>Stories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-islamic {{ request()->routeIs('prophets.*') ? 'active' : '' }}"
                        href="{{ route('prophets.index') }}">
                        <i class="bi bi-people me-1"></i>Prophets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-islamic {{ request()->routeIs('pricing') ? 'active' : '' }}"
                        href="{{ route('pricing') }}">
                        <i class="bi bi-star me-1"></i>Pricing
                    </a>
                </li>
            </ul>

            {{-- Right side --}}
            <div class="d-flex align-items-center gap-2">

                {{-- Dark mode toggle --}}
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <i id="theme-icon" class="bi bi-moon-fill" style="font-size:0.8rem"></i>
                </button>

                @auth
                    {{-- Authenticated user --}}
                    <div class="dropdown">
                        <button class="btn btn-sm d-flex align-items-center gap-2"
                            style="background:rgba(255,255,255,0.12); color:white; border-radius:var(--radius)"
                            data-bs-toggle="dropdown">

                            {{-- Plan badge --}}
                            <span class="badge badge-{{ auth()->user()->plan }}" style="font-size:0.65rem">
                                {{ strtoupper(auth()->user()->plan) }}
                            </span>

                            {{ auth()->user()->name }}
                            <i class="bi bi-chevron-down" style="font-size:0.7rem"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('bookmarks.index') }}">
                                    <i class="bi bi-bookmark me-2"></i>Bookmarks
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>
                            @if (!auth()->user()->isPremium())
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('subscription.upgrade') }}"
                                        style="color:var(--gold)">
                                        <i class="bi bi-lightning-fill me-2"></i>Upgrade Plan
                                    </a>
                                </li>
                            @endif
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    {{-- Guest --}}
                    <a href="{{ route('login') }}" class="btn btn-sm nav-link-islamic"
                        style="border:1px solid rgba(255,255,255,0.3)">
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

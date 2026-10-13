<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin — @yield('title', 'Dashboard') | Taddabur</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600&family=Lora:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
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
            --border: #E5DDD0;
            --sidebar-width: 260px;
            --font-heading: 'Cinzel', serif;
            --font-body: 'Lora', serif;
            --font-arabic: 'Amiri', serif;
            --radius: 12px;
        }

        body {
            font-family: var(--font-body);
            background: #F0F2F5;
            color: var(--ink);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .heading-font {
            font-family: var(--font-heading);
        }

        /* ══ SIDEBAR ══════════════════════════════════ */
        .admin-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--emerald-dark);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(201, 150, 58, 0.2);
        }

        /* Geometric pattern on sidebar */
        .admin-sidebar::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 0 L40 20 L20 40 L0 20 Z' fill='none' stroke='rgba(201,150,58,0.05)' stroke-width='0.8'/%3E%3C/svg%3E");
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .sidebar-brand {
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid rgba(201, 150, 58, 0.2);
            position: relative;
            z-index: 1;
        }

        .sidebar-brand small {
            display: block;
            color: rgba(255, 255, 255, 0.35);
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-top: 6px;
            padding-left: 2px;
        }

        .sidebar-nav {
            padding: 0.75rem 0;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .sidebar-label {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.62rem;
            letter-spacing: 0.12em;
            padding: 0.9rem 1rem 0.25rem;
            text-transform: uppercase;
            font-family: var(--font-heading);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            position: relative;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.07);
            color: var(--gold-light);
            border-color: var(--gold);
        }

        .sidebar-link i {
            font-size: 0.95rem;
            width: 18px;
            flex-shrink: 0;
        }



        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            margin: 0.5rem 0;
        }

        /* ══ MAIN ════════════════════════════════════ */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .admin-topbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0.65rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .topbar-title {
            font-family: var(--font-heading);
            font-size: 0.88rem;
            color: var(--ink);
            letter-spacing: 0.04em;
        }

        .topbar-breadcrumb {
            font-size: 0.75rem;
            color: #999;
            margin-top: 1px;
        }

        .admin-content {
            padding: 1.5rem;
        }

        /* ══ STAT CARDS ══════════════════════════════ */
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--border);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-number {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            line-height: 1;
            margin: 4px 0 2px;
        }

        /* ══ SECTION CARDS ═══════════════════════════ */
        .admin-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .admin-card-header {
            padding: 0.9rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #FAFAFA;
        }

        .admin-card-header h6 {
            font-family: var(--font-heading);
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            margin: 0;
            color: var(--ink);
        }

        /* ══ TABLES ══════════════════════════════════ */
        .admin-table {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .admin-table .table {
            margin: 0;
            font-size: 0.875rem;
        }

        .admin-table .table th {
            background: #F8F8F8;
            font-family: var(--font-heading);
            font-size: 0.7rem;
            letter-spacing: 0.06em;
            color: #888;
            border-bottom: 1px solid var(--border);
            padding: 0.65rem 1rem;
            text-transform: uppercase;
        }

        .admin-table .table td {
            padding: 0.7rem 1rem;
            vertical-align: middle;
            border-color: #F5F5F5;
        }

        .admin-table .table tbody tr:hover {
            background: #FAFAFA;
        }

        /* ══ BADGES ══════════════════════════════════ */
        .badge-free {
            background: #6B7280;
            color: white;
            font-size: 0.65rem;
        }

        .badge-basic {
            background: var(--emerald-light);
            color: white;
            font-size: 0.65rem;
        }


        .badge-admin {
            background: var(--gold);
            color: var(--ink);
            font-size: 0.65rem;
            letter-spacing: 0.05em;
        }


        /* ══ BUTTONS ═════════════════════════════════ */
        .btn-admin-primary {
            background: var(--emerald);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.45rem 1rem;
            font-size: 0.82rem;
            transition: all 0.2s;
        }

        .btn-admin-primary:hover {
            background: var(--emerald-light);
            color: white;
        }

        .btn-admin-gold {
            background: var(--gold);
            color: #1A1A2E;
            border: none;
            border-radius: 8px;
            padding: 0.45rem 1rem;
            font-size: 0.82rem;
        }

        .btn-admin-gold:hover {
            background: var(--gold-light);
            color: #1A1A2E;
        }

        .btn-admin-outline {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.4rem 0.9rem;
            font-size: 0.8rem;
            color: #666;
        }

        .btn-admin-outline:hover {
            border-color: var(--emerald);
            color: var(--emerald);
        }

        /* ══ PROGRESS BARS ═══════════════════════════ */
        .progress {
            border-radius: 20px;
            background: #F0F0F0;
        }

        .progress-bar-gold {
            background: var(--gold);
        }


        /* ══ FLASH ═══════════════════════════════════ */
        .alert-admin-success {
            background: rgba(27, 94, 59, 0.08);
            border: 1px solid var(--emerald-light);
            color: var(--emerald);
            border-radius: var(--radius);
        }

        /* ══ SCROLLBAR ═══════════════════════════════ */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: var(--emerald-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(201, 150, 58, 0.3);
            border-radius: 3px;
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- ══ SIDEBAR ══════════════════════════════════════ --}}
    <aside class="admin-sidebar">

        {{-- Logo --}}
        <div class="sidebar-brand">
            @include('components.logo', ['height' => 30])
            <small>Admin Panel</small>
        </div>

        <nav class="sidebar-nav">

            <div class="sidebar-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Quran</div>
            <a href="{{ route('quran.index') }}" class="sidebar-link" target="_blank" rel="noopener">
                <i class="bi bi-book"></i> Quran (Public View)
            </a>

            <div class="sidebar-label">Content</div>
            <a href="{{ route('admin.stories.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.stories.*', 'admin.chapters.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Stories
            </a>
            <a href="{{ route('admin.prophets.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.prophets.*') ? 'active' : '' }}">
                <i class="bi bi-stars"></i> Prophets
            </a>
            <a href="{{ route('admin.daily-reflections.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.daily-reflections.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Daily Content
            </a>

            <div class="sidebar-label">Users</div>
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> All Users
            </a>

            <div class="sidebar-label">Insights</div>
            <a href="{{ route('admin.donations.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
                <i class="bi bi-heart"></i> Donations
            </a>
            <a href="{{ route('admin.dashboard') }}#data-sources" class="sidebar-link">
                <i class="bi bi-shield-check"></i> Data Sources
            </a>

            <hr class="sidebar-divider mx-3">

            <div class="sidebar-label">System</div>
            <a href="{{ route('admin.backups') }}"
                class="sidebar-link {{ request()->routeIs('admin.backups*') ? 'active' : '' }}">
                <i class="bi bi-hdd"></i> Backups
            </a>
            <a href="{{ route('home') }}" class="sidebar-link" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i> View Site
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="sidebar-link w-100 border-0 text-start bg-transparent" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>

        </nav>
    </aside>

    {{-- ══ MAIN ══════════════════════════════════════════ --}}
    <div class="admin-main">

        {{-- Topbar --}}
        <div class="admin-topbar">
            <div>
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
                <div class="topbar-breadcrumb">Admin Panel · Taddabur</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span style="font-size:0.82rem; color:#888">{{ auth()->user()->name }}</span>
                <span class="badge badge-admin">ADMIN</span>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mx-3 mt-3 alert alert-admin-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-3 mt-3 alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="admin-content">
            @yield('content')
        </div>

    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>

</html>

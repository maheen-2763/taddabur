<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | Taddabur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Cinzel:wght@400;600&family=Lora:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --gold: #C9963A;
            --emerald: #1B5E3B;
            --emerald-light: #2D8A59;
            --emerald-dark: #0D3D22;
            --cream: #FAF6EE;
            --cream-dark: #F0E8D8;
            --ink: #1A1A2E;
            --border: #E5DDD0;
            --sidebar-width: 250px;
            --font-heading: 'Cinzel', serif;
            --font-body: 'Lora', serif;
        }

        body {
            font-family: var(--font-body);
            background-color: #F5F5F5;
            color: var(--ink);
        }

        /* ── SIDEBAR ─────────────────────────────── */
        .admin-sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--emerald-dark);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h5 {
            font-family: var(--font-heading);
            color: #C9963A;
            margin: 0;
            font-size: 1.1rem;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-label {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            padding: 0.75rem 1rem 0.25rem;
            text-transform: uppercase;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.88rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #C9963A;
            border-color: #C9963A;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 20px;
        }

        /* ── MAIN CONTENT ────────────────────────── */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .admin-topbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .admin-content {
            padding: 1.5rem;
        }

        /* ── STAT CARDS ──────────────────────────── */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        /* ── TABLES ──────────────────────────────── */
        .admin-table {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .admin-table .table {
            margin: 0;
        }

        .admin-table .table th {
            background: #F8F8F8;
            font-family: var(--font-heading);
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #666;
            border-bottom: 1px solid var(--border);
        }

        /* ── BADGES ──────────────────────────────── */
        .badge-free {
            background: #6B7280;
            color: white;
        }

        .badge-basic {
            background: var(--emerald-light);
            color: white;
        }

        .badge-premium {
            background: var(--gold);
            color: #1A1A2E;
        }

        /* ── BUTTONS ─────────────────────────────── */
        .btn-admin-primary {
            background: var(--emerald);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-size: 0.85rem;
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
            padding: 0.5rem 1.25rem;
            font-size: 0.85rem;
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- ── SIDEBAR ──────────────────────────────── --}}
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <h5>🕌 Taddabur</h5>
            <small>Admin Panel</small>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Content</div>
            <a href="{{ route('admin.stories.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.stories.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Stories
            </a>
            <a href="{{ route('admin.prophets.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.prophets.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Prophets
            </a>
            <a href="{{ route('admin.daily-reflections.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.daily-content.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Daily Content
            </a>

            <div class="sidebar-label">Users</div>
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> All Users
            </a>

            <div class="sidebar-label">System</div>
            <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> View Site
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="sidebar-link w-100 border-0 text-start bg-transparent">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
        </nav>
    </aside>

    {{-- ── MAIN ──────────────────────────────────── --}}
    <div class="admin-main">

        {{-- Topbar --}}
        <div class="admin-topbar">
            <h6 class="mb-0" style="font-family:var(--font-heading); font-size:0.9rem">
                @yield('title', 'Dashboard')
            </h6>
            <div class="d-flex align-items-center gap-3">
                <span style="font-size:0.85rem; color:#666">
                    {{ auth()->user()->name }}
                </span>
                <span class="badge badge-premium">ADMIN</span>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mx-3 mt-3 alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-3 mt-3 alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Page content --}}
        <div class="admin-content">
            @yield('content')
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>

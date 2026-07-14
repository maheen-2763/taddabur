@extends('layouts.app')

@section('title', 'Imam Abu Hanifa')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/scholars.css') }}">
@endpush

@section('breadcrumb')
    <a href="{{ route('scholars.index') }}">Scholars</a>
@endsection

@section('sidebar')
    <p class="sidebar-section-label">The Four Imams</p>

    @php
        $imams = [
            ['slug' => 'imam-abu-hanifa', 'name' => 'Abu Hanifa', 'arabic' => 'أبو حنيفة', 'madhab' => 'hanafi'],
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
@endsection

@section('content')
    {{-- Mobile sidebar toggle button --}}
    <button class="btn d-lg-none mb-3" onclick="openSidebar()"
        style="background: var(--emerald); color: #fff; font-size: 0.85rem; border-radius: var(--radius); padding: 0.5rem 1rem;">
        <i class="bi bi-list me-2"></i> All Scholars
    </button>

    {{-- Actual scholar detail content yahan --}}
@endsection

@push('scripts')
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
@endpush

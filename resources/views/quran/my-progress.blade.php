{{-- resources/views/quran/my-progress.blade.php --}}

@extends('layouts.app')
@section('title', 'My Quran Progress — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-my-progress.css') }}">
@endpush

@section('content')

    <div class="progress-page">

        <a href="{{ route('dashboard') }}" class="btn btn-sm"
            style="border:1px solid var(--border); margin-bottom:1rem; display:inline-flex; align-items:center; gap:0.4rem">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

        {{-- Header --}}
        <div class="progress-header">
            <p class="title">📖 My Quran Journey</p>

            <div class="progress-stats-row">
                <div class="progress-stat-box">
                    <span class="progress-stat-num">{{ number_format($totalRead) }}</span>
                    <span class="progress-stat-label">Ayahs Read</span>
                </div>
                <div class="progress-stat-box">
                    <span class="progress-stat-num">{{ round(($totalRead / $totalAyahs) * 100, 1) }}%</span>
                    <span class="progress-stat-label">Of Quran</span>
                </div>
                <div class="progress-stat-box">
                    <span class="progress-stat-num">{{ $totalCompleted }}</span>
                    <span class="progress-stat-label">Surahs Completed</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="progress-filters">
            <button class="progress-filter-btn active" data-filter="all" onclick="filterSurahs('all', this)">
                All 114
            </button>
            <button class="progress-filter-btn" data-filter="in_progress" onclick="filterSurahs('in_progress', this)">
                In Progress
            </button>
            <button class="progress-filter-btn" data-filter="completed" onclick="filterSurahs('completed', this)">
                Completed
            </button>
            <button class="progress-filter-btn" data-filter="not_started" onclick="filterSurahs('not_started', this)">
                Not Started
            </button>
        </div>

        {{-- Surah List --}}
        <div id="surahProgressList">
            @foreach ($progress as $item)
                <a href="{{ route('quran.show', $item['surah']->number) }}" class="surah-progress-row"
                    data-status="{{ $item['status'] }}">

                    <span class="surah-progress-num">{{ $item['surah']->number }}</span>

                    <div class="surah-progress-info">
                        <div class="surah-progress-name">
                            {{ $item['surah']->name_transliteration }}
                            <span style="color:var(--muted); font-size:0.78rem">
                                ({{ $item['surah']->name_arabic }})
                            </span>
                        </div>
                        <div class="surah-progress-bar-row">
                            <div class="d-progress" style="flex-grow:1">
                                <div class="d-progress-fill" style="width: {{ $item['percentage'] }}%"></div>
                            </div>
                            <span class="surah-progress-count">
                                {{ $item['read_count'] }}/{{ $item['total_ayahs'] }}
                            </span>
                        </div>
                    </div>

                    <span class="surah-status-badge {{ $item['status'] }}">
                        @if ($item['status'] === 'completed')
                            ✓ Completed
                        @elseif($item['status'] === 'in_progress')
                            In Progress
                        @else
                            Not Started
                        @endif
                    </span>

                </a>
            @endforeach
        </div>

    </div>
    <button class="scroll-top-btn" id="progressScrollTopBtn" onclick="window.scrollTo({top:0, behavior:'smooth'})"
        title="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

@endsection

@push('scripts')
    <script>
        function filterSurahs(filter, btn) {
            document.querySelectorAll('.progress-filter-btn')
                .forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.surah-progress-row').forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        window.addEventListener('scroll', () => {
            const btn = document.getElementById('progressScrollTopBtn');
            if (!btn) return;
            btn.classList.toggle('visible', window.scrollY > 400);
        });
    </script>
@endpush

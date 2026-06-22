{{-- resources/views/components/dashboard/progress.blade.php --}}
{{--
    BUG FIX: Previously calculated completion % twice —
    once rounded to 1 decimal for display, once unrounded
    for the progress bar width. They showed different
    numbers for the same stat. Now calculated ONCE here
    and reused everywhere below.
--}}

@php
    $totalQuranAyahs = 6236;
    $ayahsRead = $stats['totalAyahsRead'] ?? 0;
    $completion = $totalQuranAyahs ? round(($ayahsRead / $totalQuranAyahs) * 100, 1) : 0;
@endphp

{{-- Plan Card --}}
<div class="d-card">
    <h5 class="d-card-title">Your Plan</h5>

    <span class="d-badge d-badge-{{ $user->plan }}">
        {{ strtoupper($user->plan) }}
    </span>

    @if (!$user->isPremium())
        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm w-100 mt-3">
            Upgrade
        </a>
    @endif
</div>

{{-- Progress Card --}}
<div class="d-card">

    <h5 class="d-card-title">
        <i class="bi bi-graph-up" style="color:var(--emerald)"></i>
        Your Progress
    </h5>

    <div class="d-stats-grid d-stats-grid-3">

        <div class="d-stat-item">
            <div class="d-stat-number">{{ $stats['streak'] ?? 0 }}</div>
            <small class="d-stat-label">🔥 Day Streak</small>
        </div>

        <div class="d-stat-item">
            <div class="d-stat-number">{{ $stats['completedSurahs'] ?? 0 }}</div>
            <small class="d-stat-label">🕌 Surahs Done</small>
        </div>

        <div class="d-stat-item">
            <div class="d-stat-number">{{ $stats['storyCount'] ?? 0 }}</div>
            <small class="d-stat-label">📚 Stories Read</small>
        </div>

    </div>

    <div class="d-journey-box">
        <p class="d-journey-title">📖 Quran Journey</p>
        <p class="d-journey-count">{{ number_format($ayahsRead) }} / {{ number_format($totalQuranAyahs) }} Ayahs</p>

        <div class="d-progress">
            <div class="d-progress-fill" style="width: {{ $completion }}%"></div>
        </div>

        <p class="d-journey-percent">{{ $completion }}% Complete</p>

        {{-- ✅ Add this --}}
        <a href="{{ route('quran.my-progress') }}" class="btn-emerald btn btn-sm w-100 mt-3" style="font-size:0.78rem">
            <i class="bi bi-graph-up-arrow me-1"></i> View Full Progress
        </a>
    </div>

</div>

@push('styles')
    <style>
        .d-stats-grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }
    </style>
@endpush

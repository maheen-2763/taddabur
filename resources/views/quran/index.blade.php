@extends('layouts.app')
@section('title', 'The Holy Quran — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-index.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="quran-index-page">

        <div class="quran-hero">
            <div class="bismillah-calligraphy">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <p class="bismillah-sub">In the name of Allah, the Most Gracious, the Most Merciful</p>

            <div class="quran-stats">
                <div><span class="stat-num">114</span><span class="stat-label">surahs</span></div>
                <div><span class="stat-num">6236</span><span class="stat-label">ayahs</span></div>
                <div><span class="stat-num">30</span><span class="stat-label">juz</span></div>
                <div><span class="stat-num">{{ $completedCount }}</span><span class="stat-label">completed</span></div>
            </div>

        </div>


        <div class="search-bar-wrapper">
            <button type="button" class="js-search-toggle search-icon-btn" aria-label="Search surahs"
                aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </button>
            <input type="text" id="surahSearch" class="js-surah-search surah-search-input"
                placeholder="Search surah by name or number..." autocomplete="off">
        </div>

        <div class="filter-chips">
            <button class="js-filter-chip chip active" data-filter="all" aria-label="All Surahs">
                <i class="bi bi-grid-3x3-gap"></i>
                <span class="chip-label">All</span>
            </button>
            <button class="js-filter-chip chip" data-filter="progress" aria-label="In Progress">
                <i class="bi bi-hourglass-split"></i>
                <span class="chip-label">In Progress</span>
            </button>
            <button class="js-filter-chip chip" data-filter="completed" aria-label="Completed">
                <i class="bi bi-check-circle"></i>
                <span class="chip-label">Completed</span>
            </button>
            <button class="js-filter-chip chip" id="revelationOrderChip" aria-label="Revelation Order">
                <i class="bi bi-clock-history"></i>
                <span class="chip-label">Revelation Order</span>
            </button>
        </div>

        <div id="surahListContainer">
            @foreach ($juzGroups as $group)
                <div class="juz-section" data-juz="{{ $group['juz'] }}">
                    <div class="juz-header">
                        <span class="juz-label">Juz {{ $group['juz'] }}</span>
                        @if ($group['title_ar'])
                            <span class="juz-title-ar">{{ $group['title_ar'] }}</span>
                        @endif
                        <span class="juz-title">{{ $group['title'] }}</span>
                        <x-progress-ring :percent="$group['juz_progress']" :size="30" />
                    </div>

                    <div class="surah-list">
                        @foreach ($group['surahs'] as $surah)
                            <a href="{{ route('quran.show', $surah->number) }}" class="js-surah-row surah-row"
                                data-name="{{ strtolower($surah->name_transliteration) }} {{ strtolower($surah->name_english) }} {{ $surah->number }}"
                                data-percent="{{ $surah->progress_percent }}">
                                <span class="surah-number">{{ $surah->number }}</span>
                                <div class="surah-name-ar">{{ $surah->name_arabic }}</div>
                                <div class="surah-info">
                                    <div class="surah-name-en">{{ $surah->name_transliteration }}</div>
                                    <div class="surah-ayah-count">
                                        @if ($surah->is_continuation)
                                            <span class="continuation-marker">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M12 19V5M5 12l7-7 7 7" />
                                                </svg>
                                                continued
                                            </span>
                                        @endif

                                        @if ($surah->progress_percent == 100)
                                            <span class="ayah-status-done">Completed</span>
                                        @else
                                            Ayah {{ $surah->start_ayah }}–{{ $surah->end_ayah }}
                                            @if ($surah->has_progress)
                                                <span class="ayah-resume-tag"> ·
                                                    {{ $surah->read_count_in_slice }} read </span>
                                            @endif
                                            <span class="ayah-count-total">of {{ $surah->ayah_count }}</span>
                                        @endif
                                    </div>
                                </div>
                                <x-progress-ring :percent="$surah->progress_percent" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ✅ NAYA — Revelation-order flat list --}}
        <div id="revelationOrderContainer" style="display:none">

            <p class="revelation-order-note"
                style="font-size:0.78rem; color:var(--muted); padding:0.5rem 0 1rem; text-align:center">
                Traditional chronological order of revelation, based on the narration of Ibn Abbas (Al-Itqan).
            </p>

            <div class="surah-list">
                @foreach ($revelationOrderList as $surah)
                    <a href="{{ route('quran.show', $surah->number) }}" class="surah-row"
                        data-percent="{{ $surah->progress_percent }}">
                        <span class="surah-number" title="Revealed #{{ $surah->revelation_order }}">
                            {{ $surah->revelation_order }}
                        </span>
                        <div class="surah-name-ar">{{ $surah->name_arabic }}</div>
                        <div class="surah-info">
                            <div class="surah-name-en">{{ $surah->name_transliteration }}</div>
                            <div class="surah-ayah-count">
                                {{ ucfirst($surah->revelation_type) }} · {{ $surah->ayah_count }} ayahs
                            </div>
                        </div>
                        <x-progress-ring :percent="$surah->progress_percent" />
                    </a>
                @endforeach
            </div>
        </div>

        <p class="no-results-msg" id="noResultsMsg" style="display:none">No Surah Completed Yet.</p>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/quran-index.js') }}?v={{ time() }}"></script>
@endpush

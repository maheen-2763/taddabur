{{-- resources/views/quran/search.blade.php --}}

@extends('layouts.app')
@section('title', $query ? 'Search: ' . $query . ' — Quran' : 'Search the Quran — Taddabur')

@push('styles')
    <style>
        /* ════════════════════════════════════
       SEARCH HERO
    ════════════════════════════════════ */
        .search-hero {
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0f2d18 100%);
            padding: 3rem 1rem 2.5rem;
            text-align: center;
        }

        .search-hero-title {
            font-family: var(--font-arabic);
            font-size: clamp(1.5rem, 4vw, 2.2rem);
            color: var(--gold-light);
            direction: rtl;
            display: block;
            margin-bottom: 0.25rem;
            line-height: 1.8;
        }

        .search-hero-sub {
            font-family: var(--font-heading);
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
            margin-bottom: 1.75rem;
            display: block;
        }

        /* Search box */
        .search-box-wrap {
            max-width: 560px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(201, 150, 58, 0.4);
            border-radius: 50px;
            padding: 0.75rem 1.25rem 0.75rem 3rem;
            color: white;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .search-input:focus {
            border-color: var(--gold);
            background: rgba(255, 255, 255, 0.12);
        }

        .search-icon-left {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(201, 150, 58, 0.6);
            font-size: 0.9rem;
        }

        .search-btn {
            position: absolute;
            right: 0.4rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gold);
            color: #1A1A2E;
            border: none;
            border-radius: 50px;
            padding: 0.4rem 1.1rem;
            font-size: 0.8rem;
            font-family: var(--font-heading);
            cursor: pointer;
            transition: background 0.2s;
        }

        .search-btn:hover {
            background: var(--gold-light);
        }

        /* ════════════════════════════════════
       RESULTS AREA
    ════════════════════════════════════ */
        .results-wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }

        /* Results count bar */
        .results-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .results-count {
            font-family: var(--font-heading);
            font-size: 0.82rem;
            color: var(--muted);
            letter-spacing: 0.05em;
        }

        .results-count span {
            color: var(--emerald);
            font-weight: 600;
        }

        /* ════════════════════════════════════
       RESULT CARD
    ════════════════════════════════════ */
        .result-card {
            background: var(--cream-dark);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .result-card:hover {
            border-color: var(--gold);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        /* Left accent line */
        .result-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--gold);
            border-radius: 3px 0 0 3px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .result-card:hover::before {
            opacity: 1;
        }

        /* Surah reference */
        .result-ref {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }

        .result-surah-badge {
            background: rgba(27, 94, 59, 0.1);
            color: var(--emerald);
            border: 1px solid rgba(27, 94, 59, 0.15);
            border-radius: 50px;
            padding: 0.2rem 0.75rem;
            font-family: var(--font-heading);
            font-size: 0.72rem;
            letter-spacing: 0.04em;
        }

        .result-ayah-num {
            font-size: 0.72rem;
            color: var(--muted);
            font-family: var(--font-heading);
        }

        /* Arabic text */
        .result-arabic {
            font-family: 'Amiri', serif;
            font-size: 1.3rem;
            direction: rtl;
            text-align: right;
            color: var(--emerald-dark);
            line-height: 2;
            margin-bottom: 0.5rem;
        }

        /* Translation text */
        .result-translation {
            font-size: 0.9rem;
            color: var(--ink-soft);
            line-height: 1.8;
            font-style: italic;
        }

        /* Keyword highlight inside translation */
        .result-translation mark {
            background: rgba(201, 150, 58, 0.25);
            color: var(--gold-dark);
            border-radius: 3px;
            padding: 0 2px;
            font-style: normal;
            font-weight: 500;
        }

        /* Read button */
        .result-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 0.75rem;
            padding-top: 0.65rem;
            border-top: 1px solid var(--border);
        }

        .result-read-btn {
            font-family: var(--font-heading);
            font-size: 0.72rem;
            color: var(--emerald);
            text-decoration: none;
            letter-spacing: 0.04em;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .result-read-btn:hover {
            color: var(--gold);
        }

        /* ════════════════════════════════════
       EMPTY / INITIAL STATES
    ════════════════════════════════════ */
        .search-empty {
            text-align: center;
            padding: 4rem 1rem;
            color: var(--muted);
        }

        .search-empty-icon {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .search-empty-title {
            font-family: var(--font-heading);
            font-size: 1rem;
            color: var(--ink-soft);
            margin-bottom: 0.5rem;
        }

        /* ════════════════════════════════════
       PAGINATION
    ════════════════════════════════════ */
        .quran-pagination {
            display: flex;
            justify-content: center;
            gap: 0.3rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .quran-pagination .page-link {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            color: var(--ink-soft);
            text-decoration: none;
            transition: all 0.15s;
            font-family: var(--font-heading);
        }

        .quran-pagination .page-link:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        .quran-pagination .page-link.active {
            background: var(--emerald);
            border-color: var(--emerald);
            color: white;
        }

        .quran-pagination .page-link.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')

    {{-- ════════════════════════════════════
     SEARCH HERO
════════════════════════════════════ --}}
    <div class="search-hero">

        <span class="search-hero-title">
            وَلَقَدْ يَسَّرْنَا ٱلْقُرْءَانَ لِلذِّكْرِ
        </span>
        <span class="search-hero-sub">
            And We have certainly made the Quran easy to remember — Al-Qamar 54:17
        </span>

        {{-- Search form --}}
        <form action="{{ route('quran.search') }}" method="GET" class="search-box-wrap">

            <i class="bi bi-search search-icon-left"></i>

            <input type="text" name="q" id="searchInput" class="search-input" value="{{ $query }}"
                placeholder="Search by meaning, word or topic..." autocomplete="off" autofocus>

            <button type="submit" class="search-btn">
                Search
            </button>

        </form>

        {{-- Quick suggestions --}}
        @if (!$query)
            <div class="mt-3" style="font-size:0.78rem; color:rgba(255,255,255,0.35)">
                Try:
                @foreach (['mercy', 'patience', 'prayer', 'paradise', 'forgiveness'] as $suggestion)
                    <a href="{{ route('quran.search', ['q' => $suggestion]) }}"
                        style="color:rgba(201,150,58,0.6);
                      text-decoration:none;
                      margin: 0 0.3rem">
                        {{ $suggestion }}
                    </a>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ════════════════════════════════════
     RESULTS
════════════════════════════════════ --}}
    <div class="results-wrap">

        {{-- ── State 1: No query yet ─────────────── --}}
        @if (!$query)
            <div class="search-empty">
                <span class="search-empty-icon">
                    <i class="bi bi-search"></i>
                </span>
                <p class="search-empty-title">
                    Search the words of Allah
                </p>
                <p style="font-size:0.85rem">
                    Enter a word or topic above to find
                    relevant ayahs from the Quran.
                </p>
            </div>

            {{-- ── State 2: Query too short ──────────── --}}
        @elseif(strlen($query) < 3)
            <div class="search-empty">
                <span class="search-empty-icon">
                    <i class="bi bi-type"></i>
                </span>
                <p class="search-empty-title">
                    Please enter at least 3 characters
                </p>
                <p style="font-size:0.85rem">
                    Try a longer word like
                    <a href="{{ route('quran.search', ['q' => 'mercy']) }}" style="color:var(--emerald)">mercy</a>
                    or
                    <a href="{{ route('quran.search', ['q' => 'patience']) }}" style="color:var(--emerald)">patience</a>
                </p>
            </div>

            {{-- ── State 3: No results found ─────────── --}}
        @elseif($results && $results->isEmpty())
            <div class="search-empty">
                <span class="search-empty-icon">
                    <i class="bi bi-emoji-frown"></i>
                </span>
                <p class="search-empty-title">
                    No results found for "{{ $query }}"
                </p>
                <p style="font-size:0.85rem">
                    Try a different word or check the spelling.
                </p>
                <a href="{{ route('quran.search') }}" class="btn-emerald btn mt-2" style="font-size:0.85rem">
                    Clear Search
                </a>
            </div>

            {{-- ── State 4: Show results ───────────────── --}}
        @elseif($results && $results->isNotEmpty())
            {{-- Results meta --}}
            <div class="results-meta">
                <span class="results-count">
                    <span>{{ $results->total() }}</span>
                    {{ Str::plural('result', $results->total()) }}
                    for "{{ $query }}"
                </span>
                <a href="{{ route('quran.search') }}" style="font-size:0.78rem; color:var(--muted); text-decoration:none">
                    <i class="bi bi-x me-1"></i>Clear
                </a>
            </div>

            {{-- Result cards --}}
            @foreach ($results as $result)
                @php
                    $ayah = $result->ayah;
                    $surah = $ayah?->surah;

                    if (!$ayah || !$surah) {
                        continue;
                    }

                    // Highlight the keyword in translation
                    $highlightedText = preg_replace(
                        '/(' . preg_quote($query, '/') . ')/iu',
                        '<mark>$1</mark>',
                        e($result->text),
                    );
                @endphp

                <div class="result-card">

                    {{-- Surah reference --}}
                    <div class="result-ref">
                        <span class="result-surah-badge">
                            {{ $surah->name_transliteration }}
                        </span>
                        <span class="result-ayah-num">
                            {{ $surah->number }}:{{ $ayah->number }}
                        </span>
                        @if ($ayah->sajda)
                            <span style="font-size:0.7rem; color:var(--gold)">
                                ۩ Sajda
                            </span>
                        @endif
                    </div>

                    {{-- Arabic text --}}
                    <p class="result-arabic">
                        {{ $ayah->text_arabic }}
                    </p>

                    {{-- Translation with keyword highlighted --}}
                    <p class="result-translation">
                        {!! $highlightedText !!}
                    </p>

                    {{-- Footer --}}
                    <div class="result-footer">
                        <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}"
                            class="result-read-btn">
                            Read in context
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                </div>
            @endforeach

            {{-- Pagination --}}
            @if ($results->hasPages())
                <div class="quran-pagination">

                    {{-- Previous --}}
                    @if ($results->onFirstPage())
                        <span class="page-link disabled">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $results->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($results->getUrlRange(max(1, $results->currentPage() - 2), min($results->lastPage(), $results->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                            class="page-link {{ $page === $results->currentPage() ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Next --}}
                    @if ($results->hasMorePages())
                        <a href="{{ $results->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-link disabled">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif

                </div>

                {{-- Showing x to y of z results --}}
                <p class="text-center mt-2" style="font-size:0.75rem; color:var(--muted)">
                    Showing {{ $results->firstItem() }}–{{ $results->lastItem() }}
                    of {{ $results->total() }} results
                </p>
            @endif

        @endif

    </div>

@endsection

@push('scripts')
    <script>
        // Live search — small improvement
        // Press Enter to submit
        document.getElementById('searchInput')
            ?.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.focus();
                }
            });
    </script>
@endpush

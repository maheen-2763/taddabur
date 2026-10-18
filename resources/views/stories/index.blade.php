@extends('layouts.app')
@section('title', 'Prophet Stories — Taddabur')

@push('styles')
    <style>
        /* ── Story card ── */
        .story-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .story-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        /* ── Card cover ── */
        .story-card-cover {
            height: 175px;
            background: linear-gradient(150deg, var(--emerald-dark), var(--emerald));
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-end;
            padding: 1.1rem 1.25rem;
        }

        .story-card-cover::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .story-card-cover-arabic {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 5.5rem;
            color: rgba(255, 255, 255, 0.08);
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }

        .story-card-cover-prophet {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.2rem;
            position: relative;
            z-index: 1;
        }

        .story-card-cover-title {
            font-family: var(--font-heading);
            font-size: 0.92rem;
            color: white;
            line-height: 1.3;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        /* ── Card body ── */
        .story-card-body {
            padding: 1rem 1.25rem 1.25rem;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .story-card-summary {
            font-size: 0.82rem;
            color: var(--ink-soft);
            line-height: 1.65;
            flex: 1;
            margin-bottom: 0.85rem;
        }

        .story-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        /* ── Chips ── */
        .story-chip {
            display: inline-flex;
            align-items: center;
            font-size: 0.68rem;
            padding: 0.2rem 0.55rem;
            border-radius: 50px;
            font-weight: 500;
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
        }

        .difficulty-badge {
            width: fit-content;
            /* ✅ ye force karega ke sirf content jitni width le */
            margin-bottom: 0.6rem;
        }

        [data-bs-theme="dark"] .story-chip {
            color: var(--emerald-light);
        }

        .progress-status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.35rem;
        }

        .progress-status-text {
            font-size: 0.7rem;
            color: var(--emerald-light);
            font-weight: 500;
        }

        .progress-status-percent {
            font-size: 0.7rem;
            color: var(--emerald-light);
            font-weight: 700;
            /* percentage ko thoda bold — visually zyada "achievement" feel */
        }

        .progress-bar-islamic {
            height: 5px;
            /* thoda thicker, zyada visible */
            border-radius: 50px;
            background: rgba(27, 94, 59, 0.1);
            overflow: hidden;
        }

        .progress-bar-islamic-fill {
            height: 100%;
            background: var(--emerald);
            border-radius: 50px;
            transition: width 0.3s ease;
            /* smooth animation jab value change ho */
        }

        [data-bs-theme="dark"] .progress-bar-islamic-fill {
            background: var(--emerald-light);
        }


        .progress-section {
            background: rgba(27, 94, 59, 0.045);
            border-radius: var(--radius);
            padding: 0.55rem 0.7rem;
            margin-bottom: 0.85rem;
        }

        [data-bs-theme="dark"] .progress-section {
            background: rgba(255, 255, 255, 0.04);
        }


        /* ── Sidebar filter ── */
        .filter-item {
            font-size: 0.85rem;
            color: var(--ink-soft);
            text-decoration: none;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius);
            display: block;
            transition: all 0.18s;
        }

        .filter-item:hover {
            background: rgba(27, 94, 59, 0.05);
            /* thoda lighter than active */
        }

        .filter-item.active {
            background: rgba(27, 94, 59, 0.08);
            border-left: 3px solid var(--emerald);
            /* active ko extra clear banane ke liye */
            padding-left: calc(0.75rem - 3px);
        }

        [data-bs-theme="dark"] .filter-item:hover,
        [data-bs-theme="dark"] .filter-item.active {
            color: var(--emerald-light);
        }

        [data-bs-theme="dark"] .filter-item .honorific {
            color: var(--emerald-light);
        }

        .arabic-symbol {
            color: var(--emerald);
            font-size: 0.8rem;
        }

        [data-bs-theme="dark"] .arabic-symbol {
            color: var(--emerald-light);
        }


        .filter-item .honorific {
            font-size: 0.68rem;
            color: var(--emerald);
            /* opacity: 0.75; */
            font-weight: 500;
            margin-left: 0.2rem;
        }

        .filter-item .story-count {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 400;
            margin-left: 0.25rem;
        }

        /* ── Mobile filter pills ── */
        .filter-pill {
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.35rem 1rem;
            font-size: 0.82rem;
            background: transparent;
            color: var(--ink-soft);
            text-decoration: none;
            transition: all 0.18s;
        }

        .filter-pill:hover,
        .filter-pill.active {
            background: var(--emerald);
            border-color: var(--emerald);
            color: white;
        }

        /* ── Empty state ── */
        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: rgba(27, 94, 59, 0.07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .eyebrow-text {
            color: var(--gold);
            font-family: var(--font-heading);
            font-size: 0.78rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .header-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .header-divider::before,
        .header-divider::after {
            content: '';
            width: 40px;
            height: 1px;
            background: var(--border);
        }

        .header-divider-icon {
            color: var(--gold);
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .mobile-prophet-select {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--cream);
            color: var(--ink);
            font-family: var(--font-heading);
            font-size: 0.92rem;
            padding: 0.6rem 1rem;
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%231B5E3B' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }

        .mobile-prophet-select:focus {
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px rgba(27, 94, 59, 0.1);
            outline: none;
        }

        [data-bs-theme="dark"] .mobile-prophet-select {
            background-color: var(--cream-dark, #1e1e1e);
            color: var(--ink);
            border-color: var(--border);
        }

        .filter-pills-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.6rem;
        }

        .filter-pill {
            text-align: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .filter-soon-badge {
            font-size: 0.62rem;
            background: rgba(180, 130, 40, 0.08);
            color: var(--gold);
            border-radius: 50px;
            padding: 0.1rem 0.45rem;
            margin-left: 0.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="container py-5">

            {{-- ═══════════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════════════ --}}
            <div class="mb-5 text-center">
                <p class="eyebrow-text">
                    Guided by the Quran
                </p>
                <h1 class="heading-font mb-2" style="font-size:clamp(1.8rem,4vw,2.5rem)">
                    Prophet Stories
                </h1>
                <p class="text-muted mx-auto" style="max-width:520px; line-height:1.8; font-size:0.92rem">
                    Explore the lives of the prophets — their trials, miracles, and timeless
                    lessons, sourced from the Quran, authentic Hadith, and scholarly tradition.
                </p>
                {{-- ✅ Divider --}}
                <div class="header-divider">
                    <span class="header-divider-icon">✦</span>
                </div>
            </div>

            <div class="row g-4">

                {{-- ═══════════════════════════════════════════════
             LEFT — Sidebar filters (desktop only)
        ════════════════════════════════════════════════ --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div style="position:sticky; top:80px">

                        {{-- By Prophet --}}
                        <div class="card-islamic p-3 mb-3">
                            <h6 class="heading-font mb-3"
                                style="font-size:0.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.09em">
                                By Prophet
                            </h6>

                            <a href="{{ route('stories.index') }}"
                                class="filter-item mb-1 {{ !request('prophet') ? 'active' : '' }}">
                                All Prophets
                            </a>

                            @foreach ($prophets as $p)
                                <a href="{{ route('stories.index', array_merge(request()->query(), ['prophet' => $p->slug])) }}"
                                    class="filter-item mb-1 {{ request('prophet') === $p->slug ? 'active' : '' }}">

                                    {{ $p->name_transliteration }}

                                    @if ($p->slug === 'muhammad')
                                        <bdi><span class="arabic-symbol">(ﷺ)</span></bdi>
                                    @else
                                        <span class="honorific">{{ $p->honorific ?? 'AS' }}</span>
                                    @endif

                                    @if ($p->stories_count > 0)
                                        <span class="story-count">({{ $p->stories_count }})</span>
                                    @else
                                        <span class="filter-soon-badge">Soon</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>



                        {{-- By Difficulty --}}
                        <div class="card-islamic p-3">
                            <h6 class="heading-font mb-3"
                                style="font-size:0.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.09em">
                                Difficulty
                            </h6>
                            @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                                <a href="{{ route('stories.index', array_merge(request()->query(), ['difficulty' => $level])) }}"
                                    class="filter-item mb-1 {{ request('difficulty') === $level ? 'active' : '' }}">
                                    {{ ucfirst($level) }}
                                    @if (request('difficulty') === $level && isset($difficultyCounts[$level]))
                                        <span class="text-muted" style="font-size:0.72rem">
                                            ({{ $difficultyCounts[$level] }})
                                        </span>
                                    @endif
                                </a>
                            @endforeach


                            @if (request()->hasAny(['difficulty', 'prophet', 'category']))
                                <hr class="my-2" style="border-color:var(--border)">
                                <a href="{{ route('stories.index') }}" class="filter-item"
                                    style="color:var(--muted); font-size:0.8rem">
                                    <i class="bi bi-x me-1"></i>Clear filters
                                </a>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════
             RIGHT — Stories grid
        ════════════════════════════════════════════════ --}}
                <div class="col-lg-9">

                    {{-- Mobile filter pills --}}
                    <div class="filter-pills-wrapper mb-4 d-lg-none">
                        <a href="{{ route('stories.index') }}"
                            class="filter-pill {{ !request()->hasAny(['difficulty', 'category']) ? 'active' : '' }}">
                            All
                        </a>
                        @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                            <a href="{{ route('stories.index', array_merge(request()->query(), ['difficulty' => $level])) }}"
                                class="filter-pill {{ request('difficulty') === $level ? 'active' : '' }}">
                                {{ ucfirst($level) }}
                                @if (request('difficulty') === $level && isset($difficultyCounts[$level]))
                                    ({{ $difficultyCounts[$level] }})
                                @endif
                            </a>
                        @endforeach
                    </div>

                    {{-- Mobile Prophet filter --}}
                    <div class="mb-3 d-lg-none">
                        <select class="form-select mobile-prophet-select" onchange="window.location.href=this.value">
                            <option value="{{ route('stories.index') }}" {{ !request('prophet') ? 'selected' : '' }}>
                                All Prophets
                            </option>
                            @foreach ($prophets as $p)
                                <option
                                    value="{{ route('stories.index', array_merge(request()->query(), ['prophet' => $p->slug])) }}"
                                    {{ request('prophet') === $p->slug ? 'selected' : '' }}>
                                    {{ $p->name_transliteration }}
                                    @if ($p->slug === 'muhammad')
                                        {{ "\u{2066}" }}(ﷺ){{ "\u{2069}" }}
                                    @else
                                        {{ $p->honorific ?? 'AS' }}
                                    @endif
                                    @if ($p->stories_count > 0)
                                        - ({{ $p->stories_count }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Results count --}}
                    @if ($stories->total() > 0)
                        <p class="text-muted small mb-4">
                            Showing {{ $stories->firstItem() }}–{{ $stories->lastItem() }}
                            of {{ $stories->total() }} {{ Str::plural('story', $stories->total()) }}
                        </p>
                    @endif

                    {{-- ── Empty states ── --}}
                    @if ($stories->isEmpty())
                        <div class="text-center py-5">
                            <div class="empty-state-icon">
                                <i class="bi bi-hourglass-split" style="font-size:2rem; color:var(--emerald-dark)"></i>
                            </div>
                            @if (request('prophet') && request('difficulty'))
                                @if ($actualDifficulty)
                                    <h5 class="heading-font mb-2">
                                        Available at a Different Level
                                    </h5>
                                    <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                        {{ $prophet->name_transliteration ?? 'This prophet' }}
                                        @if (($prophet->slug ?? null) === 'muhammad')
                                            <bdi><span class="arabic-symbol">(ﷺ)</span></bdi>
                                        @else
                                            <span class="honorific">{{ $prophet->honorific ?? 'AS' }}</span>
                                        @endif
                                        story is written at the
                                        <strong>{{ ucfirst($actualDifficulty) }}</strong> level based on the
                                        depth of authentic detail available.
                                    </p>
                                    <a href="{{ route('stories.index', ['prophet' => request('prophet'), 'difficulty' => $actualDifficulty]) }}"
                                        class="btn-emerald btn">
                                        View {{ ucfirst($actualDifficulty) }} Story
                                    </a>
                                @else
                                    <h5 class="heading-font mb-2">No Story Available Yet</h5>
                                    <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                        We don't have a story for this prophet yet, In Shaa Allah, soon.
                                    </p>
                                    <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                        View All Stories
                                    </a>
                                @endif
                            @elseif (request('difficulty'))
                                <h5 class="heading-font mb-2">
                                    No {{ ucfirst(request('difficulty')) }} Stories Yet
                                </h5>
                                <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                    Every story reflects the depth of detail actually available — from the
                                    Quran, authentic Hadith, or scholarly tradition. This level isn't
                                    available yet.
                                </p>
                                <a href="{{ route('stories.index', ['difficulty' => 'beginner']) }}"
                                    class="btn-emerald btn">
                                    Explore Beginner Stories
                                </a>
                            @elseif (request('prophet'))
                                <h5 class="heading-font mb-2">Limited Source Material</h5>
                                <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                    The available narrations for this prophet don't yet support a story
                                    at this depth. Explore other available stories for now.
                                </p>
                                <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                    View All Stories
                                </a>
                            @else
                                <h5 class="heading-font mb-2">More Stories Coming Soon</h5>
                                <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                    We are building a complete collection of Prophet stories, In Shaa Allah.
                                </p>
                                <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                    Explore Available Stories
                                </a>
                            @endif

                            {{-- ── Story cards grid ── --}}
                        @else
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                                @foreach ($stories as $story)
                                    <div class="col">
                                        <a href="{{ route('stories.show', $story->slug) }}" class="story-card">

                                            {{-- Cover --}}
                                            @if ($story->cover_image)
                                                <img src="{{ asset('storage/' . $story->cover_image) }}"
                                                    alt="{{ $story->title }}"
                                                    style="height:175px; width:100%; object-fit:cover">
                                            @else
                                                <div class="story-card-cover">
                                                    {{-- Ghost Arabic watermark --}}
                                                    @if ($story->prophet)
                                                        <span class="story-card-cover-arabic">
                                                            {{ $story->prophet->name_arabic }}
                                                        </span>
                                                    @endif

                                                    {{-- Prophet name --}}
                                                    @if ($story->prophet)
                                                        <div class="story-card-cover-prophet">
                                                            {{ $story->prophet->display_name }}
                                                        </div>
                                                    @endif

                                                    {{-- Story title --}}
                                                    <div class="story-card-cover-title">
                                                        {{ Str::limit($story->title, 55) }}
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Body --}}
                                            <div class="story-card-body">

                                                @php
                                                    $difficultyIcon = match ($story->difficulty) {
                                                        'beginner' => 'bi-reception-2',
                                                        'intermediate' => 'bi-reception-3',
                                                        'advanced' => 'bi-reception-4',
                                                        default => 'bi-reception-1',
                                                    };
                                                @endphp

                                                <span class="story-chip difficulty-badge d-inline-flex">
                                                    <i class="bi {{ $difficultyIcon }} me-1"></i>
                                                    {{ ucfirst($story->difficulty) }}
                                                </span>

                                                {{-- ✅ NEW: Progress indicator --}}
                                                @auth
                                                    @if (isset($story->user_progress) && $story->user_progress['started'])
                                                        <div class="progress-section">
                                                            <div class="progress-status-row">
                                                                <span class="progress-status-text">
                                                                    {{ $story->user_progress['completed'] }}/{{ $story->user_progress['total'] }}
                                                                    chapters
                                                                </span>
                                                                <span class="progress-status-percent">
                                                                    {{ $story->user_progress['percentage'] }}%
                                                                </span>
                                                            </div>
                                                            <div class="progress-bar-islamic">
                                                                <div class="progress-bar-islamic-fill"
                                                                    style="width:{{ $story->user_progress['percentage'] }}%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <p
                                                        style="font-size:0.7rem; color:var(--muted); font-style:italic; margin-bottom:0.5rem">
                                                        <i class="bi bi-box-arrow-in-right me-1"></i>Sign in to track progress
                                                    </p>
                                                @endauth

                                                {{-- Summary --}}
                                                @if ($story->summary)
                                                    <p class="story-card-summary">
                                                        {{ Str::limit($story->summary, 90) }}
                                                    </p>
                                                @endif

                                                {{-- Footer --}}
                                                <div class="story-card-footer">
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <span class="story-chip">
                                                            <i
                                                                class="bi {{ ($story->chapters_count ?? $story->chapters->count()) > 1 ? 'bi-journals' : 'bi-journal-text' }} me-1"></i>
                                                            {{ $story->chapters_count ?? $story->chapters->count() }}
                                                            {{ Str::plural('chapter', $story->chapters_count ?? $story->chapters->count()) }}
                                                        </span>
                                                        @if ($story->read_time_minutes)
                                                            <span class="story-chip">
                                                                <i class="bi bi-hourglass-split me-1"></i>
                                                                {{ $story->read_time_minutes }}m
                                                            </span>
                                                        @endif
                                                    </div>


                                                    <span
                                                        style="font-size:0.78rem; color:var(--emerald-light); font-weight:500">Read
                                                        →</span>

                                                </div>

                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination --}}
                            @if ($stories->hasPages())
                                <div class="d-flex justify-content-center mt-5">
                                    {{ $stories->links() }}
                                </div>
                            @endif
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection

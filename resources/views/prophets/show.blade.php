@extends('layouts.app')
@section('title', $prophet->display_name . ' — Taddabur')

@push('styles')
    <style>
        /* ── Hero ── */
        .prophet-hero {
            border-radius: var(--radius);
            overflow: hidden;
            /* ✅ already hai, good */
            background: linear-gradient(150deg, var(--emerald-dark) 0%, var(--emerald) 100%);
            position: relative;
            max-width: 100%;
            /* ✅ add karo */
        }

        .prophet-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* ── Stats strip ── */
        .stat-item {
            text-align: center;
            padding: 0.5rem 1.5rem;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            line-height: 1;
            font-family: var(--font-heading);
        }

        .stat-label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.65);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 0.25rem;
        }

        /* ── Timeline ── */
        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gold-light);
            flex-shrink: 0;
            margin-top: 5px;
            opacity: 0.85;
        }

        /* ── Meta chips ── */
        .meta-chip {
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
            border-radius: 50px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        [data-bs-theme="dark"] .meta-chip {
            color: var(--emerald-light);
        }




        /* ── Story card ── */
        .story-card-inner {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .story-card-inner:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
        }

        /* ── Decorative Arabic watermark ── */
        .arabic-watermark {
            font-family: 'Amiri', serif;
            font-size: 8rem;
            color: rgba(255, 255, 255, 0.05);
            position: absolute;
            bottom: -1rem;
            right: 1.5rem;
            line-height: 2.1;
            pointer-events: none;
            user-select: none;
        }

        /* ── Mobile responsiveness ── */
        @media (max-width: 991px) {
            .arabic-watermark {
                font-size: 5rem;
                bottom: -0.5rem;
                right: 1rem;
            }
        }

        @media (max-width: 576px) {
            .prophet-hero .col-lg-6:first-child {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .prophet-hero .col-lg-6 {
                padding: 1.5rem !important;
            }

            .arabic-watermark {
                display: none;
                /* clutters small screens, hides text underneath */
            }

            .stat-item {
                padding: 0.4rem 0.9rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .stat-label {
                font-size: 0.62rem;
            }

            /* Arabic name block on hero */
            div[style*="font-size:2.8rem"] {
                font-size: 2rem !important;
            }

            h1.heading-font {
                font-size: 1.6rem !important;
            }
        }

        /* ── Timeline source pills (matches Quran-card pill style) ── */
        .source-pill {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 580;
            padding: 0.10rem 0.55rem;
            border-radius: 50px;
            letter-spacing: 0.03em;
            margin-left: 0.35rem;
            vertical-align: middle;
            border: 1px solid transparent;
        }

        .source-pill-quran {
            background: rgba(143, 217, 168, 0.15);
            color: #8FD9A8;
            border-color: rgba(143, 217, 168, 0.3);
        }

        .source-pill-hadith {
            background: rgba(143, 199, 232, 0.15);
            color: #8FC7E8;
            border-color: rgba(143, 199, 232, 0.3);
        }

        .source-pill-tradition {
            background: rgba(230, 190, 110, 0.15);
            color: var(--gold-light);
            border-color: rgba(230, 190, 110, 0.3);
        }

        @media (max-width: 576px) {
            .prophet-hero .col-lg-6:first-child {
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .prophet-hero .col-lg-6 {
                padding: 1.5rem !important;
            }

            .arabic-watermark {
                display: none;
            }

            .stat-item {
                padding: 0.4rem 0.7rem;
                border-right: none !important;
                /* ✅ wrap hone pe border ajeeb nahi lagega */
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                flex: 1 1 33%;
                /* ✅ 3 stats ek row mein equal-width rahenge */
            }

            .stat-number {
                font-size: 1.4rem;
            }

            .stat-label {
                font-size: 0.6rem;
            }

            div[style*="font-size:2.8rem"] {
                font-size: 1.8rem !important;
            }

            h1.heading-font {
                font-size: 1.6rem !important;
            }

            /* ✅ NEW — Timeline events stack better on mobile */
            .prophet-hero .d-flex.flex-column.gap-2>.d-flex.align-items-start {
                flex-wrap: wrap;
            }

            .source-pill {
                margin-left: 0;
                margin-top: 0.25rem;
                display: inline-block;
            }

            /* ✅ NEW — Period badge doesn't overflow on small screens */
            .period-badge {
                font-size: 0.7rem !important;
                white-space: normal;
            }

            /* ✅ NEW — Story cards tighter on mobile */
            .story-card-inner {
                padding: 1.1rem;
            }

            .story-card-inner h5 {
                font-size: 0.92rem !important;
            }

            .meta-chip {
                font-size: 0.72rem;
                padding: 0.22rem 0.6rem;
            }

            /* ✅ NEW — breadcrumb doesn't wrap awkwardly */
            nav[aria-label="breadcrumb"] {
                font-size: 0.78rem !important;
            }
        }

        /* ✅ NEW — Extra-small phones */
        @media (max-width: 360px) {
            .prophet-hero .col-lg-6 {
                padding: 1.1rem !important;
            }

            div[style*="font-size:2.8rem"] {
                font-size: 1.5rem !important;
            }

            h1.heading-font {
                font-size: 1.35rem !important;
            }

            .stat-number {
                font-size: 1.2rem;
            }
        }

        /* ── Timeline (Grid-based for reliable mobile wrapping) ── */
        .timeline-item {
            display: grid;
            grid-template-columns: 14px 1fr;
            column-gap: 0.5rem;
            align-items: start;
            border-bottom: 0.1rem solid var(--gold-light);
            margin-bottom: 0.5rem;
        }

        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gold-light);
            margin-top: 5px;
            opacity: 0.85;
            justify-self: center;
        }



        @media (max-width: 576px) {
            .timeline-item {
                margin-bottom: 0.75rem;
                row-gap: 0.15rem;
            }

            .source-pill {
                margin-left: 0;
                margin-top: 0.3rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4" style="font-size:0.85rem">
            <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald-light)">
                <i class="bi bi-grid me-1"></i>All Prophets
            </a>
            <span class="mx-2 text-muted">/</span>
            <span class="text-muted">{!! $prophet->display_name_html !!}</span>
        </nav>

        {{-- ═══════════════════════════════════════════════
         HERO CARD
    ════════════════════════════════════════════════ --}}
        <div class="prophet-hero mb-5">

            {{-- Arabic watermark --}}
            <span class="arabic-watermark">{!! $prophet->name_arabic_html !!}</span>

            <div class="row g-0">

                {{-- LEFT: Identity --}}
                <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-center">

                    {{-- Order pill --}}
                    <span class="badge mb-3 align-self-start"
                        style="background:rgba(255,255,255,0.12);
                           color:rgba(255,255,255,0.85);
                           font-size:0.75rem;
                           padding:0.4rem 0.9rem;
                           border-radius:50px;
                           letter-spacing:0.05em">
                        Prophet {{ $prophet->order }} of 25
                    </span>

                    {{-- Name block --}}
                    <div class="mb-4">
                        <h1 class="heading-font mb-1"
                            style="color:white;
                               font-size:clamp(1.8rem,4vw,2.6rem);
                               line-height:1.2">
                            {!! $prophet->display_name_html !!}
                        </h1>
                        <p style="color:rgba(255,255,255,0.6); font-size:0.9rem; margin:0">
                            {!! \App\Helpers\ArabicHelper::wrapHonorifics($prophet->name_english) !!}
                        </p>
                    </div>

                    {{-- ✅ NEW — Time Period --}}
                    @if ($prophet->period)
                        <div class="mb-4">
                            <span class="period-badge" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="{{ \App\Helpers\PeriodGlossary::explain($prophet->period) }}"
                                style="font-size:0.75rem; color:rgba(255,255,255,0.55); background:rgba(255,255,255,0.08); padding:0.3rem 0.8rem; border-radius:50px; display:inline-flex; align-items:center; gap:0.4rem; cursor:help; border-bottom:1px dotted rgba(255,255,255,0.3)">
                                <i class="bi bi-clock-history"></i>{{ $prophet->period }}
                            </span>
                        </div>
                    @endif

                    {{-- Arabic name + title --}}
                    <div class="mb-4" style="text-align:right; direction:rtl">
                        <div
                            style="font-family:'Amiri','Scheherazade New',serif;
                                font-size:2.8rem;
                                color:white;
                                line-height:1.4;
                                margin-bottom:0.4rem">
                            {!! $prophet->name_arabic_html !!}
                        </div>
                        @if ($prophet->title_arabic)
                            <div
                                style="font-family:'Amiri','Scheherazade New',serif;
                                    font-size:1.1rem;
                                    color:var(--gold-light);
                                    line-height:1.6;
                                    margin-bottom:0.2rem">
                                {{ $prophet->title_arabic }}
                            </div>
                        @endif
                        @if ($prophet->title_transliteration)
                            <div
                                style="font-size:0.75rem;
                                    color:rgba(255,255,255,0.45);
                                    direction:ltr;
                                    text-align:right;
                                    margin-bottom:0">
                                {{ $prophet->title_transliteration }}
                            </div>
                        @endif
                    </div>

                    {{-- English title --}}
                    @if ($prophet->title)
                        <p
                            style="color:var(--gold-light);
                              font-size:0.9rem;
                              font-style:italic;
                              margin-bottom:1.25rem">
                            {!! $prophet->title_html !!}
                        </p>
                    @endif

                    {{-- Summary --}}
                    @if ($prophet->summary)
                        <p
                            style="color:rgba(255,255,255,0.75);
                              line-height:1.8;
                              font-size:0.9rem;
                              margin:0">
                            {!! $prophet->summary_html !!}
                        </p>
                    @endif

                </div>

                {{-- RIGHT: Stats + Timeline --}}
                <div class="col-lg-6 p-4 p-md-5" style="border-left:1px solid rgba(255,255,255,0.1)">

                    {{-- Stats strip --}}
                    <div class="d-flex justify-content-start mb-4 flex-wrap gap-0">

                        @if ($prophet->quran_mentions_count > 0)
                            <div class="stat-item ps-0">
                                <div class="stat-number">{{ $prophet->quran_mentions_count }}</div>
                                <div class="stat-label">Qur'an Mentions</div>
                            </div>
                        @endif

                        <div class="stat-item {{ $prophet->quran_mentions_count > 0 ? '' : 'ps-0' }}">
                            <div class="stat-number">{{ $prophet->stories_count }}</div>
                            <div class="stat-label">{{ Str::plural('Story', (int) $prophet->stories_count) }}</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-number">#{{ $prophet->order }}</div>
                            <div class="stat-label">of 25 Prophets</div>
                        </div>

                    </div>

                    {{-- Divider --}}
                    <div style="width:40px; height:1px; background:var(--gold-light); opacity:0.4; margin-bottom:1.25rem">
                    </div>

                    {{-- Timeline --}}
                    @if (!empty($prophet->timeline))
                        <p
                            style="font-size:0.7rem; color:rgba(255,255,255,0.45); text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.75rem">
                            Life Journey
                        </p>
                        <div>
                            @foreach ($prophet->timeline as $event)
                                @php $sourceMeta = config('content_sources.' . ($event['source'] ?? 'tradition')); @endphp
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div>
                                        <span style="font-size:0.83rem; color:rgba(255,255,255,0.88); font-weight:500">
                                            {{ $event['title'] }}
                                        </span>
                                        <span style="font-size:0.78rem; color:rgba(255,255,255,0.45)">
                                            · {{ $event['period'] }}
                                        </span>
                                        <span class="source-pill source-pill-{{ $event['source'] ?? 'tradition' }}">
                                            {{ $sourceMeta['label'] }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════
         STORIES SECTION
    ════════════════════════════════════════════════ --}}
        @if ($stories->isEmpty())
            <div class="text-center py-5">
                <div style="font-size:3rem; opacity:0.3; margin-bottom:1rem">📖</div>
                <h5 class="heading-font mb-2">Coming Soon, In Shaa Allah</h5>
                <p class="text-muted small" style="max-width:400px; margin:0 auto 1.5rem">
                    We are preparing authentic, Quran-sourced stories for <span
                        class="text-muted">{!! $prophet->display_name_html !!}</span>.
                    Check back soon.
                </p>
                <a href="{{ route('prophets.index') }}" class="btn-emerald btn">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @elseif ($stories->count() > 1)
            {{-- ─────────────────────────────────────────
                MULTI-PART PROPHET (currently only Muhammad ﷺ)
                Show ONE Journey card instead of looping every part.
                Uses $stories (already filtered by published +
                premium status) — NOT $prophet->stories()->count(),
                which would wrongly include unpublished/hidden parts.
            ───────────────────────────────────────────── --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="heading-font mb-2" style="font-size:1rem">
                    The Complete Life of {{ $prophet->name_transliteration }}
                </h5>
                <span class="text-muted small">{{ $stories->count() }} parts</span>
            </div>


            <div class="row row-cols-1 g-4">
                <div class="col">
                    <div class="story-card-inner">


                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="heading-font mb-0" style="font-size:1rem">
                                The Complete Life of {{ $prophet->name_transliteration }}
                            </h5>
                            @if ($firstPart->min_plan_slug === 'free')
                                <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">Free</span>
                            @elseif ($accessible)
                                <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">
                                    <i class="bi bi-unlock-fill me-1"></i>Unlocked
                                </span>
                            @else
                                <span class="badge ms-2 flex-shrink-0"
                                    style="background:var(--gold); color:#1A1A2E; font-size:0.7rem">
                                    <i class="bi bi-stars me-1"></i>{{ ucfirst($firstPart->min_plan_slug) }}
                                </span>
                            @endif
                        </div>

                        <p class="text-muted mb-3" style="font-size:0.85rem; line-height:1.65">
                            A {{ $stories->count() }}-part journey through his blessed life — from
                            the first revelation to his final years. Complete each part to unlock the next.
                        </p>

                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <span class="meta-chip">
                                <i class="bi bi-journal-text me-1"></i>
                                {{ $stories->count() }} Parts
                            </span>
                        </div>

                        {{-- Same access rules as a normal story card --}}
                        <a href="{{ route('prophets.journey', $prophet->slug) }}" class="btn-emerald btn btn-sm">
                            Begin the Journey <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald)">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @else
            {{-- ─────────────────────────────────────────
                 NORMAL PROPHET — single Story record
                 (24 of the 25 prophets, everything unchanged)
            ───────────────────────────────────────────── --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="heading-font mb-0">
                    Stories of {{ $prophet->name_transliteration }}
                </h3>
                <span class="text-muted small">{{ $stories->count() }}
                    {{ Str::plural('story', $stories->count()) }}</span>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                @foreach ($stories as $story)
                    <div class="col">
                        <div class="story-card-inner">

                            {{-- Title + badge --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="heading-font mb-2" style="font-size:1rem">
                                    <a href="{{ route('stories.show', $story->slug) }}" class="text-decoration-none"
                                        style="color:var(--ink)">
                                        {{ $story->title }}
                                    </a>
                                </h5>
                                @if ($story->min_plan_slug === 'free')
                                    <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">Free</span>
                                @elseif ($accessible)
                                    <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">
                                        <i class="bi bi-unlock-fill me-1"></i>Unlocked
                                    </span>
                                @else
                                    <span class="badge ms-2 flex-shrink-0"
                                        style="background:var(--gold); color:#1A1A2E; font-size:0.7rem">
                                        <i class="bi bi-stars me-1"></i>{{ ucfirst($story->min_plan_slug) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Summary --}}
                            @if ($story->summary)
                                <p class="mb-3"
                                    style="font-size:0.92rem; line-height:1.8; color:var(--ink-soft); font-weight:400">
                                    {{ Str::limit($story->summary, 120) }}
                                </p>
                            @endif

                            {{-- Meta chips --}}
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                @if ($story->difficulty)
                                    <span class="meta-chip">
                                        {{ ucfirst($story->difficulty) }}
                                    </span>
                                @endif
                                <span class="meta-chip">
                                    <i class="bi bi-journals me-1"></i>
                                    {{ $story->chapters->count() }}
                                    {{ Str::plural('Chapter', $story->chapters->count()) }}
                                </span>
                                @if ($story->read_time_minutes)
                                    <span class="meta-chip">
                                        <i class="bi bi-clock me-1"></i>{{ $story->read_time_minutes }} min
                                    </span>
                                @endif
                            </div>

                            {{-- CTA — single-story branch only, no journey logic needed here --}}
                            <a href="{{ route('stories.show', $story->slug) }}" class="btn-emerald btn btn-sm">
                                Read Story <i class="bi bi-arrow-right ms-1"></i>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald-light)">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @endif

    </div>
@endsection

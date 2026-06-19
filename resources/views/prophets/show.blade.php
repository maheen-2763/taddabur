@extends('layouts.app')
@section('title', $prophet->display_name . ' — Taddabur')

@push('styles')
    <style>
        /* ── Hero ── */
        .prophet-hero {
            border-radius: var(--radius);
            overflow: hidden;
            background: linear-gradient(150deg, var(--emerald-dark) 0%, var(--emerald) 100%);
            position: relative;
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
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4" style="font-size:0.85rem">
            <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald)">
                <i class="bi bi-grid me-1"></i>All Prophets
            </a>
            <span class="mx-2 text-muted">/</span>
            <span class="text-muted">{{ $prophet->display_name }}</span>
        </nav>

        {{-- ═══════════════════════════════════════════════
         HERO CARD
    ════════════════════════════════════════════════ --}}
        <div class="prophet-hero mb-5">

            {{-- Arabic watermark --}}
            <span class="arabic-watermark">{{ $prophet->name_arabic }}</span>

            <div class="row g-0">

                {{-- LEFT: Identity --}}
                <div class="col-lg-6 p-4 p-md-5">

                    {{-- Order pill --}}
                    <span class="badge mb-3"
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
                            {{ $prophet->display_name }}
                        </h1>
                        <p style="color:rgba(255,255,255,0.6); font-size:0.9rem; margin:0">
                            {{ $prophet->name_english }}
                        </p>
                    </div>

                    {{-- Arabic name + title --}}
                    <div class="mb-4" style="text-align:right; direction:rtl">
                        <div
                            style="font-family:'Amiri','Scheherazade New',serif;
                                font-size:2.8rem;
                                color:white;
                                line-height:1.4;
                                margin-bottom:0.4rem">
                            {{ $prophet->name_arabic }}
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
                            {{ $prophet->title }}
                        </p>
                    @endif

                    {{-- Summary --}}
                    @if ($prophet->summary)
                        <p
                            style="color:rgba(255,255,255,0.75);
                              line-height:1.8;
                              font-size:0.9rem;
                              margin:0">
                            {{ $prophet->summary }}
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
                            style="font-size:0.7rem;
                              color:rgba(255,255,255,0.45);
                              text-transform:uppercase;
                              letter-spacing:0.1em;
                              margin-bottom:0.75rem">
                            Life Journey
                        </p>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($prophet->timeline as $event)
                                <div class="d-flex align-items-start gap-2">
                                    <div class="timeline-dot"></div>
                                    <div>
                                        <span style="font-size:0.83rem; color:rgba(255,255,255,0.88); font-weight:500">
                                            {{ $event['title'] }}
                                        </span>
                                        <span style="font-size:0.78rem; color:rgba(255,255,255,0.45)">
                                            · {{ $event['period'] }}
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
                    We are preparing authentic, Quran-sourced stories for {{ $prophet->display_name }}.
                    Check back soon.
                </p>
                <a href="{{ route('prophets.index') }}" class="btn-emerald btn">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @else
            <div class="d-flex align-items-center justify-content-between mb-4">
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
                                <h5 class="heading-font mb-0" style="font-size:1rem">
                                    <a href="{{ route('stories.show', $story->slug) }}" class="text-decoration-none"
                                        style="color:var(--ink)">
                                        {{ $story->title }}
                                    </a>
                                </h5>
                                @if (!$story->is_free)
                                    <span class="badge ms-2 flex-shrink-0"
                                        style="background:var(--gold); color:#1A1A2E; font-size:0.7rem">
                                        <i class="bi bi-stars me-1"></i>Premium
                                    </span>
                                @else
                                    <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">
                                        Free
                                    </span>
                                @endif
                            </div>

                            {{-- Summary --}}
                            @if ($story->summary)
                                <p class="text-muted mb-3" style="font-size:0.85rem; line-height:1.65">
                                    {{ Str::limit($story->summary, 120) }}
                                </p>
                            @endif

                            {{-- Meta chips --}}
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                @if ($story->difficulty)
                                    <span class="badge bg-light text-muted border" style="font-size:0.72rem">
                                        {{ ucfirst($story->difficulty) }}
                                    </span>
                                @endif
                                <span class="badge bg-light text-muted border" style="font-size:0.72rem">
                                    <i class="bi bi-journal-text me-1"></i>
                                    {{ $story->chapters_count }} {{ Str::plural('Chapter', $story->chapters_count) }}
                                </span>
                                @if ($story->read_time_minutes)
                                    <span class="badge bg-light text-muted border" style="font-size:0.72rem">
                                        <i class="bi bi-clock me-1"></i>{{ $story->read_time_minutes }} min
                                    </span>
                                @endif
                            </div>

                            {{-- CTA --}}
                            @if ($story->is_free)
                                <a href="{{ route('stories.show', $story->slug) }}" class="btn-emerald btn btn-sm">
                                    Read Story <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                @auth
                                    @if (auth()->user()->isPremium())
                                        <a href="{{ route('stories.show', $story->slug) }}" class="btn-emerald btn btn-sm">
                                            Read Story <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm">
                                            <i class="bi bi-lock me-1"></i>Upgrade to Read
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-emerald btn btn-sm">
                                        Sign in to Read
                                    </a>
                                @endauth
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                <a href="{{ route('prophets.index') }}" class="text-decoration-none" style="color:var(--emerald)">
                    <i class="bi bi-arrow-left me-1"></i>All Prophets
                </a>
            </div>
        @endif

    </div>
@endsection

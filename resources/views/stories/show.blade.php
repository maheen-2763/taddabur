@extends('layouts.app')
@section('title', $story->title . ' — Taddabur')

@push('styles')
    <style>
        /* ── Hero ── */
        .story-hero {
            position: relative;
            height: 340px;
            overflow: hidden;
            border-radius: var(--radius);
        }

        .story-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .story-hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top,
                    rgba(0, 0, 0, 0.78) 0%,
                    rgba(0, 0, 0, 0.2) 55%,
                    transparent 100%);
        }

        .story-hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            color: white;
        }

        /* ── Hero placeholder (no image) ── */
        .story-hero-placeholder {
            height: 340px;
            border-radius: var(--radius);
            background: linear-gradient(150deg, var(--emerald-dark), var(--emerald));
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-end;
            padding: 2rem;
        }

        .story-hero-placeholder::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .story-hero-placeholder-arabic {
            position: absolute;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 7rem;
            color: rgba(255, 255, 255, 0.07);
            line-height: 1;
            pointer-events: none;
            user-select: none;
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

        .meta-chip-gold {
            background: rgba(180, 130, 40, 0.1);
            color: var(--gold);
            border-radius: 50px;
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .meta-chip-white {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            backdrop-filter: blur(4px);
        }

        .meta-chip-white-gold {
            background: rgba(180, 130, 40, 0.25);
            color: var(--gold-light);
            border-radius: 50px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* ── Progress bar ── */
        .progress-bar-islamic {
            height: 5px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar-islamic-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--emerald), var(--gold));
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        /* ── Chapter list ── */
        .chapter-list-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1rem;
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--ink);
            transition: all 0.18s;
            border: 1px solid transparent;
        }

        .chapter-list-item:hover {
            background: rgba(27, 94, 59, 0.05);
            border-color: rgba(27, 94, 59, 0.12);
            color: var(--emerald);
        }

        .chapter-list-item.is-completed {
            border-left: 3px solid var(--gold);
        }

        .chapter-number {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            flex-shrink: 0;
            font-family: var(--font-heading);
        }

        .chapter-number.is-completed {
            background: var(--gold);
            color: white;
        }

        /* ── Sidebar CTA ── */
        .sidebar-cta {
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .sidebar-cta-header {
            background: linear-gradient(135deg, var(--emerald-dark), var(--emerald));
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .sidebar-cta-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .sidebar-cta-body {
            background: var(--cream);
            border: 1px solid var(--border);
            border-top: none;
            border-bottom-left-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
            padding: 1.25rem;
        }

        /* ── Prophet sidebar card ── */
        .prophet-sidebar-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            overflow: hidden;
        }

        .prophet-sidebar-card-header {
            background: linear-gradient(135deg, var(--emerald-dark), var(--emerald));
            padding: 1.25rem;
            text-align: center;
            position: relative;
        }

        .prophet-sidebar-arabic {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 2.8rem;
            color: white;
            line-height: 1.3;
            margin-bottom: 0.2rem;
        }

        .prophet-sidebar-title-arabic {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: 0.95rem;
            color: var(--gold-light);
            margin-top: 0.4rem;
        }

        .prophet-sidebar-card-body {
            padding: 1.1rem 1.25rem 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- Breadcrumb --}}
        <nav class="mb-4" style="font-size:0.85rem">
            <a href="{{ route('stories.index') }}" class="text-muted text-decoration-none">Stories</a>
            @if ($story->prophet)
                <span class="mx-2 text-muted">/</span>
                <a href="{{ route('prophets.show', $story->prophet->slug) }}" class="text-muted text-decoration-none">
                    {{ $story->prophet->name_transliteration }}
                </a>
            @endif
            <span class="mx-2 text-muted">/</span>
            <span style="color:var(--ink)">{{ $story->title }}</span>
        </nav>

        <div class="row g-5">

            {{-- ══════════════════════════════════════
             LEFT — Story info + chapters
        ═══════════════════════════════════════ --}}
            <div class="col-lg-8">

                {{-- Hero --}}
                @if ($story->cover_image)
                    <div class="story-hero mb-4">
                        <img src="{{ asset('storage/' . $story->cover_image) }}" alt="{{ $story->title }}">
                        <div class="story-hero-overlay"></div>
                        <div class="story-hero-content">
                            @if ($story->is_free)
                                <span class="meta-chip-white mb-2 d-inline-block">Free</span>
                            @else
                                <span class="meta-chip-white-gold mb-2 d-inline-block">
                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                </span>
                            @endif
                            <h1 class="heading-font mb-1" style="font-size:clamp(1.5rem,4vw,2rem); color:white">
                                {{ $story->title }}
                            </h1>
                            @if ($story->prophet)
                                <p class="mb-0" style="color:rgba(255,255,255,0.75); font-size:0.88rem">
                                    {{ $story->prophet->display_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Geometric placeholder hero — same language as prophet cards --}}
                    <div class="story-hero-placeholder mb-4">
                        {{-- Ghost Arabic watermark --}}
                        @if ($story->prophet)
                            <span class="story-hero-placeholder-arabic">
                                {{ $story->prophet->name_arabic }}
                            </span>
                        @endif

                        {{-- Badges --}}
                        <div class="mb-2">
                            @if ($story->is_free)
                                <span class="meta-chip-white">Free</span>
                            @else
                                <span class="meta-chip-white-gold">
                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                </span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h1 class="heading-font mb-1"
                            style="font-size:clamp(1.4rem,3.5vw,1.9rem);
                               color:white;
                               line-height:1.25;
                               position:relative;
                               z-index:1">
                            {{ $story->title }}
                        </h1>

                        {{-- Prophet name --}}
                        @if ($story->prophet)
                            <p
                                style="color:rgba(255,255,255,0.65);
                                  font-size:0.88rem;
                                  margin:0;
                                  position:relative;
                                  z-index:1">
                                {{ $story->prophet->display_name }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Meta row --}}
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @if ($story->read_time_minutes)
                        <span class="meta-chip">
                            <i class="bi bi-clock me-1"></i>{{ $story->read_time_minutes }} min read
                        </span>
                    @endif
                    <span class="meta-chip">
                        <i class="bi bi-journal-text me-1"></i>
                        {{ $story->chapters->count() }} {{ Str::plural('Chapter', $story->chapters->count()) }}
                    </span>
                    @if ($story->difficulty)
                        <span class="meta-chip">
                            <i class="bi bi-bar-chart me-1"></i>{{ ucfirst($story->difficulty) }}
                        </span>
                    @endif
                    @if ($story->category)
                        <span class="meta-chip">
                            <i class="bi bi-tag me-1"></i>{{ ucfirst($story->category) }}
                        </span>
                    @endif
                </div>

                {{-- Summary --}}
                @if ($story->summary)
                    <div class="card-islamic p-4 mb-4">
                        <h6 class="heading-font mb-2"
                            style="font-size:0.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.08em">
                            About This Story
                        </h6>
                        <p class="mb-0" style="line-height:1.85; color:var(--ink-soft)">
                            {{ $story->summary }}
                        </p>
                    </div>
                @endif

                {{-- Reading progress --}}
                @if ($progress)
                    @php
                        $lastChapter = $story->chapters->firstWhere('id', $progress->last_chapter_id);
                        $progressPct = $lastChapter
                            ? round(($lastChapter->order / $story->chapters->count()) * 100)
                            : 0;
                    @endphp
                    <div class="card-islamic p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="heading-font mb-0"
                                style="font-size:0.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.08em">
                                Your Progress
                            </h6>
                            <span style="font-size:0.8rem; color:var(--emerald); font-weight:600">
                                {{ $progressPct }}%
                            </span>
                        </div>
                        <div class="progress-bar-islamic mb-3">
                            <div class="progress-bar-islamic-fill" style="width:{{ $progressPct }}%"></div>
                        </div>
                        @if ($lastChapter)
                            <p class="text-muted small mb-0">
                                <i class="bi bi-bookmark-fill me-1" style="color:var(--gold)"></i>
                                Last read: <strong>{{ $lastChapter->title }}</strong>
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Chapter list --}}
                <div class="card-islamic p-4">
                    <h6 class="heading-font mb-3"
                        style="font-size:0.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.08em">
                        {{ $story->chapters->count() }} {{ Str::plural('Chapter', $story->chapters->count()) }}
                    </h6>

                    @foreach ($story->chapters as $chapter)
                        @php
                            $isCompleted = in_array($chapter->id, $completedChapterIds ?? []);
                            $isLastRead = $progress && $progress->last_chapter_id === $chapter->id;
                        @endphp

                        <a href="{{ route('stories.chapter', [$story->slug, $chapter->slug]) }}"
                            class="chapter-list-item {{ $isCompleted ? 'is-completed' : '' }}">

                            <div class="chapter-number {{ $isCompleted ? 'is-completed' : '' }}">
                                @if ($isCompleted)
                                    <i class="bi bi-check"></i>
                                @else
                                    {{ $chapter->order }}
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size:0.9rem; font-weight:500">
                                        {{ $chapter->title }}
                                    </span>
                                    @if ($isLastRead)
                                        <span class="meta-chip-gold" style="font-size:0.7rem">
                                            Continue →
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <i class="bi bi-chevron-right" style="font-size:0.78rem; color:var(--muted)"></i>
                        </a>

                        @if (!$loop->last)
                            <hr style="margin:0.2rem 0; border-color:var(--border); opacity:0.5">
                        @endif
                    @endforeach
                </div>

            </div>

            {{-- ══════════════════════════════════════
             RIGHT — Sticky sidebar
        ═══════════════════════════════════════ --}}
            <div class="col-lg-4">
                <div style="position:sticky; top:80px">

                    {{-- Start / Continue CTA --}}
                    <div class="sidebar-cta">
                        <div class="sidebar-cta-header">
                            @if ($progress && $progress->last_chapter_id)
                                <div
                                    style="font-family:'Amiri',serif;
                                        font-size:2rem;
                                        color:rgba(255,255,255,0.7);
                                        margin-bottom:0.5rem;
                                        position:relative; z-index:1">
                                    اقْرَأْ
                                </div>
                                <h6 class="heading-font mb-1" style="color:white; margin:0; position:relative; z-index:1">
                                    Continue Reading
                                </h6>
                                <p
                                    style="color:rgba(255,255,255,0.6);
                                      font-size:0.8rem;
                                      margin:0;
                                      position:relative; z-index:1">
                                    Pick up where you left off
                                </p>
                            @else
                                <div
                                    style="font-family:'Amiri',serif;
                                        font-size:2rem;
                                        color:rgba(255,255,255,0.7);
                                        margin-bottom:0.5rem;
                                        position:relative; z-index:1">
                                    بِسْمِ اللَّه
                                </div>
                                <h6 class="heading-font mb-1" style="color:white; margin:0; position:relative; z-index:1">
                                    Begin This Story
                                </h6>
                                <p
                                    style="color:rgba(255,255,255,0.6);
                                      font-size:0.8rem;
                                      margin:0.25rem 0 0;
                                      position:relative; z-index:1">
                                    {{ $story->chapters->count() }} chapters await you
                                </p>
                            @endif
                        </div>

                        <div class="sidebar-cta-body">
                            @if ($progress && $progress->last_chapter_id)
                                @php $continueChapter = $story->chapters->firstWhere('id', $progress->last_chapter_id); @endphp
                                @if ($continueChapter)
                                    <a href="{{ route('stories.chapter', [$story->slug, $continueChapter->slug]) }}"
                                        class="btn-emerald btn w-100 mb-2">
                                        <i class="bi bi-play-fill me-1"></i>Continue
                                    </a>
                                @endif
                                <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->slug]) }}"
                                    class="btn btn-outline-secondary w-100" style="font-size:0.85rem">
                                    Start from Beginning
                                </a>
                            @else
                                <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->slug]) }}"
                                    class="btn-emerald btn w-100">
                                    <i class="bi bi-play-fill me-1"></i>Start Reading
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Prophet info card --}}
                    @if ($story->prophet)
                        <div class="prophet-sidebar-card">
                            <div class="prophet-sidebar-card-header">
                                <div class="prophet-sidebar-arabic">
                                    {{ $story->prophet->name_arabic }}
                                </div>
                                @if ($story->prophet->title_arabic)
                                    <div class="prophet-sidebar-title-arabic">
                                        {{ $story->prophet->title_arabic }}
                                    </div>
                                @endif
                            </div>
                            <div class="prophet-sidebar-card-body">
                                <div class="heading-font mb-0" style="font-size:1rem; font-weight:600">
                                    {{ $story->prophet->display_name }}
                                </div>
                                <div class="text-muted mb-3" style="font-size:0.78rem">
                                    {{ $story->prophet->name_english }}
                                </div>
                                @if ($story->prophet->summary)
                                    <p class="text-muted small mb-3" style="line-height:1.7">
                                        {{ Str::limit($story->prophet->summary, 130) }}
                                    </p>
                                @endif
                                <a href="{{ route('prophets.show', $story->prophet->slug) }}"
                                    class="text-decoration-none" style="font-size:0.85rem; color:var(--emerald)">
                                    All stories of {{ $story->prophet->name_transliteration }}
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection

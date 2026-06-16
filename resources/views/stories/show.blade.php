@extends('layouts.app')

@section('title', $story->title . ' — Taddabur')

@push('styles')
    <style>
        .story-hero {
            position: relative;
            height: 380px;
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
                    rgba(0, 0, 0, 0.75) 0%,
                    rgba(0, 0, 0, 0.2) 60%,
                    transparent 100%);
        }

        .story-hero-placeholder {
            height: 380px;
            background: rgba(27, 94, 59, 0.06);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: var(--emerald);
            opacity: 0.4;
        }

        .story-hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            color: white;
        }

        .chapter-list-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--ink);
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .chapter-list-item:hover {
            background: rgba(27, 94, 59, 0.06);
            border-color: var(--border);
            color: var(--emerald);
        }

        .chapter-list-item.completed {
            border-left: 3px solid var(--gold);
        }

        .chapter-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .chapter-number.completed {
            background: var(--gold);
            color: white;
        }

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

        .progress-bar-islamic {
            height: 6px;
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
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- Breadcrumb --}}
        <nav class="mb-4" style="font-size: 0.85rem;">
            <a href="{{ route('stories.index') }}" class="text-muted text-decoration-none">Stories</a>
            @if ($story->prophet)
                <span class="mx-2 text-muted">/</span>
                <a href="{{ route('prophets.show', $story->prophet->slug) }}" class="text-muted text-decoration-none">
                    {{ $story->prophet->name_transliteration }}
                </a>
            @endif
            <span class="mx-2 text-muted">/</span>
            <span style="color: var(--ink);">{{ $story->title }}</span>
        </nav>

        <div class="row g-5">

            {{-- ── LEFT: Story info ────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- Hero image --}}
                @if ($story->cover_image)
                    <div class="story-hero mb-4">
                        <img src="{{ asset('storage/' . $story->cover_image) }}" alt="{{ $story->title }}">
                        <div class="story-hero-overlay"></div>
                        <div class="story-hero-content">
                            @if ($story->is_free)
                                <span class="meta-chip mb-2 d-inline-block">Free</span>
                            @else
                                <span class="meta-chip-gold mb-2 d-inline-block">
                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                </span>
                            @endif
                            <h1 class="heading-font mb-1" style="font-size: clamp(1.5rem, 4vw, 2rem);">
                                {{ $story->title }}
                            </h1>
                            @if ($story->prophet)
                                <p class="mb-0" style="opacity: 0.85; font-size: 0.9rem;">
                                    {{ $story->prophet->display_name }}
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- No image fallback --}}
                    <div class="story-hero-placeholder mb-4">☪</div>
                    <div class="mb-3">
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            @if ($story->is_free)
                                <span class="meta-chip">Free</span>
                            @else
                                <span class="meta-chip-gold">
                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                </span>
                            @endif
                        </div>
                        <h1 class="heading-font mb-1" style="font-size: clamp(1.5rem, 4vw, 2rem);">
                            {{ $story->title }}
                        </h1>
                        @if ($story->prophet)
                            <p style="color: var(--emerald); font-size: 0.9rem;">
                                {{ $story->prophet->display_name }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Meta info row --}}
                <div class="d-flex flex-wrap gap-2 mb-4">

                    @if ($story->read_time_minutes)
                        <span class="meta-chip">
                            <i class="bi bi-clock me-1"></i>
                            {{ $story->read_time_minutes }} min read
                        </span>
                    @endif
                    <span class="meta-chip">
                        <i class="bi bi-journal-text me-1"></i>
                        {{ $story->chapters->count() }} {{ Str::plural('Chapter', $story->chapters->count()) }}
                    </span>

                    @if ($story->difficulty)
                        <span class="meta-chip">
                            <i class="bi bi-bar-chart me-1"></i>
                            {{ ucfirst($story->difficulty) }}
                        </span>
                    @endif

                    @if ($story->category)
                        <span class="meta-chip">
                            <i class="bi bi-tag me-1"></i>
                            {{ ucfirst($story->category) }}
                        </span>
                    @endif
                </div>

                {{-- Summary --}}
                @if ($story->summary)
                    <div class="card-islamic p-4 mb-4">
                        <h6 class="heading-font mb-2"
                            style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                            About This Story
                        </h6>
                        <p class="mb-0" style="line-height: 1.8; color: var(--ink-soft);">
                            {{ $story->summary }}
                        </p>
                    </div>
                @endif

                {{-- Reading progress (if logged in and started) --}}
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
                                style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                                Your Progress
                            </h6>
                            <span style="font-size: 0.8rem; color: var(--emerald);">
                                {{ $progressPct }}%
                            </span>
                        </div>
                        <div class="progress-bar-islamic mb-3">
                            <div class="progress-bar-islamic-fill" style="width: {{ $progressPct }}%"></div>
                        </div>
                        @if ($lastChapter)
                            <p class="text-muted small mb-0">
                                <i class="bi bi-bookmark-fill me-1" style="color: var(--gold);"></i>
                                Last read: <strong>{{ $lastChapter->title }}</strong>
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Chapter list --}}
                <div class="card-islamic p-4">
                    <h6 class="heading-font mb-3"
                        style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                        Chapters
                    </h6>

                    @foreach ($story->chapters as $chapter)
                        @php
                            $isCompleted = $progress && $progress->last_chapter_id >= $chapter->id;
                            $isLastRead = $progress && $progress->last_chapter_id === $chapter->id;
                        @endphp

                        <a href="{{ route('stories.chapter', [$story->slug, $chapter->slug]) }}"
                            class="chapter-list-item {{ $isCompleted ? 'completed' : '' }}">

                            {{-- Chapter number --}}
                            <div class="chapter-number {{ $isCompleted ? 'completed' : '' }}">
                                @if ($isCompleted)
                                    <i class="bi bi-check"></i>
                                @else
                                    {{ $chapter->order }}
                                @endif
                            </div>

                            {{-- Chapter info --}}
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.9rem; font-weight: 500;">
                                        {{ $chapter->title }}
                                    </span>
                                    @if ($isLastRead)
                                        <span class="meta-chip-gold" style="font-size: 0.72rem;">
                                            Continue
                                        </span>
                                    @endif
                                </div>
                                @if ($chapter->read_time ?? null)
                                    <span style="font-size: 0.78rem; color: var(--muted);">
                                        <i class="bi bi-clock me-1"></i>{{ $chapter->read_time }}
                                    </span>
                                @endif
                            </div>

                            <i class="bi bi-chevron-right" style="font-size: 0.8rem; color: var(--muted);"></i>
                        </a>

                        {{-- Divider between chapters --}}
                        @if (!$loop->last)
                            <hr style="margin: 0.25rem 0; border-color: var(--border); opacity: 0.5;">
                        @endif
                    @endforeach
                </div>

            </div>

            {{-- ── RIGHT: Sticky sidebar ───────────────────────── --}}
            <div class="col-lg-4">
                <div style="position: sticky; top: 80px;">

                    {{-- Start / Continue reading CTA --}}
                    <div class="card-islamic p-4 mb-4 text-center">

                        @if ($progress && $progress->last_chapter_id)
                            @php
                                $continueChapter = $story->chapters->firstWhere('id', $progress->last_chapter_id);
                            @endphp
                            <div class="mb-3" style="font-size: 2.5rem;">📖</div>
                            <h6 class="heading-font mb-1">Continue Reading</h6>
                            <p class="text-muted small mb-3">
                                Pick up where you left off.
                            </p>
                            <a href="{{ route('stories.chapter', [$story->slug, $progress->last_chapter_id]) }}"
                                class="btn-emerald btn w-100 mb-2">
                                <i class="bi bi-play-fill me-1"></i> Continue
                            </a>
                            <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->id]) }}"
                                class="btn btn-outline-secondary w-100" style="font-size: 0.85rem;">
                                Start from Beginning
                            </a>
                        @else
                            <div class="mb-3" style="font-size: 2.5rem;">🌙</div>
                            <h6 class="heading-font mb-1">Begin This Story</h6>
                            <p class="text-muted small mb-3">
                                {{ $story->chapters->count() }} chapters await you.
                            </p>
                            <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->id]) }}"
                                class="btn-emerald btn w-100">
                                <i class="bi bi-play-fill me-1"></i> Start Reading
                            </a>
                        @endif

                    </div>

                    {{-- Prophet info card --}}
                    @if ($story->prophet)
                        <div class="card-islamic p-4">
                            <h6 class="heading-font mb-3"
                                style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                                About The Prophet
                            </h6>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div style="font-size: 2rem; color: var(--emerald); font-family: serif;">
                                    {{ $story->prophet->name_arabic }}
                                </div>
                                <div>
                                    <div class="heading-font" style="font-weight: 600;">
                                        {{ $story->prophet->display_name }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.8rem;">
                                        {{ $story->prophet->name_english }}
                                    </div>
                                </div>
                            </div>

                            @if ($story->prophet->summary)
                                <p class="text-muted small mb-3" style="line-height: 1.7;">
                                    {{ Str::limit($story->prophet->summary, 130) }}
                                </p>
                            @endif

                            <a href="{{ route('prophets.show', $story->prophet->slug) }}" class="text-decoration-none"
                                style="font-size: 0.85rem; color: var(--emerald);">
                                All stories of {{ $story->prophet->name_transliteration }}
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>
@endsection

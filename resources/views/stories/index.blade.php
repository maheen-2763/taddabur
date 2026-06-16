@extends('layouts.app')

@section('title', 'Prophet Stories — Taddabur')

@push('styles')
    <style>
        .story-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .story-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        .story-card-img {
            height: 190px;
            object-fit: cover;
            width: 100%;
        }

        .story-card-placeholder {
            height: 190px;
            background: rgba(27, 94, 59, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-size: 2.5rem;
            color: var(--emerald);
            opacity: 0.5;
        }

        .story-badge-free {
            background: rgba(27, 94, 59, 0.1);
            color: var(--emerald);
            font-size: 0.72rem;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .story-badge-premium {
            background: rgba(180, 130, 40, 0.1);
            color: var(--gold);
            font-size: 0.72rem;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .filter-pill {
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.35rem 1rem;
            font-size: 0.85rem;
            background: transparent;
            color: var(--ink-soft);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .filter-pill:hover,
        .filter-pill.active {
            background: var(--emerald);
            border-color: var(--emerald);
            color: white;
        }

        .prophet-filter-item {
            font-size: 0.85rem;
            color: var(--ink-soft);
            text-decoration: none;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius);
            display: block;
            transition: all 0.2s;
        }

        .prophet-filter-item:hover,
        .prophet-filter-item.active {
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
        }

        .sidebar-sticky {
            position: sticky;
            top: 80px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- Page Header --}}
        <div class="mb-5">
            <p class="mb-1"
                style="color: var(--gold); font-family: var(--font-heading);
           font-size: 0.8rem; letter-spacing: 0.12em; text-transform: uppercase;">
                Guided by the Quran
            </p>
            <h1 class="heading-font mb-2" style="font-size: clamp(1.8rem, 4vw, 2.5rem);">
                Prophet Stories
            </h1>
            <p class="text-muted" style="max-width: 540px;">
                Explore the lives of the prophets — their trials, miracles, and timeless
                lessons for every believer.
            </p>
        </div>

        <div class="row g-4">

            {{-- ── LEFT: Sidebar filters ───────────────────────── --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar-sticky">

                    {{-- Prophets filter --}}
                    <div class="card-islamic p-3 mb-3">
                        <h6 class="heading-font mb-3"
                            style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                            By Prophet
                        </h6>

                        <a href="{{ route('stories.index') }}"
                            class="prophet-filter-item {{ !request('prophet') ? 'active' : '' }} mb-1">
                            All Prophets
                        </a>


                        @foreach ($prophets as $prophet)
                            <a href="{{ route('stories.index', ['prophet' => $prophet->slug]) }}"
                                class="prophet-filter-item {{ request('prophet') === $prophet->slug ? 'active' : '' }} mb-1">

                                {{ $prophet->name_transliteration }}

                                {{-- ﷺ ONLY for Prophet Muhammad --}}
                                @if ($prophet->slug === 'muhammad')
                                    <span style="font-size:0.8rem; color: var(--emerald);">ﷺ</span>
                                @endif

                                @if ($prophet->stories_count > 0)
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        ({{ $prophet->stories_count }})
                                    </span>
                                @else
                                    <span class="badge"
                                        style="font-size:0.65rem; background: rgba(16,185,129,0.08); color: var(--emerald-dark);">
                                        Coming soon
                                    </span>
                                @endif

                            </a>
                        @endforeach
                    </div>

                    {{-- Difficulty filter --}}
                    <div class="card-islamic p-3">
                        <h6 class="heading-font mb-3"
                            style="font-size: 0.8rem; color: var(--muted); text-transform: uppercase;">
                            Difficulty
                        </h6>
                        @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                            <a href="{{ route('stories.index', array_merge(request()->query(), ['difficulty' => $level])) }}"
                                class="prophet-filter-item {{ request('difficulty') === $level ? 'active' : '' }} mb-1">
                                {{ ucfirst($level) }}
                            </a>
                        @endforeach
                        @if (request()->has('difficulty') || request()->has('prophet') || request()->has('category'))
                            <hr class="my-2">
                            <a href="{{ route('stories.index') }}" class="prophet-filter-item text-danger mb-1">
                                <i class="bi bi-x me-1"></i>Clear All Filters
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── RIGHT: Stories grid ──────────────────────────── --}}
            <div class="col-lg-9">

                {{-- Mobile filters --}}
                <div class="d-flex flex-wrap gap-2 mb-4 d-lg-none">
                    <a href="{{ route('stories.index') }}"
                        class="filter-pill {{ !request()->hasAny(['difficulty', 'category']) ? 'active' : '' }}">
                        All
                    </a>
                    @foreach (['beginner', 'intermediate', 'advanced'] as $level)
                        <a href="{{ route('stories.index', ['difficulty' => $level]) }}"
                            class="filter-pill {{ request('difficulty') === $level ? 'active' : '' }}">
                            {{ ucfirst($level) }}
                        </a>
                    @endforeach
                </div>

                {{-- Results count --}}
                <p class="text-muted small mb-4">
                    Showing {{ $stories->firstItem() }}–{{ $stories->lastItem() }}
                    of {{ $stories->total() }} stories
                </p>

                {{-- Story cards --}}
                @if ($stories->isEmpty())

                    <div class="text-center py-5">

                        <div
                            style="
            width: 90px;
            height: 90px;
            margin: 0 auto;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        ">
                            <i class="bi bi-hourglass-split" style="font-size: 2.5rem; color: var(--emerald-dark);"></i>
                        </div>

                        {{-- CASE 1: Difficulty filter --}}
                        @if (request('difficulty'))
                            <h5 class="heading-font mt-4">
                                {{ ucfirst(request('difficulty')) }} Stories Coming Soon
                            </h5>

                            <p class="text-muted small mt-2">
                                We are preparing authentic {{ request('difficulty') }} level stories.
                                Meanwhile, explore beginner stories.
                            </p>

                            <a href="{{ route('stories.index', ['difficulty' => 'beginner']) }}"
                                class="btn-emerald btn mt-3">
                                Explore Beginner Stories
                            </a>

                            {{-- CASE 2: Prophet filter --}}
                        @elseif(request('prophet'))
                            <h5 class="heading-font mt-4">
                                Stories of this Prophet are Coming Soon
                            </h5>

                            <p class="text-muted small mt-2">
                                We are continuously adding authentic Prophet stories.
                                Explore other available stories for now.
                            </p>

                            <a href="{{ route('stories.index') }}" class="btn-emerald btn mt-3">
                                View All Stories
                            </a>

                            {{-- CASE 3: Default --}}
                        @else
                            <h5 class="heading-font mt-4">
                                More Stories Are Coming Soon
                            </h5>

                            <p class="text-muted small mt-2">
                                We are building a complete collection of Prophet stories.
                                Stay tuned as more content is added.
                            </p>

                            <a href="{{ route('stories.index') }}" class="btn-emerald btn mt-3">
                                Explore Available Stories
                            </a>
                        @endif

                    </div>
                @else
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">

                        @foreach ($stories as $story)
                            <div class="col">
                                <a href="{{ route('stories.show', $story->slug) }}" class="story-card h-100">

                                    {{-- Image or placeholder --}}
                                    @if ($story->cover_image)
                                        <img src="{{ asset('storage/' . $story->cover_image) }}" alt="{{ $story->title }}"
                                            class="story-card-img">
                                    @else
                                        <div class="story-card-placeholder">
                                            ☪
                                        </div>
                                    @endif

                                    <div class="p-3">

                                        {{-- Badges --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            @if ($story->is_free)
                                                <span class="story-badge-free">Free</span>
                                            @else
                                                <span class="story-badge-premium">
                                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <h5 class="heading-font mb-1" style="font-size: 1rem; color: var(--ink);">
                                            {{ $story->title }}
                                        </h5>

                                        {{-- Prophet name --}}
                                        @if ($story->prophet)
                                            <p class="mb-2" style="font-size: 0.8rem; color: var(--emerald);">
                                                {{ $story->prophet->display_name }}
                                            </p>
                                        @endif

                                        {{-- Story Stats --}}
                                        <div class="d-flex flex-wrap gap-2 mb-2"
                                            style="font-size: 0.75rem; color: var(--muted);">


                                            <span style="color: var(--emerald);">
                                                <i class="bi bi-journal-text me-1"></i>
                                                {{ $story->chapters_count ?? $story->chapters->count() }}
                                                {{ Str::plural('chapter', $story->chapters_count ?? $story->chapters->count()) }}
                                            </span>


                                            @if ($story->difficulty)
                                                <span>•</span>
                                                <span>{{ ucfirst($story->difficulty) }}</span>
                                            @endif

                                            @if ($story->formatted_read_time)
                                                <span>•</span>
                                                <span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $story->formatted_read_time }}
                                                </span>
                                            @endif

                                        </div>

                                        {{-- Summary --}}
                                        @if ($story->summary)
                                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                                                {{ Str::limit($story->summary, 90) }}
                                            </p>
                                        @endif

                                        {{-- Footer: chapters & read time --}}
                                        <div class="d-flex justify-content-between align-items-center mt-2"
                                            style="font-size: 0.78rem; color: var(--muted);">
                                            @if (!$story->is_free && !Auth::user()?->isPremium())
                                                <span style="color: var(--gold);">
                                                    <i class="bi bi-arrow-up-circle me-1"></i>Upgrade to read
                                                </span>
                                            @else
                                                <span style="color: var(--emerald);">
                                                    @if (isset($story->user_progress))
                                                        Continue Reading →
                                                    @else
                                                        Read →
                                                    @endif
                                                </span>
                                            @endif
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
@endsection

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
            font-size: 0.68rem;
            padding: 0.2rem 0.55rem;
            border-radius: 50px;
            font-weight: 500;
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
        }

        .story-chip-gold {
            font-size: 0.68rem;
            padding: 0.2rem 0.55rem;
            border-radius: 50px;
            font-weight: 500;
            background: rgba(180, 130, 40, 0.1);
            color: var(--gold);
        }

        .story-badge-free {
            font-size: 0.65rem;
            padding: 0.18rem 0.55rem;
            border-radius: 50px;
            font-weight: 600;
            background: rgba(27, 94, 59, 0.1);
            color: var(--emerald);
        }

        .story-badge-premium {
            font-size: 0.65rem;
            padding: 0.18rem 0.55rem;
            border-radius: 50px;
            font-weight: 600;
            background: rgba(180, 130, 40, 0.1);
            color: var(--gold);
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

        .filter-item:hover,
        .filter-item.active {
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald);
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
    </style>
@endpush

@section('content')
    <div class="container py-5">

        {{-- ═══════════════════════════════════════════════
         PAGE HEADER
    ════════════════════════════════════════════════ --}}
        <div class="mb-5">
            <p
                style="color:var(--gold);
                  font-family:var(--font-heading);
                  font-size:0.78rem;
                  letter-spacing:0.14em;
                  text-transform:uppercase;
                  margin-bottom:0.4rem">
                Guided by the Quran
            </p>
            <h1 class="heading-font mb-2" style="font-size:clamp(1.8rem,4vw,2.5rem)">
                Prophet Stories
            </h1>
            <p class="text-muted" style="max-width:520px; line-height:1.8; font-size:0.92rem">
                Explore the lives of the prophets — their trials, miracles, and timeless
                lessons for every believer.
            </p>
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
                            <a href="{{ route('stories.index', ['prophet' => $p->slug]) }}"
                                class="filter-item mb-1 {{ request('prophet') === $p->slug ? 'active' : '' }}">

                                {{ $p->name_transliteration }}

                                @if ($p->slug === 'muhammad')
                                    <span style="font-size:0.8rem; color:var(--emerald)">ﷺ</span>
                                @endif

                                @if ($p->stories_count > 0)
                                    <span class="text-muted" style="font-size:0.72rem">
                                        ({{ $p->stories_count }})
                                    </span>
                                @else
                                    <span
                                        style="font-size:0.62rem;
                                             background:rgba(180,130,40,0.08);
                                             color:var(--gold);
                                             border-radius:50px;
                                             padding:0.1rem 0.45rem;
                                             margin-left:0.2rem">
                                        Soon
                                    </span>
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

                        @if (request('difficulty'))
                            <h5 class="heading-font mb-2">
                                {{ ucfirst(request('difficulty')) }} Stories Coming Soon
                            </h5>
                            <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                We are preparing authentic {{ request('difficulty') }} level stories.
                                Explore beginner stories for now.
                            </p>
                            <a href="{{ route('stories.index', ['difficulty' => 'beginner']) }}" class="btn-emerald btn">
                                Explore Beginner Stories
                            </a>
                        @elseif (request('prophet'))
                            <h5 class="heading-font mb-2">Coming Soon, In Shaa Allah</h5>
                            <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                We are preparing authentic stories for this prophet.
                                Explore other available stories for now.
                            </p>
                            <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                View All Stories
                            </a>
                        @else
                            <h5 class="heading-font mb-2">More Stories Coming Soon</h5>
                            <p class="text-muted small mb-3" style="max-width:360px; margin:0 auto">
                                We are building a complete collection of Prophet stories, in shaa Allah.
                            </p>
                            <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                Explore Available Stories
                            </a>
                        @endif
                    </div>

                    {{-- ── Story cards grid ── --}}
                @else
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                        @foreach ($stories as $story)
                            <div class="col">
                                <a href="{{ route('stories.show', $story->slug) }}" class="story-card">

                                    {{-- Cover --}}
                                    @if ($story->cover_image)
                                        <img src="{{ asset('storage/' . $story->cover_image) }}" alt="{{ $story->title }}"
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

                                        {{-- Badge row --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            @if ($story->is_free)
                                                <span class="story-badge-free">Free</span>
                                            @else
                                                <span class="story-badge-premium">
                                                    <i class="bi bi-lock-fill me-1"></i>Premium
                                                </span>
                                            @endif
                                            @if ($story->difficulty)
                                                <span class="story-chip">
                                                    {{ ucfirst($story->difficulty) }}
                                                </span>
                                            @endif
                                        </div>

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
                                                    <i class="bi bi-journal-text me-1"></i>
                                                    {{ $story->chapters_count ?? $story->chapters->count() }}
                                                    {{ Str::plural('chapters', $story->chapters_count ?? $story->chapters->count()) }}
                                                </span>
                                                @if ($story->read_time_minutes)
                                                    <span class="story-chip">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $story->read_time_minutes }}m
                                                    </span>
                                                @endif
                                            </div>

                                            @if (!$story->is_free && !Auth::user()?->isPremium())
                                                <span style="font-size:0.75rem; color:var(--gold)">
                                                    Upgrade →
                                                </span>
                                            @else
                                                <span style="font-size:0.78rem; color:var(--emerald); font-weight:500">
                                                    Read →
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

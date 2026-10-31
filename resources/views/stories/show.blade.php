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
            justify-content: center;
            /* ✅ flex-end se center kiya */
            padding: 2rem;
        }

        @media (max-width: 576px) {

            .story-hero-placeholder,
            .story-hero {
                height: 230px;
                padding: 1.25rem;
            }

            .story-hero-placeholder-arabic {
                bottom: 1.5rem;
                /* chaho to 2rem/2.5rem try kar sakte ho agar center title ke sath neeche thoda khaali lage */
                right: 1.5rem;
            }
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
            bottom: 2.2rem;
            right: 1.5rem;
            font-family: 'Amiri', 'Scheherazade New', serif;
            color: rgba(255, 255, 255, 0.07);
            line-height: 1;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            max-width: 55%;
            text-align: right;
        }

        .story-hero-placeholder-arabic.watermark-lg {
            font-size: 6.5rem;
        }

        .story-hero-placeholder-arabic.watermark-md {
            font-size: 5.5rem;
        }

        .story-hero-placeholder-arabic.watermark-sm {
            font-size: 4.2rem;
        }

        @media (max-width: 576px) {
            .story-hero-placeholder-arabic.watermark-lg {
                font-size: 3.2rem;
            }

            .story-hero-placeholder-arabic.watermark-md {
                font-size: 2.6rem;
            }

            .story-hero-placeholder-arabic.watermark-sm {
                font-size: 2.1rem;
            }
        }

        .story-hero-placeholder h1,
        .story-hero-placeholder p {
            max-width: 95%;
            /* taaki lamba title bhi watermark zone tak na pahunche */
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

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 480px) {
            .meta-row {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.5rem;
            }


            .meta-chip {
                flex: 0 0 auto;
            }

            /* 3rd chip (difficulty) akela hai, isko full-width span karao */
            .meta-row .meta-chip:last-child {
                grid-column: 1 / -1;
            }
        }

        /* Segmented — kam chapters ke liye */
        .progress-segments {
            display: flex;
            gap: 3px;
            margin-bottom: 0.75rem;
        }

        .progress-segment {
            flex: 1;
            height: 6px;
            border-radius: 3px;
            background: var(--border);
            transition: background 0.3s ease;
        }

        .progress-segment.is-filled {
            background: linear-gradient(90deg, var(--emerald), var(--gold));
        }

        /* Continuous — zyada chapters ke liye (already tha) */
        .progress-bar-islamic {
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.75rem;
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
            padding-top: 1rem;
            align-items: flex-start;
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
            color: var(--emerald-light);
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

        [data-bs-theme="dark"] .chapter-number {
            color: var(--emerald-light);
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

        .sidebar-cta-body .btn-emerald {
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            box-shadow: 0 2px 8px rgba(27, 94, 59, 0.15);
        }

        .sidebar-cta-body .btn-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27, 94, 59, 0.25);
        }

        /* ── Prophet sidebar card ── */
        .prophet-sidebar-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            overflow: hidden;
        }

        .prophet-sidebar-card-header {
            background: linear-gradient(135deg, var(--gold-dark, #8a6414), var(--gold));
            padding: 1.75rem 1.25rem;
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

        .sidebar-cta-arabic {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-cta-title {
            color: white;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-cta-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .breadcrumb-nav {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            -ms-overflow-style: none;
            scrollbar-width: none;
            padding-bottom: 0.25rem;
        }

        .about-heading-font {
            font-size: 0.75rem;
            color: var(--emerald);
            text-transform: uppercase;
            letter-spacing: 0.08rem;
        }

        [data-bs-theme="dark"] .about-heading-font {
            color: var(--emerald-light);
        }

        .breadcrumb-nav::-webkit-scrollbar {
            display: none;
        }

        .breadcrumb-link {
            color: var(--muted);
            text-decoration: none;
            flex-shrink: 0;
            transition: color 0.18s;
        }

        .breadcrumb-link:hover {
            color: var(--emerald);
        }

        .breadcrumb-separator {
            margin: 0 0.5rem;
            color: var(--border);
            flex-shrink: 0;
        }

        .breadcrumb-current {
            color: var(--ink);
            font-weight: 500;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .breadcrumb-current {
                max-width: 140px;
            }
        }

        .chapter-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            /* ✅ ye important hai */
        }

        .chapter-title-row .meta-chip-gold {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .sidebar-sticky-wrap {
            position: sticky;
            top: 80px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            max-height: calc(100vh - 100px);
            /* ✅ chhoti screens pe sidebar cut na ho */
            overflow-y: auto;
            scrollbar-width: thin;
        }


        .sidebar-sticky-wrap::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-sticky-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        @media (max-width: 991px) {
            .sidebar-sticky-wrap {
                position: static;
                /* sticky hata do mobile pe */
                max-height: none;
                overflow-y: visible;
            }
        }

        @media (max-width: 991px) {
            .sidebar-cta {
                order: -1;
                /* ✅ ye sabse pehle dikhega mobile pe */
            }
        }

        .sidebar-cta,
        .prophet-sidebar-card {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .chapter-number.is-current {
            background: var(--emerald);
            color: white;
        }

        .chapter-list-item.is-current {
            border-left: 3px solid var(--emerald);
            background: rgba(27, 94, 59, 0.04);
        }

        .sidebar-cta-header.state-completed {
            background: linear-gradient(135deg, var(--gold-dark, #8a6414), var(--gold));
        }

        .sidebar-cta-header.state-continue {
            background: linear-gradient(135deg, var(--emerald-dark), var(--emerald));
        }

        .sidebar-cta-header.state-begin {
            background: linear-gradient(135deg, var(--emerald), var(--emerald-light));
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="container py-5">

            {{-- Breadcrumb --}}
            <nav class="mb-4 breadcrumb-nav">
                <a href="{{ route('stories.index') }}" class="breadcrumb-link">Stories</a>
                @if ($story->prophet)
                    <span class="breadcrumb-separator">/</span>
                    <a href="{{ route('stories.index', ['prophet' => $story->prophet->slug]) }}" class="breadcrumb-link">
                        {!! $story->prophet->display_name_html !!}
                    </a>
                @endif
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current" title="{{ $story->title }}">
                    {{ $story->title }}
                </span>
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
                                <h1 class="heading-font mb-1" style="font-size:clamp(1.5rem,4vw,2rem); color:white">
                                    {{ $story->title }}
                                </h1>
                                @if ($story->prophet)
                                    <p class="mb-0" style="color:rgba(255,255,255,0.75); font-size:0.88rem">
                                        {!! $story->prophet->display_name_html !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- Geometric placeholder hero — same language as prophet cards --}}
                        <div class="story-hero-placeholder mb-4">

                            @php
                                $arabicLength = mb_strlen(strip_tags($story->prophet->name_arabic_watermark ?? ''));
                                $watermarkSizeClass = match (true) {
                                    $arabicLength <= 4 => 'watermark-lg', // Muhammad, Nuh jaise chhote naam
                                    $arabicLength <= 6 => 'watermark-md', // medium naam
                                    default => 'watermark-sm', // Idris, Ibrahim jaise lambe naam
                                };
                            @endphp
                            {{-- Ghost Arabic watermark --}}
                            @if ($story->prophet)
                                <span class="story-hero-placeholder-arabic {{ $watermarkSizeClass }}">
                                    {{ $story->prophet->name_arabic_watermark }}
                                </span>
                            @endif

                            {{-- Title --}}
                            <h1 class="heading-font mb-1" style="font-size:clamp(1.4rem,3.5vw,1.9rem);
                                                                                               color:white;
                                                                                               line-height:1.25;
                                                                                               position:relative;
                                                                                               z-index:1">
                                {!! \App\Helpers\ArabicHelper::wrapHonorificsInHtml($story->title) !!}

                            </h1>

                            {{-- Prophet name --}}
                            @if ($story->prophet)
                                <p style="color:rgba(255,255,255,0.65);
                                                                            font-size:0.88rem;
                                            margin:0;
                                            position:relative;
                                            z-index:1">
                                    {!! $story->prophet->display_name_html !!}
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Meta row --}}
                    <div class="meta-row mb-4">
                        @if ($story->read_time_minutes)
                            <span class="meta-chip">
                                <i class="bi bi-hourglass-split me-1"></i>{{ $story->read_time_minutes }} min read
                            </span>
                        @endif
                        <span class="meta-chip">
                            <i class="bi {{ $story->chapters->count() > 1 ? 'bi-journals' : 'bi-journal-text' }} me-1"></i>
                            {{ $story->chapters->count() }} {{ Str::plural('Chapter', $story->chapters->count()) }}
                        </span>
                        @php
                            $difficultyIcon = match ($story->difficulty) {
                                'beginner' => 'bi-reception-2',
                                'intermediate' => 'bi-reception-3',
                                'advanced' => 'bi-reception-4',
                                default => 'bi-reception-1',
                            };
                        @endphp
                        <span class="meta-chip">
                            <i class="bi {{ $difficultyIcon }} me-1"></i>{{ ucfirst($story->difficulty) }}
                        </span>
                    </div>
                    {{-- Summary --}}
                    @if ($story->summary)
                        <div class="card-islamic p-4 mb-4">
                            <h6 class="about-heading-font mb-2"> About This Story </h6>
                            <p class="mb-0" style="line-height:1.85; color:var(--ink-soft)">
                                {{ $story->summary }}
                            </p>
                        </div>
                    @endif

                    <div class="d-lg-none mb-4">
                        @include('partials.sidebar-cta')
                    </div>

                    {{-- Reading progress --}}
                    @if ($progress)
                        @php
                            $lastChapter = $story->chapters->firstWhere('id', $progress->last_chapter_id);
                            $completedCount = count($completedChapterIds ?? []);
                            $progressPct = $totalChapters > 0 ? round(($completedCount / $totalChapters) * 100) : 0;

                            // ✅ 12 se kam chapters ho to segments dikhao, warna continuous bar
                            $useSegments = $totalChapters > 0 && $totalChapters <= 12;
                        @endphp
                        <div class="card-islamic p-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="heading-font mb-0"
                                    style="font-size:0.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.08em">
                                    Your Progress
                                </h6>
                                @if ($isStoryCompleted)
                                    <span class="meta-chip-gold" style="font-size:0.75rem">
                                        <i class="bi bi-check-circle-fill me-1"></i>Completed
                                    </span>
                                @else
                                    <span style="font-size:0.8rem; color:var(--emerald-light); font-weight:500">
                                        {{ $progressPct }}%
                                    </span>
                                @endif
                            </div>

                            @if ($useSegments)
                                <div class="progress-segments">
                                    @foreach ($story->chapters as $chapter)
                                        <div
                                            class="progress-segment {{ in_array($chapter->id, $completedChapterIds ?? []) ? 'is-filled' : '' }}">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="progress-bar-islamic">
                                    <div class="progress-bar-islamic-fill" style="width:{{ $progressPct }}%"></div>
                                </div>
                            @endif

                            @if ($lastChapter)
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-bookmark-fill me-1" style="color:var(--gold)"></i>
                                    You last visited: <strong>{{ $lastChapter->title }}</strong>
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
                                $isCurrent = $continueChapter && $continueChapter->id === $chapter->id;
                            @endphp

                            <a href="{{ route('stories.chapter', [$story->slug, $chapter->slug]) }}"
                                class="chapter-list-item {{ $isCompleted ? 'is-completed' : '' }} {{ $isCurrent ? 'is-current' : '' }}">

                                <div
                                    class="chapter-number {{ $isCompleted ? 'is-completed' : '' }} {{ $isCurrent ? 'is-current' : '' }}">
                                    @if ($isCompleted)
                                        <i class="bi bi-check"></i>
                                    @else
                                        {{ $chapter->order }}
                                    @endif
                                </div>

                                <div class="flex-grow-1">
                                    <div class="chapter-title-row">

                                        <span style="font-size:0.9rem; font-weight:500">
                                            {{ $chapter->title }}
                                        </span>
                                        @if ($loop->first && !$continueChapter && !$isStoryCompleted)
                                            <span class="meta-chip" style="font-size:0.65rem">Start Here <i
                                                    class="bi bi-arrow-right"></i></span>
                                        @endif
                                        @if ($isCurrent)
                                            <span class="meta-chip-gold" style="font-size:0.7rem">
                                                Continue <i class="bi bi-arrow-right"></i>
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
                    <div class="sidebar-sticky-wrap">

                        <div class="d-none d-lg-block">
                            @include('partials.sidebar-cta')
                        </div>

                        {{-- Prophet info card --}}
                        @if ($story->prophet)
                            <div class="prophet-sidebar-card">
                                <div class="prophet-sidebar-card-header">
                                    <div class="prophet-sidebar-arabic">
                                        {!! $story->prophet->name_arabic_html !!}
                                    </div>
                                    @if ($story->prophet->title_arabic)
                                        <div class="prophet-sidebar-title-arabic">
                                            {{ $story->prophet->title_arabic }}
                                        </div>
                                    @endif
                                    <div class="heading-font mt-2" style="color:rgba(255,255,255,0.85); font-size:0.95rem">
                                        {!! $story->prophet->display_name_html !!}
                                    </div>
                                </div>
                                @php
                                    $hasMultipleStories = $story->prophet->stories()->published()->count() > 1;
                                @endphp

                                <div class="prophet-sidebar-card-body text-center">
                                    @if ($hasMultipleStories)
                                        <a href="{{ route('stories.index', ['prophet' => $story->prophet->slug]) }}"
                                            class="btn btn-outline-secondary w-100 mb-2" style="font-size:0.82rem">
                                            <i class="bi bi-journal-text me-1"></i>
                                            All Stories of {!! $story->prophet->display_name_html !!}
                                        </a>
                                    @endif

                                    <a href="{{ route('prophets.show', $story->prophet->slug) }}" class="btn-emerald btn w-100"
                                        style="font-size:0.82rem">
                                        <i class="bi bi-person-badge me-1"></i>
                                        Learn More About {{ $story->prophet->name_transliteration }}
                                        @if ($story->prophet->slug === 'muhammad')
                                            <bdi><span class="arabic-symbol">ﷺ</span></bdi>
                                        @else
                                            <span class="honorific"
                                                style="color:white">{{ $story->prophet->honorific ?? 'AS' }}</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resetProgressModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:var(--radius); border:1px solid var(--border)">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle" style="font-size:2rem; color:var(--gold)"></i>
                        </div>
                        <h6 class="heading-font mb-2">Reset Progress?</h6>
                        <p class="text-muted small mb-4">
                            This will reset your progress for this story. This action cannot be
                            undone.
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <form action="{{ route('stories.reset', $story->slug) }}" method="POST" class="w-50">
                                @csrf
                                <button type="submit" class="btn-emerald btn w-100">Yes,
                                    Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="resetProgressModalContinue" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:var(--radius); border:1px solid var(--border)">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle" style="font-size:2rem; color:var(--gold)"></i>
                        </div>
                        <h6 class="heading-font mb-2">Start from Beginning?</h6>
                        <p class="text-muted small mb-4">
                            This will reset your progress and start you back at Chapter 1.
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <form action="{{ route('stories.reset', $story->slug) }}" method="POST" class="w-50">
                                @csrf
                                <button type="submit" class="btn-emerald btn w-100">Yes,
                                    Restart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
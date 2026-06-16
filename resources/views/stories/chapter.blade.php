@extends('layouts.app')
@section('title', $chapter->title . ' — ' . $story->title)

@push('styles')
    <style>
        .story-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .story-layout {
                grid-template-columns: 1fr;
            }
        }


        .chapter-list {
            max-height: calc(100vh - 320px);
            overflow-y: auto;
        }

        .chapter-sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }


        .chapter-nav-item {
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            color: var(--ink-soft);
            text-decoration: none;
            display: block;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .chapter-nav-item:hover {
            background: var(--cream-dark);
            color: var(--ink);
        }

        .chapter-nav-item.active {
            background: rgba(27, 94, 59, 0.08);
            border-color: var(--emerald);
            color: var(--emerald);
            font-weight: 500;
            scroll-margin-top: 100px;
        }

        .story-content {
            font-family: var(--font-body);
            font-size: 1.1rem;
            line-height: 1.95;
            color: var(--ink);
        }

        .story-content p {
            margin-bottom: 1.25rem;
        }

        .story-content em {
            color: var(--emerald-dark);
            font-style: italic;
        }

        .story-content strong {
            color: var(--ink);
        }

        .story-content blockquote {
            border-left: 3px solid var(--gold);
            padding-left: 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: var(--ink-soft);
        }

        .quran-ref-chip {
            background: rgba(27, 94, 59, 0.08);
            border: 1px solid rgba(27, 94, 59, 0.2);
            border-radius: 50px;
            padding: 0.2rem 0.7rem;
            font-size: 0.78rem;
            color: var(--emerald);
            text-decoration: none;
            transition: all 0.2s;
        }

        .quran-ref-chip:hover {
            background: var(--emerald);
            color: white;
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
    <div class="container py-4">

        {{-- Breadcrumb --}}
        <nav class="mb-3" style="font-size:0.85rem">
            <a href="{{ route('stories.index') }}" class="text-muted text-decoration-none">Stories</a>
            <span class="mx-2 text-muted">/</span>
            <a href="{{ route('stories.show', $story->slug) }}" class="text-muted text-decoration-none">
                {{ $story->title }}
            </a>
            <span class="mx-2 text-muted">/</span>
            <span>{{ $chapter->title }}</span>
        </nav>

        {{-- Progress bar --}}
        <div class="progress-bar-islamic mb-3">
            <div class="progress-bar-islamic-fill" id="progressFill"
                style="width:{{ round(($chapter->order / $allChapters->count()) * 100) }}%">
            </div>
        </div>
        <p class="text-muted mb-1">
            {{ $story->title }}
        </p>
        <p class="text-muted mb-4" style="font-size:0.78rem">
            Chapter {{ $chapter->order }} of {{ $allChapters->count() }} —
            {{ round(($chapter->order / $allChapters->count()) * 100) }}% complete
        </p>

        {{-- Mobile chapter selector --}}
        <div class="d-md-none mb-3">
            <select class="form-select form-select-sm" onchange="window.location.href=this.value"
                style="border-color:var(--border)">
                @foreach ($allChapters as $ch)
                    <option value="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}"
                        {{ $ch->id === $chapter->id ? 'selected' : '' }}>
                        {{ $ch->order }}. {{ $ch->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="story-layout">

            {{-- LEFT: Chapter navigation sidebar (desktop only) --}}
            <aside class="chapter-sidebar d-none d-md-block">
                <div class="card-islamic p-3">
                    <h6 class="heading-font mb-3" style="font-size:0.85rem; color:var(--muted)">
                        CHAPTERS
                    </h6>
                    <div class="chapter-list">
                        @foreach ($allChapters as $ch)
                            <a href="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}"
                                class="chapter-nav-item {{ $ch->id === $chapter->id ? 'active' : '' }} mb-1">
                                <span class="me-2" style="color:var(--gold); font-size:0.75rem">
                                    {{ $ch->order }}
                                </span>
                                {{ $ch->title }}
                            </a>
                        @endforeach

                        {{-- Quran references --}}
                        @if ($chapter->quran_references)
                            <hr class="divider-gold">
                            <h6 class="heading-font mb-2" style="font-size:0.8rem; color:var(--muted)">
                                Qur'an References
                            </h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($chapter->quran_references as $ref)
                                    @php
                                        [$surah, $ayah] = explode(':', $ref);
                                    @endphp

                                    <a href="{{ route('quran.index', [
                                        'surah' => $surah, // MUST be slug from DB
                                        'ayah' => $ayah,
                                    ]) }}"
                                        class="quran-ref-chip">
                                        {{ $ref }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Hadith references --}}
                        @if ($chapter->hadith_references)
                            <div class="card-islamic p-3 mt-4">
                                <h6 class="heading-font mb-2" style="font-size:0.8rem; color:var(--muted)">
                                    Hadith References
                                </h6>

                                @foreach ($chapter->hadith_references as $hadith)
                                    <div class="text-muted small mb-1">
                                        • {{ $hadith }}
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Story info --}}
                        <hr class="divider-gold">
                        <div style="font-size:0.78rem; color:var(--muted)">
                            <div class="mb-1">
                                <i class="bi bi-clock me-1"></i>
                                {{ $story->read_time_minutes }} min total read
                            </div>
                            @if ($story->prophet)
                                <div>
                                    <i class="bi bi-person me-1"></i>
                                    {{ $story->prophet->display_name }}
                                </div>
                            @endif
                        </div>

                    </div>
            </aside>

            {{-- RIGHT: Chapter content --}}
            <article>

                {{-- Chapter header --}}
                <div class="mb-4">
                    <span
                        style="color:var(--gold);
                                 font-size:0.8rem;
                                 font-family:var(--font-heading)">
                        CHAPTER {{ $chapter->order }} OF {{ $allChapters->count() }}
                    </span>
                    <h1 class="heading-font mt-1 mb-0" style="font-size:clamp(1.5rem, 4vw, 2.2rem)">
                        {{ $chapter->title }}
                    </h1>
                    @if ($chapter->read_time_minutes)
                        <p class="text-muted mb-0" style="font-size:0.85rem">
                            <i class="bi bi-clock me-1"></i>
                            {{ $chapter->read_time_minutes }} min read
                        </p>
                    @endif
                </div>

                {{-- Chapter image --}}
                @if ($chapter->image)
                    <img src="{{ asset('storage/' . $chapter->image) }}" alt="{{ $chapter->title }}"
                        class="img-fluid rounded mb-4 w-100" style="max-height:350px; object-fit:cover">
                @endif

                {{-- Chapter body --}}
                <div class="story-content">
                    {!! $chapter->content !!}
                </div>

                {{-- Quran references (mobile) --}}
                @if ($chapter->quran_references)
                    <div class="d-md-none mt-3 mb-3">
                        <h6 class="heading-font mb-2" style="font-size:0.8rem; color:var(--muted)">
                            QURAN REFERENCES
                        </h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($chapter->quran_references as $ref)
                                <a href="{{ route('quran.index') }}" class="quran-ref-chip">
                                    {{ $ref }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <hr class="divider-gold">

                {{-- Navigation --}}
                <div class="d-flex justify-content-between
                            align-items-center flex-wrap gap-3">

                    @if ($prevChapter)
                        <a href="{{ route('stories.chapter', [$story->slug, $prevChapter->slug]) }}"
                            class="btn-emerald btn">
                            <i class="bi bi-arrow-left me-2"></i>{{ $prevChapter->title }}
                        </a>
                    @else
                        <div></div>
                    @endif

                    {{-- Mark as read --}}
                    @auth
                        <button id="markCompleteBtn" class="btn-gold btn"
                            onclick="markComplete({{ $story->slug }}, {{ $chapter->slug }})">
                            <i class="bi bi-check-circle me-1"></i>Mark as Read
                        </button>
                    @endauth

                    @if ($nextChapter)
                        <a href="{{ route('stories.chapter', [$story->slug, $nextChapter->slug]) }}"
                            class="btn-emerald btn">
                            {{ $nextChapter->title }}
                            <i class="bi bi-arrow-right ms-2"></i>

                        </a>
                    @else
                        <div class="card-islamic p-3 text-center mt-2">
                            <div style="font-size:1.5rem;">🎉</div>
                            <h6 class="heading-font mt-2">
                                Story Completed
                            </h6>
                            <p class="text-muted">
                                You have completed {{ $story->title }}.
                            </p>

                            <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                                Explore More Stories
                            </a>
                        </div>
                    @endif
                    @if ($chapter->lessons)
                        <div class="card-islamic p-4 mt-4">
                            <h6 class="heading-font mb-3">
                                Lessons From This Chapter
                            </h6>

                            {!! $chapter->lessons !!}
                        </div>
                    @endif

                </div>

            </article>
        </div>
    </div>

    @push('scripts')
        <script>
            function markComplete(storySlug, chapterSlug) {
                fetch(`/stories/${storySlug}/chapters/${chapterSlug}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        const btn = document.getElementById('markCompleteBtn');
                        btn.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${data.message}`;
                        btn.disabled = true;
                        document.getElementById('progressFill').style.width = data.percentage + '%';
                    });
            }

            document.querySelector('.chapter-nav-item.active')
                ?.scrollIntoView({
                    block: 'center'
                });
        </script>
    @endpush

@endsection

@extends('layouts.app')
@section('title', $chapter->title . ' — ' . $story->title)

@push('styles')
    <style>
        .story-layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 2rem;
            align-items: start;
        }



        @media (max-width: 991.98px) {
            .story-layout {
                grid-template-columns: 1fr;
            }

            .chapter-sidebar {
                display: none;
            }
        }

        /* ── Sidebar (desktop) ── */
        .chapter-sidebar {
            position: sticky;
            top: 92px;
            align-self: start;
            height: calc(100vh - 92px - 24px);
            /* ← ye line add karo */
            max-height: calc(100vh - 92px - 24px);
            overflow: hidden;
            /* ← safety net */
        }

        @media (max-width: 991.98px) {
            .chapter-sidebar {
                display: none;
            }
        }

        .chapter-sidebar-card {
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--cream);
            max-height: 100%;
            position: relative;
            overflow-y: auto;
            padding: 1.1rem;
        }

        .chapter-nav-item {
            padding: 0.6rem 0.85rem;
            border-radius: var(--radius);
            font-size: 0.84rem;
            color: var(--ink-soft);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.18s;
            margin-bottom: 0.2rem;
        }

        .chapter-nav-item:hover {
            background: rgba(27, 94, 59, 0.05);
            color: var(--emerald);
        }

        .chapter-nav-item.active {
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald-light);
            font-weight: 600;
        }

        .chapter-nav-num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(27, 94, 59, 0.08);
            color: var(--emerald-light);
            font-size: 0.68rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chapter-nav-item.active .chapter-nav-num {
            background: var(--gold);
            color: white;
        }

        .chapter-nav-num.completed {
            background: var(--emerald-light);
            color: white;
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-family: var(--font-heading);
            margin-bottom: 0.6rem;
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

        .chapter-count {
            color: var(--gold-dark);
        }

        [data-bs-theme="dark"] .chapter-count {
            color: var(--gold-light);
        }


        /* ── Reading content ── */

        .reading-column {
            max-width: 48rem;
            margin-inline: auto;
        }

        .story-content {
            font-family: var(--font-body);
            font-size: calc(clamp(1.02rem, 0.96rem + 0.35vw, 1.15rem) * var(--read-scale, 1));
            overflow-wrap: break-word;
            line-height: 1.9;
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
            background: rgba(27, 94, 59, 0.04);
            border-left: 3px solid var(--gold);
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 1.1rem 1.5rem;
            margin: 1.5rem 0;
            color: var(--gold-dark);
        }

        [data-bs-theme="dark"] .story-content blockquote {
            color: var(--gold-light);
        }

        .story-content blockquote p:first-child {
            font-family: 'Amiri', 'Scheherazade New', serif;
            font-size: calc(1.5rem * var(--read-scale, 1));
            direction: rtl;
            text-align: right;
            line-height: 2;
            color: var(--emerald-light);
            margin-bottom: 0.6rem;
        }

        .story-content blockquote small {
            color: var(--gold);
            font-size: 0.78rem;
            font-style: normal;
        }

        .story-content img,
        .story-content table {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 575.98px) {
            .story-content {
                line-height: 1.85;
            }

            .story-content p {
                margin-bottom: 1.1rem;
            }

            .story-content blockquote {
                padding: 0.9rem 1rem;
                margin: 1.25rem 0;
            }

            .story-content blockquote p:first-child {
                font-size: calc(1.3rem * var(--read-scale, 1));
                line-height: 1.95;
            }
        }

        /* ── Quran reference chips ── */
        .quran-ref-chip {
            background: rgba(27, 94, 59, 0.08);
            border: 1px solid rgba(27, 94, 59, 0.18);
            border-radius: 50px;
            padding: 0.22rem 0.7rem;
            font-size: 0.76rem;
            color: var(--emerald-light);
            text-decoration: none;
            transition: all 0.18s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .quran-ref-chip:hover {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald);
        }

        /* ── Mobile chapter selector ── */
        .mobile-chapter-select {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.6rem 0.9rem;
            font-size: 0.85rem;
            background: var(--cream);
            width: 100%;
        }

        .mobile-chapter-select {
            color: var(--ink);
            cursor: pointer;
        }

        .chapter-dropdown-menu {
            max-height: 60vh;
            overflow-y: auto;
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .chapter-dropdown-menu .dropdown-item {
            white-space: normal;
            font-size: 0.85rem;
            color: var(--ink);
            padding: 0.75rem 0.9rem;
            min-height: 44px;
        }

        .chapter-dropdown-menu .dropdown-item.active {
            background: rgba(27, 94, 59, 0.1);
            color: var(--emerald);
            font-weight: 600;
        }

        [data-bs-theme="dark"] .chapter-dropdown-menu .dropdown-item.active {
            color: var(--emerald-light);
        }

        /* ── Completion card ── */
        .completion-card {
            border-radius: var(--radius);
            background: linear-gradient(150deg, var(--emerald-dark), var(--emerald));
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .completion-card>* {
            position: relative;
            z-index: 1;
        }

        .cc-arabic {
            font-family: 'Amiri', serif;
            font-size: 1.8rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .cc-title {
            color: white;
        }

        .cc-text {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .cc-stats {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.78rem;
            margin-bottom: 1.25rem;
        }

        .cc-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.6rem;
        }

        .cc-btn-gold {
            background: var(--gold);
            color: #1A1A2E;
            font-weight: 600;
        }

        .cc-btn-gold:hover {
            background: var(--gold-light);
        }

        .cc-btn-ghost {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.45);
        }

        .cc-btn-ghost:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        @media (max-width: 575.98px) {
            .completion-card {
                padding: 1.5rem 1.1rem;
            }

            .cc-actions .btn {
                width: 100%;
            }
        }

        .completion-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        /* ── Prev / Mark / Next ── */
        .chapter-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            align-items: stretch;
            gap: 0.75rem;
        }

        .chapter-nav-spacer {
            display: none;
        }

        .chapter-nav-btn {
            display: flex;
            align-items: center;
            width: fit-content;
            min-width: 0;
            max-width: 100%;
            padding: 0.7rem 1rem;
        }

        .chapter-nav-btn.prev {
            grid-column: 1;
            justify-self: start;
        }

        .chapter-mark-btn {
            grid-column: 2;
            white-space: nowrap;
        }

        .chapter-nav-btn.next {
            grid-column: 3;
            justify-self: end;
        }

        .chapter-nav-title {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Mobile: Mark as Read upar full-width, Prev/Next neeche barabar width me */
        @media (max-width: 575.98px) {
            .chapter-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .chapter-mark-btn {
                grid-column: 1 / -1;
                order: -1;
                width: 100%;
            }

            .chapter-nav-btn {
                width: 100%;
                min-width: 0;
                padding: 0.7rem 0.8rem;
                font-size: 0.85rem;
            }

            .chapter-nav-btn.prev {
                grid-column: 1;
            }

            .chapter-nav-btn.next {
                grid-column: 2;
            }
        }

        /* ── Breadcrumb ── */
        .chapter-crumbs {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            min-width: 0;
        }

        .chapter-crumb-link {
            color: var(--muted);
            text-decoration: none;
            flex-shrink: 0;
        }

        .chapter-crumb-link:hover {
            color: var(--emerald);
        }

        .chapter-crumb-sep {
            margin: 0 0.5rem;
            color: var(--border);
            flex-shrink: 0;
        }

        .chapter-crumb-story {
            flex-shrink: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ── Segmented progress ── */
        .chapter-segs {
            display: flex;
            gap: 4px;
        }

        .chapter-seg {
            position: relative;
            flex: 1;
            height: 8px;
            border-radius: 4px;
            background: var(--border);
            transition: background 0.3s ease;
        }

        /* tap area bada, mobile pe 8px ki bar dabana mushkil hota hai */
        .chapter-seg::after {
            content: '';
            position: absolute;
            inset: -10px 0;
        }

        .chapter-seg.done {
            background: linear-gradient(90deg, var(--emerald), var(--gold));
        }

        .chapter-seg.current {
            box-shadow: 0 0 0 2px var(--gold);
        }

        .chapter-complete-toast {
            width: max-content;
            max-width: calc(100vw - 1.5rem);
        }

        .chapter-complete-toast .toast-ar {
            font-family: 'Amiri', serif;
            font-size: 1.3rem;
            color: var(--gold-light);
        }

        .chapter-complete-toast .toast-txt {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.9);
            margin-left: 0.6rem;
        }

        @media (max-width: 575.98px) {
            .chapter-complete-toast {
                top: 72px !important;
                padding: 0.65rem 1.1rem !important;
            }

            .chapter-complete-toast .toast-ar {
                font-size: 1.15rem;
            }

            .chapter-complete-toast .toast-txt {
                font-size: 0.85rem;
                white-space: nowrap;
            }
        }

        .read-size-ctrl {
            display: inline-flex;
            border: 1px solid var(--border);
            border-radius: 50px;
            overflow: hidden;
        }

        .read-size-ctrl button {
            background: transparent;
            border: 0;
            color: var(--ink-soft);
            padding: 0.35rem 0.85rem;
            min-width: 44px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .read-size-ctrl button+button {
            border-left: 1px solid var(--border);
        }

        .read-size-ctrl button:hover {
            background: rgba(27, 94, 59, 0.06);
            color: var(--emerald-light);
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">

        {{-- Breadcrumb --}}
        {{-- Breadcrumb --}}
        <nav class="chapter-crumbs mb-3">
            <a href="{{ route('stories.index') }}" class="chapter-crumb-link">Stories</a>
            <span class="chapter-crumb-sep">/</span>
            <a href="{{ route('stories.show', $story->slug) }}" class="chapter-crumb-link chapter-crumb-story"
                title="{{ $story->title }}">{{ $story->title }}</a>
        </nav>

        {{-- Progress strip (logged-in users only) --}}
        @auth
            @php
                $pct = $totalChapters > 0 ? round(($completedChaptersCount / $totalChapters) * 100) : 0;
                $useSegments = $totalChapters > 0 && $totalChapters <= 12;
            @endphp
            <div class="chapter-progress mb-4">
                @if ($useSegments)
                    <div class="chapter-segs">
                        @foreach ($allChapters as $ch)
                            <a href="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}"
                                class="chapter-seg {{ in_array($ch->id, $completedChapterIds ?? []) ? 'done' : '' }} {{ $ch->id === $chapter->id ? 'current' : '' }}"
                                title="{{ $ch->order }}. {{ $ch->title }}"></a>
                        @endforeach
                    </div>
                @else
                    <div class="progress-bar-islamic">
                        <div class="progress-bar-islamic-fill" id="progressFill" style="width:{{ $pct }}%"></div>
                    </div>
                @endif
                <div class="d-flex justify-content-between mt-2">
                    <span class="text-muted" style="font-size:0.78rem">Your progress</span>
                    <span class="text-muted" style="font-size:0.78rem" id="progressText">{{ $pct }}% completed</span>
                </div>
            </div>
        @endauth
        {{-- Mobile chapter selector --}}
        <div class="d-lg-none mb-3 dropdown">
            <button class="mobile-chapter-select d-flex justify-content-between align-items-center text-start" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="text-truncate">{{ $chapter->order }}. {{ $chapter->title }}</span>
                <i class="bi bi-chevron-down ms-2"></i>
            </button>
            <ul class="dropdown-menu w-100 chapter-dropdown-menu">
                @foreach ($allChapters as $ch)
                    <li>
                        <a class="dropdown-item {{ $ch->id === $chapter->id ? 'active' : '' }}"
                            href="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}">
                            {{ in_array($ch->id, $completedChapterIds ?? []) ? '✓' : $ch->order }}. {{ $ch->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="story-layout">

            {{-- ═══════════════════════════
            LEFT — Sidebar (desktop only)
            ════════════════════════════ --}}
            <aside class="chapter-sidebar">
                <div class="chapter-sidebar-card">

                    <p class="sidebar-section-title">Chapters</p>
                    @foreach ($allChapters as $ch)
                        <a href="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}"
                            class="chapter-nav-item {{ $ch->id === $chapter->id ? 'active' : '' }}">
                            <span
                                class="chapter-nav-num {{ in_array($ch->id, $completedChapterIds ?? []) ? 'completed' : '' }}">
                                @if (in_array($ch->id, $completedChapterIds ?? []))
                                    <i class="bi bi-check-lg" style="font-size:0.75rem"></i>
                                @else
                                    {{ $ch->order }}
                                @endif
                            </span>
                            {{ $ch->title }}
                            @if (in_array($ch->id, $completedChapterIds ?? []))
                                <i class="bi bi-check-circle-fill ms-auto"
                                    style="font-size:0.7rem; color:var(--emerald-light); flex-shrink:0"></i>
                            @endif
                        </a>
                    @endforeach

                    {{-- Quran references --}}
                    @if ($chapter->quran_references)
                        <hr class="divider-gold my-3">
                        <p class="sidebar-section-title">Qur'an References</p>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            @include('stories.partials.quran-refs')
                        </div>
                    @endif

                    <hr class="divider-gold my-3">
                    <div style="font-size:0.76rem; color:var(--muted)">
                        @if ($story->read_time_minutes)
                            <div class="mb-1">
                                <i class="bi bi-clock me-1"></i>
                                {{ $story->read_time_minutes }} min total read
                            </div>
                        @endif
                        @if ($story->prophet)
                            <div>
                                <i class="bi bi-person me-1"></i>
                                {{ $story->prophet->display_name }}
                            </div>
                        @endif
                    </div>

                </div>
            </aside>

            {{-- ═══════════════════════════
            RIGHT — Chapter content
            ════════════════════════════ --}}
            <article class="reading-column">
                {{-- Chapter header --}}
                <div class="mb-4">
                    <span class="chapter-count"
                        style="font-size:0.78rem; font-family:var(--font-heading); letter-spacing:0.05em">
                        CHAPTER {{ $chapter->order }} OF {{ $allChapters->count() }}
                    </span>
                    <h1 class="heading-font mt-1 mb-0" style="font-size:clamp(1.5rem,4vw,2.2rem)">
                        {{ $chapter->title }}
                    </h1>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted" style="font-size:0.85rem">
                            @if ($chapter->read_time_minutes)
                                <i class="bi bi-clock me-1"></i>{{ $chapter->read_time_minutes }} min read
                            @endif
                        </span>
                        <div class="read-size-ctrl" role="group" aria-label="Text size">
                            <button type="button" onclick="setReadScale(-1)" aria-label="Smaller text">A−</button>
                            <button type="button" onclick="setReadScale(1)" aria-label="Larger text">A+</button>
                        </div>
                    </div>
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

                {{-- Quran references (mobile only) --}}
                @if ($chapter->quran_references)
                    <div class="d-lg-none mt-3 mb-4">
                        <p class="sidebar-section-title">Qur'an References</p>
                        <div class="d-flex flex-wrap gap-1">
                            @include('stories.partials.quran-refs')
                        </div>
                    </div>
                @endif

                <hr class="divider-gold">

                {{-- Navigation --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                    {{-- Navigation --}}
                    <div class="chapter-actions mb-4">

                        @if ($prevChapter)
                            <a href="{{ route('stories.chapter', [$story->slug, $prevChapter->slug]) }}"
                                class="btn-emerald btn chapter-nav-btn prev" title="{{ $prevChapter->title }}">
                                <i class="bi bi-arrow-left me-2"></i>
                                <span class="chapter-nav-title">{{ $prevChapter->title }}</span>
                            </a>
                        @else
                            <div class="chapter-nav-spacer"></div>
                        @endif

                        @auth
                            @if ($isChapterCompleted)
                                <button class="btn-gold btn chapter-mark-btn" disabled style="opacity:0.85">
                                    <i class="bi bi-check-circle-fill me-1"></i>Completed
                                </button>
                            @else
                                <button id="markCompleteBtn" class="btn-gold btn chapter-mark-btn"
                                    onclick="markComplete('{{ $story->slug }}', '{{ $chapter->slug }}')">
                                    <i class="bi bi-check-circle me-1"></i>Mark as Read
                                </button>
                            @endif
                        @endauth

                        @if ($nextChapter)
                            <a href="{{ route('stories.chapter', [$story->slug, $nextChapter->slug]) }}"
                                class="btn-emerald btn chapter-nav-btn next" title="{{ $nextChapter->title }}">
                                <span class="chapter-nav-title">{{ $nextChapter->title }}</span>
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @else
                            <div class="chapter-nav-spacer"></div>
                        @endif

                    </div>
                </div>

                {{-- Story completed --}}
                @if ($isStoryCompleted)
                    <div class="completion-card" id="completionCard">
                        <div class="cc-arabic">الْحَمْدُ لِلَّٰهِ</div>
                        <h5 class="heading-font mt-2 mb-1 cc-title">Story Completed</h5>
                        <p class="cc-text">You have completed {{ $story->title }}.</p>
                        <p class="cc-stats">
                            {{ $totalChapters }} {{ Str::plural('chapter', $totalChapters) }}
                            @if ($story->read_time_minutes) · {{ $story->read_time_minutes }} min read @endif
                        </p>
                        <div class="cc-actions">
                            <a href="{{ route('stories.index') }}" class="btn cc-btn-gold">Explore More Stories</a>
                            <a href="{{ route('stories.show', $story->slug) }}" class="btn cc-btn-ghost">Back to Story</a>
                        </div>
                    </div>
                @endif

            </article>
        </div>
    </div>

    @push('scripts')
        <script>
            const storiesIndexUrl = "{{ route('stories.index') }}";
            const storyShowUrl = "{{ route('stories.show', $story->slug) }}";
            const storyTitle = @json($story->title);
            const storyStats = @json($totalChapters . ' ' . Str::plural('chapter', $totalChapters) . ($story->read_time_minutes ? ' · ' . $story->read_time_minutes . ' min read' : ''));

            function markComplete(storySlug, chapterSlug) {
                const btn = document.getElementById('markCompleteBtn');
                if (!btn || btn.disabled) return;

                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

                fetch(`/stories/${storySlug}/chapters/${chapterSlug}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(r => {
                        if (!r.ok) throw new Error(r.status);
                        return r.json();
                    })
                    .then(data => {
                        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Completed';

                        const fill = document.getElementById('progressFill');
                        if (fill) fill.style.width = data.percentage + '%';

                        document.querySelector('.chapter-seg.current')?.classList.add('done');

                        const progressText = document.getElementById('progressText');
                        if (progressText) progressText.textContent = data.percentage + '% completed';

                        const navNum = document.querySelector('.chapter-nav-item.active .chapter-nav-num');
                        if (navNum) {
                            navNum.classList.add('completed');
                            navNum.innerHTML = '<i class="bi bi-check-lg" style="font-size:0.75rem"></i>';
                        }

                        if (data.isStoryCompleted) {
                            showStoryCompletedBanner();
                        } else {
                            showCompletionToast();
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        showFlash('Could not save. Please try again.', 'error');
                    });
            }

            function showStoryCompletedBanner() {
                if (document.getElementById('completionCard')) return;

                const article = document.querySelector('article');
                const banner = document.createElement('div');
                banner.className = 'completion-card';
                banner.id = 'completionCard';
                banner.innerHTML = `
                                                                                        <div class="cc-arabic">الْحَمْدُ لِلَّٰهِ</div>
                                                                                        <h5 class="heading-font mt-2 mb-1 cc-title">Story Completed</h5>
                                                                                        <p class="cc-text"></p>
                                                                                        <p class="cc-stats"></p>
                                                                                        <div class="cc-actions">
                                                                                            <a href="${storiesIndexUrl}" class="btn cc-btn-gold">Explore More Stories</a>
                                                                                            <a href="${storyShowUrl}" class="btn cc-btn-ghost">Back to Story</a>
                                                                                        </div>
                                                                                    `;
                banner.querySelector('.cc-text').textContent = `You have completed ${storyTitle}.`;
                banner.querySelector('.cc-stats').textContent = storyStats;
                article.appendChild(banner);

                banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            function showCompletionToast() {
                const existing = document.querySelector('.chapter-complete-toast');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.className = 'chapter-complete-toast';
                toast.innerHTML = `
                                                                                                                                                                                            <span class="toast-ar">الْحَمْدُ لِلَّٰهِ</span>
                                                                                                                                                                                            <span class="toast-txt">Chapter completed</span>
                                                                                                                                                                                        `;
                toast.style.cssText =
                    `
                                                                                                                                                                                                                                                                                position: fixed;
                                                                                                                                                                                                                                                                                top: 80px;
                                                                                                                                                                                                                                                                                left: 50%;
                                                                                                                                                                                                                                                                                transform: translateX(-50%) translateY(-12px);
                                                                                                                                                                                                                                                                                background: linear-gradient(135deg, var(--emerald-dark), var(--emerald));
                                                                                                                                                                                                                                                                                padding: 0.75rem 1.5rem;
                                                                                                                                                                                                                                                        border-radius: 50px;
                                                                                                                                                                                                                                                                                                                                                                                    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
                                                                                                                                                                                                                                                                                                                                                                                    z-index: 9999;
                                                                                                                                                                                                                                                                                                                                                                                    display: flex;
                                                                                                                                                                                                                                                                                                                                                                                    align-items: center;
                                                                                                                                                                                                                                                                                                                                                                                    opacity: 0;
                                                                                                                                                                                                                                                                                                                                                                                    transition: opacity 0.35s ease, transform 0.35s ease;
                                                                                                                                                                                                                                                                                                                                                                                `;
                document.body.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(-50%) translateY(0)';
                });

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 350);
                }, 2800);
            }

            const sidebarCard = document.querySelector('.chapter-sidebar-card');
            const activeNav = document.querySelector('.chapter-nav-item.active');
            if (sidebarCard && activeNav) {
                sidebarCard.scrollTop = activeNav.offsetTop - sidebarCard.clientHeight / 2;
            }

            (function () {
                const content = document.querySelector('.story-content');
                if (!content) return;

                const MIN = 0.9, MAX = 1.4, STEP = 0.1;
                let scale = 1;
                try { scale = parseFloat(localStorage.getItem('readScale')) || 1; } catch (e) { }
                scale = Math.min(MAX, Math.max(MIN, scale));
                content.style.setProperty('--read-scale', scale);

                window.setReadScale = function (dir) {
                    scale = Math.round(Math.min(MAX, Math.max(MIN, scale + dir * STEP)) * 10) / 10;
                    content.style.setProperty('--read-scale', scale);
                    try { localStorage.setItem('readScale', scale); } catch (e) { }
                };
            })();
        </script>
    @endpush

@endsection
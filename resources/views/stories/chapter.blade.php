@extends('layouts.app')
@section('title', $chapter->title . ' — ' . $story->title)

@push('styles')
<style>
    /* ════════════════════════════════════════
       UNIVERSAL RESPONSIVE LAYOUT
       Single grid system that adapts at one breakpoint
    ═══════════════════════════════════════════ */
    .story-layout {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .story-layout {
            grid-template-columns: 1fr;
        }
    }

    /* ── Sidebar (desktop) ── */
    .chapter-sidebar {
        position: sticky;
        top: 88px;
        align-self: start;
    }
    @media (max-width: 900px) {
        .chapter-sidebar {
            display: none;
        }
    }

    .chapter-sidebar-card {
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--cream);
        max-height: calc(100vh - 120px);
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
        background: rgba(27,94,59,0.05);
        color: var(--emerald);
    }
    .chapter-nav-item.active {
        background: rgba(27,94,59,0.08);
        color: var(--emerald);
        font-weight: 600;
    }
    .chapter-nav-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(27,94,59,0.08);
        color: var(--emerald);
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

    /* ── Reading content ── */
    .story-content {
        font-family: var(--font-body);
        font-size: 1.08rem;
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
        background: rgba(27,94,59,0.04);
        border-left: 3px solid var(--gold);
        border-radius: 0 var(--radius) var(--radius) 0;
        padding: 1.1rem 1.5rem;
        margin: 1.5rem 0;
        color: var(--ink-soft);
    }
    .story-content blockquote p:first-child {
        font-family: 'Amiri', 'Scheherazade New', serif;
        font-size: 1.5rem;
        direction: rtl;
        text-align: right;
        line-height: 2;
        color: var(--emerald-dark);
        margin-bottom: 0.6rem;
    }
    .story-content blockquote small {
        color: var(--gold);
        font-size: 0.78rem;
        font-style: normal;
    }

    /* ── Quran reference chips ── */
    .quran-ref-chip {
        background: rgba(27,94,59,0.08);
        border: 1px solid rgba(27,94,59,0.18);
        border-radius: 50px;
        padding: 0.22rem 0.7rem;
        font-size: 0.76rem;
        color: var(--emerald);
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

    /* ── Completion card ── */
    .completion-card {
        border-radius: var(--radius);
        background: linear-gradient(150deg, var(--emerald-dark), var(--emerald));
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .completion-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
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
    <div class="progress-bar-islamic mb-2">
        <div class="progress-bar-islamic-fill" id="progressFill"
            style="width:{{ round(($chapter->order / $allChapters->count()) * 100) }}%">
        </div>
    </div>
    <div class="d-flex justify-content-between mb-4">
        <span class="text-muted" style="font-size:0.8rem">{{ $story->title }}</span>
        <span class="text-muted" style="font-size:0.78rem">
            Chapter {{ $chapter->order }} of {{ $allChapters->count() }} ·
            {{ round(($chapter->order / $allChapters->count()) * 100) }}%
        </span>
    </div>

    {{-- Mobile chapter selector --}}
    <div class="d-md-none mb-3">
        <select class="mobile-chapter-select" onchange="window.location.href=this.value">
            @foreach ($allChapters as $ch)
                <option value="{{ route('stories.chapter', [$story->slug, $ch->slug]) }}"
                    {{ $ch->id === $chapter->id ? 'selected' : '' }}>
                    {{ $ch->order }}. {{ $ch->title }}
                </option>
            @endforeach
        </select>
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
                        <span class="chapter-nav-num">{{ $ch->order }}</span>
                        {{ $ch->title }}
                    </a>
                @endforeach

                {{-- Quran references --}}
                @if ($chapter->quran_references)
                    <hr class="divider-gold my-3">
                    <p class="sidebar-section-title">Qur'an References</p>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @foreach ($chapter->quran_references as $ref)
                            @php
                                [$surahNum, $ayahNum] = explode(':', $ref);
                                $surahRecord = \App\Models\Surah::where('number', $surahNum)->first();
                            @endphp
                            @if ($surahRecord)
                                <a href="{{ route('quran.show', $surahRecord->number) }}#ayah-{{ $ayahNum }}"
                                    class="quran-ref-chip">
                                    <i class="bi bi-bookmark-star" style="font-size:0.68rem"></i>
                                    {{ $ref }}
                                </a>
                            @endif
                        @endforeach
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
        <article>

            {{-- Chapter header --}}
            <div class="mb-4">
                <span style="color:var(--gold); font-size:0.78rem; font-family:var(--font-heading); letter-spacing:0.05em">
                    CHAPTER {{ $chapter->order }} OF {{ $allChapters->count() }}
                </span>
                <h1 class="heading-font mt-1 mb-0" style="font-size:clamp(1.5rem,4vw,2.2rem)">
                    {{ $chapter->title }}
                </h1>
                @if ($chapter->read_time_minutes)
                    <p class="text-muted mb-0" style="font-size:0.85rem">
                        <i class="bi bi-clock me-1"></i>{{ $chapter->read_time_minutes }} min read
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

            {{-- Quran references (mobile only) --}}
            @if ($chapter->quran_references)
                <div class="d-md-none mt-3 mb-4">
                    <p class="sidebar-section-title">Qur'an References</p>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($chapter->quran_references as $ref)
                            @php
                                [$surahNum, $ayahNum] = explode(':', $ref);
                                $surahRecord = \App\Models\Surah::where('number', $surahNum)->first();
                            @endphp
                            @if ($surahRecord)
                                <a href="{{ route('quran.show', $surahRecord->number) }}#ayah-{{ $ayahNum }}"
                                    class="quran-ref-chip">
                                    <i class="bi bi-bookmark-star" style="font-size:0.68rem"></i>
                                    {{ $ref }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <hr class="divider-gold">

            {{-- Navigation --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

                @if ($prevChapter)
                    <a href="{{ route('stories.chapter', [$story->slug, $prevChapter->slug]) }}" class="btn-emerald btn">
                        <i class="bi bi-arrow-left me-2"></i>{{ $prevChapter->title }}
                    </a>
                @else
                    <div></div>
                @endif

                @auth
                    @if ($isChapterCompleted)
                        <button class="btn-gold btn" disabled style="opacity:0.85">
                            <i class="bi bi-check-circle-fill me-1"></i>Completed
                        </button>
                    @else
                        <button id="markCompleteBtn" class="btn-gold btn"
                            onclick="markComplete('{{ $story->slug }}', '{{ $chapter->slug }}')">
                            <i class="bi bi-check-circle me-1"></i>Mark as Read
                        </button>
                    @endif
                @endauth

                @if ($nextChapter)
                    <a href="{{ route('stories.chapter', [$story->slug, $nextChapter->slug]) }}" class="btn-emerald btn">
                        {{ $nextChapter->title }}<i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @else
                    <div></div>
                @endif
            </div>

            {{-- Story completed --}}
            @unless ($nextChapter)
                <div class="completion-card">
                    <div style="font-family:'Amiri',serif; font-size:1.8rem; color:rgba(255,255,255,0.8); position:relative; z-index:1">
                        الْحَمْدُ لِلَّٰهِ
                    </div>
                    <h5 class="heading-font mt-2 mb-1" style="color:white; position:relative; z-index:1">
                        Story Completed
                    </h5>
                    <p style="color:rgba(255,255,255,0.7); font-size:0.88rem; position:relative; z-index:1">
                        You have completed {{ $story->title }}.
                    </p>
                    <a href="{{ route('stories.index') }}" class="btn"
                        style="background:var(--gold); color:#1A1A2E; position:relative; z-index:1; font-weight:600">
                        Explore More Stories
                    </a>
                </div>
            @endunless

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
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Completed';
                    btn.disabled = true;
                }
                const fill = document.getElementById('progressFill');
                if (fill) fill.style.width = data.percentage + '%';

                showCompletionToast();
            });
    }

    function showCompletionToast() {
        const existing = document.querySelector('.chapter-complete-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'chapter-complete-toast';
        toast.innerHTML = `
            <span style="font-family:'Amiri',serif; font-size:1.3rem; color:var(--gold-light)">الْحَمْدُ لِلَّٰهِ</span>
            <span style="font-size:0.82rem; color:rgba(255,255,255,0.85); margin-left:0.6rem">Chapter completed</span>
        `;
        toast.style.cssText = `
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
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
            toast.style.transform = 'translateX(-50%) translateY(-8px)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 350);
        }, 2800);
    }

    document.querySelector('.chapter-nav-item.active')
        ?.scrollIntoView({ block: 'center' });
</script>
@endpush

@endsection
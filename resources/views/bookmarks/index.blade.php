@extends('layouts.app')
@section('title', 'My Bookmarks')

@section('content')
    <div class="container py-4">

        <h2 class="heading-font mb-4">
            <i class="bi bi-bookmark-fill me-2" style="color:var(--gold)"></i>
            My Bookmarks
        </h2>

        @php
            $ayahBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable instanceof \App\Models\Ayah);
            $storyBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable instanceof \App\Models\StoryChapter);
            $hadithBookmarks = $bookmarks->filter(fn($b) => $b->bookmarkable instanceof \App\Models\Hadith);
        @endphp

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ayahs">
                    <i class="bi bi-book me-1"></i>
                    Quran Ayahs
                    <span class="badge bg-secondary ms-1">{{ $ayahBookmarks->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#stories">
                    <i class="bi bi-journal-text me-1"></i>
                    Story Chapters
                    <span class="badge bg-secondary ms-1">{{ $storyBookmarks->count() }}</span>
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hadiths">
                    <i class="bi bi-collection me-1"></i>
                    Hadiths
                    <span class="badge bg-secondary ms-1">{{ $hadithBookmarks->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ── AYAH BOOKMARKS ────────────────────── --}}
            <div class="tab-pane fade show active" id="ayahs">
                @forelse($ayahBookmarks as $bookmark)
                    <div class="card-islamic p-4 mb-3">
                        <div class="d-flex justify-content-between align-items-start">

                            <div class="flex-grow-1">
                                {{-- Surah reference --}}
                                <div class="mb-2">
                                    <span style="color:var(--gold); font-family:var(--font-heading); font-size:0.8rem">
                                        {{ $bookmark->bookmarkable?->surah?->name_transliteration }}
                                        {{ $bookmark->bookmarkable?->surah?->number }}:{{ $bookmark->bookmarkable?->number }}
                                    </span>
                                </div>

                                {{-- Arabic text --}}
                                <p class="arabic mb-2" style="font-size:1.4rem">
                                    {{ $bookmark->bookmarkable?->text_arabic }}
                                </p>

                                {{-- Translation --}}

                                @if ($bookmark->display_preview)
                                    <p class="text-muted mb-0" style="font-size:0.9rem; font-style:italic">
                                        {{ $bookmark->display_preview }}
                                    </p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex flex-column gap-2 ms-3">
                                <a href="{{ route('quran.show', $bookmark->bookmarkable?->surah?->number) }}#ayah-{{ $bookmark->bookmarkable?->number }}"
                                    class="btn btn-sm btn-emerald">
                                    <i class="bi bi-book"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="removeBookmark({{ $bookmark->id }}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bookmark" style="font-size:3rem; opacity:0.3"></i>
                        <p class="mt-3">No ayah bookmarks yet.</p>
                        <a href="{{ route('quran.index') }}" class="btn-emerald btn btn-sm">
                            Start Reading Quran
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- ── STORY BOOKMARKS ───────────────────── --}}
            <div class="tab-pane fade" id="stories">
                @forelse($storyBookmarks as $bookmark)
                    <div class="card-islamic p-4 mb-3">
                        <div class="d-flex justify-content-between align-items-start">

                            <div class="flex-grow-1">
                                {{-- Story title --}}
                                <p class="heading-font mb-1" style="font-size:0.8rem; color:var(--muted)">
                                    {{ $bookmark->bookmarkable?->story?->title }}
                                </p>

                                {{-- Chapter title --}}
                                <h6 class="mb-2">
                                    Chapter {{ $bookmark->bookmarkable?->order }}:
                                    {{ $bookmark->bookmarkable?->title }}
                                </h6>

                                {{-- Read time --}}
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $bookmark->bookmarkable?->read_time_minutes }} min
                                </small>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex flex-column gap-2 ms-3">
                                @if ($bookmark->bookmarkable?->story)
                                    <a href="{{ route('stories.chapter', [$bookmark->bookmarkable->story->slug, $bookmark->bookmarkable->id]) }}"
                                        class="btn btn-sm btn-emerald">
                                        <i class="bi bi-book"></i>
                                    </a>
                                @endif
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="removeBookmark({{ $bookmark->id }}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bookmark" style="font-size:3rem; opacity:0.3"></i>
                        <p class="mt-3">No story bookmarks yet.</p>
                        <a href="{{ route('stories.index') }}" class="btn-emerald btn btn-sm">
                            Browse Stories
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- ── HADITH BOOKMARKS ───────────────────── --}}

            <div class="tab-pane fade" id="hadiths">
                @forelse($hadithBookmarks as $bookmark)
                    <div class="card-islamic p-4 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="heading-font mb-1" style="font-size:0.8rem; color:var(--muted)">
                                    {{ $bookmark->bookmarkable?->collection?->name }} —
                                    {{ $bookmark->bookmarkable?->chapter?->title }}
                                </p>
                                <p class="arabic mb-2" style="font-size:1.3rem">
                                    {{ $bookmark->bookmarkable?->arabic }}
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.9rem">
                                    {{ \Str::limit($bookmark->bookmarkable?->english, 150) }}
                                </p>
                                @if ($bookmark->bookmarkable?->grade)
                                    <span class="badge-grade mt-2">{{ $bookmark->bookmarkable->grade }}</span>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-2 ms-3">
                                <a href="{{ route('hadith.show', [$bookmark->bookmarkable?->collection?->slug, $bookmark->bookmarkable?->chapter?->number]) }}?highlight={{ $bookmark->bookmarkable?->number }}"
                                    class="btn btn-sm btn-emerald">
                                    <i class="bi bi-book"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="removeBookmark({{ $bookmark->id }}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bookmark" style="font-size:3rem; opacity:0.3"></i>
                        <p class="mt-3">No hadith bookmarks yet.</p>
                        <a href="{{ route('hadith.index') }}" class="btn-emerald btn btn-sm">Browse Hadiths</a>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $bookmarks->links() }}
        </div>

    </div>

    @push('scripts')
        <script>
            function removeBookmark(id, btn) {
                if (!confirm('Remove this bookmark?')) return;

                fetch(`/bookmarks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'deleted') {
                            const card = btn.closest('.card-islamic');
                            const tabPane = card.closest('.tab-pane');

                            card.remove();

                            // Us tab ka badge dhoondo aur count -1 karo
                            const tabId = tabPane.id; // e.g. "ayahs"
                            const badge = document.querySelector(`[data-bs-target="#${tabId}"] .badge`);
                            if (badge) {
                                const currentCount = parseInt(badge.textContent) || 0;
                                badge.textContent = Math.max(0, currentCount - 1);
                            }
                        }
                    });
            }
        </script>
    @endpush

@endsection

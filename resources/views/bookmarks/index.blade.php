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
                                @if ($bookmark->bookmarkable?->translations->isNotEmpty())
                                    <p class="text-muted mb-0" style="font-size:0.9rem; font-style:italic">
                                        {{ $bookmark->bookmarkable->translations->first()->text }}
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
                                    {{ $bookmark->bookmarkable?->read_time }}
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
                            // Remove the card from DOM
                            btn.closest('.card-islamic').remove();
                        }
                    });
            }
        </script>
    @endpush

@endsection

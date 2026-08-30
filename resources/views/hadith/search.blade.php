@extends('layouts.app')
@section('title', $query ? 'Search: ' . $query . ' — Hadith' : 'Search Hadiths — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
    <style>
        mark.search-highlight {
            background: var(--emerald-light, #d4edda);
            color: var(--emerald, #1b4332);
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="heading-font mb-0" style="color:var(--emerald-light)">Search Hadiths</h2>
        </div>

        <form action="{{ route('hadith.search') }}" method="GET" class="mb-4">
            <div class="input-group input-group-lg">
                <input type="text" name="q" value="{{ $query }}" class="form-control"
                    placeholder="Search in Arabic or English..." autofocus style="border-color: var(--emerald);">
                <button class="btn" type="submit" style="background: var(--emerald); color: #fff;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>

        {{-- ── State 1: No query yet ── --}}
        @if (!$query)
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size:2.5rem; opacity:0.3;"></i>
                <p class="mt-3 mb-1" style="font-family:var(--font-heading);">Search the Hadith collections</p>
                <p class="text-muted small">Enter a word or topic to find relevant hadiths.</p>
                <div class="mt-3" style="font-size:0.85rem;">
                    Try:
                    @foreach (['patience', 'charity', 'prayer', 'kindness', 'fasting'] as $suggestion)
                        <a href="{{ route('hadith.search', ['q' => $suggestion]) }}"
                            style="color:var(--emerald); text-decoration:none; margin:0 0.3rem;">{{ $suggestion }}</a>
                    @endforeach
                </div>
            </div>

            {{-- ── State 2: Query too short ── --}}
        @elseif (mb_strlen($query) < 3)
            <div class="text-center py-5">
                <p class="text-muted">Please enter at least 3 characters to search.</p>
            </div>

            {{-- ── State 3: No results ── --}}
        @elseif ($results->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown" style="font-size:2.5rem; opacity:0.3;"></i>
                <p class="mt-3">No results found for "{{ $query }}"</p>
                <a href="{{ route('hadith.search') }}" class="btn-emerald btn btn-sm mt-2">Clear Search</a>
            </div>

            {{-- ── State 4: Results ── --}}
        @else
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0" style="font-size:0.9rem">
                    {{ $results->total() }} {{ Str::plural('result', $results->total()) }} for "{{ $query }}"
                </p>
                <a href="{{ route('hadith.search') }}" style="font-size:0.82rem; color:var(--muted); text-decoration:none">
                    <i class="bi bi-x me-1"></i>Clear
                </a>
            </div>

            @foreach ($results as $h)
                @php
                    $isBookmarked = in_array($h->id, $bookmarkedIds);
                    $hasNote = $userNotes->has($h->id);
                    $isRead = in_array($h->id, $readIds);
                @endphp

                @include('hadith._hadith-card', [
                    'h' => (object) [
                        'id' => $h->id,
                        'arabic' => $h->arabic_highlighted,
                        'english' => $h->english_highlighted,
                        'grade' => $h->grade,
                        'needs_review' => $h->needs_review,
                        'translation_incomplete' => $h->translation_incomplete,
                        'number' => $h->number,
                    ],
                    'collectionSlug' => $h->collection_slug,
                    'collectionName' => $h->collection_name,
                    'chapterTitle' => $h->chapter_title,
                    'isBookmarked' => $isBookmarked,
                    'hasNote' => $hasNote,
                    'isRead' => $isRead,
                ])
            @endforeach

            {{-- Pagination --}}
            @if ($results->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $results->links() }}
                </div>
            @endif
        @endif

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/hadithActions.js') }}"></script>
    <script>
        // Live search — small improvement
        // Press Enter to submit
        document.getElementById('searchInput')
            ?.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    this.focus();
                }
            });
    </script>
@endpush

@extends('layouts.app')
@section('title', $chapter->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
@endpush

@php
    $userNotesForJs = $userNotes->map(fn($n) => ['id' => $n->id, 'title' => $n->title, 'content' => $n->content]);
@endphp

@section('content')
    <div class="container py-4">
        {{-- Breadcrumb --}}
        <div class="mb-3" style="font-size:0.85rem">
            <a href="{{ route('hadith.index') }}">Hadith</a> /
            <a href="{{ route('hadith.chapters', $collection->slug) }}">{{ $collection->name }}</a> /
            <span class="text-muted">{{ $chapter->title }}</span>
        </div>

        <h3 class="heading-font mb-4" style="color:var(--emerald)">{{ $chapter->title }}</h3>

        <div id="hadithList">
            @foreach ($hadiths as $h)
                @php
                    $isBookmarked = in_array($h->id, $bookmarkedIds);
                    $hasNote = $userNotes->has($h->id);
                @endphp
                <div class="hadith-card" id="hadith-{{ $h->number }}" data-hadith-id="{{ $h->id }}">
                    <div class="hadith-number-badge">{{ $h->number }}</div>
                    <p class="hadith-arabic" dir="rtl">{{ $h->arabic }}</p>
                    <p class="hadith-english">{{ $h->english }}</p>
                    @if ($h->grade)
                        <span class="grade-badge grade-{{ strtolower($h->grade) }}">{{ $h->grade }}</span>
                    @endif

                    <div class="hadith-actions mt-2">
                        @auth
                            <button class="ayah-btn {{ $isBookmarked ? 'bookmarked' : '' }}"
                                id="hadith-bookmark-{{ $h->id }}"
                                onclick="toggleHadithBookmark(this, {{ $h->id }})">
                                <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        @endauth

                        <button class="ayah-btn" onclick="copyHadithText(this, {{ $h->number }})">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>

                        <button class="ayah-btn"
                            onclick="shareHadith('{{ $collection->slug }}', {{ $h->number }}, this)">
                            <i class="bi bi-share"></i> Share
                        </button>

                        @auth
                            <button class="ayah-btn {{ $hasNote ? 'has-note' : '' }}" id="hadith-note-btn-{{ $h->id }}"
                                onclick="toggleHadithNoteEditor(this, {{ $h->id }})">
                                <i class="bi bi-pencil-square"></i>
                                {{ $hasNote ? 'Note' : 'Add Note' }}
                            </button>
                        @endauth
                    </div>

                    {{-- Note Banner --}}
                    @auth
                        <div class="note-banner" id="hadith-note-{{ $h->id }}">
                            <div class="note-inner">
                                <div class="note-head">
                                    <strong>Your Note</strong>
                                    <button class="note-close"
                                        onclick="closeHadithNoteEditor({{ $h->id }})">&times;</button>
                                </div>
                                <input type="text" class="note-title-input" id="hadith-note-title-{{ $h->id }}"
                                    placeholder="Optional title...">
                                <textarea class="note-content-input" id="hadith-note-content-{{ $h->id }}" rows="3"
                                    placeholder="Write your reflection..."></textarea>
                                <div class="note-actions">
                                    <button class="note-delete-btn" id="hadith-note-delete-{{ $h->id }}"
                                        onclick="deleteHadithNote({{ $h->id }})" style="display:none">Delete</button>
                                    <button class="note-save-btn" onclick="saveHadithNote({{ $h->id }})">Save
                                        Note</button>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            @endforeach
        </div>
        <div id="hadith-sentinel"></div>
    </div>

    <script>
        window.HADITH_CONFIG = {
            collectionSlug: '{{ $collection->slug }}',
            chapterNumber: {{ $chapter->number }}
        };
        window.HADITH_USER_NOTES = @json($userNotesForJs);
    </script>
    <script src="{{ asset('js/hadith-show.js') }}"></script>
@endsection

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
        <div class="hadith-breadcrumb">
            <a href="{{ route('hadith.index') }}">Hadith</a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('hadith.chapters', $collection->slug) }}">{{ $collection->name }}</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">{{ $chapter->title }}</span>
        </div>

        <h3 class="heading-font mb-4" style="color:var(--emerald)">{{ $chapter->title }}</h3>

        <div id="hadithList">
            @forelse ($hadiths as $h)
                @php
                    $isBookmarked = in_array($h->id, $bookmarkedIds);
                    $hasNote = $userNotes->has($h->id);
                @endphp

                @include('hadith._hadith-card', [
                    'h' => $h,
                    'collectionSlug' => $collection->slug,
                    'isBookmarked' => $isBookmarked,
                    'hasNote' => $hasNote,
                ])
            @empty
                <div class="hadith-empty-state">
                    <i class="bi bi-journal-text hadith-empty-icon"></i>
                    <div class="hadith-empty-title">No Hadiths Found</div>
                    <p class="hadith-empty-text">
                        This chapter doesn't have any hadiths available right now.
                    </p>
                </div>
            @endforelse
        </div>
        <div id="hadith-sentinel"></div>
    </div>

    <script>
        window.HADITH_CONFIG = {
            collectionSlug: "{{ $collection->slug }}",
            chapterNumber: {{ $chapter->number }},
            targetPage: {{ $targetPage ? (int) $targetPage : 'null' }},
            targetHadithId: {{ $targetHadithId ? (int) $targetHadithId : 'null' }},
        };
        window.HADITH_USER_NOTES = @json($userNotesForJs);
    </script>
    <script src="{{ asset('js/hadithActions.js') }}"></script>
    <script src="{{ asset('js/hadith-show.js') }}"></script>
@endsection

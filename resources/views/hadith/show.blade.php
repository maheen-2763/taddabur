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

        <div class="hadith-glossary-toggle mb-3">
            <button class="hadith-info-btn" type="button" data-bs-toggle="collapse" data-bs-target="#hadithGlossary">
                <i class="bi bi-info-circle"></i>
                @if (in_array($collection->slug, ['bukhari', 'muslim']))
                    What do Isnad, Marfu, Mawquf, Maqtu mean?
                @else
                    What do these terms and grades mean?
                @endif
            </button>

            <div class="collapse" id="hadithGlossary">
                <div class="hadith-glossary-box mt-2">
                    <p><strong>Isnad</strong> — the chain of narrators connecting a hadith back to its source. This chain is
                        what scholars examine to judge a hadith's reliability.</p>
                    <p><strong>Marfu' ("elevated")</strong> — a narration whose speech, action, or approval is directly
                        attributed to Prophet Muhammad ﷺ, regardless of who reported it.</p>
                    <p><strong>Mawquf ("stopped")</strong> — a narration whose attribution stops at a Companion (Sahabi) —
                        it records what a Companion said or did, not something attributed to the Prophet ﷺ himself.</p>
                    <p><strong>Maqtu' ("severed")</strong> — a narration attributed to a Tabi'i (a Successor who met the
                        Companions but not the Prophet ﷺ).</p>

                    @unless (in_array($collection->slug, ['bukhari', 'muslim']))
                        <hr class="my-2" style="border-color: rgba(0,0,0,0.08);">

                        <div class="hadith-grade-legend">
                            <p class="text-muted mb-2" style="font-size:0.85rem">What these grades mean</p>
                            <div class="grade-legend-grid">
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-sahih">Sahih</span>
                                    <p>Authentic — a strong, unbroken chain of trustworthy narrators.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-hasan">Hasan</span>
                                    <p>Good — reliable, though slightly weaker than Sahih in chain or memory.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-daif">Da'if</span>
                                    <p>Weak — a gap or unreliable narrator in the chain. Not a basis for rulings.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-very-daif">Very Da'if</span>
                                    <p>Very weak — a more serious chain defect than Da'if.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-munkar">Munkar</span>
                                    <p>Rejected — narrated by an unreliable source, contradicting stronger reports.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-shadh">Shadh</span>
                                    <p>Irregular — contradicts a more reliable, widely-accepted narration.</p>
                                </div>
                                <div class="grade-legend-item">
                                    <span class="grade-badge grade-mawdu">Mawdu</span>
                                    <p>Fabricated — not authentic; falsely attributed. Shown for awareness only.</p>
                                </div>
                            </div>
                        </div>
                    @endunless
                </div>
            </div>
        </div>

        <h3 class="heading-font mb-4" style="color:var(--emerald)">{{ $chapter->title }}</h3>

        <div id="hadithList">
            @forelse ($hadiths as $h)
                @php
                    $isBookmarked = in_array($h->id, $bookmarkedIds);
                    $hasNote = $userNotes->has($h->id);
                    $isRead = in_array($h->id, $readIds);
                @endphp

                @include('hadith._hadith-card', [
                    'h' => $h,
                    'collectionSlug' => $collection->slug,
                    'collectionName' => $collection->name,
                    'chapterTitle' => $chapter->title,
                    'isBookmarked' => $isBookmarked,
                    'hasNote' => $hasNote,
                    'isRead' => $isRead,
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
            collectionName: "{{ $collection->name }}",
            chapterTitle: "{{ $chapter->title }}",
            chapterNumber: {{ $chapter->number }},
            targetPage: {{ $targetPage ? (int) $targetPage : 'null' }},
            targetHadithId: {{ $targetHadithId ? (int) $targetHadithId : 'null' }},
        };
        window.HADITH_USER_NOTES = @json($userNotesForJs);
    </script>
    <script src="{{ asset('js/hadithActions.js') }}"></script>
    <script src="{{ asset('js/hadith-show.js') }}"></script>
    <script src="{{ asset('js/shareCard.js') }}"></script>
@endsection

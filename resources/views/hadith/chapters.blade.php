@extends('layouts.app')
@section('title', $collection->name . ' — Chapters')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
@endpush

@section('content')
    <div class="container py-4">
        {{-- Breadcrumb --}}
        <div class="hadith-breadcrumb mb-3">
            <a href="{{ route('hadith.index') }}">Hadith</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">{{ $collection->name }}</span>
        </div>

        <a href="{{ route('hadith.index') }}" class="hadith-back-btn mb-3">
            <i class="bi bi-arrow-left"></i> Collections
        </a>
        <h2 class="heading-font mb-1" style="color:var(--emerald)">{{ $collection->name }}</h2>
        <p class="text-muted mb-4">{{ $collection->scholar }}</p>

        @if ($resumeHadith)
            <a href="{{ route('hadith.show', [$collection->slug, $resumeHadith->chapter->number]) }}?highlight={{ $resumeHadith->id }}"
                class="hadith-resume-btn mb-4">
                <i class="bi bi-play-circle-fill"></i>
                Resume Reading — Hadith {{ $resumeHadith->number }}
            </a>
        @elseif(Auth::check() && $hasReadAnything ?? false)
            <div class="hadith-complete-banner mb-4">
                <i class="bi bi-check-circle-fill"></i>
                MashaAllah — you've completed this collection!
            </div>
        @endif
        @unless (in_array($collection->slug, ['bukhari', 'muslim']))
            <div class="mb-4">
                <p class="text-muted mb-2" style="font-size:0.85rem">Browse by grade</p>
                <div class="grade-buttons d-flex flex-wrap gap-2">
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Sahih']) }}"
                        class="hadith-grade-btn">Sahih</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Hasan']) }}"
                        class="hadith-grade-btn">Hasan</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Daif']) }}"
                        class="hadith-grade-btn">Da'if</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Very Daif']) }}"
                        class="hadith-grade-btn">Very Da'if</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Munkar']) }}"
                        class="hadith-grade-btn">Munkar</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Shadh']) }}"
                        class="hadith-grade-btn">Shadh</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Mawdu']) }}"
                        class="hadith-grade-btn">Mawdu</a>
                </div>
            </div>
        @endunless

        @if ($chapters->isEmpty())
            <div class="hadith-empty-state">
                <i class="bi bi-hourglass-split hadith-empty-icon"></i>
                <div class="hadith-empty-title">Coming Soon</div>
                <p class="hadith-empty-text">
                    This collection is being prepared and will be available soon, In sha Allah.
                </p>
            </div>
        @else
            <div>
                @foreach ($chapters as $ch)
                    <a href="{{ route('hadith.show', [$collection->slug, $ch->number]) }}"
                        class="hadith-chapter-row hadith-nav-link">

                        <span class="hadith-chapter-title">
                            <span class="chapter-number-badge">{{ $ch->number }}</span>
                            <span class="chapter-title-text">{{ $ch->title }}</span>
                        </span>

                        <span class="chapter-meta">
                            @if ($ch->start_number && $ch->end_number)
                                <span class="chapter-range-badge">Hadith
                                    {{ $ch->start_number }}–{{ $ch->end_number }}</span>
                            @endif
                            <span class="hadith-count-badge">{{ $ch->hadiths_count }}</span>
                        </span>

                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection

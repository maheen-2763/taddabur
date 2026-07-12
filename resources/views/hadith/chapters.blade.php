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
        @unless (in_array($collection->slug, ['bukhari', 'muslim']))
            <div class="mb-4">
                <p class="text-muted mb-2" style="font-size:0.85rem">Browse by grade</p>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Sahih']) }}"
                        class="hadith-back-btn">Sahih</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Hasan']) }}"
                        class="hadith-back-btn">Hasan</a>
                    <a href="{{ route('hadith.grade.collection', [$collection->slug, 'Daif']) }}"
                        class="hadith-back-btn">Da'if</a>

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
                            {{ $ch->title }}
                        </span>
                        <span class="hadith-count-badge">{{ $ch->hadiths_count }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection

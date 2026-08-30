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

        <div class="hadith-page-header-actions">
            <a href="{{ route('hadith.index') }}" class="hadith-back-btn">
                <i class="bi bi-arrow-left"></i> Collections
            </a>

            @if ($resumeHadith)
                <a href="{{ route('hadith.show', [$collection->slug, $resumeHadith->chapter->number]) }}?highlight={{ $resumeHadith->id }}"
                    class="hadith-resume-btn">
                    <i class="bi bi-play-circle-fill"></i>
                    Resume Reading — Hadith {{ $resumeHadith->number }}
                </a>
            @elseif(Auth::check() && $hasReadAnything ?? false)
                <div class="hadith-complete-banner">
                    <i class="bi bi-check-circle-fill"></i>
                    MashaAllah — you've completed this collection!
                </div>
            @endif
        </div>

        <h2 class="heading-font mb-1" style="color:var(--emerald-light)">{{ $collection->name }}</h2>

        <div class="d-flex align-items-center gap-2 mb-4">
            <p class="text-muted mb-0" style="font-size:0.92rem;">
                <i class="bi bi-person-fill" style="font-size:0.8rem; opacity:0.6; margin-right:4px;"></i>
                {{ $collection->scholar }}
            </p>

            @if ($collection->scholar_bio)
                <button type="button" class="scholar-info-icon-btn" data-bs-toggle="collapse" data-bs-target="#scholarBio"
                    aria-expanded="false" title="About {{ $collection->scholar }}">
                    <i class="bi bi-info-lg"></i>
                </button>
            @endif
        </div>

        @if ($collection->scholar_bio)
            <div class="collapse mb-4" id="scholarBio">
                <div class="hadith-glossary-box">
                    @if ($collection->scholar_arabic_name)
                        <p class="mb-1" dir="rtl"
                            style="font-family:'Amiri',serif; font-size:1.1rem; color:var(--emerald-light);">
                            {{ $collection->scholar_arabic_name }}
                        </p>
                    @endif
                    @if ($collection->scholar_years)
                        <p class="mb-2" style="font-size:0.78rem; color:var(--muted);">
                            {{ $collection->scholar_years }}
                        </p>
                    @endif
                    <p class="mb-0">{{ $collection->scholar_bio }}</p>
                </div>
            </div>
        @endif

        @guest
            <p class="text-muted mb-3" style="font-size:0.82rem; font-style:italic;">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Sign in to track your progress through this collection.
            </p>
        @endguest


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
                        class="hadith-chapter-row hadith-nav-link {{ $ch->is_complete ?? false ? 'chapter-complete' : '' }}">

                        <span class="hadith-chapter-title">
                            <span class="chapter-number-badge">{{ $ch->number }}</span>
                            <span class="chapter-title-block">
                                <span class="chapter-title-text">{{ $ch->title }}</span>
                                @if ($ch->hadiths_count)
                                    <span class="chapter-range-subtitle">
                                        <span class="range-symbol">❖</span>
                                        {{ $ch->hadiths_count }} {{ Str::plural('Hadith', $ch->hadiths_count) }}
                                    </span>
                                @endif
                            </span>
                        </span>

                        <span class="chapter-meta">
                            @if ($ch->is_complete ?? false)
                                <span class="chapter-complete-badge">
                                    <i class="bi bi-check-circle-fill"></i> Completed
                                </span>
                            @elseif(($ch->progress_percent ?? 0) > 0)
                                <span class="hadith-progress-ring" style="--pct: {{ $ch->progress_percent }}">
                                    <span class="ring-label">{{ $ch->progress_percent }}%</span>
                                </span>
                                <span class="chapter-progress-text">{{ $ch->read_count }}/{{ $ch->hadiths_count }}</span>
                            @endif


                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@extends('layouts.app')
@section('title', $reliability . ' Hadiths')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
@endpush

@section('content')
    <div class="container py-4">
        <div class="hadith-breadcrumb mb-3">
            <a href="{{ route('hadith.index') }}">Hadith</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">{{ $reliability }}</span>
        </div>

        <h3 class="heading-font mb-2" style="color:var(--emerald)">{{ $reliability }} Hadiths</h3>

        @if ($reliability === 'Sahih')
            <p class="text-muted mb-3" style="font-size:0.82rem">
                This list draws from collections with individual hadith grading.
                Sahih Bukhari and Sahih Muslim are considered authentic by scholarly consensus as a whole and appear
                separately in their own collection pages.
            </p>
        @endif

        @if (in_array($reliability, ['Daif', 'Very Daif', 'Mawdu', 'Munkar', 'Shadh']))
            <div class="hadith-empty-state mb-4" style="text-align:left; padding:1rem 1.25rem;">
                <p class="hadith-empty-text" style="margin:0; max-width:none;">
                    <i class="bi bi-info-circle"></i>
                    These hadiths carry a weaker chain of narration according to the scholar noted on each card.
                    They are shown for reference and study, not as a basis for religious rulings.
                </p>
            </div>
        @endif

        <div>
            @forelse ($hadiths as $h)
                <div class="hadith-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <span class="hadith-count-badge">{{ $h->collection->name }}</span>
                        <span class="text-muted" style="font-size:0.78rem;">Hadith {{ $h->id }}</span>
                    </div>

                    <p class="hadith-arabic" dir="rtl">{{ $h->arabic }}</p>
                    <p class="hadith-english">{{ $h->english }}</p>

                    @if ($h->translation_incomplete)
                        <div class="hadith-incomplete-notice">
                            <i class="bi bi-exclamation-triangle"></i>
                            Translation not available for this hadith yet.
                        </div>
                    @endif

                    <div style="margin-top:0.5rem; display:flex; gap:0.4rem; flex-wrap:wrap;">
                        <span class="grade-badge grade-{{ Str::slug($reliability) }}">{{ $h->grade }}</span>
                        @if ($h->attribution_type && $h->attribution_type !== 'Marfu')
                            <span class="grade-badge" style="background:rgba(0,0,0,0.05); color:var(--emerald-light)">
                                {{ $h->attribution_type }}
                            </span>
                        @endif
                    </div>

                    <div class="hadith-actions mt-2">
                        <a href="{{ route('hadith.show', [$h->collection->slug, $h->chapter->number ?? 1]) }}?highlight={{ $h->id }}"
                            class="ayah-btn hadith-nav-link hadith-view-in-chapter-btn">
                            <i class="bi bi-box-arrow-up-right"></i> View in chapter
                        </a>
                    </div>
                </div>
            @empty
                <div class="hadith-empty-state">
                    <i class="bi bi-journal-text hadith-empty-icon"></i>
                    <div class="hadith-empty-title">No hadiths found</div>
                    <p class="hadith-empty-text">No hadiths are classified under this grade yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $hadiths->links() }}
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Hadith Collections')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="heading-font mb-0" style="color:var(--emerald-light)">Hadith Collections</h2>

            <a href="{{ route('hadith.search') }}" class="btn btn-sm"
                style="color:var(--emerald-light); border: 1px solid var(--emerald-light);" title="Search Hadiths">
                <i class="bi bi-search"></i>
            </a>
        </div>

        {{-- ✅ Guest nudge --}}
        @guest
            <p class="text-muted mb-3" style="font-size:0.82rem; font-style:italic;">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Sign in to track your reading progress across collections.
            </p>
        @endguest

        {{-- Browse by grade --}}
        <div class="mb-4">
            <p class="text-muted mb-2" style="font-size:0.85rem">Browse by grade</p>

            @php
                $gradeDefinitions = [
                    'Sahih' => 'Authentic — a strong, unbroken chain of trustworthy narrators.',
                    'Hasan' => 'Good — reliable, though slightly weaker than Sahih in chain or memory.',
                    'Daif' => 'Weak — a gap or unreliable narrator in the chain. Not a basis for rulings.',
                    'Very Daif' => "Very weak — a more serious chain defect than Da'if.",
                    'Munkar' => 'Rejected — narrated by an unreliable source, contradicting stronger reports.',
                    'Shadh' => 'Irregular — contradicts a more reliable, widely-accepted narration.',
                    'Mawdu' => 'Fabricated — not authentic; falsely attributed. Shown for awareness only.',
                ];
                $gradeLabels = [
                    'Sahih' => 'Sahih',
                    'Hasan' => 'Hasan',
                    'Daif' => "Da'if",
                    'Very Daif' => "Very Da'if",
                    'Munkar' => 'Munkar',
                    'Shadh' => 'Shadh',
                    'Mawdu' => 'Mawdu',
                ];
            @endphp

            <div class="d-flex flex-wrap gap-2" style="color:var(--emerald)">
                @foreach ($gradeDefinitions as $key => $definition)
                    <div class="grade-pill-group">
                        <a href="{{ route('hadith.grade', $key) }}" class="hadith-grade-btn">
                            {{ $gradeLabels[$key] }}
                        </a>
                        <button type="button" class="grade-info-btn" data-bs-toggle="popover" data-bs-trigger="focus"
                            data-bs-placement="top" data-bs-content="{{ $definition }}"
                            aria-label="What does {{ $gradeLabels[$key] }} mean?">
                            <i class="bi bi-info-lg"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            <p class="hadith-disclaimer">
                Grades shown are based on established scholarly sources. Learn more about our
                sources and accuracy standards on the <a href="{{ route('about') }}">About page</a>.
            </p>
            <p class="text-muted mt-2" style="font-size:0.80rem">
                Sahih Bukhari and Sahih Muslim are considered authentic by scholarly consensus
                and are not individually graded — so they won't appear in these grade pages.
            </p>
        </div>

        {{-- ✅ Collapsible full grade legend — collapsed by default now that
             individual info-icons above give quick per-grade lookup --}}
        <div class="mb-4">
            <button class="hadith-info-btn" type="button" data-bs-toggle="collapse" data-bs-target="#gradeLegendFull"
                aria-expanded="false">
                <i class="bi bi-info-circle"></i>
                What do all these grades mean?
            </button>

            <div class="collapse mt-2" id="gradeLegendFull">
                @include('hadith._grade-legend')
            </div>
        </div>

        {{-- Collections grid --}}
        <div class="d-flex gap-4 align-items-start hadith-layout">

            <div class="flex-grow-1" style="min-width: 0;">
                <div class="row g-3">
                    @foreach ($collections as $c)
                        <div class="col-md-4">

                            <a href="{{ route('hadith.chapters', $c->slug) }}" class="text-decoration-none hadith-nav-link">
                                <div class="hadith-collection-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 style="color:var(--emerald-light)">{{ $c->name }}</h5>
                                            <p class="text-muted mb-1" style="font-size:0.85rem">{{ $c->scholar }}</p>
                                            <span class="hadith-count-badge">{{ $c->hadiths_count }} Hadiths</span>

                                            @if ($c->progress_percent > 0)
                                                <p class="hadith-progress-label mb-0">
                                                    {{ $c->read_count }} / {{ $c->hadiths_count }} read
                                                </p>
                                            @endif
                                        </div>

                                        @if ($c->progress_percent > 0)
                                            <div class="hadith-progress-ring" style="--pct: {{ $c->progress_percent }}">
                                                <span class="ring-label">{{ $c->progress_percent }}%</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap === 'undefined') {
                console.warn('Bootstrap JS not loaded — grade info popovers will not work.');
                return;
            }
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function(el) {
                new bootstrap.Popover(el);
            });
        });
    </script>
@endpush

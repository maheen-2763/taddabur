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
                style="color:var(--emerald); border: 1px solid var(--emerald);" title="Search Hadiths">
                <i class="bi bi-search"></i>
            </a>
        </div>

        {{-- Browse by grade --}}
        <div class="mb-4">
            <p class="text-muted mb-2" style="font-size:0.85rem">Browse by grade</p>
            <div class="d-flex flex-wrap gap-2" style="color:var(--emerald)">
                <a href="{{ route('hadith.grade', 'Sahih') }}" class="hadith-grade-btn">Sahih</a>
                <a href="{{ route('hadith.grade', 'Hasan') }}" class="hadith-grade-btn">Hasan</a>
                <a href="{{ route('hadith.grade', 'Daif') }}" class="hadith-grade-btn">Da'if</a>
                <a href="{{ route('hadith.grade', 'Very Daif') }}" class="hadith-grade-btn">Very Da'if</a>
                <a href="{{ route('hadith.grade', 'Munkar') }}" class="hadith-grade-btn">Munkar</a>
                <a href="{{ route('hadith.grade', 'Shadh') }}" class="hadith-grade-btn">Shadh</a>
                <a href="{{ route('hadith.grade', 'Mawdu') }}" class="hadith-grade-btn">Mawdu</a>
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

        <div class="hadith-grade-legend-top mb-4">
            @include('hadith._grade-legend')
        </div>

        {{-- Yahan se naya flex layout shuru --}}
        <div class="d-flex gap-4 align-items-start hadith-layout">

            {{-- LEFT: Collections grid --}}
            <div class="flex-grow-1" style="min-width: 0;">
                <div class="row g-3">
                    @foreach ($collections as $c)
                        <div class="col-md-4">

                            <a href="{{ route('hadith.chapters', $c->slug) }}"
                                class="text-decoration-none hadith-nav-link">
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

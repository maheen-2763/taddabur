@extends('layouts.app')
@section('title', 'Hadith Collections')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hadith-show.css') }}">
@endpush

@section('content')
    <div class="container py-4">
        <h2 class="heading-font mb-3" style="color:var(--emerald)">Hadith Collections</h2>
        {{-- Browse by grade --}}
        <div class="mb-4">
            <p class="text-muted mb-2" style="font-size:0.85rem">Browse by grade</p>
            <div class="grade-buttons d-flex flex-wrap gap-2">
                <a href="{{ route('hadith.grade', 'Sahih') }}" class="hadith-back-btn">Sahih</a>
                <a href="{{ route('hadith.grade', 'Hasan') }}" class="hadith-back-btn">Hasan</a>
                <a href="{{ route('hadith.grade', 'Daif') }}" class="hadith-back-btn">Da'if</a>
                <a href="{{ route('hadith.grade', 'Very Daif') }}" class="hadith-back-btn">Very Da'if</a>
                <a href="{{ route('hadith.grade', 'Munkar') }}" class="hadith-back-btn">Munkar</a>
                <a href="{{ route('hadith.grade', 'Shadh') }}" class="hadith-back-btn">Shadh</a>
                <a href="{{ route('hadith.grade', 'Mawdu') }}" class="hadith-back-btn">Mawdu</a>
            </div>
            <p class="text-muted mt-2" style="font-size:0.80rem">
                Sahih Bukhari and Sahih Muslim are considered authentic by scholarly consensus
                and are not individually graded — so they won't appear in these grade pages.
            </p>
        </div>
        @include('hadith._grade-legend')
        <div class="row g-3">
            @foreach ($collections as $c)
                <div class="col-md-4">
                    <a href="{{ route('hadith.chapters', $c->slug) }}" class="text-decoration-none">
                        <div class="hadith-collection-card">
                            <h5 style="color:var(--emerald)">{{ $c->name }}</h5>
                            <p class="text-muted mb-1" style="font-size:0.85rem">{{ $c->scholar }}</p>
                            <span class="hadith-count-badge">{{ $c->display_count }} Hadiths</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection

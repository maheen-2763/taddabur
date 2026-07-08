@extends('layouts.app')
@section('title', $collection->name . ' — Chapters')

@section('content')
    <div class="container py-4">
        <a href="{{ route('hadith.index') }}" class="btn btn-sm mb-3" style="border:1px solid var(--border)">
            <i class="bi bi-arrow-left"></i> Collections
        </a>
        <h2 class="heading-font mb-1" style="color:var(--emerald)">{{ $collection->name }}</h2>
        <p class="text-muted mb-4">{{ $collection->scholar }}</p>

        <div class="list-group">
            @foreach ($chapters as $ch)
                <a href="{{ route('hadith.show', [$collection->slug, $ch->number]) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span>{{ $ch->number }}. {{ $ch->title }}</span>
                    <span class="badge bg-secondary">{{ $ch->hadiths_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Hadith Collections')

@section('content')
    <div class="container py-4">
        <h2 class="heading-font mb-4" style="color:var(--emerald)">Hadith Collections</h2>

        <div class="row g-3">
            @foreach ($collections as $c)
                <div class="col-md-4">
                    <a href="{{ route('hadith.chapters', $c->slug) }}" class="text-decoration-none">
                        <div class="p-3"
                            style="background:var(--cream); border:1px solid var(--border); border-radius:var(--radius)">
                            <h5 style="color:var(--emerald)">{{ $c->name }}</h5>
                            <p class="text-muted mb-1" style="font-size:0.85rem">{{ $c->scholar }}</p>
                            <span class="badge bg-secondary">{{ $c->display_count }} Hadiths</span>

                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="heading-font mb-0" style="color:var(--emerald-light)">Search Hadiths</h2>
        </div>

        <form action="{{ route('hadith.search') }}" method="GET" class="mb-4">
            <div class="input-group input-group-lg">
                <input type="text" name="q" value="{{ $query }}" class="form-control"
                    placeholder="Search in Arabic or English..." autofocus style="border-color: var(--emerald);">
                <button class="btn" type="submit" style="background: var(--emerald); color: #fff;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>

        @if ($query)
            <p class="text-muted mb-3" style="font-size:0.9rem">
                @if ($results->count())
                    {{ $results->count() }} result(s) found
                @else
                    No results found for "{{ $query }}"
                @endif
            </p>
        @endif

        @foreach ($results as $hadith)
            <div class="card mb-3 hadith-card">
                <div class="card-body">
                    <p class="text-end fs-5 mb-2" dir="rtl" lang="ar">
                        {!! $hadith->arabic_highlighted !!}
                    </p>
                    <p class="mb-3">{!! $hadith->english_highlighted !!}</p>

                    <a href="{{ route('hadith.show', ['collection' => $hadith->collection_slug, 'chapter' => $hadith->chapter_number]) }}?highlight={{ $hadith->id }}"
                        class="btn btn-sm d-inline-flex align-items-center gap-1"
                        style="color:var(--emerald-light); border: 1px solid var(--emerald-light);">
                        View in context <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endforeach

    </div>

    <style>
        mark.search-highlight {
            background: var(--emerald-light, #d4edda);
            color: var(--emerald, #1b4332);
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 600;
        }
    </style>
@endsection

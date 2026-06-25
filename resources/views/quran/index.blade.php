@extends('layouts.app')
@section('title', 'The Holy Quran — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-index.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="quran-page">

        @include('quran.partials.hero', [
            'surahCount' => 114,
            'ayahCount' => 6236,
            'juzCount' => 30,
            'completedCount' => $completedCount ?? 0,
        ])

        @include('quran.partials.search')
        @include('quran.partials.search-results')

        <div class="quran-bookshelf-wrapper" id="bookshelfWrapper">
            @foreach ($juzData->chunk(5) as $rowIndex => $row)
                <div class="juz-row" data-row="{{ $rowIndex }}">
                    @foreach ($row as $juz)
                        @include('quran.partials.juz-book', [
                            'juz' => $juz,
                            'juzNum' => $juz['juz'],
                            'row' => $rowIndex,
                        ])
                    @endforeach
                </div>

                <div class="inline-panel" id="inline-panel-{{ $rowIndex }}" data-row="{{ $rowIndex }}">
                    <div class="inline-panel-inner">
                        <div class="inline-panel-header">
                            <div class="inline-panel-title" id="panel-title-{{ $rowIndex }}"></div>
                            <button class="inline-panel-close" onclick="closePanel({{ $rowIndex }})">✕</button>
                        </div>
                        <div class="panel-tile-grid" id="surah-grid-{{ $rowIndex }}"></div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/quran-index.js') }}"></script>
@endpush

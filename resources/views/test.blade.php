{{-- resources/views/quran/index.blade.php --}}

@extends('layouts.app')
@section('title', 'The Holy Quran — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-index.css') }}">
@endpush

@section('content')

    @include('quran.partials._hero')
    @include('quran.partials._search')

    <div class="bookshelf-section">

        <p class="bookshelf-label">✦ The Holy Quran — 30 Juz ✦</p>

        <div class="shelf-grid" id="shelfGrid">

            @foreach ($juzData as $juz)
                @php
                    $progress = $juz['progress'] ?? ['total' => 0, 'completed' => 0];
                @endphp

                <div class="juz-card" onclick="openJuz({{ $juz['juz'] }})">

                    <div class="juz-glow"></div>

                    <div class="juz-arabic">
                        {{ $juz['title_ar'] }}
                    </div>

                    <div class="juz-title">
                        {{ $juz['title_en'] }}
                    </div>

                    <div class="juz-number">
                        Juz {{ $juz['juz'] }}
                    </div>

                    <div class="juz-footer">
                        {{ $progress['total'] }} Surahs
                        ({{ $progress['completed'] }} completed)
                    </div>

                </div>
            @endforeach

            <div class="shelf-plank"></div>

        </div>

    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/quran-index.js') }}" defer></script>
@endpush

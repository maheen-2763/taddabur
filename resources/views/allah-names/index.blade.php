{{-- resources/views/allah-names/index.blade.php --}}

@extends('layouts.app')
@section('title', 'The 99 Names of Allah — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/allah-names.css') }}">
@endpush

@section('content')

    <div class="honeycomb-wrap">

        {{-- Header --}}
        <div class="honeycomb-header">


            <span class="arabic-title">أَسْمَاءُ ٱللَّهِ ٱلْحُسْنَى</span>
            <span class="latin-title">The 99 Beautiful Names of Allah</span>

            <p class="verse-ref">
                "And to Allah belong the best names, so invoke Him by them."
                <small>— Surah Al-A'raf 7:180</small>
            </p>
        </div>

        {{-- Honeycomb Grid — each cell is a "book" that opens on click --}}
        <div class="honeycomb-container">
            @foreach ($rows as $rowIndex => $row)
                <div class="hex-row {{ $rowIndex % 2 === 1 ? 'hex-row-offset' : '' }}">
                    @foreach ($row as $name)
                        <div class="hex-cell" data-slug="{{ $name->slug }}">

                            {{-- Layer 1: revealed name, glows upward when opened --}}
                            <div class="hex-back">
                                <span class="hex-back-arabic">{{ $name->name_ar }}</span>
                            </div>

                            {{-- Layer 2: closed book cover, swings open on click --}}
                            <div class="hex-cover">
                                <span class="hex-number">{{ $name->position }}</span>
                                <span class="hex-cover-arabic">{{ $name->name_ar }}</span>
                            </div>

                            {{-- Meaning tooltip, fades in above when opened --}}
                            <div class="hex-reflection">
                                <strong>{{ $name->transliteration }}</strong>
                                <span>{{ $name->english_name }}</span>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>

    {{-- One shared audio element reused for all 99 names --}}
    <audio id="namesAudioPlayer" style="display:none"></audio>

@endsection

@push('scripts')
    <script src="{{ asset('js/allah-names.js') }}"></script>
@endpush

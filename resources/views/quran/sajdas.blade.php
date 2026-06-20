{{-- resources/views/quran/sajdas.blade.php --}}

@extends('layouts.app')
@section('title', 'Verses of Prostration — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-sajdas.css') }}">
@endpush

@section('content')

    <div class="sajdas-page">

        {{-- Header --}}
        <div class="sajdas-header">
            <span class="symbol">۩</span>
            <span class="arabic-title">آيَاتُ ٱلسَّجْدَة</span>
            <span class="latin-title">Verses of Prostration (Sajdah at-Tilawah)</span>
        </div>

        {{-- Guidance note --}}
        <div class="sajdas-note">
            <strong>What to do:</strong> Upon reciting or hearing one of
            these verses, it is recommended — and considered obligatory
            in the Hanafi school — to perform a single prostration
            (Sajdah at-Tilawah).
            <br><br>
            <strong>Note on count:</strong> Scholars differ slightly on
            the exact number and placement of these verses — the Hanafi
            school counts 14, while others count 15, differing mainly on
            Surah Al-Hajj. The list below reflects the verses marked in
            this app's Quran text.
        </div>

        {{-- Count --}}
        <p class="sajdas-count">
            {{ $sajdaAyahs->count() }} Verses Found
        </p>

        {{-- List --}}
        @foreach ($sajdaAyahs as $ayah)
            <a href="{{ route('quran.show', $ayah->surah->number) }}#ayah-{{ $ayah->number }}" class="sajda-card">

                <div class="sajda-symbol">۩</div>

                <div class="sajda-info">
                    <div class="sajda-ref">
                        {{ $ayah->surah->name_transliteration }}
                        ({{ $ayah->surah->name_arabic }})
                        — {{ $ayah->surah->number }}:{{ $ayah->number }}
                    </div>
                    <div class="sajda-arabic-preview">
                        {{ Str::limit($ayah->text_arabic, 60) }}
                    </div>
                </div>

                <i class="bi bi-arrow-right sajda-arrow"></i>

            </a>
        @endforeach

    </div>

@endsection

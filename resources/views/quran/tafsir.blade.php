{{-- resources/views/quran/tafsir.blade.php --}}

@extends('layouts.app')
@section('title', $surah->name_transliteration . ' ' . $surah->number . ':' . $ayah->number . ' — Tafsir — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tafsir-page.css') }}">
@endpush

@section('content')

    <div class="tafsir-page">

        {{-- Breadcrumb --}}
        <nav class="tafsir-breadcrumb">
            <a href="{{ route('quran.index') }}">Quran</a>
            <span class="sep">›</span>
            <a href="{{ route('quran.show', $surah->number) }}">
                {{ $surah->name_transliteration }}
            </a>
            <span class="sep">›</span>
            <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}">
                Ayah {{ $ayah->number }}
            </a>
            <span class="sep">›</span>
            <span style="color:var(--ink)">Tafsir</span>
        </nav>

        {{-- Ayah Card --}}
        <div class="tafsir-ayah-card">

            {{-- Reference + back button --}}
            <div class="tafsir-ayah-ref">
                <span class="tafsir-ref-badge">
                    {{ $surah->name_transliteration }} · {{ $surah->number }}:{{ $ayah->number }}
                    @if ($ayah->sajda)
                        · ۩ Sajda
                    @endif
                </span>
                <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}" class="tafsir-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    Back to Reader
                </a>
            </div>

            @php
                $displayText = $ayah->text_arabic;
                if (!in_array($surah->number, [1, 9]) && $ayah->number === 1) {
                    $displayText = \App\Helpers\ArabicHelper::stripBismillah($displayText);
                }
            @endphp

            {{-- Arabic text --}}
            <p class="tafsir-arabic">
                {{ $displayText }}
                <span
                    style="font-family:'Amiri',serif;
                         font-size:1rem;
                         color:var(--gold-dark)">
                    &#xFD3F;{{ \App\Helpers\ArabicHelper::toEasternArabic($ayah->number) }}&#xFD3E;
                </span>
            </p>

            {{-- Translation --}}
            @if ($ayah->translations->isNotEmpty())
                <p class="tafsir-translation">
                    {{ $ayah->translations->first()->text }}
                </p>
            @endif

        </div>

        {{-- Tafsir Selector --}}
        <div class="tafsir-selector-wrap">
            <span class="tafsir-selector-label">Select Tafsir</span>
            <select id="tafsirSelector" class="tafsir-selector" onchange="onTafsirChange(this)">
                @foreach ($tafsirs as $t)
                    <option value="{{ $t->slug }}" {{ $selectedTafsir?->slug === $t->slug ? 'selected' : '' }}>
                        {{ $t->name }}
                        @if ($t->scholar)
                            — {{ $t->scholar }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tafsir Content Card --}}
        <div class="tafsir-content-card">

            {{-- Header --}}
            <div class="tafsir-content-header">
                <div class="tafsir-scholar-info">
                    <div class="tafsir-scholar-name" id="tafsirScholarName">
                        {{ $selectedTafsir?->name ?? 'Loading...' }}
                    </div>
                    <div class="tafsir-scholar-period" id="tafsirScholarPeriod">
                        {{ $selectedTafsir?->scholar ?? '' }}
                    </div>
                </div>
                <div class="tafsir-scholar-arabic" id="tafsirScholarArabic">
                    {{ $selectedTafsir?->name_arabic ?? '' }}
                </div>
            </div>

            {{-- Tafsir text — loaded via JS --}}
            <div id="tafsirBody">
                <div class="tafsir-loading">
                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                    <span>Loading tafsir...</span>
                </div>
            </div>

        </div>

        {{-- Prev / Next Ayah Navigation --}}
        <div class="tafsir-nav">

            {{-- Previous Ayah --}}
            @if ($prevAyah)
                <a href="{{ route('quran.tafsir', [$surah->number, $prevAyah->id]) }}" class="tafsir-nav-btn">
                    <i class="bi bi-chevron-left"></i>
                    <div>
                        <div style="font-size:0.65rem; color:var(--muted)">Previous</div>
                        <div>Ayah {{ $prevAyah->number }}</div>
                    </div>
                </a>
            @else
                <div></div>
            @endif

            {{-- Center info --}}
            <div class="tafsir-nav-center">
                <div style="font-family:var(--font-heading); font-size:0.75rem; color:var(--ink)">
                    {{ $surah->name_transliteration }}
                </div>
                <div style="font-size:0.7rem; color:var(--muted)">
                    Ayah {{ $ayah->number }} of {{ $surah->ayah_count }}
                </div>
            </div>

            {{-- Next Ayah --}}
            @if ($nextAyah)
                <a href="{{ route('quran.tafsir', [$surah->number, $nextAyah->id]) }}" class="tafsir-nav-btn">
                    <div style="text-align:right">
                        <div style="font-size:0.65rem; color:var(--muted)">Next</div>
                        <div>Ayah {{ $nextAyah->number }}</div>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <div></div>
            @endif

        </div>

    </div>

@endsection

@push('scripts')
    <script>
        window.TAFSIR_CONFIG = {
            surahNumber: {{ $surah->number }},
            ayahId: {{ $ayah->id }},
            ayahNumber: {{ $ayah->number }},
            defaultTafsir: '{{ $selectedTafsir?->slug ?? 'ibn-kathir-en' }}',
        };
    </script>
    <script src="{{ asset('js/tafsir-page.js') }}" defer></script>
@endpush

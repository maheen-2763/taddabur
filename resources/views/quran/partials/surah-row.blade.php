{{-- resources/views/quran/partials/_surah_row.blade.php --}}

@php
    $completed = $completed ?? false;
@endphp

<div class="col-12 col-md-6 col-lg-4">

    <a href="{{ route('quran.show', ['surah' => $surah['number']]) }}" class="surah-row">

        <div class="surah-row-num">

            @if ($completed)
                <i class="bi bi-check surah-row-check"></i>
            @else
                {{ $surah['number'] }}
            @endif

        </div>

        <div class="flex-grow-1 min-width-0">

            <a class="surah-row"
                data-search="
        {{ strtolower($surah['name_transliteration']) }}
        {{ strtolower($surah['name_english']) }}
    ">

                <div class="surah-row-meta">
                    {{ $surah['name_english'] }}
                    · {{ $surah['ayah_count'] }} Ayahs
                    · {{ ucfirst($surah['type']) }}
                </div>

        </div>

        <div class="surah-row-arabic">
            {{ $surah['name_arabic'] }}
        </div>

        @if ($completed)
            <div class="surah-row-completed">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        @endif

    </a>

</div>

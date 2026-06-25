@php $surahs = collect($juz['surahs'] ?? []); @endphp

<div class="book-card" id="book-{{ $juzNum }}" data-juz="{{ $juzNum }}" data-row="{{ $row }}"
    data-surahs="{{ json_encode($surahs) }}" data-arabic="{{ $juz['arabic_name'] ?? '' }}"
    data-trans="{{ $juz['transliteration'] ?? 'Juz ' . $juzNum }}"
    onclick="toggleJuz({{ $juzNum }}, {{ $row }})">

    <div class="flip-inner" id="flip-{{ $juzNum }}">

        {{-- FRONT — C3: Arabic name dominant --}}
        <div class="book-front">
            <div class="book-spine"></div>
            <div class="book-pages"></div>
            <div class="book-border"></div>

            <div class="book-ornament-top">✦</div>

            <div class="book-arabic-dominant">
                {{ $juz['arabic_name'] ?? '' }}
            </div>

            <div class="book-transliteration">
                {{ $juz['transliteration'] ?? '' }}
            </div>

            <div class="book-juz-badge">Juz {{ $juzNum }}</div>

            <div class="book-ornament-bottom">❧</div>
        </div>

        {{-- BACK — B4: Ornamental tiles (up to 4, rest in panel) --}}
        <div class="book-back">
            <div class="book-spine"></div>
            <div class="back-header">
                <div class="back-arabic">{{ $juz['arabic_name'] ?? '' }}</div>
                <div class="back-subtitle">Juz {{ $juzNum }} · {{ count($surahs) }}
                    {{ Str::plural('Surah', count($surahs)) }}</div>
            </div>
            <div class="back-tile-grid">
                @foreach (array_slice(is_array($surahs) ? $surahs : $surahs->toArray(), 0, 4) as $surah)
                    <a href="{{ route('quran.show', $surah['number']) }}" class="back-tile"
                        onclick="event.stopPropagation()">
                        <div class="back-tile-num">{{ $surah['number'] }}</div>
                        <div class="back-tile-arabic">{{ $surah['name_arabic'] }}</div>
                        <div class="back-tile-en">{{ $surah['name_transliteration'] }}</div>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    <div class="book-active-arrow">▼</div>
</div>

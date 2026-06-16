{{-- resources/views/quran/partials/_book_card.blade.php --}}
{{--
    Variables expected:
    $juzNum          → int (1-30)
    $juz             → array (arabic, name, start, surahs)
    $juzSurahs       → collection of Surah models
    $completedInJuz  → int (how many surahs completed)
    $isJuzCompleted  → bool
--}}

<div class="book-card {{ $isJuzCompleted ? 'completed' : '' }}" id="book-{{ $juzNum }}"
    onclick="toggleJuz({{ $juzNum }})">

    {{-- Book spine --}}
    <div class="book-spine"></div>

    {{-- Book cover --}}
    <div class="book-cover">

        {{-- Completed checkmark --}}
        @if ($isJuzCompleted)
            <div class="book-completed-check">
                <i class="bi bi-check" style="font-size:0.65rem"></i>
            </div>
        @endif

        {{-- Juz number label --}}
        <div class="book-juz-num">JUZ {{ $juzNum }}</div>

        {{-- Arabic title --}}
        <div>
            <div class="book-arabic-title">{{ $juz['arabic'] }}</div>
            <div class="book-arabic-sub">{{ $juz['name'] }}</div>
        </div>

        {{-- Surah count --}}
        <div class="book-surah-count">
            {{ count($juzSurahs) }} Surahs
            @if ($completedInJuz > 0 && !$isJuzCompleted)
                · {{ $completedInJuz }} done
            @elseif($isJuzCompleted)
                · All done ✓
            @endif
        </div>

    </div>
</div>

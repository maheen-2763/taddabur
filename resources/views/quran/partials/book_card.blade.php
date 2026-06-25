{{-- resources/views/quran/partials/_book_card.blade.php --}}

<div class="book-card {{ $isJuzCompleted ? 'book-completed' : '' }}" id="book-{{ $juz['juz'] }}"
    onclick="toggleJuz({{ $juz['juz'] }})">

    {{-- Spine --}}
    <div class="book-spine"></div>

    {{-- Cover --}}
    <div class="book-cover">

        @if ($isJuzCompleted)
            <div class="book-check">
                <i class="bi bi-check"></i>
            </div>
        @endif

        <div class="book-juz-num">
            Juz {{ $juzNum }}
        </div>

        <div class="book-title-group">
            <div class="book-arabic-name">
                {{ $juz['arabic'] }}
            </div>

            <div class="book-arabic-sub">
                {{ $juz['name'] }}
            </div>
        </div>

        <div class="book-count">

            {{ count($juzSurahs) }} Surahs

            @if ($completedInJuz > 0 && !$isJuzCompleted)
                · {{ $completedInJuz }} Completed
            @endif

            @if ($isJuzCompleted)
                · Complete ✓
            @endif

        </div>

    </div>

</div>


@push('styles')
    <style>
        .book-check i {
            font-size: 0.65rem;
        }

        .book-title-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.15rem;
        }
    </style>
@endpush

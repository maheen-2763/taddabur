{{-- resources/views/components/dashboard/resume-quran.blade.php --}}

@if ($quranProgress?->lastAyah)
    @php
        $ayah = $quranProgress->lastAyah;
        $surah = $ayah->surah;
        $ayahPosition = $ayah->number;
        $totalAyahs = $surah->ayah_count;
        $progress = $totalAyahs ? round(($ayahPosition / $totalAyahs) * 100) : 0;
    @endphp

    <div class="d-card">

        <h5 class="d-card-title">
            <i class="bi bi-book" style="color:var(--emerald)"></i>
            Continue Your Quran Journey
        </h5>

        <div class="d-resume-meta">
            <small class="d-resume-lastread">
                Last read
                @if ($quranProgress->last_read_at)
                    · {{ $quranProgress->last_read_at?->diffForHumans() }}
                @endif
            </small>
        </div>

        <div class="d-resume-ayah-box">
            <p class="arabic-sm">﴿ {{ $ayah->text_arabic }} ﴾</p>
        </div>

        <div class="d-resume-progress-row">
            <span class="d-resume-surah-name">Surah : {{ $surah->name_transliteration }}</span>
            <span class="d-resume-position">Ayah {{ $ayahPosition }} of {{ $totalAyahs }}</span>
        </div>

        <div class="d-progress mb-3">
            <div class="d-progress-fill" style="width: {{ $progress }}%"></div>
        </div>

        <div class="text-end">
            <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}" class="btn-emerald btn btn-sm">
                Continue Reading →
            </a>
        </div>

    </div>
@else
    <div class="d-card">
        <div class="d-empty">
            <i class="bi bi-book d-empty-icon"></i>
            <p class="d-empty-message">
                Begin your Quran journey and your progress will appear here.
            </p>
            <a href="{{ route('quran.index') }}" class="btn-emerald btn btn-sm">
                Start Reading
            </a>
        </div>
    </div>
@endif

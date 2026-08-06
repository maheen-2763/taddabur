{{-- resources/views/components/dashboard/resume-quran.blade.php --}}
@if ($quranProgress?->lastAyah)
    @php
        $ayah = $quranProgress->lastAyah;
        $surah = $ayah->surah;
        $totalAyahs = $surah->ayah_count;
        $readCountSafe = $readCount ?? 0;

        // ✅ Progress ab actual "read count" pe based hai, position pe nahi
        $progress = $totalAyahs ? round(($readCountSafe / $totalAyahs) * 100) : 0;

        $displayText = $ayah->text_arabic;
        if (!in_array($surah->number, [1, 9]) && $ayah->number === 1) {
            $displayText = \App\Helpers\ArabicHelper::stripBismillah($displayText);
        }
    @endphp

    <div class="d-card">

        <h5 class="d-card-title">
            <i class="bi bi-book" style="color:var(--emerald-light)"></i>
            Continue Your Quran Journey
        </h5>

        <div class="d-resume-meta">
            <small class="d-resume-lastread">
                {{ $readCountSafe }} of {{ $totalAyahs }} ayahs read
                @if ($quranProgress->last_read_at)
                    · {{ $quranProgress->last_read_at?->diffForHumans() }}
                @endif
            </small>
        </div>

        <div class="d-resume-ayah-box">
            <p class="arabic-sm mb-3">
                ﴿ {{ $displayText }}﴾
            </p>
        </div>

        <div class="d-resume-progress-row">
            <span class="d-resume-surah-name">Surah : {{ $surah->name_transliteration }}</span>
            <span class="d-resume-position">{{ $readCountSafe }} of {{ $totalAyahs }} read</span>
        </div>

        <div class="d-progress mb-3">
            <div class="d-progress-fill" style="width: {{ $progress }}%"></div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-1">
            <a href="{{ route('quran.show', $surah->number) }}#ayah-1" class="btn-emerald btn btn-sm flex-shrink-0">
                Start Over
            </a>
            <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}"
                class="btn-emerald btn btn-sm flex-shrink-0">
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

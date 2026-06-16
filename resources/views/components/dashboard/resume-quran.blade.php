{{-- Resume Quran Component --}}

@if ($quranProgress?->lastAyah)

    @php
        $ayah = $quranProgress->lastAyah;
        $surah = $ayah->surah;
        $ayahPosition = $ayah->number;
        $totalAyahs = $surah->ayah_count;
    @endphp

    <div class="card-islamic p-4 mb-4">

        <h5 class="heading-font mb-3">
            <i class="bi bi-book me-2" style="color:var(--emerald-light)"></i>
            Continue Your Quran Journey
        </h5>

        {{-- Last Read Info --}}
        <div class="mb-3">

            <small class="text-muted d-block mb-2">
                Last read
                @if ($quranProgress->last_read_at)
                    • {{ $quranProgress->last_read_at?->format('d M Y, h:i A') }}
                @endif
            </small>

            <p class="arabic-sm mb-3">
                {{ $ayah->text_arabic }}
            </p>

        </div>

        {{-- Surah Details --}}
        <div class="mb-3">

            <div class="fw-semibold">
                {{ $surah->name_transliteration }}
            </div>

            <small class="text-muted">
                Ayah {{ $ayahPosition }} of {{ $totalAyahs }}
            </small>

        </div>

        {{-- Reading Progress Inside Surah --}}
        <div class="mb-3">

            @php
                $progress = round(($ayahPosition / $totalAyahs) * 100);
            @endphp

            <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Surah Progress</small>
                <small class="text-muted">{{ $progress }}%</small>
            </div>

            <div class="progress" style="height:6px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;">
                </div>
            </div>

        </div>

        {{-- Action --}}
        <div class="text-end">

            <a href="{{ route('quran.show', $surah->number) }}#ayah-{{ $ayah->number }}" class="btn btn-sm btn-emerald">
                Continue Reading →
            </a>

        </div>

    </div>
@else
    <div class="card-islamic p-4">

        <x-empty-state message="Begin your Quran journey and your progress will appear here." icon="bi-book"
            action="Start Reading" link="{{ route('quran.index') }}" />

    </div>

@endif

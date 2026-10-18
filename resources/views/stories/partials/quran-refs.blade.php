@php
    $refs = collect($chapter->quran_references ?? [])
        ->map(function ($ref) {
            [$surah, $ayah] = array_pad(explode(':', $ref, 2), 2, null);
            return [
                'label' => $ref,
                'surah' => (int) $surah,
                'ayah' => $ayah ? (int) explode('-', $ayah)[0] : null,
            ];
        })
        ->filter(fn($r) => $r['surah'] > 0);

    $validSurahs = \App\Models\Surah::whereIn('number', $refs->pluck('surah'))->pluck('number')->all();
@endphp

@if ($refs->isNotEmpty())
    <div class="d-flex flex-wrap gap-1">
        @foreach ($refs as $r)
            @if (in_array($r['surah'], $validSurahs))
                <a href="{{ route('quran.show', $r['surah']) }}{{ $r['ayah'] ? '#ayah-' . $r['ayah'] : '' }}"
                    class="quran-ref-chip">
                    <i class="bi bi-bookmark-star" style="font-size:0.68rem"></i>{{ $r['label'] }}
                </a>
            @endif
        @endforeach
    </div>
@endif

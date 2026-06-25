{{-- resources/views/quran/partials/_hero.blade.php --}}

<div class="quran-hero">

    <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
        <div style="width:70px; height:1px; background:var(--gold); opacity:0.4"></div>
        <span style="font-family:'Amiri',serif; font-size:1.8rem; color:var(--gold); opacity:0.7">﷽</span>
        <div style="width:70px; height:1px; background:var(--gold); opacity:0.4"></div>
    </div>

    <p class="bismillah-sub">
        In the name of Allah, the Most Gracious, the Most Merciful
    </p>

    <div class="quran-stats">

        <div class="q-stat">
            <span class="q-stat-num">{{ $surahCount ?? 114 }}</span>
            <span class="q-stat-lbl">Surahs</span>
        </div>

        <div class="q-stat">
            <span class="q-stat-num">{{ $ayahCount ?? 6236 }}</span>
            <span class="q-stat-lbl">Ayahs</span>
        </div>

        <div class="q-stat">
            <span class="q-stat-num">{{ $juzCount ?? 30 }}</span>
            <span class="q-stat-lbl">Juz</span>
        </div>

        @auth
            <div class="q-stat">
                <span class="q-stat-num q-stat-completed">
                    {{ $completedCount ?? 0 }}
                </span>
                <span class="q-stat-lbl">Completed</span>
            </div>
        @endauth

    </div>

</div>

@push('styles')
    <style>
        .q-stat-completed {
            color: var(--emerald-light);
        }
    </style>
@endpush

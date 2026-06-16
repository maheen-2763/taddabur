{{-- resources/views/quran/partials/_hero.blade.php --}}

<div class="quran-hero">

    {{-- Bismillah --}}
    <p class="bismillah-grand">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
    <p class="bismillah-sub">
        In the name of Allah, the Most Gracious, the Most Merciful
    </p>

    {{-- Stats --}}
    <div class="quran-stats">
        <div>
            <span class="q-stat-num">١١٤</span>
            <span class="q-stat-lbl">Surahs</span>
        </div>
        <div>
            <span class="q-stat-num">٦٢٣٦</span>
            <span class="q-stat-lbl">Ayahs</span>
        </div>
        <div>
            <span class="q-stat-num">٣٠</span>
            <span class="q-stat-lbl">Juz</span>
        </div>
        @auth
            <div>
                <span class="q-stat-num" style="color:var(--emerald-light)">
                    {{ $completedCount ?? 0 }}
                </span>
                <span class="q-stat-lbl">Completed</span>
            </div>
        @endauth
    </div>

</div>

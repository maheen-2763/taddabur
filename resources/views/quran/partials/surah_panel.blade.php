{{-- resources/views/quran/partials/_surah_panel.blade.php --}}
{{--
    Variables expected:
    $juzNum           → int (1-30)
    $juz              → array (arabic, name, start, surahs)
    $juzSurahs        → collection of Surah models
    $completedSurahIds → collect() of completed surah IDs
--}}

<div class="surah-panel" id="panel-{{ $juzNum }}">
    <div class="surah-panel-inner">

        {{-- Panel header --}}
        <div class="surah-panel-header">
            <div class="d-flex align-items-center gap-3">
                <span class="surah-panel-title">
                    JUZ {{ $juzNum }} — {{ $juz['name'] }}
                </span>
                <span
                    style="font-family:var(--font-arabic);
                             font-size:1.3rem;
                             color:var(--gold);
                             direction:rtl">
                    {{ $juz['arabic'] }}
                </span>
            </div>
            <button data-close-juz="{{ $juzNum }}" onclick="closeJuz({{ $juzNum }})"
                style="background:none; border:none;
                           color:rgba(255,255,255,0.4);
                           font-size:1.3rem; cursor:pointer;
                           line-height:1">
                &times;
            </button>
        </div>

        {{-- Surah grid --}}
        <div class="row g-2">
            @foreach ($juzSurahs as $surah)
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('quran.show', $surah->number) }}" class="surah-row">

                        {{-- Number --}}
                        <div
                            class="surah-row-num
                        {{ $completedSurahIds->contains($surah->id) ? 'text-success' : '' }}">
                            @if ($completedSurahIds->contains($surah->id))
                                <i class="bi bi-check" style="color:var(--emerald-light); font-size:0.8rem"></i>
                            @else
                                {{ $surah->number }}
                            @endif
                        </div>

                        {{-- Name --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="surah-row-name">
                                {{ $surah->name_transliteration }}
                            </div>
                            <div class="surah-row-meta">
                                {{ $surah->name_english }} ·
                                {{ $surah->ayah_count }} ayahs ·
                                {{ ucfirst($surah->revelation_type) }}
                            </div>
                        </div>

                        {{-- Arabic name --}}
                        <div class="surah-row-arabic">
                            {{ $surah->name_arabic }}
                        </div>

                        {{-- Completed icon --}}
                        @if ($completedSurahIds->contains($surah->id))
                            <div class="surah-row-completed">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                        @endif

                    </a>
                </div>
            @endforeach
        </div>

    </div>
</div>

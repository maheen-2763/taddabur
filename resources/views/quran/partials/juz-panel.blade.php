{{-- resources/views/quran/partials/_juz_panel.blade.php --}}

<div class="surah-panel" id="panel-{{ $juz['juz'] }}">

    <div class="surah-panel-inner">

        <div class="surah-panel-header">

            <div class="panel-title-group">

                <span class="surah-panel-title">
                    Juz {{ $juz['juz'] }} — {{ $juz['title']['en'] }}
                </span>

                <span class="surah-panel-title-ar">
                    {{ $juz['title']['ar'] }}

                </span>

            </div>

            <button type="button" class="surah-panel-close" onclick="closeJuz({{ $juz['juz'] }})">

                &times;

            </button>

        </div>

        <div class="row g-2">

            @foreach ($juz['surahs'] as $surah)
                @include('quran.partials.surah-row', [
                    'surah' => $surah,
                    'completed' => $surah['completed'],
                ])
            @endforeach

        </div>

    </div>

</div>

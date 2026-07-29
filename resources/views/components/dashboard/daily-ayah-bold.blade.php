{{-- resources/views/components/dashboard/daily-ayah-bold.blade.php
     OPTION A — Bold dark gradient hero card
--}}

@if ($dailyContent?->ayah)

    @php
        $showBismillahTop = !in_array($dailyContent->ayah->surah->number, [1, 9]) && $dailyContent->ayah->number === 1;
        $ayahText = $showBismillahTop
            ? \App\Helpers\ArabicHelper::stripBismillah($dailyContent->ayah->text_arabic)
            : $dailyContent->ayah->text_arabic;
    @endphp
    <div class="d-reflection-bold">

        <div class="d-reflection-bold-header">
            <h5 class="d-reflection-bold-title">۞ Today's Reflection</h5>
        </div>

        @if ($showBismillahTop)
            <div class="bismillah-mini">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيمِ</div>
        @endif

        <p class="arabic">﴿ {{ $ayahText }} ﴾</p>

        <p class="d-reflection-bold-translation">
            "{{ $dailyContent->ayah->translations->first()?->text }}"
        </p>

        @if ($dailyContent->reflection)
            <div class="d-reflection-bold-box">
                <small class="d-reflection-bold-box-label">Reflection</small>
                <p>{{ Str::limit($dailyContent->reflection, 180) }}</p>
            </div>
        @endif

        <div class="d-reflection-bold-prompt">
            <span class="d-reflection-bold-prompt-label">💭 Pause and reflect for 30 seconds</span>
            <p>How does this ayah connect with your life today? What message
                is Allah ﷻ inviting you to reflect upon?</p>
        </div>

        <div class="d-reflection-bold-footer">
            <small class="d-reflection-bold-ref">
                {{ $dailyContent->ayah->surah->name_transliteration }} ·
                {{ $dailyContent->ayah->surah->number }}:{{ $dailyContent->ayah->number }}
            </small>

            @if ($dailyContent->reflection)
                <a href="{{ route('reflections.show', $dailyContent) }}" class="btn btn-sm"
                    style="background:var(--gold); color:#1A1A2E; border:none; font-size:0.78rem">
                    Read Reflection →
                </a>
            @endif
        </div>

    </div>
@endif

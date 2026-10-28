@php
    $src = $chapter->sources ?? [];
    $hadith = array_filter((array) ($src['hadith'] ?? []));
    $scholarly = array_filter((array) ($src['scholarly'] ?? []));
    $tradition = array_filter((array) ($src['tradition'] ?? []));
    $hasQuran = !empty($chapter->quran_references);
@endphp

@if ($hasQuran || $hadith || $scholarly || $tradition)
    <section class="sources-box" aria-labelledby="sourcesHeading">
        <h2 class="sources-title" id="sourcesHeading">Sources</h2>

        @if ($hasQuran)
            <div class="sources-row">
                <span class="src-badge src-quran">Qur'an</span>
                <div class="sources-body">
                    @include('stories.partials.quran-refs')
                </div>
            </div>
        @endif

        @if ($hadith)
            <div class="sources-row">
                <span class="src-badge src-hadith">Hadith</span>
                <ul class="sources-list">
                    @foreach ($hadith as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($scholarly)
            <div class="sources-row">
                <span class="src-badge src-scholarly">Scholarly</span>
                <ul class="sources-list">
                    @foreach ($scholarly as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($tradition)
            <div class="sources-row">
                <span class="src-badge src-tradition">Tradition</span>
                <div class="sources-body">
                    <ul class="sources-list">
                        @foreach ($tradition as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <p class="sources-note">
                        These details come from tradition and tafsir, not from the Qur'an text itself.
                    </p>
                </div>
            </div>
        @endif
    </section>
@endif
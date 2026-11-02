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
        @if ($scholarly || $tradition)
            <div class="sources-row">
                <span class="src-badge src-scholarly">Scholarly Tradition</span>
                <div class="sources-body">
                    <ul class="sources-list">
                        @foreach ($scholarly as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                        @foreach ($tradition as $item)
                            <li class="sources-caveat">{{ $item }}</li>
                        @endforeach
                    </ul>
                    @if ($tradition)
                        <p class="sources-note">Italic notes explain where a report is weaker or disputed.</p>
                    @endif
                </div>
            </div>
        @endif

    </section>
@endif
@push('styles')
    <style>
        .sources-caveat {
            font-style: italic;
        }
    </style>
@endpush
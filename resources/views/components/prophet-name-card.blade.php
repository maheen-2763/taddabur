@props(['item', 'index'])

<div class="pname-item {{ $index % 2 === 0 ? 'pname-left' : 'pname-right' }}">
    <div class="pname-dot"></div>
    <div class="pname-card">
        <span class="pname-arabic">{{ $item->name_ar }}</span>
        <h3 class="pname-translit">{{ $item->name_transliteration }}</h3>
        <p class="pname-meaning">{{ $item->meaning }}</p>

        <div class="pname-source">
            @if ($item->hadith_id)
                {{-- ✅ Hadith reference — same pill family, emerald tone --}}
                <div class="pname-ref-pills">
                    <span class="pname-ref-icon">📗</span>
                    <a href="{{ route('hadith.show', ['collection' => $item->hadith->collection->slug, 'chapter' => $item->hadith->chapter->number]) }}?highlight={{ $item->hadith_id }}"
                        class="pname-ref-pill pname-ref-hadith">
                        {{ $item->source_reference }}
                    </a>
                </div>
            @elseif ($item->all_references)
                {{-- ✅ Multiple Quran references — pill row --}}
                <div class="pname-ref-pills">
                    <span class="pname-ref-icon">📖</span>
                    @foreach ($item->all_references as $i => $ref)
                        <a href="{{ route('quran.show', $ref['surah']) }}?highlight={{ $ref['ayah'] }}"
                            class="pname-ref-pill {{ $i === 0 ? 'pname-ref-primary' : '' }}">
                            {{ $ref['surah'] }}:{{ $ref['ayah'] }}
                        </a>
                    @endforeach
                </div>
            @elseif ($item->ayah_id)
                {{-- ✅ Single Quran reference — same pill style --}}
                <div class="pname-ref-pills">
                    <span class="pname-ref-icon">📖</span>
                    <a href="{{ route('quran.show', $item->ayah->surah->number) }}?highlight={{ $item->ayah->number }}"
                        class="pname-ref-pill pname-ref-primary">
                        {{ $item->source_reference }}
                    </a>
                </div>
            @else
                <span class="pname-ref-static">📖 {{ $item->source_reference }}</span>
            @endif
        </div>
    </div>
</div>

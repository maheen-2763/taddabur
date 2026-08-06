{{-- resources/views/components/dashboard/daily-hadith-bold.blade.php --}}

@if ($dailyContent?->hadith)

    <div class="d-reflection-bold">

        <div class="d-reflection-bold-header">
            <h5 class="d-reflection-bold-title"><i class="bi-chat-square-quote-fill"></i> Hadith of the Day</h5>
        </div>

        <div class="mb-2">
            <span class="hadith-badge badge-bukhari">
                {{ $dailyContent->hadith->collection?->name }}
            </span>
        </div>

        <p class="arabic">﴿ {{ $dailyContent->hadith->arabic }} ﴾</p>

        <p class="d-reflection-bold-translation">
            "{{ $dailyContent->hadith->english }}"
        </p>

        @if ($dailyContent->hadith->narrator_chain)
            <p class="text-muted small">
                <i class="bi bi-link-45deg"></i>
                Narrated via: {{ Str::limit($dailyContent->hadith->narrator_chain, 80) }}
            </p>
        @endif

        <div class="d-reflection-bold-footer">
            <small class="d-reflection-bold-ref">
                {{ $dailyContent->hadith->chapter?->title }} ·
                Hadith #{{ $dailyContent->hadith->number }}
            </small>

            <a href="{{ route('reflections.show', $dailyContent) }}" class="btn btn-sm hadith-read-more">
                <i class="bi bi-book "></i> Read Full Hadith
            </a>
        </div>

    </div>
@endif

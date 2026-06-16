<div class="card-islamic p-4">

    <h5 class="heading-font mb-3">Explore Stories</h5>

    @if ($recommendedStories->isNotEmpty())

        @foreach ($recommendedStories as $story)
            <a href="{{ route('stories.show', $story->slug) }}" class="d-flex gap-2 mb-3 text-decoration-none pb-3">

                <div class="flex-grow-1">

                    <p class="mb-0">{{ $story->title }}</p>

                    <small class="text-muted">
                        {{ $story->read_time_minutes }} min ·
                        {{ $story->chapters_count ?? $story->chapters->count() }} chapters
                    </small>

                </div>

            </a>
        @endforeach
    @else
        <x-empty-state icon="bi-journal-text" title="No recommendations yet" message="Keep reading to unlock suggestions"
            link="{{ route('stories.index') }}" action="Browse Stories" />

    @endif

</div>

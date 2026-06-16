{{-- resources/views/components/dashboard/story-progress.blade.php --}}

@if (!empty($storyProgress) && $storyProgress->isNotEmpty())

    <div class="card-islamic p-4">
        <h5 class="heading-font mb-3">
            <i class="bi bi-journal-text me-2" style="color:var(--gold)"></i>
            Continue Stories
        </h5>

        @foreach ($storyProgress as $progress)
            <div class="d-flex align-items-center gap-3 mb-3 pb-3"
                style="{{ !$loop->last ? 'border-bottom:1px solid var(--border)' : '' }}">

                <div class="flex-grow-1">
                    <p class="mb-0 fw-medium" style="font-size:0.9rem">
                        {{ $progress?->story?->title }}
                    </p>

                    <small class="text-muted">
                        Last Read: {{ $progress?->lastChapter?->title }}
                    </small>
                </div>

                @if ($progress->lastChapter)
                    <a href="{{ route('stories.chapter', [$progress->story->slug, $progress->lastChapter->slug]) }}"
                        class="btn btn-sm btn-emerald">
                        Continue
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@else
    <div class="card-islamic p-4">
        <x-empty-state message="No stories in progress yet" icon="bi-journal-text" action="Browse Stories"
            link="{{ route('stories.index') }}" />
    </div>
@endif

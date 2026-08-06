{{-- resources/views/components/dashboard/story-progress.blade.php --}}

@if ($storyProgress && $storyProgress->isNotEmpty())
    <div class="d-card">

        <h5 class="d-card-title"><i class="bi bi-journals"></i></i> Continue Learning</h5>

        @foreach ($storyProgress as $progress)
            @continue(!$progress->story)

            @php
                $totalChapters = $progress->story->user_progress['total'] ?? $progress->story->chapters->count();
                $completedChapters = $progress->story->user_progress['completed'] ?? 0;
                $progressPercent = $progress->story->user_progress['percentage'] ?? 0;

                $categoryIcons = [
                    'prophet' => 'bi-moon-stars-fill',
                    'companion' => 'bi-people-fill',
                    'general' => 'bi-book-half',
                ];
                $categoryIcon = $categoryIcons[$progress->story->category] ?? 'bi-book-half';
            @endphp

            <div class="d-story-item">
                <div class="d-story-item-row">

                    <div class="flex-grow-1">

                        <div class="d-story-title">
                            <i class="bi {{ $categoryIcon }}" style="color:var(--gold)"></i>
                            {{ $progress->story->title }}
                        </div>

                        <small class="d-story-meta">{{ ucfirst($progress->story->category) }}</small>
                        <small class="d-story-meta">Last chapter: {{ $progress->lastChapter?->title }}</small>
                        <small class="d-story-meta">
                            {{ $completedChapters }} of {{ $totalChapters }} chapters completed ·
                            {{ $progress->updated_at?->diffForHumans() }}
                        </small>

                        <div class="d-story-progress-row">
                            <div class="d-progress mb-1">
                                <div class="d-progress-fill" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <small class="d-stat-label" style="color:var(--emerald-light)">
                                {{ $progressPercent }}% Complete
                            </small>
                        </div>

                    </div>

                    @if ($progress->lastChapter)
                        <a href="{{ route('stories.chapter', [$progress->story->slug, $progress->lastChapter->slug]) }}"
                            class="btn-emerald btn btn-sm flex-shrink-0 d-story-btn">
                            Continue Story →
                        </a>
                    @endif

                </div>
            </div>
        @endforeach

    </div>
@else
    <div class="d-card">
        <div class="d-empty">
            <i class="bi bi-journal-text d-empty-icon"></i>
            <p class="d-empty-message">No stories in progress yet</p>
            <a href="{{ route('stories.index') }}" class="btn-emerald btn btn-sm">
                Browse Stories
            </a>
        </div>
    </div>
@endif

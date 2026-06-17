{{-- resources/views/components/dashboard/story-progress.blade.php --}}

@if ($storyProgress && $storyProgress->isNotEmpty())

    <div class="card-islamic p-4">

        <h5 class="heading-font mb-4">
            ۞ Continue Learning
        </h5>

        @foreach ($storyProgress as $progress)
            @php

                $totalChapters = $progress->story->chapters->count();

                $currentChapter = $progress->lastChapter?->order ?? 1;

                $progressPercentage = $totalChapters ? round(($currentChapter / $totalChapters) * 100) : 0;

                $categoryIcons = [
                    'prophet' => 'ﷺ',
                    'companion' => '۞',
                    'general' => '✦',
                ];

                $categoryIcon = $categoryIcons[$progress->story->category] ?? '✦';

            @endphp

            <div class="story-progress-item">

                <div class="d-flex justify-content-between align-items-start gap-3">

                    <div class="flex-grow-1">

                        {{-- Story Title --}}
                        <h6 class="mb-1 fw-semibold">

                            {{ $categoryIcon }}
                            {{ $progress->story->title }}

                        </h6>

                        {{-- Category --}}
                        <small class="story-meta">

                            {{ ucfirst($progress->story->category) }}

                        </small>

                        {{-- Last Chapter --}}
                        <small class="story-meta d-block mt-2">

                            Last Chapter:

                            {{ $progress->lastChapter?->title }}

                        </small>

                        {{-- Chapter Progress --}}
                        <small class="story-meta d-block">

                            Chapter
                            {{ $currentChapter }}
                            of
                            {{ $totalChapters }}

                        </small>

                        {{-- Last Read --}}
                        <small class="story-meta d-block">

                            Last Read
                            {{ $progress->updated_at?->diffForHumans() }}

                        </small>

                        {{-- Progress Bar --}}
                        <div class="progress mt-3">

                            <div class="progress-bar" role="progressbar" style="width: {{ $progressPercentage }}%">
                            </div>

                        </div>

                        <small class="text-success">

                            {{ $progressPercentage }}%
                            Complete

                        </small>

                    </div>

                    {{-- Continue Button --}}
                    @if ($progress->lastChapter)
                        <a href="{{ route('stories.chapter', [$progress->story->slug, $progress->lastChapter->slug]) }}"
                            class="btn btn-sm btn-emerald">

                            Continue →

                        </a>
                    @endif

                </div>

            </div>

            @unless ($loop->last)
                <hr class="my-4">
            @endunless
        @endforeach

    </div>
@else
    <div class="card-islamic p-4">

        <x-empty-state message="No stories in progress yet" icon="bi-journal-text" action="Browse Stories"
            link="{{ route('stories.index') }}" />

    </div>

@endif


@push('styles')
    <style>
        .story-progress-item {
            transition: .2s ease;
        }

        .story-meta {
            color: var(--muted);
            font-size: .85rem;
        }

        .progress {
            height: 6px;
            background: #e8e1d2;
            border-radius: 999px;
        }

        .progress-bar {
            background: var(--emerald-dark);
        }
    </style>
@endpush

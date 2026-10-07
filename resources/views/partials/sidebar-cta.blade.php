{{-- Start / Continue CTA --}}
<div class="sidebar-cta mb-0">
    <div
        class="sidebar-cta-header 
        {{ $isStoryCompleted ? 'state-completed' : ($continueChapter ? 'state-continue' : 'state-begin') }}">

        @if ($isStoryCompleted)
            <div class="sidebar-cta-arabic">الْحَمْدُ لِلَّٰه</div>
            <h6 class="heading-font mb-1 sidebar-cta-title">Story Completed</h6>
            <p class="sidebar-cta-subtitle">You've finished this story, Alhamdulillah</p>
        @elseif ($continueChapter)
            <div class="sidebar-cta-arabic">بِسْمِ اللَّه</div>
            <h6 class="heading-font mb-1 sidebar-cta-title">Continue Reading</h6>
            <p class="sidebar-cta-subtitle">Pick up where you left off</p>
        @else
            <div class="sidebar-cta-arabic">اقْرَأْ</div>
            <h6 class="heading-font mb-1 sidebar-cta-title">Begin This Story</h6>
            @if ($story->chapters->isNotEmpty())
                <p class="sidebar-cta-subtitle mt-1">
                    {{ $story->chapters->count() }}
                    {{ Str::plural('chapter', $story->chapters->count()) }} await you
                </p>
            @else
                <p class="sidebar-cta-subtitle mt-1">Coming soon, In Shaa Allah</p>
            @endif
        @endif
    </div>
    <div class="sidebar-cta-body">
        @if ($isStoryCompleted)
            <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->slug]) }}"
                class="btn-emerald btn w-100 mb-2">
                <i class="bi bi-arrow-repeat me-1"></i>Read Again
            </a>

            <button type="button" class="btn btn-outline-secondary w-100" style="font-size:0.85rem"
                data-bs-toggle="modal" data-bs-target="#resetProgressModal">
                Reset Progress
            </button>
        @elseif ($continueChapter)
            <a href="{{ route('stories.chapter', [$story->slug, $continueChapter->slug]) }}"
                class="btn-emerald btn w-100 mb-2 d-flex flex-column align-items-center py-2">
                <span><i class="bi bi-play-fill me-1"></i>Continue</span>
                <span style="font-size:0.7rem; opacity:0.8; font-weight:400">
                    {{ $continueChapter->title }}
                </span>
            </a>
            <button type="button" class="btn btn-outline-secondary w-100" style="font-size:0.85rem"
                data-bs-toggle="modal" data-bs-target="#resetProgressModalContinue">
                Start from Beginning
            </button>
        @else
            @if ($story->chapters->isNotEmpty())
                <a href="{{ route('stories.chapter', [$story->slug, $story->chapters->first()->slug]) }}"
                    class="btn-emerald btn w-100">
                    <i class="bi bi-play-fill me-1"></i>Start Reading
                </a>
            @else
                <p class="text-muted small text-center mb-0">Chapters coming soon</p>
            @endif
        @endif
    </div>
</div>

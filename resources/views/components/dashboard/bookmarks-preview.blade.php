@props(['bookmarks'])

<div class="d-card">
    <h3 class="d-card-title">
        <i class="bi bi-bookmarks-fill"></i>Saved Bookmarks
    </h3>

    @forelse ($bookmarks as $bookmark)
        <div class="d-story-item">
            <div class="d-flex align-items-center gap-2">
                <i class="bi {{ $bookmark->type_icon }}"></i>
                <span class="d-story-title mb-0">{{ $bookmark->display_title }}</span>
                @if ($bookmark->grade)
                    <span class="badge-grade">{{ $bookmark->grade }}</span>
                @endif
            </div>
            @if ($bookmark->display_preview)
                <span class="d-story-meta">{{ $bookmark->display_preview }}</span>
            @endif
        </div>
    @empty
        <div class="d-empty">
            <i class="bi bi-bookmark d-empty-icon"></i>
            <p class="d-empty-message">No Bookmarks yet. Start bookmarking your favorite Ayahs and Hadiths!</p>
        </div>
    @endforelse

    @if ($bookmarks->isNotEmpty())
        <div class="text-center mt-3">
            <a href="{{ route('bookmarks.index') }}" class="d-explore-allah-names-link">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @endif
</div>

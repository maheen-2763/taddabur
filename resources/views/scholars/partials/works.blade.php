@forelse($scholar->works as $work)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold">{{ $work->title }}</h6>
            @if ($work->arabic_title)
                <p class="arabic mb-1" dir="rtl" lang="ar">{{ $work->arabic_title }}</p>
            @endif
            @if ($work->description)
                <small class="text-muted">{{ $work->description }}</small>
            @endif
        </div>
    </div>
@empty
    <p class="alert alert-info">No works recorded yet.</p>
@endforelse

@forelse($scholar->teachings as $teaching)
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title fw-bold">{{ $teaching->title }}</h5>
            <p class="card-text">{{ $teaching->content }}</p>
        </div>
    </div>
@empty
    <p class="alert alert-info">No teachings recorded yet.</p>
@endforelse

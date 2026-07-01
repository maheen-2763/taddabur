@if ($scholar->trials)
    <div class="card border-warning border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold text-warning">Trials & Tests of Faith</h5>
            <p>{{ $scholar->trials }}</p>
        </div>
    </div>
@else
    <p class="alert alert-secondary">No trials information available.</p>
@endif

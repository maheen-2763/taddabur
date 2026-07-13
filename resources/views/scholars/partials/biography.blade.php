<div class="card border-0 shadow-sm p-4 biography-card" style="--madhab-color: var(--madhab-{{ $scholar->madhab }})">
    <h5 class="fw-bold mb-3">Early Life</h5>
    <p class="text-muted">{{ $scholar->early_life }}</p>
    <hr class="my-4 opacity-25">
    <h5 class="fw-bold mb-3">Biography</h5>
    <p>{{ $scholar->biography }}</p>
</div>

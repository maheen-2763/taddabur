@forelse($scholar->quotes as $quote)
    <div class="card mb-4 border-start border-5 shadow-sm quote-card"
        style="--madhab-color: var(--madhab-{{ $scholar->madhab }})">
        <div class="card-body">
            <blockquote class="blockquote mb-3">
                <p class="arabic fs-5" dir="rtl" lang="ar">
                    {{ $quote->quote_arabic }}
                </p>
            </blockquote>
            <p class="fst-italic text-secondary">{{ $quote->quote_english }}</p>
            @if ($quote->source)
                <footer class="blockquote-footer mt-2">
                    <cite>{{ $quote->source }}</cite>
                </footer>
            @endif
        </div>
    </div>
@empty
    <p class="alert alert-info">No quotes recorded yet.</p>
@endforelse

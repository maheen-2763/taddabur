<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

    <div>

        <h2 class="arabic mb-1">
            رَّبِّ زِدْنِي عِلْمًا
        </h2>

        <p class="mb-1">
            <em>
                "My Lord, increase me in knowledge."
            </em>
        </p>

        <small class="text-muted d-block">
            — Surah Taha (20:114)
        </small>

        <p class="text-muted mb-0 mt-2">
            May Allah increase you in beneficial knowledge,
            help you understand His Book,
            and make you a means of guidance for others.
        </p>

    </div>

    @if (!$user->isPremium())
        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn">
            Upgrade Plan
        </a>
    @endif

</div>
@push('styles')
    <style>
        .arabic {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            line-height: 1.8;
            color: var(--emerald-dark);
        }
    </style>
@endpush

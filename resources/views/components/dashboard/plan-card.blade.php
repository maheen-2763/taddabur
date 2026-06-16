<div class="card-islamic p-4 mb-4">

    <h5 class="heading-font mb-3">Your Plan</h5>

    <span class="badge badge-{{ $user->plan }}">
        {{ strtoupper($user->plan) }}
    </span>

    @if (!$user->isPremium())
        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm w-100 mt-3">
            Upgrade
        </a>
    @endif

</div>

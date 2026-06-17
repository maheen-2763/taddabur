<div class="card-islamic p-4 mb-4">

    <div class="row align-items-center">

        <div class="col-lg-8">

            <h5 class="text-muted">
                السلام عليكم ورحمة الله وبركاته
            </h5>

            <h3 class="heading-font mt-2 mb-3">
                {{ $user->name }} 🌙
            </h3>

            <h2 class="arabic text-center mb-2">
                رَّبِّ زِدْنِي عِلْمًا
            </h2>

            <p class="mb-1">
                <em>
                    "My Lord, increase me in knowledge."
                </em>
            </p>

            <small class="text-muted">
                — Surah Taha (20:114)
            </small>

            <p class="text-muted mt-3 mb-0">
                May Allah increase you in beneficial knowledge,
                deepen your understanding of the Quran,
                and make your journey of Taddabur a source of light.
            </p>

        </div>

    </div>

    @if (!$user->isPremium())
        <div class="mt-4 text-end">

            <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn">

                Upgrade Plan

            </a>

        </div>
    @endif

</div>

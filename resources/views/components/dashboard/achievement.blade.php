<div class="card-islamic p-4">

    <h5 class="heading-font mb-4">
        <i class="bi bi-award-fill me-2"></i>
        Achievement
    </h5>

    <div class="text-center">

        <div style="font-size:3rem">
            🏅
        </div>

        <h5 class="heading-font mt-3 mb-2">
            {{ $achievement['title'] }}
        </h5>

        <p class="text-muted mb-3">
            {{ number_format($achievement['ayahsRead']) }}
            Ayahs Read
        </p>

        @if ($achievement['nextGoal'])
            <hr>

            <small class="text-muted d-block mb-2">
                Next Goal
            </small>

            <div class="fw-bold">
                {{ number_format($achievement['nextGoal']) }}
                Ayahs
            </div>

            <small class="text-success">
                {{ number_format($achievement['remaining']) }}
                Remaining
            </small>
        @else
            <div class="badge bg-success mt-2">
                Quran Completed 🎉
            </div>
        @endif

    </div>

</div>

<div class="card-islamic p-4">

    <h5 class="heading-font mb-4">
        <i class="bi bi-award-fill me-2"></i>
        Quran Achievement
    </h5>

    <div class="text-center">

        <div style="font-size:3rem">
            {{ $achievement['icon'] }}
        </div>

        <div class="arabic text-center">
            {{ $achievement['arabic'] }}
        </div>

        <h5 class="heading-font mt-3 mb-2">
            {{ $achievement['title'] }}
        </h5>

        <p class="text-muted mb-3">
            📖 {{ number_format($achievement['ayahsRead']) }}
            Ayahs Read
        </p>

        @if ($achievement['nextGoal'])
            <hr>

            <small class="text-muted d-block mb-2">
                Next Milestone
            </small>

            <div class="fw-bold mb-2">
                {{ number_format($achievement['nextGoal']) }}
                Ayahs
            </div>

            <div class="progress mb-2" style="height:8px;">
                <div class="progress-bar" style="width: {{ $achievement['progress'] }}%">
                </div>
            </div>

            <small class="text-success">
                {{ number_format($achievement['remaining']) }}
                Ayahs Remaining
            </small>
        @else
            <div class="badge bg-success mt-3 px-3 py-2">
                👑 Quran Completed
            </div>
        @endif

    </div>

</div>
@push('styles')
    <style>
        .arabic {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
        }
    </style>
@endpush

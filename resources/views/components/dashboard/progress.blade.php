<div class="card-islamic p-4 mb-3">

    <h5 class="heading-font mb-3">
        <i class="bi bi-graph-up me-2"></i>
        Your Progress
    </h5>

    <div class="row text-center g-3">

        <div class="col-6">
            <div class="stat-number">
                {{ $stats['streak'] ?? 0 }}
            </div>

            <small class="text-muted">
                🔥 Streak
            </small>
        </div>

        <div class="col-6">
            <div class="stat-number">
                {{ $stats['totalAyahsRead'] ?? 0 }}
            </div>

            <small class="text-muted">
                📖 Ayahs Read
            </small>
        </div>

        <div class="col-6">
            <div class="stat-number">
                {{ $stats['completedSurahs'] ?? 0 }}
            </div>

            <small class="text-muted">
                🕌 Surah Completed
            </small>
        </div>



        <div class="col-6">
            <div class="stat-number">
                {{ $stats['storyCount'] ?? 0 }}
            </div>

            <small class="text-muted">
                📚 Stories Read
            </small>
        </div>
        <hr>
        @php
            $completion = round((($stats['totalAyahsRead'] ?? 0) / 6236) * 100, 1);
        @endphp

        <div class="small text-muted">
            {{ $completion }}% Complete
        </div>

        <div class="text-center">
            <div class="fw-bold">
                📖 Quran Journey
            </div>

            <div>
                {{ $stats['totalAyahsRead'] ?? 0 }} / 6236 Ayahs
            </div>

            <div class="progress mt-2">
                <div class="progress-bar" style="width: {{ ($stats['totalAyahsRead'] / 6236) * 100 }}%">
                </div>
            </div>
        </div>

    </div>

</div>

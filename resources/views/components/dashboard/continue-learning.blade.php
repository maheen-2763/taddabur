{{-- resources/views/components/dashboard/continue-learning.blade.php --}}

<div class="d-card">

    <h5 class="d-card-title">
        <i class="bi bi-compass" style="color:var(--emerald)"></i>
        Explore More
    </h5>

    <div class="d-explore-grid">

        <a href="{{ route('stories.index') }}" class="d-explore-item is-live">
            <span class="d-explore-item-label">📖 Stories</span>
            <i class="bi bi-arrow-right d-explore-arrow"></i>
        </a>

        <div class="d-explore-item">
            <span class="d-explore-item-label">👳 Sahabah</span>
            <span class="d-explore-soon">Coming Soon</span>
        </div>

        <div class="d-explore-item">
            <span class="d-explore-item-label">🕌 Four Imams</span>
            <span class="d-explore-soon">Coming Soon</span>
        </div>

        <div class="d-explore-item">
            <span class="d-explore-item-label">📜 Hadith</span>
            <span class="d-explore-soon">Coming Soon</span>
        </div>

        <div class="d-explore-item">
            <span class="d-explore-item-label">📚 Tafsir</span>
            <span class="d-explore-soon">Coming Soon</span>
        </div>

        <div class="d-explore-item">
            <span class="d-explore-item-label">🤖 Taddabur AI</span>
            <span class="d-explore-soon">Coming Soon</span>
        </div>

    </div>

</div>

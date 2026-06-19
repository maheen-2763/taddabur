{{-- resources/views/components/dashboard/achievement.blade.php --}}

<div class="d-card text-center">

    <h5 class="d-card-title justify-content-center">❋ Quran Achievement</h5>

    <div class="d-achievement-icon">{{ $achievement['icon'] }}</div>

    <p class="arabic" style="font-size:1.3rem">{{ $achievement['arabic'] }}</p>

    <div class="d-achievement-title">{{ $achievement['title'] }}</div>
    <p class="d-achievement-count">📖 {{ number_format($achievement['ayahsRead']) }} Ayahs Read</p>

    @if ($achievement['nextGoal'])
        <hr style="border-color:var(--border)">

        <p class="d-achievement-milestone-label">Next Milestone</p>
        <div class="d-achievement-milestone-num">{{ number_format($achievement['nextGoal']) }} Ayahs</div>

        <div class="d-progress mb-2">
            <div class="d-progress-fill" style="width: {{ $achievement['progress'] }}%"></div>
        </div>

        <small class="d-achievement-remaining">
            {{ number_format($achievement['remaining']) }} Ayahs Remaining
        </small>
    @else
        <div class="d-achievement-complete">👑 Quran Completed</div>
    @endif

</div>

// public/js/allah-names.js
//
// PURPOSE: Click handler for honeycomb book-cells —
//          works on both the full page and the
//          dashboard preview.
//
// v3 CHANGES:
//   - Audio pronunciation now wired up for real
//     (was a placeholder before)
//   - Stops any currently playing audio before
//     starting a new one (prevents overlap when
//     clicking quickly between names)
//   - Settle timer extended slightly (3.2s) to give
//     the book-opening + glow-rise animation room
//     to finish before it closes
//
// PERFORMANCE: ONE click listener per container via
// event delegation — not 99 individual handlers.

document.addEventListener("DOMContentLoaded", function () {
    document
        .querySelectorAll(".honeycomb-container, .honeycomb-preview-row")
        .forEach(initHoneycombContainer);
});

function initHoneycombContainer(container) {
    container.addEventListener("click", function (e) {
        const cell = e.target.closest(".hex-cell");
        if (!cell || !container.contains(cell)) return;

        const wasActive = cell.classList.contains("is-active");

        // Only one open "book" per container at a time
        container.querySelectorAll(".hex-cell.is-active").forEach((c) => {
            c.classList.remove("is-active");
            clearTimeout(c._settleTimer);
        });

        stopPronunciation();

        if (wasActive) return; // Was already open — just close it

        cell.classList.add("is-active");
        playPronunciation(cell.dataset.slug);

        cell._settleTimer = setTimeout(() => {
            cell.classList.remove("is-active");
        }, 3200);
    });
}

// ════════════════════════════════════════════
// AUDIO — one shared <audio> element reused for
// all 99 names (NOT a new Audio() per click)
// ════════════════════════════════════════════
//
// EXPECTED FILE PATH:
//   public/storage/asma-ul-husna/{slug}.mp3
//
// e.g. for "Ar-Rahman" with slug "ar-rahman":
//   public/storage/asma-ul-husna/ar-rahman.mp3
//
// If your audio files live somewhere else, just
// change the path template below.
//
function playPronunciation(slug) {
    const audioEl = document.getElementById("namesAudioPlayer");
    if (!slug || !audioEl) return;

    audioEl.src = `/storage/asma-ul-husna/${slug}.mp3`;
    audioEl.currentTime = 0;

    // .catch() silences errors if the file doesn't exist
    // yet, or if the browser blocks autoplay — the visual
    // animation still works fine either way
    audioEl.play().catch(() => {});
}

function stopPronunciation() {
    const audioEl = document.getElementById("namesAudioPlayer");
    if (!audioEl) return;
    audioEl.pause();
}

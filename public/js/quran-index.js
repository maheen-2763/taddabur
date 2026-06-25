/*
 * STATE
 * openJuz  — which juz number is currently flipped open (null = none)
 * openRow  — which row's inline panel is visible
 */
let openJuz = null;
let openRow = null;

/**
 * toggleJuz(juzNum, rowIndex)
 * Called by each book card's onclick.
 */
function toggleJuz(juzNum, rowIndex) {
    // Same book clicked again → close
    if (openJuz === juzNum) {
        closePanel(rowIndex);
        return;
    }

    // Different row open → close it silently first
    if (openRow !== null && openRow !== rowIndex) {
        _closePanelSilent(openRow);
    }

    // Deactivate previous book flip
    if (openJuz !== null) {
        document.getElementById(`book-${openJuz}`)?.classList.remove("open");
    }

    // Flip the new book open
    document.getElementById(`book-${juzNum}`)?.classList.add("open");
    openJuz = juzNum;
    openRow = rowIndex;

    // Fill the panel with this juz's surahs
    _populatePanel(rowIndex, document.getElementById(`book-${juzNum}`));

    // Reveal the panel
    document.getElementById(`inline-panel-${rowIndex}`)?.classList.add("open");

    // Smooth scroll so the panel comes into view
    setTimeout(() => {
        document
            .getElementById(`inline-panel-${rowIndex}`)
            ?.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }, 80);
}

/**
 * closePanel(rowIndex)
 * Called by the panel's close button. Public.
 */
function closePanel(rowIndex) {
    _closePanelSilent(rowIndex);
}

/**
 * _closePanelSilent — internal, no scroll
 */
function _closePanelSilent(rowIndex) {
    document
        .getElementById(`inline-panel-${rowIndex}`)
        ?.classList.remove("open");
    if (openJuz !== null) {
        document.getElementById(`book-${openJuz}`)?.classList.remove("open");
    }
    openJuz = null;
    openRow = null;
}

/**
 * _populatePanel(rowIndex, bookEl)
 * Reads surah JSON from data-surahs and builds ornamental tiles.
 */
function _populatePanel(rowIndex, bookEl) {
    const surahs = JSON.parse(bookEl?.dataset?.surahs || "[]");
    const arabic = bookEl?.dataset?.arabic || "";
    const juzNum = bookEl?.dataset?.juz || "";

    // Panel title
    const titleEl = document.getElementById(`panel-title-${rowIndex}`);
    if (titleEl) titleEl.textContent = `${arabic} — Juz ${juzNum}`;

    // Build tiles
    const grid = document.getElementById(`surah-grid-${rowIndex}`);
    if (!grid) return;

    grid.innerHTML = surahs
        .map(
            (s) => `
        <a href="/quran/${s.number}" class="panel-tile">
            <div class="panel-tile-num">${s.number}</div>
            <div class="panel-tile-arabic">${s.name_arabic}</div>
            <div class="panel-tile-en">${s.name_transliteration}</div>
            <div class="panel-tile-meta">${s.ayah_count} Ayahs</div>
        </a>
    `,
        )
        .join("");
}

/* ================================================
   SEARCH
   Reads surah data embedded in book card data-attrs.
   No server call — O(114) in-memory scan.
================================================ */
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("surahSearch");
    const resultsWrap = document.getElementById("searchResults");
    const resultsList = document.getElementById("searchList");
    if (!input) return;

    // Pre-collect all surahs from embedded JSON once
    const allSurahs = [];
    document.querySelectorAll(".book-card").forEach((card) => {
        const surahs = JSON.parse(card.dataset.surahs || "[]");
        const juzNum = card.dataset.juz;
        surahs.forEach((s) => {
            if (!allSurahs.find((x) => x.number === s.number)) {
                allSurahs.push({ ...s, juz: juzNum });
            }
        });
    });

    input.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();

        if (!query) {
            resultsWrap?.classList.add("d-none");
            if (resultsList) resultsList.innerHTML = "";
            return;
        }

        const matches = allSurahs
            .filter(
                (s) =>
                    s.name_transliteration?.toLowerCase().includes(query) ||
                    s.name_english?.toLowerCase().includes(query) ||
                    s.name_arabic?.includes(query) ||
                    String(s.number).includes(query),
            )
            .slice(0, 10);

        if (resultsList) {
            resultsList.innerHTML = matches
                .map(
                    (s) => `
                <a href="/quran/${s.number}" class="search-surah-row">
                    <div class="search-num">${s.number}</div>
                    <div style="flex:1">
                        <div class="search-name">${s.name_transliteration}</div>
                        <div class="search-meta">Juz ${s.juz} · ${s.ayah_count} Ayahs</div>
                    </div>
                    <div class="search-arabic">${s.name_arabic}</div>
                </a>
            `,
                )
                .join("");
        }

        resultsWrap?.classList.toggle("d-none", matches.length === 0);
    });

    document.getElementById("searchClear")?.addEventListener("click", () => {
        input.value = "";
        resultsWrap?.classList.add("d-none");
        if (resultsList) resultsList.innerHTML = "";
    });
});

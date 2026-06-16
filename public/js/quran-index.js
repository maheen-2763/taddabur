// public/js/quran-index.js

// ══════════════════════════════════════════════
// JUZ TOGGLE
// ══════════════════════════════════════════════
let openJuz = null;

function toggleJuz(juzNum) {
    // If same book clicked — close it
    if (openJuz === juzNum) {
        closeJuz(juzNum);
        return;
    }

    // Close currently open panel
    if (openJuz !== null) {
        closeJuz(openJuz);
    }

    // Open the clicked Juz panel
    openPanel(juzNum);
}

function openPanel(juzNum) {
    const panel = document.getElementById("panel-" + juzNum);
    const book = document.getElementById("book-" + juzNum);

    if (panel) {
        panel.classList.add("open");
        setTimeout(() => {
            panel.scrollIntoView({ behavior: "smooth", block: "nearest" });
        }, 350);
    }

    if (book) {
        book.style.transform = "translateY(-12px) rotate(-2deg)";
        book.querySelector(".book-cover").style.boxShadow =
            "0 20px 50px rgba(201,150,58,0.4)";
    }

    openJuz = juzNum;
}

function closeJuz(juzNum) {
    const panel = document.getElementById("panel-" + juzNum);
    const book = document.getElementById("book-" + juzNum);

    if (panel) panel.classList.remove("open");

    if (book) {
        book.style.transform = "";
        book.querySelector(".book-cover").style.boxShadow = "";
    }

    openJuz = null;
}

// Close panel when clicking outside
document.addEventListener("click", function (e) {
    if (openJuz === null) return;

    const clickedBook = e.target.closest(".book-card");
    const clickedPanel = e.target.closest(".surah-panel");
    const clickedClose = e.target.closest("[data-close-juz]");

    if (!clickedBook && !clickedPanel && !clickedClose) {
        closeJuz(openJuz);
    }
});

// ══════════════════════════════════════════════
// SEARCH
// ══════════════════════════════════════════════
function initSearch(surahsData) {
    const searchInput = document.getElementById("surahSearch");
    const searchResults = document.getElementById("searchResults");
    const searchList = document.getElementById("searchList");
    const shelfGrid = document.getElementById("shelfGrid");

    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
        const q = this.value.toLowerCase().trim();

        // Clear search — show bookshelf
        if (q.length < 2) {
            searchResults.style.display = "none";
            shelfGrid.style.display = "grid";
            return;
        }

        // Filter surahs
        const results = surahsData.filter(
            (s) =>
                s.name_transliteration.toLowerCase().includes(q) ||
                s.name_english.toLowerCase().includes(q) ||
                String(s.number).includes(q),
        );

        // Render results
        searchList.innerHTML =
            results.length > 0
                ? results.map((s) => buildSurahRow(s)).join("")
                : buildEmptyState();

        searchResults.style.display = "block";
        shelfGrid.style.display = "none";
    });

    // Clear search on Escape
    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            this.value = "";
            searchResults.style.display = "none";
            shelfGrid.style.display = "grid";
        }
    });
}

function buildSurahRow(surah) {
    const completedIcon = surah.is_completed
        ? `<span class="surah-row-completed"><i class="bi bi-check-circle-fill"></i></span>`
        : "";

    return `
        <a href="${surah.url}" class="surah-row">
            <div class="surah-row-num">${surah.number}</div>
            <div class="flex-grow-1">
                <div class="surah-row-name">${surah.name_transliteration}</div>
                <div class="surah-row-meta">
                    ${surah.name_english} ·
                    ${surah.ayah_count} ayahs ·
                    ${capitalize(surah.revelation_type)}
                </div>
            </div>
            <div class="surah-row-arabic">${surah.name_arabic}</div>
            ${completedIcon}
        </a>
    `;
}

function buildEmptyState() {
    return `
        <p style="color:rgba(255,255,255,0.4);
                  text-align:center;
                  padding:1.5rem;
                  font-style:italic">
            No surahs found. Try another name.
        </p>
    `;
}

// ══════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

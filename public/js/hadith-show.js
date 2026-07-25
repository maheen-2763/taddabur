// ════════════ LAZY LOAD ════════════
let hadithCurrentPage = 1;
let hadithIsLoading = false;
let hadithHasMore = true;

const hadithObserver = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !hadithIsLoading && hadithHasMore) {
        loadMoreHadithsAsync();
    }
});

hadithObserver.observe(document.querySelector("#hadith-sentinel"));
// Page load ke baad, agar target hai to auto-jump karo
document.addEventListener("DOMContentLoaded", () => {
    if (
        window.HADITH_CONFIG.targetPage &&
        window.HADITH_CONFIG.targetPage > 1
    ) {
        jumpToHadith(
            window.HADITH_CONFIG.targetPage,
            window.HADITH_CONFIG.targetHadithId,
        );
    } else if (window.HADITH_CONFIG.targetHadithId) {
        // pehle page mein hi hai, seedha highlight karo
        highlightHadith(window.HADITH_CONFIG.targetHadithId);
    }
});

async function jumpToHadith(targetPage, targetHadithId) {
    showPageLoader(); // tumhara tasbih loader reuse ho gaya

    // observer ko temporarily hata do, taaki normal scroll-triggered loading beech mein interfere na kare
    hadithObserver.unobserve(document.querySelector("#hadith-sentinel"));

    while (hadithCurrentPage < targetPage && hadithHasMore) {
        await loadMoreHadithsAsync();
    }

    hidePageLoader();
    hadithObserver.observe(document.querySelector("#hadith-sentinel")); // wapas normal lazy-load resume

    highlightHadith(targetHadithId);
}

// loadMoreHadiths() ko Promise-based banao taaki 'await' kaam kare
function loadMoreHadithsAsync() {
    return new Promise((resolve) => {
        if (hadithIsLoading || !hadithHasMore) {
            resolve();
            return;
        }
        hadithIsLoading = true;
        hadithCurrentPage++;

        fetch(
            `/hadith/${window.HADITH_CONFIG.collectionSlug}/${window.HADITH_CONFIG.chapterNumber}/items?page=${hadithCurrentPage}`,
        )
            .then((res) => res.json())
            .then((data) => {
                appendHadiths(data.hadiths);
                hadithHasMore = data.has_more;
                hadithIsLoading = false;
                resolve();
            })
            .catch(() => {
                hadithIsLoading = false;
                hadithCurrentPage--;
                showFlash(
                    "Could not load more hadiths. Scroll to retry.",
                    "error",
                );
                resolve(); // resolve karo taaki loop stuck na ho
            });
    });
}

function highlightHadith(id) {
    const el = document.getElementById(`hadith-${id}`);
    if (!el) return;

    requestAnimationFrame(() => {
        setTimeout(() => {
            el.scrollIntoView({ behavior: "smooth", block: "center" });
            el.classList.add("highlight-flash");
            setTimeout(() => el.classList.remove("highlight-flash"), 3000);
        }, 100);
    });
}

function appendHadiths(hadiths) {
    const list = document.querySelector("#hadithList");
    hadiths.forEach((h) => {
        if (h.has_note && h.note_data) {
            window.HADITH_USER_NOTES = window.HADITH_USER_NOTES || {};
            window.HADITH_USER_NOTES[h.id] = h.note_data;
        }

        // ✅ Str::slug() jaisa hi behavior — comma/space ko single hyphen mein badalta hai
        const gradeSlug = h.grade
            ? h.grade
                  .toLowerCase()
                  .replace(/[^a-z0-9]+/g, "-")
                  .replace(/(^-|-$)/g, "")
            : "";

        const div = document.createElement("div");
        div.className = "hadith-card";
        div.id = `hadith-${h.id}`;
        div.dataset.hadithId = h.id;
        div.innerHTML = `
            <div class="hadith-card-header">
                <span class="hadith-number-badge">${h.id}</span>
                <span class="hadith-header-badges">
                    ${h.grade ? `<span class="grade-badge grade-${gradeSlug}">${h.grade}</span>` : ""}
                    ${h.needs_review ? `<span class="review-flag" title="This grade is pending scholarly verification">⚠ Under Review</span>` : ""}
                </span>
            </div>

            <div class="hadith-arabic-zone">
                <p class="hadith-arabic" dir="rtl">${h.arabic}</p>
            </div>

            <div class="hadith-divider"></div>

            <p class="hadith-english">${h.english}</p>

            <div class="hadith-reference-line">
                ${window.HADITH_CONFIG.collectionName} › ${window.HADITH_CONFIG.chapterTitle} › Hadith# ${h.number}
            </div>

            <div class="hadith-actions mt-2">
                <button class="ayah-btn ${h.is_bookmarked ? "bookmarked" : ""}" id="hadith-bookmark-${h.id}" onclick="toggleHadithBookmark(this, ${h.id})">
                    <i class="bi bi-bookmark${h.is_bookmarked ? "-fill" : ""}"></i> ${h.is_bookmarked ? "Bookmarked" : "Bookmark"}
                </button>
                <button class="ayah-btn" onclick="copyHadithText(this, ${h.number})">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
                <button class="ayah-btn" onclick="shareHadith('${window.HADITH_CONFIG.collectionSlug}', ${h.number}, this)">
                    <i class="bi bi-share"></i> Share
                </button>
                <button class="ayah-btn ${h.has_note ? "has-note" : ""}" id="hadith-note-btn-${h.id}" onclick="toggleHadithNoteEditor(this, ${h.id})">
                    <i class="bi bi-pencil-square"></i> ${h.has_note ? "Note" : "Add Note"}
                </button>
                <button class="ayah-btn js-mark-read ${h.is_read ? "bookmarked" : ""}" id="hadith-read-btn-${h.id}" onclick="toggleHadithRead(this, ${h.id})">
                    <i class="bi bi-check-circle${h.is_read ? "-fill" : ""}"></i> ${h.is_read ? "Read" : "Mark as Read"}
                </button>
            </div>

            <div class="note-banner" id="hadith-note-${h.id}">
                <div class="note-inner">
                    <div class="note-head">
                        <strong>Your Note</strong>
                        <button class="note-close" onclick="closeHadithNoteEditor(${h.id})">&times;</button>
                    </div>
                    <input type="text" class="note-title-input" id="hadith-note-title-${h.id}" placeholder="Optional title...">
                    <textarea class="note-content-input" id="hadith-note-content-${h.id}" rows="3" placeholder="Write your reflection..."></textarea>
                    <div class="note-actions">
                        <button class="note-delete-btn" id="hadith-note-delete-${h.id}" onclick="deleteHadithNote(${h.id})" style="${h.has_note ? "" : "display:none"}">Delete</button>
                        <button class="note-save-btn" onclick="saveHadithNote(${h.id})">Save Note</button>
                    </div>
                </div>
            </div>
        `;
        list.appendChild(div);
    });
}

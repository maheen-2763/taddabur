// ════════════ LAZY LOAD ════════════
let hadithCurrentPage = 1;
let hadithIsLoading = false;
let hadithHasMore = true;

const hadithObserver = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !hadithIsLoading && hadithHasMore) {
        loadMoreHadiths();
    }
});
hadithObserver.observe(document.querySelector("#hadith-sentinel"));

function loadMoreHadiths() {
    if (hadithIsLoading || !hadithHasMore) return;
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
        })
        .catch(() => {
            hadithIsLoading = false;
            hadithCurrentPage--;
            showFlash("Could not load more hadiths. Scroll to retry.", "error");
        });
}

function appendHadiths(hadiths) {
    const list = document.querySelector("#hadithList");
    hadiths.forEach((h) => {
        const div = document.createElement("div");
        div.className = "hadith-card";
        div.id = `hadith-${h.number}`;
        div.dataset.hadithId = h.id;
        div.innerHTML = `
            <div class="hadith-number-badge">${h.number}</div>
            <p class="hadith-arabic" dir="rtl">${h.arabic}</p>
            <p class="hadith-english">${h.english}</p>
            ${h.grade ? `<span class="grade-badge grade-${h.grade.toLowerCase()}">${h.grade}</span>` : ""}
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
                <button class="ayah-btn" id="hadith-note-btn-${h.id}" onclick="toggleHadithNoteEditor(this, ${h.id})">
                    <i class="bi bi-pencil-square"></i> Add Note
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
                        <button class="note-delete-btn" id="hadith-note-delete-${h.id}" onclick="deleteHadithNote(${h.id})" style="display:none">Delete</button>
                        <button class="note-save-btn" onclick="saveHadithNote(${h.id})">Save Note</button>
                    </div>
                </div>
            </div>
        `;
        list.appendChild(div);
    });
}

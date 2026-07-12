// ════════════ SHARED HELPERS ════════════

// Ek hi jagah se CSRF token nikalo
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

// POST/PUT/DELETE requests ke liye common wrapper
function apiRequest(url, method, body = null) {
    return fetch(url, {
        method,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": getCsrfToken(),
        },
        body: body ? JSON.stringify(body) : null,
    }).then((r) => {
        if (r.status === 403) {
            throw new Error("forbidden");
        }
        return r.json();
    });
}

// Agar app.js abhi load nahi hua (showFlash missing), crash na ho
if (typeof showFlash !== "function") {
    window.showFlash = function (message, type = "info") {
        console.warn(`[showFlash missing from app.js] ${type}: ${message}`);
    };
}

// ════════════ BOOKMARK ════════════
function toggleHadithBookmark(btn, hadithId) {
    apiRequest("/bookmarks", "POST", { type: "hadith", id: hadithId })
        .then((data) => {
            if (data.status === "added") {
                btn.classList.add("bookmarked");
                btn.innerHTML =
                    '<i class="bi bi-bookmark-fill"></i> Bookmarked';
            } else {
                btn.classList.remove("bookmarked");
                btn.innerHTML = '<i class="bi bi-bookmark"></i> Bookmark';
            }
        })
        .catch(() => showFlash("Could not update bookmark.", "error"));
}

// ════════════ COPY ════════════
function copyHadithText(btn, number) {
    const card = btn.closest(".hadith-card");
    const arabic = card.querySelector(".hadith-arabic").textContent.trim();
    const english = card.querySelector(".hadith-english").textContent.trim();
    const text = `${arabic}\n\n${english}\n\n— Hadith ${number}`;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => flashCopied(btn));
    } else {
        fallbackCopyToClipboard(text);
        flashCopied(btn);
    }
}

// ════════════ SHARE ════════════
function shareHadith(collectionSlug, number, btn) {
    const url = `${location.origin}/hadith/${collectionSlug}#hadith-${number}`;

    if (navigator.share) {
        navigator.share({ title: `Hadith #${number}`, url }).catch(() => {});
        return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
            .writeText(url)
            .then(() => flashCopied(btn, "Link Copied!"));
    } else {
        fallbackCopyToClipboard(url);
        flashCopied(btn, "Link Copied!");
    }
}

// ════════════ NOTES ════════════
function toggleHadithNoteEditor(btn, hadithId) {
    const banner = document.getElementById("hadith-note-" + hadithId);
    const isOpen = banner.classList.contains("open");

    document.querySelectorAll(".note-banner.open").forEach((b) => {
        if (b !== banner) b.classList.remove("open");
    });

    if (isOpen) {
        banner.classList.remove("open");
        return;
    }

    const existing = (window.HADITH_USER_NOTES || {})[hadithId];
    document.getElementById("hadith-note-title-" + hadithId).value =
        existing?.title || "";
    document.getElementById("hadith-note-content-" + hadithId).value =
        existing?.content || "";
    document.getElementById("hadith-note-delete-" + hadithId).style.display =
        existing ? "inline" : "none";

    banner.classList.add("open");
}

function closeHadithNoteEditor(hadithId) {
    document
        .getElementById("hadith-note-" + hadithId)
        ?.classList.remove("open");
}

function saveHadithNote(hadithId) {
    const content = document
        .getElementById("hadith-note-content-" + hadithId)
        .value.trim();

    if (!content) {
        showFlash("Please write something before saving.", "warning");
        return;
    }

    const existing = (window.HADITH_USER_NOTES || {})[hadithId];
    const isUpdate = !!existing?.id;
    const url = isUpdate ? `/notes/${existing.id}` : "/notes";
    const method = isUpdate ? "PUT" : "POST";

    apiRequest(url, method, {
        hadith_id: hadithId,
        title: document
            .getElementById("hadith-note-title-" + hadithId)
            .value.trim(),
        content,
        is_private: true,
    })
        .then((data) => {
            if (!data.note) {
                showFlash("Could not save your note.", "error");
                return;
            }
            window.HADITH_USER_NOTES = window.HADITH_USER_NOTES || {};
            window.HADITH_USER_NOTES[hadithId] = data.note;

            const btn = document.getElementById("hadith-note-btn-" + hadithId);
            btn.classList.add("has-note");
            btn.innerHTML = '<i class="bi bi-pencil-square"></i> Note';

            closeHadithNoteEditor(hadithId);
            showFlash("✓ Note saved", "success");
        })
        .catch((err) => {
            if (err.message === "forbidden") {
                showFlash(
                    "Notes are a Premium feature. Upgrade to unlock!",
                    "warning",
                );
            } else {
                showFlash("Could not save your note.", "error");
            }
        });
}

function deleteHadithNote(hadithId) {
    const existing = (window.HADITH_USER_NOTES || {})[hadithId];
    if (!existing?.id) return;
    if (!confirm("Delete this note? This cannot be undone.")) return;

    apiRequest(`/notes/${existing.id}`, "DELETE")
        .then((data) => {
            if (data.status !== "deleted") {
                showFlash("Could not delete note.", "error");
                return;
            }
            delete window.HADITH_USER_NOTES[hadithId];
            const btn = document.getElementById("hadith-note-btn-" + hadithId);
            btn.classList.remove("has-note");
            btn.innerHTML = '<i class="bi bi-pencil-square"></i> Add Note';
            closeHadithNoteEditor(hadithId);
            showFlash("Note deleted", "info");
        })
        .catch((err) => {
            if (err.message === "forbidden") {
                showFlash(
                    "You don't have permission to delete this note.",
                    "warning",
                );
            } else {
                showFlash("Could not delete note.", "error");
            }
        });
}

// ════════════ CLIPBOARD FALLBACK ════════════
function fallbackCopyToClipboard(text) {
    const ta = document.createElement("textarea");
    ta.value = text;
    ta.style.position = "fixed";
    document.body.appendChild(ta);
    ta.select();
    document.execCommand("copy");
    document.body.removeChild(ta);
}

function flashCopied(btn, label = "Copied!") {
    const orig = btn.innerHTML;
    btn.innerHTML = `<i class="bi bi-check"></i> ${label}`;
    setTimeout(() => (btn.innerHTML = orig), 1500);
}

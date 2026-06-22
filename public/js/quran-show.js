// public/js/quran-show.js

// ════════════════════════════════════════════
// STATE — All variables in one place
// ════════════════════════════════════════════
let currentAyahId = null;
let currentWordCount = 0;
let wordHighlightTimer = null;
let currentAudioBtn = null;
let manualJumpActive = false;
let highestMarkedAyahNumber = window.QURAN_CONFIG?.lastAyahNumber || 0;

// These are set by the blade via window object
// window.QURAN_CONFIG = { surahNumber, totalAyahs, ... }
const cfg = window.QURAN_CONFIG || {};

// ════════════════════════════════════════════
// SCROLL TO TOP
// ════════════════════════════════════════════
window.addEventListener("scroll", () => {
    const btn = document.getElementById("scrollTopBtn");
    if (!btn) return;

    if (window.scrollY > 400) {
        btn.classList.add("visible");
    } else {
        btn.classList.remove("visible");
    }

    updateSidebarActive();
});

// ════════════════════════════════════════════
// SIDEBAR — Highlight current ayah
// ═══════════════════════════════════════════
//
// ✅ FIX: getBoundingClientRect().bottom gives the
//         REAL position of toolbar's bottom edge on
//         screen right now — not a hardcoded guess.
//         offsetHeight was wrong because it doesn't
//         account for sticky positioning + scroll.
//
function updateSidebarActive() {
    const cards = document.querySelectorAll(".ayah-card");
    if (manualJumpActive) return;

    const toolbar =
        document.getElementById("readerToolbar") ||
        document.querySelector(".reader-toolbar");

    const triggerLine = toolbar
        ? toolbar.getBoundingClientRect().bottom + 10
        : 90;

    let activeNum = null;

    cards.forEach((card) => {
        const rect = card.getBoundingClientRect();
        if (rect.top <= triggerLine && rect.bottom > triggerLine) {
            activeNum = card.dataset.ayahNumber;
        }
    });

    if (!activeNum) return;

    document
        .querySelectorAll(".sidebar-item")
        .forEach((i) => i.classList.remove("active"));

    const active = document.getElementById("sidebar-" + activeNum);
    if (active) {
        active.classList.add("active");
        active.scrollIntoView({ block: "nearest", behavior: "smooth" });
    }
}

// ════════════════════════════════════════════
// JUMP / SCROLL
// ════════════════════════════════════════════
function scrollToAyah(num) {
    const el = document.getElementById("ayah-" + num);
    if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
}

function jumpToAyah(num) {
    const n = parseInt(num);
    const total = window.QURAN_CONFIG?.totalAyahs || 0;
    if (n >= 1 && n <= total) scrollToAyah(n);
}

// ════════════════════════════════════════════
// FLASH MESSAGE
// ════════════════════════════════════════════
function showFlash(message, type = "info") {
    // Remove existing flash
    document.querySelectorAll(".quran-flash").forEach((f) => f.remove());

    const colors = {
        info: "var(--emerald-dark)",
        warning: "var(--gold-dark)",
        success: "var(--emerald)",
        error: "#8B0000",
    };

    const flash = document.createElement("div");
    flash.className = "quran-flash";
    flash.style.cssText = `
        position:      fixed;
        top:           75px;
        left:          50%;
        transform:     translateX(-50%);
        background:    ${colors[type] || colors.info};
        color:         white;
        padding:       0.6rem 1.4rem;
        border-radius: 50px;
        font-size:     0.82rem;
        z-index:       9999;
        box-shadow:    0 4px 16px rgba(0,0,0,0.25);
        border:        1px solid rgba(255,255,255,0.12);
        max-width:     90vw;
        text-align:    center;
        animation:     slideDown 0.3s ease;
        white-space:   nowrap;
    `;
    flash.innerHTML = message;
    document.body.appendChild(flash);

    setTimeout(() => {
        flash.style.opacity = "0";
        flash.style.transition = "opacity 0.3s";
        setTimeout(() => flash.remove(), 300);
    }, 3000);
}

// ════════════════════════════════════════════
// PLAN ACCESS
// ════════════════════════════════════════════
function redirectToUpgrade(featureName) {
    const upgradeUrl =
        window.QURAN_CONFIG?.upgradeUrl || "/subscription/upgrade";

    showFlash(
        `🔒 ${featureName} requires an upgrade. <a href="${upgradeUrl}"
         style="color:var(--gold-light); margin-left:0.4rem">Upgrade →</a>`,
        "info",
    );
}

function handleTranslationChange(select) {
    const isPremium = window.QURAN_CONFIG?.isPremium || false;
    const option = select.options[select.selectedIndex];
    const isFree = option?.dataset.free === "1";
    const freeSlug =
        window.QURAN_CONFIG?.freeTranslationSlug || "sahih-international";

    if (!isFree && !isPremium) {
        select.value = freeSlug;
        redirectToUpgrade("This translation");
        return;
    }

    changeTranslation(select.value);
}

function handleReciterChange(select) {
    const isPremium = window.QURAN_CONFIG?.isPremium || false;
    const option = select.options[select.selectedIndex];
    const isFree = option?.dataset.free === "1";

    if (!isFree && !isPremium) {
        select.value = "mishary-rashid";
        showFlash(
            "🎙 Premium reciters require an upgrade. Using Mishary Rashid.",
            "info",
        );
    }
}

function changeActiveTafsir(slug) {
    const isPremium = window.QURAN_CONFIG?.isPremium || false;

    if (slug && !isPremium) {
        document.getElementById("tafsirPicker").value = "";
        redirectToUpgrade("Tafsir");
        return;
    }

    // Close all open tafsir banners — they will reload with new selection
    document
        .querySelectorAll(".tafsir-banner.open")
        .forEach((b) => b.classList.remove("open"));
    document
        .querySelectorAll('[id^="tafsir-text-"]')
        .forEach((el) => delete el.dataset.loaded);

    window.selectedTafsir = slug;
}

// ════════════════════════════════════════════
// TRANSLATION
// ════════════════════════════════════════════
function changeTranslation(slug) {
    const url = new URL(location.href);
    url.searchParams.set("translation", slug);
    location.href = url.toString();
}

// ════════════════════════════════════════════
// AUDIO
// ════════════════════════════════════════════
function getSelectedReciter() {
    const picker = document.getElementById("reciterPicker");
    const isPremium = window.QURAN_CONFIG?.isPremium || false;

    if (!picker || !picker.value) return "mishary-rashid";

    const option = picker.options[picker.selectedIndex];
    const isFree = option?.dataset.free === "1";

    // Free user trying premium reciter → fallback silently
    if (!isPremium && !isFree) return "mishary-rashid";

    return picker.value;
}

function playAudio(surah, ayahNumber, ayahId, btn) {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const audioEl = document.getElementById("audioElement");
    const reciter = getSelectedReciter();

    // Toggle pause if same ayah
    if (currentAyahId === ayahId && !audioEl.paused) {
        audioEl.pause();
        if (btn) {
            btn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
            btn.classList.remove("active");
        }
        return;
    }

    // Save reading progress

    // Reset previous state
    clearWordHighlights();

    if (currentAudioBtn) {
        currentAudioBtn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
        currentAudioBtn.classList.remove("active");
    }

    document
        .querySelectorAll(".ayah-card.playing-now")
        .forEach((c) => c.classList.remove("playing-now"));

    // Set new state
    const card = document.querySelector(`[data-ayah-id="${ayahId}"]`);
    if (card) card.classList.add("playing-now");

    currentAudioBtn = btn;
    if (btn) {
        btn.innerHTML = '<i class="bi bi-pause-circle"></i> Playing...';
        btn.classList.add("active");
    }

    // Fetch audio URL
    fetch(`/quran/${surah}/${ayahId}/audio?reciter=${reciter}`, {
        headers: { Accept: "application/json", "X-CSRF-TOKEN": CSRF },
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.error) {
                showFlash("Audio not available for this ayah.", "error");
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
                    btn.classList.remove("active");
                }
                return;
            }

            currentAyahId = ayahId;

            // ✅ Wrap words JUST before playing — lazy loading
            currentWordCount = wrapWordsForHighlight(ayahId);

            document.getElementById("audioLabel").textContent =
                `${data.reciter} — ${data.surah_name} ${surah}:${ayahNumber}`;

            document.getElementById("audioPlayer").style.display = "block";
            audioEl.src = data.audio_url;
            audioEl.play();

            card?.scrollIntoView({ behavior: "smooth", block: "center" });
        })
        .catch(() => {
            showFlash("Could not load audio. Please try again.", "error");
            if (btn) {
                btn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
                btn.classList.remove("active");
            }
        });
}

function stopAudio() {
    document.getElementById("audioElement").pause();
    document.getElementById("audioPlayer").style.display = "none";
    clearWordHighlights();

    document
        .querySelectorAll(".ayah-card.playing-now")
        .forEach((c) => c.classList.remove("playing-now"));

    if (currentAudioBtn) {
        currentAudioBtn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
        currentAudioBtn.classList.remove("active");
    }

    currentAyahId = null;
    currentAudioBtn = null;
}

function onAudioLoaded() {
    startWordHighlight();
}

function onAudioTimeUpdate() {
    const audioEl = document.getElementById("audioElement");
    const fill = document.getElementById("audioBarFill");
    const timeEl = document.getElementById("audioTime");

    if (!audioEl.duration) return;

    const pct = (audioEl.currentTime / audioEl.duration) * 100;
    if (fill) fill.style.width = pct + "%";
    if (timeEl)
        timeEl.textContent = `${formatTime(audioEl.currentTime)} / ${formatTime(audioEl.duration)}`;
}

// ════════════════════════════════════════════
// AUDIO ENDED — Server is the source of truth
// ════════════════════════════════════════════
function onAudioEnded() {
    clearWordHighlights();

    if (currentAudioBtn) {
        currentAudioBtn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
        currentAudioBtn.classList.remove("active");
    }

    document
        .querySelectorAll(".ayah-card.playing-now")
        .forEach((c) => c.classList.remove("playing-now"));

    // ✅ Tell the SERVER this ayah was listened to.
    //    Server remembers forever — not just this session.
    if (currentAyahId && window.QURAN_CONFIG?.isLoggedIn) {
        markAyahListened(currentAyahId);
    }
}

function markAyahListened(ayahId) {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    fetch("/quran/audio-completed", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
        body: JSON.stringify({ ayah_id: ayahId }),
    })
        .then((r) => r.json())
        .then((data) => {
            // ✅ Only show modal the FIRST time it becomes complete
            //    Re-listening later will NOT show it again
            if (data.newly_completed) {
                const modalEl = document.getElementById("completionModal");
                if (modalEl) {
                    new bootstrap.Modal(modalEl, {
                        backdrop: "static",
                        keyboard: false,
                    }).show();
                }
                updateCompletedBadge();
            }
        })
        .catch((err) => console.error("Listened tracking error:", err));
}

function seekAudio(event) {
    const audioEl = document.getElementById("audioElement");
    const rect = event.currentTarget.getBoundingClientRect();
    audioEl.currentTime =
        ((event.clientX - rect.left) / rect.width) * audioEl.duration;
}

// ════════════════════════════════════════════
// WORD HIGHLIGHT
// ════════════════════════════════════════════
function startWordHighlight() {
    if (!currentAyahId || currentWordCount === 0) return;

    const audioEl = document.getElementById("audioElement");
    clearInterval(wordHighlightTimer);

    wordHighlightTimer = setInterval(() => {
        if (audioEl.paused || audioEl.ended) {
            clearInterval(wordHighlightTimer);
            return;
        }

        const progress = audioEl.currentTime / (audioEl.duration || 1);
        const wordIndex = Math.min(
            Math.floor(progress * currentWordCount),
            currentWordCount - 1,
        );

        document
            .querySelectorAll(`#arabic-${currentAyahId} .arabic-word`)
            .forEach((w) => w.classList.remove("highlighted"));

        const words = document.querySelectorAll(
            `#arabic-${currentAyahId} .arabic-word`,
        );
        if (words[wordIndex]) words[wordIndex].classList.add("highlighted");
    }, 50);
}

function clearWordHighlights() {
    clearInterval(wordHighlightTimer);
    document
        .querySelectorAll(".arabic-word.highlighted")
        .forEach((w) => w.classList.remove("highlighted"));
}

// ════════════════════════════════════════════
// WRAP WORDS FOR HIGHLIGHT
// ════════════════════════════════════════════
//
// WHY: Blade renders plain text for performance
//      (2,860 spans upfront would slow page load
//      for a 286-ayah surah like Al-Baqarah).
//
//      We wrap words into <span> elements ONLY
//      when audio actually starts playing for
//      that specific ayah — lazy loading.
//
function wrapWordsForHighlight(ayahId) {
    const container = document.getElementById("arabic-" + ayahId);
    if (!container) return 0;

    // Already wrapped before — don't redo it
    if (container.dataset.wrapped === "1") {
        return container.querySelectorAll(".arabic-word").length;
    }

    // Save the ornament/sajda elements so we can
    // put them back after rebuilding the words
    const ornament = container.querySelector(".ayah-end-ornament");
    const sajda = container.querySelector('[title="Sajda"]');

    // Get ONLY the Arabic text (ignore ornament/sajda nodes)
    let arabicText = "";
    container.childNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            arabicText += node.textContent;
        }
    });

    arabicText = arabicText.trim();
    if (!arabicText) return 0;

    // Split text into words and wrap each one
    const words = arabicText.split(/\s+/).filter((w) => w.trim());

    const wrappedHtml = words
        .map(
            (word, i) =>
                `<span class="arabic-word" data-word-index="${i}">${word}</span>`,
        )
        .join(" ");

    // Rebuild the container with wrapped words
    container.innerHTML = wrappedHtml + " ";

    // Put ornament and sajda back at the end
    if (ornament) container.appendChild(ornament);
    if (sajda) container.appendChild(sajda);

    // Mark as wrapped so we never redo this
    container.dataset.wrapped = "1";

    return words.length;
}

// ✅ Fixed — waits for modal animation before navigating
function closeModalAndGo(url) {
    const modalEl = document.getElementById("completionModal");
    const modal = bootstrap.Modal.getInstance(modalEl);

    if (modal) {
        modal.hide();
        modalEl.addEventListener(
            "hidden.bs.modal",
            () => {
                window.location.href = url;
            },
            { once: true },
        );
    } else {
        window.location.href = url;
    }
}

function updateCompletedBadge() {
    const container = document.getElementById("surahBadges");
    if (!container || container.querySelector(".badge-completed")) return;

    const badge = document.createElement("span");
    badge.className = "badge badge-completed";
    badge.style.cssText =
        "background:var(--emerald); color:white; padding:0.35rem 0.7rem";
    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Completed';
    container.appendChild(badge);
}

// ════════════════════════════════════════════
// BOOKMARK
// ════════════════════════════════════════════
function toggleBookmark(btn, ayahId) {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    fetch("/bookmarks", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
        body: JSON.stringify({ type: "ayah", id: ayahId }),
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.status === "added") {
                btn.classList.add("bookmarked");
                btn.innerHTML =
                    '<i class="bi bi-bookmark-fill"></i> Bookmarked';
            } else {
                btn.classList.remove("bookmarked");
                btn.innerHTML = '<i class="bi bi-bookmark"></i> Bookmark';
            }
        });
}

// ════════════════════════════════════════════
// COPY
// ════════════════════════════════════════════
function copyText(text, btn) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard
            .writeText(text)
            .then(() => showFeedback(btn, "Copied!"))
            .catch(() => fallbackCopy(text, btn));
    } else {
        fallbackCopy(text, btn);
    }
}

function fallbackCopy(text, btn) {
    const ta = document.createElement("textarea");
    ta.value = text;
    ta.style.cssText = "position:fixed;opacity:0;top:0;left:0";
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand("copy");
        showFeedback(btn, "Copied!");
    } catch (e) {
        showFlash("Could not copy text.", "error");
    }
    document.body.removeChild(ta);
}

// ════════════════════════════════════════════
// SHARE
// ════════════════════════════════════════════
function shareAyah(surah, ayah, btn) {
    const url = `${location.origin}/quran/${surah}#ayah-${ayah}`;
    const text = `Quran ${surah}:${ayah} — Read on Taddabur`;

    if (navigator.share) {
        navigator.share({ title: text, url }).catch(() => {});
        return;
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard
            .writeText(url)
            .then(() => showFeedback(btn, "Link Copied!"));
    } else {
        fallbackCopy(url, btn);
    }
}

// ════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════
function showFeedback(btn, msg) {
    const orig = btn.innerHTML;
    btn.innerHTML = `<i class="bi bi-check"></i> ${msg}`;
    btn.style.borderColor = "var(--emerald-light)";
    btn.style.color = "var(--emerald-light)";
    setTimeout(() => {
        btn.innerHTML = orig;
        btn.style = "";
    }, 1500);
}

function hideBanner() {
    setTimeout(() => {
        const b = document.getElementById("lastReadBanner");
        if (b) {
            b.style.transition = "opacity 0.4s";
            b.style.opacity = "0";
            setTimeout(() => b.remove(), 400);
        }
    }, 500);
}

function formatTime(s) {
    return `${Math.floor(s / 60)}:${Math.floor(s % 60)
        .toString()
        .padStart(2, "0")}`;
}

// ════════════════════════════════════════════
// DOM READY
// ════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    const cfg = window.QURAN_CONFIG || {};
    const hash = window.location.hash;

    // ✅ PRIORITY 1 — User arrived via a direct ayah link
    //    (search result, shared link, bookmark)
    //    This ALWAYS wins — never overridden by resume logic
    if (hash && hash.startsWith("#ayah-")) {
        const num = parseInt(hash.replace("#ayah-", ""));
        if (num) {
            setTimeout(() => {
                scrollToAyah(num);
                flashHighlightAyah(num);
            }, 400);
        }
    }
});

// ════════════════════════════════════════════
// MARK AS READ — explicit, one-way confirmation
// Once marked, button disables — no accidental
// re-triggering, no ambiguity about user intent
// ════════════════════════════════════════════
function markAsRead(btn, ayahId) {
    if (btn.classList.contains("marked-read")) return;

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    fetch("/quran/progress", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
        body: JSON.stringify({ ayah_id: ayahId }),
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.status !== "saved") return;

            btn.classList.add("marked-read");
            btn.disabled = true;
            btn.innerHTML =
                '<i class="bi bi-check-circle-fill"></i><span class="d-none d-sm-inline"> Read</span>';

            const card = btn.closest(".ayah-card");
            if (card) card.classList.add("marked-read");

            updateResumeBannerLive(
                data.ayah_number,
                data.read_count,
                data.total_ayahs,
            );
            flashHighlightAyah(data.ayah_number);
        })
        .catch(() => showFlash("Could not save your progress.", "error"));
}

function updateResumeBannerLive(ayahNumber, readCount, totalAyahs) {
    // Only advance forward — never let the banner
    // regress to an earlier ayah, even if marked later
    if (ayahNumber > highestMarkedAyahNumber) {
        highestMarkedAyahNumber = ayahNumber;

        const continueBtn = document.getElementById("continueBtn");
        if (continueBtn) {
            continueBtn.textContent = `Continue from Ayah ${highestMarkedAyahNumber}`;
            continueBtn.setAttribute(
                "onclick",
                `hideBanner(); scrollToAyah(${highestMarkedAyahNumber}); flashHighlightAyah(${highestMarkedAyahNumber})`,
            );
        }
    }

    const countText = document.getElementById("readCountText");
    if (countText) {
        countText.innerHTML =
            `<i class="bi bi-bookmark-fill me-2" style="color:var(--gold)"></i>` +
            `${readCount} of ${totalAyahs} ayahs read in this Surah`;
    }
}

// ════════════════════════════════════════════
// MOBILE SIDEBAR DRAWER
// ════════════════════════════════════════════
function toggleMobileSidebar() {
    const sidebar = document.getElementById("quranSidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const isOpen = sidebar.classList.contains("mobile-open");

    if (isOpen) {
        closeMobileSidebar();
    } else {
        sidebar.classList.add("mobile-open");
        overlay.classList.add("visible");
        document.body.style.overflow = "hidden";
    }
}

function closeMobileSidebar() {
    const sidebar = document.getElementById("quranSidebar");
    const overlay = document.getElementById("sidebarOverlay");

    sidebar.classList.remove("mobile-open");
    overlay.classList.remove("visible");
    document.body.style.overflow = "";
}

// Close mobile sidebar when user jumps to an ayah
// Update jumpFromSidebar to also close on mobile
const _originalJumpFromSidebar = jumpFromSidebar;
jumpFromSidebar = function (num) {
    _originalJumpFromSidebar(num);
    if (window.innerWidth < 600) {
        setTimeout(closeMobileSidebar, 400);
    }
};

// ════════════════════════════════════════════
// FLASH HIGHLIGHT — used everywhere an ayah
// needs to visually "announce itself" to the user
// (sidebar click, jump input, search result, resume)
// ════════════════════════════════════════════
function flashHighlightAyah(num) {
    const el = document.getElementById("ayah-" + num);
    if (!el) return;

    el.classList.add("flash-highlight");
    setTimeout(() => el.classList.remove("flash-highlight"), 2000);

    // Also sync the sidebar active state
    document
        .querySelectorAll(".sidebar-item")
        .forEach((i) => i.classList.remove("active"));

    const sidebarItem = document.getElementById("sidebar-" + num);
    if (sidebarItem) sidebarItem.classList.add("active");
}

// Sidebar click — now flashes + syncs active state
function jumpFromSidebar(num) {
    // ✅ Pause scroll-based sidebar updates
    manualJumpActive = true;

    // Remove active from all sidebar items
    document
        .querySelectorAll(".sidebar-item")
        .forEach((i) => i.classList.remove("active"));

    // Set correct sidebar item active IMMEDIATELY
    const sidebarItem = document.getElementById("sidebar-" + num);
    if (sidebarItem) {
        sidebarItem.classList.add("active");
        sidebarItem.scrollIntoView({ block: "nearest", behavior: "smooth" });
    }

    // Scroll reader to correct ayah
    scrollToAyah(num);

    // Flash highlight in reader
    flashHighlightAyah(num);

    // ✅ After scroll finishes — resume auto-highlight
    // 1000ms is enough for smooth scroll to complete
    setTimeout(() => {
        manualJumpActive = false;
    }, 1000);
}

// Jump-to-Ayah input — now flashes too
function jumpToAyah(num) {
    const n = parseInt(num);
    const total = window.QURAN_CONFIG?.totalAyahs || 0;

    if (n >= 1 && n <= total) {
        manualJumpActive = true;

        document
            .querySelectorAll(".sidebar-item")
            .forEach((i) => i.classList.remove("active"));

        const sidebarItem = document.getElementById("sidebar-" + n);
        if (sidebarItem) {
            sidebarItem.classList.add("active");
            sidebarItem.scrollIntoView({
                block: "nearest",
                behavior: "smooth",
            });
        }

        scrollToAyah(n);
        flashHighlightAyah(n);

        setTimeout(() => {
            manualJumpActive = false;
        }, 1000);
    } else {
        showFlash(`Please enter a number between 1 and ${total}`, "warning");
    }
}

// ════════════════════════════════════════════
// NOTES — Personal reflections per ayah
// ════════════════════════════════════════════
function toggleNoteEditor(btn, ayahId) {
    const banner = document.getElementById("note-" + ayahId);
    const isOpen = banner.classList.contains("open");

    document.querySelectorAll(".note-banner.open").forEach((b) => {
        if (b !== banner) b.classList.remove("open");
    });

    if (isOpen) {
        banner.classList.remove("open");
        return;
    }

    const existing = (window.USER_NOTES || {})[ayahId];
    const titleEl = document.getElementById("note-title-" + ayahId);
    const contentEl = document.getElementById("note-content-" + ayahId);
    const deleteBtn = document.getElementById("note-delete-" + ayahId);

    titleEl.value = existing?.title || "";
    contentEl.value = existing?.content || "";
    deleteBtn.style.display = existing ? "inline" : "none";

    banner.classList.add("open");
    contentEl.focus();
}

function closeNoteEditor(ayahId) {
    document.getElementById("note-" + ayahId)?.classList.remove("open");
}

function saveNote(ayahId, surahNumber) {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const titleEl = document.getElementById("note-title-" + ayahId);
    const contentEl = document.getElementById("note-content-" + ayahId);
    const content = contentEl.value.trim();

    if (!content) {
        showFlash("Please write something before saving.", "warning");
        return;
    }

    const existing = (window.USER_NOTES || {})[ayahId];
    const isUpdate = !!existing?.id;
    const url = isUpdate ? `/notes/${existing.id}` : "/notes";
    const method = isUpdate ? "PUT" : "POST";

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
        body: JSON.stringify({
            ayah_id: ayahId,
            title: titleEl.value.trim(),
            content: content,
            is_private: true,
        }),
    })
        .then((r) => r.json())
        .then((data) => {
            if (!data.note) {
                showFlash(
                    "Could not save your note. Please try again.",
                    "error",
                );
                return;
            }
            window.USER_NOTES = window.USER_NOTES || {};
            window.USER_NOTES[ayahId] = data.note;
            updateNoteButtonState(ayahId, true);
            closeNoteEditor(ayahId);
            showFlash("✓ Note saved", "success");
        })
        .catch(() =>
            showFlash(
                "Could not save your note. Check your connection.",
                "error",
            ),
        );
}

function deleteNote(ayahId) {
    const existing = (window.USER_NOTES || {})[ayahId];
    if (!existing?.id) return;
    if (!confirm("Delete this note? This cannot be undone.")) return;

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/notes/${existing.id}`, {
        method: "DELETE",
        headers: { Accept: "application/json", "X-CSRF-TOKEN": CSRF },
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.status !== "deleted") {
                showFlash("Could not delete your note.", "error");
                return;
            }
            delete window.USER_NOTES[ayahId];
            updateNoteButtonState(ayahId, false);
            closeNoteEditor(ayahId);
            showFlash("Note deleted", "info");
        })
        .catch(() =>
            showFlash(
                "Could not delete your note. Check your connection.",
                "error",
            ),
        );
}

function updateNoteButtonState(ayahId, hasNote) {
    const btn = document.getElementById("note-btn-" + ayahId);
    if (!btn) return;
    const label = btn.querySelector("span");
    if (hasNote) {
        btn.classList.add("has-note");
        if (label) label.textContent = " Note";
    } else {
        btn.classList.remove("has-note");
        if (label) label.textContent = " Add Note";
    }
}

// ← JS FILE ENDS HERE. Nothing after this.

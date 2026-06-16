// public/js/quran-show.js

// ════════════════════════════════════════════
// STATE — All variables in one place
// ════════════════════════════════════════════
let currentAyahId = null;
let currentWordCount = 0;
let wordHighlightTimer = null;
let currentAudioBtn = null;
let listenedAyahs = new Set();

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
// ════════════════════════════════════════════
function updateSidebarActive() {
    const cards = document.querySelectorAll(".ayah-card");
    const toolbar = document.querySelector(".reader-toolbar");
    const toolbarHeight = toolbar ? toolbar.offsetHeight + 20 : 100;

    let activeNum = null;

    cards.forEach((card) => {
        const rect = card.getBoundingClientRect();

        if (rect.top <= toolbarHeight && rect.bottom > toolbarHeight) {
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
// TAFSIR
// ════════════════════════════════════════════
function toggleTafsir(btn, surah, ayahId) {
    const banner = document.getElementById("tafsir-" + ayahId);
    const isOpen = banner.classList.contains("open");

    // Close all open ones first
    document
        .querySelectorAll(".tafsir-banner.open")
        .forEach((b) => b.classList.remove("open"));
    document
        .querySelectorAll('.ayah-btn.active[id^="tafsir-btn"]')
        .forEach((b) => {
            b.classList.remove("active");
            b.innerHTML = '<i class="bi bi-book"></i> Tafsir';
        });

    if (isOpen) return; // Was open — just close it

    // Open this one
    banner.classList.add("open");
    btn.classList.add("active");
    btn.innerHTML = '<i class="bi bi-book-fill"></i> Tafsir';

    // Load content if not already loaded
    const textEl = document.getElementById("tafsir-text-" + ayahId);
    if (textEl.dataset.loaded) return;

    const tafsirSlug = window.selectedTafsir || "";
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/quran/${surah}/${ayahId}/tafsir?tafsir=${tafsirSlug}`, {
        headers: { Accept: "application/json", "X-CSRF-TOKEN": CSRF },
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.error) {
                textEl.innerHTML = `<span class="text-danger">${data.message || data.error}</span>`;
                return;
            }
            document.getElementById("tafsir-name-" + ayahId).textContent =
                data.tafsir_name;
            document.getElementById("tafsir-scholar-" + ayahId).textContent =
                data.scholar;
            textEl.innerHTML = `<p class="mb-0" style="color:var(--ink)">${data.text}</p>`;
            textEl.dataset.loaded = "1";
        })
        .catch(() => {
            textEl.innerHTML =
                '<span class="text-danger">Failed to load. Try again.</span>';
        });
}

function closeTafsir(ayahId) {
    document.getElementById("tafsir-" + ayahId)?.classList.remove("open");
    const btn = document.getElementById("tafsir-btn-" + ayahId);
    if (btn) {
        btn.classList.remove("active");
        btn.innerHTML = '<i class="bi bi-book"></i> Tafsir';
    }
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
    if (window.QURAN_CONFIG?.isLoggedIn) {
        fetch("/quran/progress", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": CSRF,
            },
            body: JSON.stringify({ ayah_id: ayahId }),
        });
    }

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
            currentWordCount = document.querySelectorAll(
                `#arabic-${ayahId} .arabic-word`,
            ).length;

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

function onAudioEnded() {
    clearWordHighlights();

    if (currentAudioBtn) {
        currentAudioBtn.innerHTML = '<i class="bi bi-play-circle"></i> Listen';
        currentAudioBtn.classList.remove("active");
    }

    document
        .querySelectorAll(".ayah-card.playing-now")
        .forEach((c) => c.classList.remove("playing-now"));

    // ✅ Track listened ayahs for completion
    if (currentAyahId) {
        listenedAyahs.add(String(currentAyahId));

        console.log("Current Ayah:", currentAyahId);
        console.log("Listened Count:", listenedAyahs.size);
        console.log("Total Ayahs:", window.QURAN_CONFIG.totalAyahs);

        console.log("ENDED");

        const cfg = window.QURAN_CONFIG || {};
        if (
            cfg.isLoggedIn &&
            !cfg.isCompleted &&
            listenedAyahs.size >= cfg.totalAyahs
        ) {
            console.log("🚀 Calling completeSurah()");
            completeSurah();
        }
    }
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
// COMPLETION
// ════════════════════════════════════════════
function completeSurah() {
    if (window.surahCompletionCalled) return;
    window.surahCompletionCalled = true;

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const surahNumber = window.QURAN_CONFIG?.surahNumber;

    fetch(`/quran/${surahNumber}/complete`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.status === "completed") {
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
        .catch((err) => console.error("Completion error:", err));
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

    // Scroll to last read ayah
    if (cfg.lastAyahNumber && !cfg.isCompleted) {
        setTimeout(() => scrollToAyah(cfg.lastAyahNumber), 700);
    }

    // Save progress on ayah click
    if (cfg.isLoggedIn) {
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        document
            .querySelectorAll(".ayah-card[data-ayah-id]")
            .forEach((card) => {
                card.addEventListener("click", function () {
                    const ayahId = this.dataset.ayahId;
                    if (!ayahId) return;

                    fetch("/quran/progress", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": CSRF,
                        },
                        body: JSON.stringify({ ayah_id: ayahId }),
                    });

                    document
                        .querySelectorAll(".ayah-card.last-read")
                        .forEach((c) => c.classList.remove("last-read"));
                    this.classList.add("last-read");
                });
            });
    }
});

/*
    |----------------------------------------------------------
    | BISMILLAH LOGIC
    |----------------------------------------------------------
    | The Bismillah text that appears at start of ayah 1
    | in the database for most surahs
    */
// ════════════════════════════════════════════
// DOM READY
// ════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    const cfg = window.QURAN_CONFIG || {};

    // Scroll to last read ayah
    if (cfg.lastAyahNumber && !cfg.isSurahCompleted) {
        setTimeout(() => scrollToAyah(cfg.lastAyahNumber), 700);
    }

    // Save progress on ayah click
    if (cfg.isLoggedIn) {
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        document
            .querySelectorAll(".ayah-card[data-ayah-id]")
            .forEach((card) => {
                card.addEventListener("click", function () {
                    const ayahId = this.dataset.ayahId;
                    if (!ayahId) return;

                    fetch("/quran/progress", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": CSRF,
                        },
                        body: JSON.stringify({ ayah_id: ayahId }),
                    });

                    document
                        .querySelectorAll(".ayah-card.last-read")
                        .forEach((c) => c.classList.remove("last-read"));
                    this.classList.add("last-read");
                });
            });
    }
});

// ← JS FILE ENDS HERE. Nothing after this.

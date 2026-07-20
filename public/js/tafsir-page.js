// public/js/tafsir-page.js

// ════════════════════════════════════
// CONFIG — Set by blade via window object
// window.TAFSIR_CONFIG = { surahNumber, ayahId, ayahNumber, ... }
// ════════════════════════════════════

// ════════════════════════════════════
// LOAD TAFSIR
// ════════════════════════════════════
function loadTafsir(tafsirSlug) {
    const cfg = window.TAFSIR_CONFIG || {};
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const body = document.getElementById("tafsirBody");
    const name = document.getElementById("tafsirScholarName");
    const period = document.getElementById("tafsirScholarPeriod");
    const arabic = document.getElementById("tafsirScholarArabic");

    if (!body) return;

    // Show loading state
    body.innerHTML = `
        <div class="tafsir-loading">
            <div class="spinner-border spinner-border-sm text-warning"
                 role="status"></div>
            <span>Loading tafsir...</span>
        </div>
    `;

    const url =
        `/quran/${cfg.surahNumber}/${cfg.ayahId}/tafsir` +
        (tafsirSlug ? `?tafsir=${tafsirSlug}` : "");
    console.log(url);

    fetch(url, {
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": CSRF,
        },
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.error) {
                body.innerHTML = `
                <div class="tafsir-unavailable">
                    <span class="tafsir-unavailable-icon">
                        <i class="bi bi-journal-x"></i>
                    </span>
                    <p>${data.message || "Tafsir not available for this ayah."}</p>
                    ${
                        data.upgrade_url
                            ? `
                        <a href="${data.upgrade_url}"
                           class="btn-emerald btn mt-2"
                           style="font-size:0.85rem">
                            Upgrade to Access Tafsir
                        </a>
                    `
                            : ""
                    }
                </div>
            `;
                return;
            }

            // Update scholar info
            if (name) name.textContent = data.tafsir_name || "";
            if (period) period.textContent = data.scholar || "";

            // Format tafsir text into paragraphs

            const noteEl = document.getElementById("tafsirFallbackNote");
            const noteTextEl = document.getElementById(
                "tafsirFallbackNoteText",
            );

            if (noteEl && noteTextEl) {
                if (data.note) {
                    noteTextEl.textContent = data.note;
                    noteEl.classList.remove("d-none");
                } else {
                    noteEl.classList.add("d-none");
                }
            }

            const formatted = formatTafsirText(data.text);
            body.innerHTML = `<div class="tafsir-body">${formatted}</div>`;
        })
        .catch(() => {
            body.innerHTML = `
            <div class="tafsir-unavailable">
                <span class="tafsir-unavailable-icon">
                    <i class="bi bi-wifi-off"></i>
                </span>
                <p>Failed to load tafsir. Please check your connection.</p>
                <button onclick="loadTafsir('${tafsirSlug}')"
                        class="btn-emerald btn mt-2"
                        style="font-size:0.85rem">
                    Try Again
                </button>
            </div>
        `;
        });
}

// ════════════════════════════════════
// FORMAT TAFSIR TEXT
// Splits long text into readable paragraphs
// ════════════════════════════════════
function formatTafsirText(text) {
    if (!text) return "<p>No tafsir text available.</p>";

    const paragraphs = text
        .split(/\n\n+/)
        .filter((p) => p.trim().length > 0)
        .map((p) => `<p>${p.trim()}</p>`);

    if (paragraphs.length <= 1) {
        const sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
        const chunks = [];

        for (let i = 0; i < sentences.length; i += 3) {
            const chunk = sentences
                .slice(i, i + 3)
                .join(" ")
                .trim();
            if (chunk) chunks.push(`<p>${chunk}</p>`);
        }

        return applyArabicStyling(chunks.join("") || `<p>${text}</p>`);
    }

    return applyArabicStyling(paragraphs.join(""));
}

// ════════════════════════════════════
// NAYA FUNCTION — Arabic paragraphs ko style class deta hai
// ════════════════════════════════════
function applyArabicStyling(html) {
    return html.replace(/<p>([\s\S]*?)<\/p>/g, function (match, content) {
        const arabicChars = (
            content.match(/[\u0600-\u06FF\u0750-\u077F]/g) || []
        ).length;
        const totalChars = content
            .replace(/<[^>]*>/g, "")
            .replace(/\s+/g, "").length;
        const ratio = totalChars > 0 ? arabicChars / totalChars : 0;
        const cls = ratio > 0.5 ? "tafsir-arabic-quote" : "tafsir-body-text";
        return `<p class="${cls}">${content}</p>`;
    });
}

// ════════════════════════════════════
// TAFSIR SELECTOR CHANGE
// ════════════════════════════════════
function onTafsirChange(select) {
    const slug = select.value;
    if (!slug) return;

    // Preference save karo — future mein har jagah yehi default use hoga
    localStorage.setItem("taddabur_preferred_tafsir", slug);

    const url = new URL(window.location.href);
    url.searchParams.set("tafsir", slug);
    window.history.pushState({}, "", url.toString());

    loadTafsir(slug);
}

// ════════════════════════════════════
// DOM READY
// ════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    const cfg = window.TAFSIR_CONFIG || {};
    const select = document.getElementById("tafsirSelector");
    const savedTafsir = localStorage.getItem("taddabur_preferred_tafsir");

    // Priority: URL param (agar explicitly share kiya gaya link hai) > saved preference > server default
    const urlParams = new URLSearchParams(window.location.search);
    const urlTafsir = urlParams.get("tafsir");

    const initialTafsir = urlTafsir || savedTafsir || cfg.defaultTafsir || "";

    // Dropdown ko bhi sync kar do taaki wahi selected dikhe
    if (select && initialTafsir) {
        select.value = initialTafsir;
    }

    loadTafsir(select?.value || initialTafsir);
});

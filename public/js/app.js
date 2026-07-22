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

document.querySelectorAll(".hadith-nav-link").forEach((link) => {
    link.addEventListener("click", showPageLoader);
});
function showPageLoader() {
    document.getElementById("page-loader")?.classList.remove("d-none");
    document.getElementById("tasbih-loader")?.classList.add("active");
}

function hidePageLoader() {
    document.getElementById("page-loader")?.classList.add("d-none");
    document.getElementById("tasbih-loader")?.classList.remove("active");
}

const scrollBtn = document.getElementById("scrollTopBtn");
if (scrollBtn) {
    window.addEventListener("scroll", () => {
        scrollBtn.classList.toggle("visible", window.scrollY > 300);
    });
    scrollBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

function toggleDevNote() {
    const modal = document.getElementById("devNoteModal");
    const overlay = document.getElementById("devNoteOverlay");

    modal.classList.toggle("visible");
    overlay.classList.toggle("visible");
    document.body.style.overflow = modal.classList.contains("visible")
        ? "hidden"
        : "";
}

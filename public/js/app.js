function showFlash(message, type = "info") {
    const flash = document.createElement("div");
    flash.className = `flash-toast flash-${type}`;
    flash.textContent = message;
    document.body.appendChild(flash);

    setTimeout(() => flash.remove(), 3000);
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

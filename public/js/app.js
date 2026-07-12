function showFlash(message, type = "info") {
    const flash = document.createElement("div");
    flash.className = `flash-toast flash-${type}`;
    flash.textContent = message;
    document.body.appendChild(flash);

    setTimeout(() => flash.remove(), 3000);
}

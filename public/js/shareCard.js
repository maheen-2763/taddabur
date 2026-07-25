// ════════════════════════════════════════════
// UNIFIED SHARE-CARD GENERATOR (Hadith + Ayah)
// ════════════════════════════════════════════

let amiriFontLoaded = false;

async function ensureAmiriFontLoaded() {
    if (amiriFontLoaded) return;
    const font = new FontFace(
        "Amiri Quran",
        "url(/fonts/amiri-quran/amiri-quran-v19-arabic_latin-regular.woff2)",
    );
    await font.load();
    document.fonts.add(font);
    amiriFontLoaded = true;
}

function truncateText(text, maxLen) {
    if (text.length <= maxLen) return text;
    return text.slice(0, maxLen).trim() + "…";
}

function wrapText(ctx, text, maxWidth) {
    const words = text.split(" ");
    const lines = [];
    let line = "";
    words.forEach((word) => {
        const test = line ? `${line} ${word}` : word;
        if (ctx.measureText(test).width > maxWidth && line) {
            lines.push(line);
            line = word;
        } else {
            line = test;
        }
    });
    if (line) lines.push(line);
    return lines;
}

// ════════════ LOGO (inline SVG → cached Image) ════════════
let logoImageCache = null;

function getLogoImage() {
    if (logoImageCache) return Promise.resolve(logoImageCache);

    const svgMarkup = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <rect x="20" y="20" width="60" height="60" fill="none" stroke="#E8BE6D" stroke-width="4" />
            <rect x="20" y="20" width="60" height="60" fill="none" stroke="#C9963A" stroke-width="4"
                transform="rotate(45 50 50)" />
            <polygon points="50,8.2 57.2,42.8 91.8,50 57.2,57.2 50,91.8 42.8,57.2 8.2,50 42.8,42.8" fill="#E8BE6D" />
            <circle cx="50" cy="50" r="10" fill="#0D3D22" />
            <circle cx="50" cy="50" r="6.5" fill="#FAF6EE" opacity="0.9" />
        </svg>
    `;

    const svgBlob = new Blob([svgMarkup], { type: "image/svg+xml" });
    const url = URL.createObjectURL(svgBlob);

    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            logoImageCache = img;
            URL.revokeObjectURL(url);
            resolve(img);
        };
        img.onerror = reject;
        img.src = url;
    });
}

/**
 * data = { arabic, translation, reference, badge (optional grade/surah info) }
 */
async function generateShareCardImage(data) {
    await ensureAmiriFontLoaded();

    const width = 900;
    const canvas = document.createElement("canvas");
    canvas.width = width;
    const ctx = canvas.getContext("2d");

    const bg = "#faf6ee";
    const emerald = "#1b5e3b";
    const gold = "#c9963a";
    const ink = "#2b2b2b";
    const muted = "#7a7a7a";

    const padding = 60;
    const maxTextWidth = width - padding * 2;

    const arabicText = data.arabic || "";
    const translationText = truncateText(data.translation || "", 280);

    // ✅ Dynamic Arabic font size — chhoti ayah ke liye bada, lambi hadith ke liye chhota
    // (taaki lines kam rahe aur diacritics ko breathing room mile)
    let arabicFontSize = 40;
    if (arabicText.length > 250) arabicFontSize = 30;
    else if (arabicText.length > 120) arabicFontSize = 34;

    ctx.font = `${arabicFontSize}px 'Amiri Quran'`;
    const arabicLines = wrapText(ctx, arabicText, maxTextWidth);

    ctx.font = "22px Georgia, serif";
    const translationLines = wrapText(ctx, translationText, maxTextWidth);

    // ✅ Line-height ab font-size ka ~1.85x — diacritics ke liye zaroori breathing room
    const arabicLineHeight = Math.round(arabicFontSize * 1.85);
    const headerHeight = 100;
    const translationLineHeight = 34;
    const footerHeight = 100;

    const height =
        headerHeight +
        arabicLines.length * arabicLineHeight +
        70 +
        translationLines.length * translationLineHeight +
        footerHeight;

    canvas.height = height;

    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, width, height);

    ctx.fillStyle = gold;
    ctx.fillRect(0, 0, width, 6);

    drawStarChainBorder(ctx, width, height);

    let y = 75;

    if (data.badge) {
        ctx.font = "bold 15px Georgia, serif";
        ctx.fillStyle = gold;
        ctx.textAlign = "left";
        ctx.fillText(data.badge.toUpperCase(), padding, y);
        y += 45;
    }

    // Arabic — extra top padding pehli line ke liye (diacritic clipping fix)
    ctx.font = `${arabicFontSize}px 'Amiri Quran'`;
    ctx.fillStyle = emerald;
    ctx.textAlign = "right";
    ctx.direction = "rtl";
    ctx.textBaseline = "alphabetic";
    y += arabicFontSize * 0.35; // top breathing room
    arabicLines.forEach((line) => {
        ctx.fillText(line, width - padding, y);
        y += arabicLineHeight;
    });

    y += 25;

    // ✅ Ornamental divider — matches app's ⊙ brand signature
    ctx.strokeStyle = "rgba(201, 150, 58, 0.4)";
    ctx.lineWidth = 1;
    const midX = width / 2;
    ctx.beginPath();
    ctx.moveTo(padding, y);
    ctx.lineTo(midX - 20, y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(midX + 20, y);
    ctx.lineTo(width - padding, y);
    ctx.stroke();

    // Center ornament circle
    ctx.beginPath();
    ctx.arc(midX, y, 6, 0, Math.PI * 2);
    ctx.strokeStyle = gold;
    ctx.lineWidth = 1.5;
    ctx.stroke();
    ctx.beginPath();
    ctx.arc(midX, y, 2, 0, Math.PI * 2);
    ctx.fillStyle = gold;
    ctx.fill();

    y += 45;

    ctx.font = "22px Georgia, serif";
    ctx.fillStyle = ink;
    ctx.textAlign = "left";
    ctx.direction = "ltr";
    translationLines.forEach((line) => {
        ctx.fillText(line, padding, y);
        y += translationLineHeight;
    });

    y += 30;

    ctx.font = "italic 17px Georgia, serif";
    ctx.fillStyle = muted;
    ctx.fillText(data.reference || "", padding, y);

    // ── Footer branding: logo + text side-by-side, centered ──
    const logo = await getLogoImage();
    const logoSize = 26;
    const brandText = "Taddabur — تدبّر";

    ctx.font = "bold 16px Georgia, serif";
    const textWidth = ctx.measureText(brandText).width;
    const gap = 8;
    const totalWidth = logoSize + gap + textWidth;
    const startX = width / 2 - totalWidth / 2;
    const footerY = height - 56;

    ctx.drawImage(logo, startX, footerY, logoSize, logoSize);

    ctx.fillStyle = emerald;
    ctx.textAlign = "left";
    ctx.textBaseline = "middle";
    ctx.fillText(brandText, startX + logoSize + gap, footerY + logoSize / 2);

    // Reset baseline for safety (in case function is reused)
    ctx.textBaseline = "alphabetic";

    return new Promise((resolve) => {
        canvas.toBlob((blob) => resolve(blob), "image/png");
    });
}

/**
 * Shares the image blob via Web Share API (with file support),
 * falls back to downloading the image if sharing isn't available.
 */
async function shareCardImage(blob, filename, fallbackUrl, btn) {
    const file = new File([blob], filename, { type: "image/png" });

    if (navigator.canShare && navigator.canShare({ files: [file] })) {
        try {
            await navigator.share({
                files: [file],
                title: "Taddabur",
                text: fallbackUrl,
            });
            return;
        } catch (e) {
            // user cancelled — do nothing
            return;
        }
    }

    // Fallback: trigger download
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    URL.revokeObjectURL(link.href);

    if (btn) flashCopied(btn, "Image Downloaded!");
}

// ════════════ STAR-CHAIN BORDER (all 4 sides) ════════════
function drawStar8(ctx, cx, cy, r) {
    ctx.save();
    ctx.translate(cx, cy);
    ctx.strokeStyle = "#c9963a";
    ctx.lineWidth = 1;
    for (let rot = 0; rot < 2; rot++) {
        ctx.rotate(rot === 0 ? 0 : (Math.PI / 180) * 22.5);
        ctx.beginPath();
        for (let i = 0; i < 8; i++) {
            const angle = (Math.PI / 4) * i;
            const px = Math.cos(angle) * r;
            const py = Math.sin(angle) * r;
            if (i === 0) ctx.moveTo(px, py);
            else ctx.lineTo(px, py);
        }
        ctx.closePath();
        ctx.stroke();
    }
    ctx.restore();
}

// ════════════ DIAMOND-CHAIN BORDER (all 4 sides) ════════════
function drawDiamond(ctx, cx, cy, size) {
    ctx.beginPath();
    ctx.moveTo(cx, cy - size);
    ctx.lineTo(cx + size, cy);
    ctx.lineTo(cx, cy + size);
    ctx.lineTo(cx - size, cy);
    ctx.closePath();
    ctx.stroke();

    // Inner dot — chhota accent
    ctx.beginPath();
    ctx.arc(cx, cy, 1.6, 0, Math.PI * 2);
    ctx.fillStyle = "#c9963a";
    ctx.fill();
}

function drawDiamondChainBorder(ctx, width, height) {
    const inset = 20;
    const spacing = 26;
    const size = 7;

    ctx.strokeStyle = "#c9963a";
    ctx.lineWidth = 1;

    // Guide lines top/bottom
    ctx.beginPath();
    ctx.moveTo(0, 10);
    ctx.lineTo(width, 10);
    ctx.moveTo(0, height - 10);
    ctx.lineTo(width, height - 10);
    ctx.strokeStyle = "rgba(201, 150, 58, 0.35)";
    ctx.lineWidth = 0.5;
    ctx.stroke();

    // Guide lines left/right
    ctx.beginPath();
    ctx.moveTo(10, 0);
    ctx.lineTo(10, height);
    ctx.moveTo(width - 10, 0);
    ctx.lineTo(width - 10, height);
    ctx.stroke();

    ctx.lineWidth = 1;

    // Top & bottom diamonds
    for (let x = spacing; x < width - spacing / 2; x += spacing) {
        drawDiamond(ctx, x, inset, size);
        drawDiamond(ctx, x, height - inset, size);
    }

    // Left & right diamonds (avoid corner overlap)
    for (let y = spacing + inset; y < height - spacing - inset; y += spacing) {
        drawDiamond(ctx, inset, y, size);
        drawDiamond(ctx, width - inset, y, size);
    }
}

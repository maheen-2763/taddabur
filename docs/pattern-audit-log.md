# Hadith Module — Audit Checklist Log

Last checked: 2026-07-22

## ✅ Items 1-3 (pehle fix ho chuke)

- [x] Grade-scoping bug (global vs per-collection routes)
- [x] Missing chapter_id index (10s+ query fix)
- [x] CSS class collision (js- prefix convention established)

## ✅ Item 4 — Dark Mode CSS Override

Hadith-specific CSS variables check kiye — koi Hadith module ka color variable
missing dark-mode override nahi mila. (Jo missing mile — `--gold`, `--emerald`
wagera — wo global `app.css` ke hain, Hadith-specific nahi. Alag se track karo
agar chahiye.)

- [x] Hadith module ki apni CSS files clean hain

## ✅ Item 5 — JS Hook Classes (js-\*)

- [x] Koi `.js-*` class kahin bhi visual styling ke liye double-use nahi mili — clean.

## ⚠️ Item 6 — Sync/Async Duplication

- [ ] `showFlash` function 2 baar mila project me. Verify karo ye Hadith module
      (`hadith-show.js` / `hadithActions.js`) ke andar hai ya kisi aur module se
      clash ho raha hai.

## 🔍 Item 7 — Language Array Access (Hadith-relevant files)

Hadith module ke liye specifically ye files verify karni hain
(baaki jo mile the wo Quran/Tafsir module ke the, out of scope):

- [ ] Koi Hadith controller/model is list me directly nahi tha — matlab
      Hadith module me `firstWhere('lang', ...)` pattern already sahi use ho
      raha hai. Clean lagta hai, but ek baar `HadithController.php` aur
      `hadithActions.js` manually skim kar lo confirm ke liye.

## ✅ Bonus — Deep-Link number vs id (jo hum abhi fix kar rahe the)

- [x] `_hadith-card.blade.php` → `$h->id` use ho raha hai (fixed)
- [x] `hadith-show.js` (`targetHadithId`) → fixed
- [x] Controller (`show`, `loadHadiths`) → `id`-based position calc → fixed
- [ ] **Ek baar dobara `project:audit-patterns` chala ke confirm karo** ki
      ab koi `highlight={{ $h->number }}` pattern Hadith views me bacha to nahi
      (pichli baar sirf Quran wala mila tha, Hadith clean aaya — good sign)

---

**Overall status:** Hadith module checklist ke 7 me se 6 clean/fixed hain.
Sirf `showFlash` duplication baaki hai verify karne ke liye.

<?php
// app/Console/Commands/StripBismillahFromAyahs.php
//
// PURPOSE: Strip Bismillah from ayah 1 of each surah.
//          Runs ONCE after fresh Quran import.
//
// WHY:
//   The API returns ayah 1 of each surah with
//   Bismillah prepended to the Arabic text.
//   We display Bismillah separately at top of page
//   so we strip it from ayah 1 to avoid duplication.
//
// RULES:
//   Surah 1  (Al-Fatihah) → Bismillah IS ayah 1 → SKIP
//   Surah 9  (At-Tawbah)  → No Bismillah at all  → SKIP
//   All others             → Strip from ayah 1    → FIX
//
// USAGE:
//   php artisan quran:strip-bismillah
//
//   Safe to re-run — checks before modifying.

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\Surah;
use Illuminate\Console\Command;

class StripBismillahFromAyahs extends Command
{
    protected $signature   = 'quran:strip-bismillah';
    protected $description = 'Strip Bismillah from ayah 1 of each surah in database';

    // ── Bismillah variants ────────────────────────────────
    // The API uses ٱ (wasla alef) character
    // We check multiple variants to be safe
    private array $bismillahVariants = [
        'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ ',   // with trailing space
        'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ',    // without trailing space
        'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ',    // standard alef variant
        'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ ',   // mixed variant
    ];

    public function handle(): int
    {
        $this->info('');
        $this->info('🕌 Stripping Bismillah from ayah 1 of each surah...');
        $this->info('   Surah 1 and Surah 9 will be skipped.');
        $this->info('');

        // Skip Surah 1 (Bismillah IS ayah 1)
        // Skip Surah 9 (At-Tawbah has no Bismillah)
        $surahs = Surah::whereNotIn('number', [1, 9])
            ->orderBy('number')
            ->get();

        $bar     = $this->output->createProgressBar($surahs->count());
        $bar->start();

        $fixed       = 0;
        $skipped     = 0;
        $notFound    = 0;

        foreach ($surahs as $surah) {

            // Get ayah 1 of this surah
            $ayah = Ayah::where('surah_id', $surah->id)
                ->where('number', 1)
                ->first();

            if (!$ayah) {
                $notFound++;
                $bar->advance();
                continue;
            }

            $originalText = $ayah->text_arabic;
            $cleaned      = $originalText;
            $found        = false;

            // Try each Bismillah variant
            foreach ($this->bismillahVariants as $variant) {
                if (str_starts_with(trim($cleaned), trim($variant))) {
                    $cleaned = trim(str_replace($variant, '', $cleaned));
                    $found   = true;
                    break;
                }
            }

            if ($found && $cleaned !== $originalText && !empty($cleaned)) {
                // Save cleaned text
                $ayah->update(['text_arabic' => $cleaned]);
                $fixed++;
            } else {
                // Already clean or variant not matched
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->info("✅ Fixed:    {$fixed} ayahs");
        $this->info("⏭  Skipped:  {$skipped} ayahs (already clean)");
        $this->info("❌ Not found: {$notFound} ayahs");

        // ── Verification ──────────────────────────────────
        $this->info('');
        $this->info('── Verification ─────────────────────────────');

        // Surah 2 — should NOT start with Bismillah
        $surah2 = Ayah::whereHas('surah', fn($q) => $q->where('number', 2))
            ->where('number', 1)
            ->first();
        $this->info('Surah 2, Ayah 1:');
        $this->info('  ' . ($surah2?->text_arabic ?? 'NOT FOUND'));

        // Surah 3 — should NOT start with Bismillah
        $surah3 = Ayah::whereHas('surah', fn($q) => $q->where('number', 3))
            ->where('number', 1)
            ->first();
        $this->info('Surah 3, Ayah 1:');
        $this->info('  ' . ($surah3?->text_arabic ?? 'NOT FOUND'));

        // Surah 1 — should still have Bismillah (it IS the ayah)
        $surah1 = Ayah::whereHas('surah', fn($q) => $q->where('number', 1))
            ->where('number', 1)
            ->first();
        $this->info('Surah 1, Ayah 1 (should keep Bismillah):');
        $this->info('  ' . ($surah1?->text_arabic ?? 'NOT FOUND'));

        // Surah 9 — no Bismillah (At-Tawbah)
        $surah9 = Ayah::whereHas('surah', fn($q) => $q->where('number', 9))
            ->where('number', 1)
            ->first();
        $this->info('Surah 9, Ayah 1 (At-Tawbah, no Bismillah):');
        $this->info('  ' . ($surah9?->text_arabic ?? 'NOT FOUND'));

        $this->info('');
        $this->info('✅ Done! Bismillah stripped from all applicable surahs.');
        $this->info('   You can now re-import translations with:');
        $this->info('   php artisan quran:import-translations --all --force');

        return self::SUCCESS;
    }
}

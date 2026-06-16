<?php
// app/Console/Commands/ImportTranslations.php
//
// PURPOSE: Import actual ayah translation TEXT from quran.com API.
//          Runs ONCE after fresh install.
//          Safe to re-run — uses updateOrCreate per ayah.
//
// USAGE:
//   php artisan quran:import-translations
//             --translation=sahih-international
//
//   php artisan quran:import-translations --all
//
// PERFORMANCE:
//   Processes one surah at a time.
//   Adds 100ms delay between API calls.
//   Logs progress clearly.
//
// BISMILLAH FIX:
//   The API returns ayah 1 of each surah with
//   Bismillah prepended. We strip it here so the
//   blade can show it separately at the top.

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\AyahTranslation;
use App\Models\Surah;
use App\Models\Translation;
use App\Services\QuranApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportTranslations extends Command
{
    protected $signature = 'quran:import-translations
                            {--translation= : Slug of translation to import}
                            {--all          : Import all active translations}
                            {--surah=       : Import one surah only (for testing)}
                            {--force        : Re-import even if already exists}';

    protected $description = 'Import Quran translation text from quran.com API';

    // ── Bismillah variants to strip from ayah 1 ──────────
    // The API prepends Bismillah to ayah 1 of each surah
    // We display it separately at the top of the page
    private array $bismillahVariants = [
        'In the name of Allah, the Entirely Merciful, the Especially Merciful.',
        'In the name of Allah, the Most Gracious, the Most Merciful.',
        'In the Name of Allah, the Most Gracious, the Most Merciful.',
        'In the name of Allāh, the Entirely Merciful, the Especially Merciful.',
        'In the name of Allāh, the Most Gracious, the Most Merciful.',
        'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ',
        'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
    ];

    public function __construct(private QuranApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        // ── Which translations to import ──────────────────
        if ($this->option('all')) {
            $translations = Translation::where('is_active', true)->get();
        } elseif ($slug = $this->option('translation')) {
            $translations = Translation::where('slug', $slug)->get();

            if ($translations->isEmpty()) {
                $this->error("Translation not found: {$slug}");
                return self::FAILURE;
            }
        } else {
            $this->error('Provide --translation=slug or --all');
            return self::FAILURE;
        }

        // ── Which surahs ──────────────────────────────────
        if ($surahNum = $this->option('surah')) {
            $surahs = Surah::where('number', $surahNum)->get();
        } else {
            $surahs = Surah::orderBy('number')->get();
        }

        $this->info("Importing {$translations->count()} translation(s) for {$surahs->count()} surah(s)");

        foreach ($translations as $translation) {
            $this->importTranslation($translation, $surahs);
        }

        $this->info('');
        $this->info('✅ Import complete!');
        $this->info('Total: ' . AyahTranslation::count() . ' translation records');

        return self::SUCCESS;
    }

    // ── Import one translation for all surahs ────────────
    private function importTranslation(Translation $translation, $surahs): void
    {
        $this->info('');
        $this->info("📖 {$translation->name} (source: {$translation->source})");
        $bar = $this->output->createProgressBar($surahs->count());
        $bar->start();

        $imported = 0;
        $skipped  = 0;
        $failed   = 0;

        foreach ($surahs as $surah) {

            try {
                $verses = $this->api->fetchTranslationForSurah(
                    $surah->number,
                    $translation->source
                );



                if (empty($verses)) {
                    $failed++;
                    Log::warning("No verses returned for surah {$surah->number} translation {$translation->slug}");
                    $bar->advance();
                    continue;
                }



                foreach ($verses as $verse) {
                    $ayahNum = $verse['verse_number'] ?? null;

                    if (!$ayahNum) continue;

                    $text = $this->cleanText(
                        $verse['translations'][0]['text'] ?? '',
                        $surah->number,
                        $ayahNum
                    );

                    if (!$text) continue;

                    // Find ayah in database
                    $ayah = Ayah::where('surah_id', $surah->id)
                        ->where('number', (int) $ayahNum)
                        ->first();

                    if (!$ayah) {
                        Log::warning("Ayah not found: surah {$surah->number} ayah {$ayahNum}");
                        continue;
                    }

                    // Skip if already exists and not forcing
                    if (!$this->option('force')) {
                        $exists = AyahTranslation::where('ayah_id', $ayah->id)
                            ->where('translation_id', $translation->id)
                            ->exists();

                        if ($exists) {
                            $skipped++;
                            continue;
                        }
                    }

                    // ✅ updateOrCreate — safe to re-run
                    AyahTranslation::updateOrCreate(
                        [
                            'ayah_id'        => $ayah->id,
                            'translation_id' => $translation->id,
                        ],
                        ['text' => $text]
                    );

                    $imported++;
                }

                // Polite delay between API calls
                usleep(100000); // 100ms

            } catch (\Exception $e) {
                Log::error("Import failed surah {$surah->number}: " . $e->getMessage());
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info("  ✅ Imported: {$imported} | ⏭ Skipped: {$skipped} | ❌ Failed: {$failed}");
    }

    // ── Clean translation text ────────────────────────────
    private function cleanText(string $text, int $surahNumber, int $ayahNumber): string
    {
        // 1. Remove <sup> footnote tags AND their content
        $text = preg_replace('/<sup[^>]*>.*?<\/sup>/is', '', $text);

        // 2. Remove all remaining HTML tags
        $text = strip_tags($text);

        // 3. Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // 4. ✅ Strip Bismillah from ayah 1 of surahs 2-113
        //    Surah 1  → Bismillah IS ayah 1, keep it
        //    Surah 9  → No Bismillah
        //    Others   → Strip Bismillah from ayah 1
        if ($ayahNumber === 1 && !in_array($surahNumber, [1, 9])) {
            foreach ($this->bismillahVariants as $variant) {
                $trimmed = trim($text);
                if (stripos($trimmed, $variant) === 0) {
                    $text = trim(substr($trimmed, strlen($variant)));
                    break;
                }
            }
        }

        // 5. Normalize whitespace
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return $text;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\AyahTafsir;
use App\Models\Surah;
use App\Models\Tafsir;
use App\Services\QuranApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportTafsir extends Command
{
    protected $signature = 'quran:import-tafsir
                            {--tafsir= : Tafsir slug from tafsirs table}
                            {--all : Import all active tafsirs}
                            {--surah= : Only import for specific surah}';


    protected $description = 'Import Quran tafsir from Quran Foundation API';

    public function __construct(private QuranApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('');
        $this->info('📚 Quran Tafsir Import');
        $this->info('=======================');

        $tafsirs = $this->resolveTafsirs();
        if ($tafsirs === null) return self::FAILURE;

        $surahNumbers = $this->option('surah')
            ? [(int) $this->option('surah')]
            : range(1, 114);

        $emptyResponses = [];


        foreach ($tafsirs as $tafsir) {
            // BUG #1 FIX: source must be numeric resource ID, not slug
            if (!is_numeric($tafsir->source)) {
                $this->error("  ❌ Tafsir '{$tafsir->slug}' has non-numeric source: '{$tafsir->source}' — skipping. Fix the source column in DB.");
                Log::error("ImportTafsir: non-numeric source for tafsir '{$tafsir->slug}': '{$tafsir->source}'");
                continue;
            }

            $this->info('');
            $this->info("Importing: {$tafsir->name} by {$tafsir->scholar}");

            $bar = $this->output->createProgressBar(count($surahNumbers));
            $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%");
            $bar->start();

            $totalImported    = 0;
            $totalSkippedEmpty = 0;
            $totalAlreadyDone = 0; // yeh line naya add karo

            foreach ($surahNumbers as $surahNumber) {
                $bar->setMessage("Surah {$surahNumber}...");

                $surah = Surah::where('number', $surahNumber)->first();
                if (!$surah) {
                    $bar->advance();
                    continue;
                }

                $stored = AyahTafsir::whereHas('ayah', fn($q) => $q->where('surah_id', $surah->id))
                    ->where('tafsir_id', $tafsir->id)
                    ->count();

                if ($stored === $surah->ayah_count) {
                    $totalAlreadyDone++;
                    $bar->advance();
                    continue;
                }

                try {
                    $tafsirData = $this->api->fetchTafsirForSurah($surahNumber, (int) $tafsir->source);

                    if (empty($tafsirData)) {
                        $emptyResponses[] = "{$tafsir->slug} — Surah {$surahNumber}";
                        Log::warning("Empty tafsir response: source='{$tafsir->source}' surah={$surahNumber}");
                        $bar->advance();
                        continue;
                    }

                    foreach ($tafsirData as $item) {
                        if (empty($item['verse_key']) || !str_contains($item['verse_key'], ':')) {
                            continue;
                        }

                        [$vSurahNum, $aNum] = explode(':', $item['verse_key']);

                        if ((int) $vSurahNum !== $surahNumber) {
                            Log::warning("Mismatch: requested surah {$surahNumber} lekin API ne verse_key '{$item['verse_key']}' return kiya — skipped.");
                            continue;
                        }

                        $ayah = Ayah::where('surah_id', $surah->id)
                            ->where('number', (int) $aNum)
                            ->first();

                        if (!$ayah) continue;

                        $cleanText = $this->htmlToPlainText($item['tafsirs'][0]['text'] ?? '');

                        if ($cleanText === '') {
                            $totalSkippedEmpty++;
                            continue;
                        }

                        AyahTafsir::updateOrCreate(
                            ['ayah_id'   => $ayah->id, 'tafsir_id' => $tafsir->id],
                            ['text'      => $cleanText],
                            ['api_source_version' => 'qf_oauth2_v1'],
                        );

                        $totalImported++;
                    }
                } catch (\Exception $e) {
                    Log::error("Tafsir import failed for {$tafsir->slug} surah {$surahNumber}: " . $e->getMessage());
                } finally {
                    // BUG #3 FIX: pause() always runs — success ya fail dono cases mein
                    $this->api->pause();
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("  ✅ Imported: {$totalImported}");

            if ($totalSkippedEmpty > 0) {
                $this->warn("  ⚠️  Skipped (empty text): {$totalSkippedEmpty}");
            }
            $this->info("  ⏭️  Already complete (skipped): {$totalAlreadyDone} surahs");
        }

        // Accuracy warning report
        if (!empty($emptyResponses)) {
            $this->newLine();
            $this->warn('⚠️  ACCURACY WARNING — API returned 0 items for:');
            foreach ($emptyResponses as $entry) {
                $this->warn("   - {$entry}");
            }
            $this->warn('   Check tafsir "source" column in DB — numeric resource ID hona chahiye.');
        }

        $this->newLine();
        $this->info('Total tafsir records in DB: ' . number_format(AyahTafsir::count()));

        return self::SUCCESS;
    }

    private function resolveTafsirs()
    {
        if ($this->option('all')) {
            return Tafsir::where('is_active', true)->whereNotNull('source')->get();
        }

        if ($this->option('tafsir')) {
            $tafsirs = Tafsir::where('slug', $this->option('tafsir'))->get();

            if ($tafsirs->isEmpty()) {
                $this->error("Tafsir '{$this->option('tafsir')}' not found. Available slugs:");
                Tafsir::all()->each(fn($t) => $this->line("  {$t->slug} — {$t->name}"));
                return null;
            }

            return $tafsirs;
        }

        $this->error('Please specify --tafsir=slug or --all');
        return null;
    }

    private function htmlToPlainText(string $html): string
    {
        if (trim($html) === '') return '';

        $withBreaks = preg_replace(
            ['/<\/p>/i', '/<br\s*\/?>/i', '/<\/h[1-6]>/i', '/<\/div>/i', '/<div[^>]*>/i'],
            ["\n\n", "\n", "\n\n", "\n\n", "\n\n"],
            $html
        );

        $plain = strip_tags($withBreaks);
        $plain = preg_replace("/\n{3,}/", "\n\n", $plain);
        $plain = preg_replace('/[ \t]+/', ' ', $plain);

        return trim($plain);
    }
}

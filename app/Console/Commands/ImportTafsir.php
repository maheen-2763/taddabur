<?php
// app/Console/Commands/ImportTafsir.php

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
                            {--tafsir= : Tafsir slug from tafsirs table (e.g. ibn-kathir-en, NOT the API source key)}
                            {--all : Import all active tafsirs}
                            {--surah= : Only import for specific surah}';

    protected $description = 'Import Quran tafsir from Quran.com API';

    public function __construct(private QuranApiService $api)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('');
        $this->info('📚 Quran Tafsir Import');
        $this->info('=======================');

        if ($this->option('all')) {
            $tafsirs = Tafsir::where('is_active', true)->whereNotNull('source')->get();
        } elseif ($this->option('tafsir')) {
            $tafsirs = Tafsir::where('slug', $this->option('tafsir'))->get();

            if ($tafsirs->isEmpty()) {
                $this->error("Tafsir '{$this->option('tafsir')}' not found.");
                Tafsir::all()->each(fn($t) => $this->line("  {$t->slug} — {$t->name}"));
                return self::FAILURE;
            }
        } else {
            $this->error('Please specify --tafsir=slug or --all');
            return self::FAILURE;
        }

        $surahNumbers = $this->option('surah')
            ? [(int) $this->option('surah')]
            : range(1, 114);

        // Track problem surahs across all tafsirs for a final accuracy report
        $emptyResponses = [];

        foreach ($tafsirs as $tafsir) {
            $this->info('');
            $this->info("Importing: {$tafsir->name} by {$tafsir->scholar}");

            $bar = $this->output->createProgressBar(count($surahNumbers));
            $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%");
            $bar->start();

            $totalImported = 0;
            $totalSkippedEmpty = 0;

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
                    $bar->advance();
                    continue;
                }

                try {
                    $tafsirData = $this->api->fetchTafsirForSurah($surahNumber, $tafsir->source);

                    // FIX #1: Empty API response is NOT an exception — flag it instead of
                    // silently moving on. A bad source slug returns [] quietly, not an error.
                    if (empty($tafsirData)) {
                        $emptyResponses[] = "{$tafsir->slug} — Surah {$surahNumber}";
                        Log::warning("Empty tafsir response: source='{$tafsir->source}' surah={$surahNumber}");
                        $bar->advance();
                        continue;
                    }

                    foreach ($tafsirData as $item) {
                        if (empty($item['verse_key'])) {
                            continue;
                        }

                        [$sNum, $aNum] = explode(':', $item['verse_key']);

                        $ayah = Ayah::where('surah_id', $surah->id)
                            ->where('number', (int) $aNum)
                            ->first();

                        if (!$ayah) continue;

                        // FIX #2: Preserve paragraph structure before stripping tags,
                        // so multi-paragraph tafsir (e.g. Ibn Kathir) doesn't collapse
                        // into one unreadable block of text.
                        $cleanText = $this->htmlToPlainText($item['text'] ?? '');

                        if ($cleanText === '') {
                            $totalSkippedEmpty++;
                            continue;
                        }

                        AyahTafsir::updateOrCreate(
                            [
                                'ayah_id'   => $ayah->id,
                                'tafsir_id' => $tafsir->id,
                            ],
                            ['text' => $cleanText]
                        );

                        $totalImported++;
                    }

                    $this->api->pause();
                } catch (\Exception $e) {
                    Log::error(
                        "Tafsir import failed for {$tafsir->slug} surah {$surahNumber}: " . $e->getMessage()
                    );
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("  ✅ Tafsir records imported: {$totalImported}");
            if ($totalSkippedEmpty > 0) {
                $this->warn("  ⚠️ Skipped (empty text from API): {$totalSkippedEmpty}");
            }
        }

        // FIX #1 continued: surface every empty-response surah at the end,
        // so a bad source slug can't pass silently as "0 errors".
        if (!empty($emptyResponses)) {
            $this->newLine();
            $this->warn('⚠️ ACCURACY WARNING — API returned 0 items for:');
            foreach ($emptyResponses as $entry) {
                $this->warn("   - {$entry}");
            }
            $this->warn('   Check the tafsir "source" slug in the database — it may be wrong.');
        }

        $this->newLine();
        $this->info("Total tafsir records in DB: " . number_format(AyahTafsir::count()));

        return self::SUCCESS;
    }

    /**
     * Convert tafsir HTML to plain text while preserving paragraph breaks,
     * instead of strip_tags() collapsing everything into one line.
     */
    private function htmlToPlainText(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $withBreaks = preg_replace(
            ['/<\/p>/i', '/<br\s*\/?>/i', '/<\/h[1-6]>/i'],
            ["\n\n", "\n", "\n\n"],
            $html
        );

        $plain = strip_tags($withBreaks);
        $plain = preg_replace("/\n{3,}/", "\n\n", $plain);

        return trim($plain);
    }
}

<?php
// app/Console/Commands/ImportQuran.php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\Surah;
use App\Services\QuranApiService;
use Illuminate\Console\Command;

class ImportQuran extends Command
{
    // -------------------------------------------------------
    // The command signature — what you type in terminal
    // {--surah=} means optional: --surah=1 to import only one surah
    // {--fresh}  means optional flag: --fresh to wipe and reimport
    // -------------------------------------------------------
    protected $signature = 'quran:import
                            {--surah= : Import only a specific surah number (1-114)}
                            {--fresh : Wipe existing ayahs and reimport everything}
                            {--no-simple : Skip importing simple Arabic text}';

    protected $description = 'Import all Quran ayahs (Arabic text) from alquran.cloud API';

    // Inject the service via constructor
    public function __construct(private QuranApiService $api)
    {
        parent::__construct();
    }

    // -------------------------------------------------------
    // HANDLE — this runs when the command is called
    // -------------------------------------------------------
    public function handle(): int
    {
        $this->info('');
        $this->info('🕌 ==========================================');
        $this->info('   Quran Import — alquran.cloud API');
        $this->info('==========================================');
        $this->info('');

        // --fresh flag: wipe existing data
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️  This will delete all existing ayahs. Are you sure?')) {
                Ayah::truncate();
                $this->warn('✓ Existing ayahs cleared.');
            } else {
                $this->info('Cancelled.');
                return self::SUCCESS;
            }
        }

        // Which surahs to import?
        $surahOption = $this->option('surah');

        if ($surahOption) {
            // Single surah mode
            $surahNumbers = [(int) $surahOption];
            $this->info("Importing single surah: {$surahOption}");
        } else {
            // All 114 surahs
            $surahNumbers = range(1, 114);
            $this->info('Importing all 114 surahs...');
        }

        $this->info('');

        // ── PROGRESS BAR ─────────────────────────────────
        // Laravel provides a built-in progress bar for commands
        $bar = $this->output->createProgressBar(count($surahNumbers));
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%");
        $bar->start();

        $totalAyahs  = 0;
        $totalErrors = 0;

        // ── LOOP THROUGH SURAHS ──────────────────────────
        foreach ($surahNumbers as $surahNumber) {

            $bar->setMessage("Surah {$surahNumber}/114...");

            try {
                // Make sure surah record exists in our DB
                $surah = Surah::where('number', $surahNumber)->first();

                if (!$surah) {
                    $this->newLine();
                    $this->warn("Surah {$surahNumber} not found in DB. Did you run SurahSeeder?");
                    $totalErrors++;
                    $bar->advance();
                    continue;
                }

                // Skip if already imported (unless --fresh was used)
                $existingCount = Ayah::where('surah_id', $surah->id)->count();
                if ($existingCount === $surah->ayah_count) {
                    $bar->advance();
                    continue; // Already complete, skip
                }

                // Fetch this surah's ayahs from API
                $surahData = $this->api->fetchSurahWithAyahs($surahNumber, 'quran-uthmani');

                // Fetch simple Arabic (for search)
                $simpleAyahs = [];
                if (!$this->option('no-simple')) {
                    $simpleData  = $this->api->fetchSimpleArabicForSurah($surahNumber);
                    // Index by ayah number for quick lookup
                    foreach ($simpleData as $sa) {
                        $simpleAyahs[$sa['numberInSurah']] = $sa['text'];
                    }
                }

                // Store each ayah
                foreach ($surahData['ayahs'] as $ayahData) {
                    Ayah::updateOrCreate(
                        [
                            'surah_id' => $surah->id,
                            'number'   => $ayahData['numberInSurah'],
                        ],
                        [
                            'number_in_quran'     => $ayahData['number'],
                            'text_arabic'         => $ayahData['text'],
                            'text_arabic_simple'  => $simpleAyahs[$ayahData['numberInSurah']] ?? null,
                            'page'                => $ayahData['page'] ?? null,
                            'juz'                 => $ayahData['juz'] ?? null,
                            'hizb'                => $ayahData['hizbQuarter'] ?? null,
                            'ruku'                => $ayahData['ruku'] ?? null,
                            'sajda'               => isset($ayahData['sajda']) && $ayahData['sajda'] !== false,
                        ]
                    );

                    $totalAyahs++;
                }

                // Be polite to the free API — pause between requests
                $this->api->pause();
            } catch (\Exception $e) {
                $totalErrors++;
                \Illuminate\Support\Facades\Log::error(
                    "ImportQuran failed for surah {$surahNumber}: " . $e->getMessage()
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // ── RESULTS SUMMARY ──────────────────────────────
        $this->info("✅ Import complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Ayahs imported', number_format($totalAyahs)],
                ['Errors',         $totalErrors],
                ['Total in DB',    number_format(Ayah::count())],
            ]
        );

        if ($totalErrors > 0) {
            $this->warn("⚠️  {$totalErrors} errors occurred. Check storage/logs/laravel.log");
        }

        $this->info('');
        $this->info('Next steps:');
        $this->info('  php artisan quran:import-translations --translation=sahih-international');
        $this->info('  php artisan quran:import-tafsir --tafsir=ibn-kathir-en');
        $this->info('');

        return self::SUCCESS;
    }
}

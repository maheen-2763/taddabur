<?php
// app/Console/Commands/ImportTafsir.php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\AyahTafsir;
use App\Models\Surah;
use App\Models\Tafsir;
use App\Services\QuranApiService;
use Illuminate\Console\Command;

class ImportTafsir extends Command
{
    protected $signature = 'quran:import-tafsir
                            {--tafsir= : Tafsir slug (e.g. ibn-kathir-en)}
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

        // Determine which tafsirs to import
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

        foreach ($tafsirs as $tafsir) {
            $this->info('');
            $this->info("Importing: {$tafsir->name} by {$tafsir->scholar}");

            $bar = $this->output->createProgressBar(count($surahNumbers));
            $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%");
            $bar->start();

            $totalImported = 0;

            foreach ($surahNumbers as $surahNumber) {
                $bar->setMessage("Surah {$surahNumber}...");

                $surah = Surah::where('number', $surahNumber)->first();
                if (!$surah) {
                    $bar->advance();
                    continue;
                }

                // Skip if already stored
                $stored = AyahTafsir::whereHas('ayah', fn($q) => $q->where('surah_id', $surah->id))
                    ->where('tafsir_id', $tafsir->id)
                    ->count();

                if ($stored === $surah->ayah_count) {
                    $bar->advance();
                    continue;
                }

                try {
                    $tafsirData = $this->api->fetchTafsirForSurah($surahNumber, $tafsir->source);

                    foreach ($tafsirData as $item) {
                        // Parse verse_key "2:255" → ayah number 255 in surah 2
                        [$sNum, $aNum] = explode(':', $item['verse_key']);

                        $ayah = Ayah::where('surah_id', $surah->id)
                            ->where('number', (int) $aNum)
                            ->first();

                        if (!$ayah) continue;

                        // Tafsir text often contains HTML — strip for clean storage
                        $cleanText = strip_tags($item['text'] ?? '');

                        if (empty(trim($cleanText))) continue;

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
                    \Illuminate\Support\Facades\Log::error(
                        "Tafsir import failed for {$tafsir->slug} surah {$surahNumber}: " . $e->getMessage()
                    );
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("  ✅ Tafsir records imported: {$totalImported}");
        }

        $this->newLine();
        $this->info("Total tafsir records in DB: " . number_format(AyahTafsir::count()));

        return self::SUCCESS;
    }
}

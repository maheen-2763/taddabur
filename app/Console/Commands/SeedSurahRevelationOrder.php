<?php

namespace App\Console\Commands;

use App\Models\Surah;
use Illuminate\Console\Command;

class SeedSurahRevelationOrder extends Command
{
    protected $signature = 'quran:seed-revelation-order';
    protected $description = 'Populate revelation_order column on surahs table from config/surah_revelation_order.php';

    public function handle(): int
    {
        $map = config('surah_revelation_order'); // [order => surah_number]

        if (count($map) !== 114) {
            $this->error('Config map does not have exactly 114 entries — aborting for safety.');
            return self::FAILURE;
        }

        $updated = 0;

        foreach ($map as $order => $surahNumber) {
            $affected = Surah::where('number', $surahNumber)->update(['revelation_order' => $order]);
            $updated += $affected;
        }

        $this->info("✅ Updated {$updated} surahs with revelation_order.");

        // Sanity check — koi surah miss to nahi hui
        $missing = Surah::whereNull('revelation_order')->count();
        if ($missing > 0) {
            $this->warn("⚠️ {$missing} surahs still have null revelation_order — check config coverage.");
        }

        return self::SUCCESS;
    }
}

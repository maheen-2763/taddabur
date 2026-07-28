<?php

namespace App\Console\Commands;

use App\Helpers\ArabicHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildHadithSearchIndex extends Command
{
    protected $signature = 'hadith:rebuild-search-index';

    protected $description = 'Rebuild the hadiths_fts search index with normalized Arabic text for all existing hadiths.';

    public function handle(): int
    {
        $this->info('Rebuilding Hadith search index...');

        // Purani entries clear karo (fresh rebuild)
        DB::statement("INSERT INTO hadiths_fts(hadiths_fts) VALUES('delete-all')");

        $total = DB::table('hadiths')->count();
        $bar = $this->output->createProgressBar($total);

        DB::table('hadiths')
            ->orderBy('id')
            ->chunk(200, function ($hadiths) use ($bar) {
                foreach ($hadiths as $hadith) {
                    $normalized = ArabicHelper::normalizeArabic($hadith->arabic);

                    DB::table('hadiths_fts')->insert([
                        'rowid' => $hadith->id,
                        'arabic' => $hadith->arabic,
                        'english' => $hadith->english,
                        'arabic_normalized' => $normalized,
                    ]);

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("✅ Index rebuilt for {$total} hadiths.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Hadith;
use Illuminate\Console\Command;

class ClassifyHadithGrades extends Command
{
    protected $signature = 'hadith:classify-grades';
    protected $description = 'Raw grade text ko reliability + attribution_type mein classify karta hai';

    public function handle(): int
    {
        $map = config('hadith_grade_map');
        $unmapped = [];

        $query = Hadith::whereNotNull('grade')->whereNull('reliability');
        $total = $query->count();

        if ($total === 0) {
            $this->info('Koi hadith baaki nahi hai classify karne ke liye.');
            return 0;
        }

        $bar = $this->output->createProgressBar($total);

        Hadith::whereNotNull('grade')
            ->whereNull('reliability')
            ->cursor()
            ->each(function (Hadith $hadith) use ($map, &$unmapped, $bar) {
                $mapped = $map[$hadith->grade] ?? null;

                if (!$mapped) {
                    $unmapped[$hadith->grade] = true;
                    $bar->advance();
                    return;
                }

                $hadith->update([
                    'reliability'      => $mapped['reliability'],
                    'attribution_type' => $mapped['attribution'],
                ]);

                $bar->advance();
            });

        $bar->finish();
        $this->newLine();

        if (!empty($unmapped)) {
            $this->warn('⚠️ Ye grades mapping mein nahi mile, config file update karo:');
            foreach (array_keys($unmapped) as $grade) {
                $this->line("  - {$grade}");
            }
            return 1;
        }

        $this->info('✅ Sab hadiths classify ho gaye!');
        return 0;
    }
}

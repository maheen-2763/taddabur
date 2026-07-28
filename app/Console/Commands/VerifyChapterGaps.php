<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyChapterGaps extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hadith:verify-chapter-gaps';

    /**
     * The console command description.
     */
    protected $description = 'Verify each hadith chapter\'s actual MIN/MAX hadith number against its stored start_number/end_number, flagging only genuine chapter_id linkage corruption (not benign sparse/non-sequential numbering).';

    public function handle(): int
    {
        $this->info('Scanning all hadith chapters for genuine range corruption...');

        $corruptions = [];
        $totalChapters = 0;

        DB::table('hadith_chapters')
            ->orderBy('collection_id')
            ->orderBy('number')
            ->chunk(50, function ($chapters) use (&$corruptions, &$totalChapters) {
                foreach ($chapters as $ch) {
                    $totalChapters++;

                    // start_number/end_number null hone par skip karo (defensive check)
                    if (is_null($ch->start_number) || is_null($ch->end_number)) {
                        continue;
                    }

                    $stats = DB::table('hadiths')
                        ->where('collection_id', $ch->collection_id)
                        ->where('chapter_id', $ch->id)
                        ->selectRaw('MIN(number) as min_num, MAX(number) as max_num, COUNT(*) as total')
                        ->first();

                    // Chapter mein koi hadith hi nahi hai - alag se flag karo
                    if ($stats->total === 0) {
                        $corruptions[] = sprintf(
                            '[Collection %d] Chapter #%d "%s": NO HADITHS FOUND (expected range %d-%d)',
                            $ch->collection_id,
                            $ch->number,
                            $ch->title,
                            $ch->start_number,
                            $ch->end_number
                        );
                        continue;
                    }

                    // Asli corruption check: actual MIN/MAX stored start/end se match karta hai?
                    if ((int) $stats->min_num !== (int) $ch->start_number || (int) $stats->max_num !== (int) $ch->end_number) {
                        $corruptions[] = sprintf(
                            '[Collection %d] Chapter #%d "%s": stored range %d-%d, but actual hadiths span %d-%d (total: %d) — LINKAGE MISMATCH',
                            $ch->collection_id,
                            $ch->number,
                            $ch->title,
                            $ch->start_number,
                            $ch->end_number,
                            $stats->min_num,
                            $stats->max_num,
                            $stats->total
                        );
                    }
                }
            });

        $this->newLine();
        $this->info("Total chapters scanned: {$totalChapters}");

        if (empty($corruptions)) {
            $this->info('✅ No linkage corruption found. All chapters verified — sparse/non-sequential numbering is expected and safe.');
            return self::SUCCESS;
        }

        $this->error(count($corruptions) . ' genuine corruption(s) found — these need manual review:');
        $this->newLine();

        foreach ($corruptions as $line) {
            $this->line($line);
        }

        $reportPath = storage_path('logs/chapter-gap-report.txt');
        file_put_contents(
            $reportPath,
            "Chapter Corruption Report — generated " . now()->toDateTimeString() . "\n\n" . implode("\n", $corruptions)
        );

        $this->newLine();
        $this->info("Report saved to: {$reportPath}");

        return self::FAILURE;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\HadithCollection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditHadithCounts extends Command
{
    protected $signature = 'hadith:audit-counts';
    protected $description = 'Audit hadith counts per collection and find chapters with zero hadiths';

    public function handle()
    {
        $collections = HadithCollection::with('chapters')->get();

        foreach ($collections as $collection) {
            $totalHadiths = DB::table('hadiths')
                ->where('collection_id', $collection->id)
                ->count();

            $this->newLine();
            $this->info("=== {$collection->name} ===");
            $this->line("Total hadiths in DB: {$totalHadiths}");

            // Zero-count chapters nikaalo
            $zeroChapters = DB::table('hadith_chapters')
                ->leftJoin('hadiths', 'hadith_chapters.id', '=', 'hadiths.chapter_id')
                ->where('hadith_chapters.collection_id', $collection->id)
                ->select('hadith_chapters.id', 'hadith_chapters.number', 'hadith_chapters.title')
                ->groupBy('hadith_chapters.id', 'hadith_chapters.number', 'hadith_chapters.title')
                ->havingRaw('COUNT(hadiths.id) = 0')
                ->get();

            if ($zeroChapters->isEmpty()) {
                $this->line('✅ No zero-count chapters.');
            } else {
                $this->warn("⚠️  {$zeroChapters->count()} chapters with 0 hadiths:");
                foreach ($zeroChapters as $ch) {
                    $this->line("   - Chapter #{$ch->number}: {$ch->title} (id: {$ch->id})");
                }
            }

            // Duplicate 'number' check (jaisa Ibn Majah me mila tha)
            $duplicateNumbers = collect(DB::table('hadiths')
                ->where('collection_id', $collection->id)
                ->select('number', DB::raw('COUNT(*) as duplicate_count'))
                ->groupBy('number')
                ->havingRaw('COUNT(*) > 1')
                ->get());

            if ($duplicateNumbers->isNotEmpty()) {
                $firstDuplicate = $duplicateNumbers->first();
                $this->warn("⚠️  {$duplicateNumbers->count()} duplicate 'number' values found (e.g. number {$firstDuplicate->number} appears {$firstDuplicate->duplicate_count} times)");
            }
        }

        $this->newLine();
        $this->info('Audit complete. Zero-count chapters aur duplicate numbers upar list ho gaye — inhe API se cross-check karke root cause nikalo.');
    }
}

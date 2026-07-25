<?php

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Models\HadithCollection;
use Illuminate\Console\Command;

class AuditHadithData extends Command
{
    protected $signature = 'hadith:audit';
    protected $description = 'Har collection ke hadith data ka read-only audit — missing translations, unmapped grades, orphans';

    public function handle(): int
    {
        $collections = HadithCollection::all();
        $rows = [];

        foreach ($collections as $collection) {
            $base = Hadith::where('collection_id', $collection->id);

            $missingArabic = (clone $base)
                ->where(fn($q) => $q->whereNull('arabic')->orWhere('arabic', ''))
                ->count();

            $missingEnglish = (clone $base)
                ->where(fn($q) => $q->whereNull('english')->orWhere('english', ''))
                ->count();

            $missingGrade = (clone $base)->whereNull('grade')->count();

            $unclassified = (clone $base)
                ->whereNotNull('grade')
                ->whereNull('reliability')
                ->whereNull('attribution_type')
                ->count();

            $orphanChapter = (clone $base)->whereNull('chapter_id')->count();

            $rows[] = [
                $collection->name,
                (clone $base)->count(),
                $missingArabic,
                $missingEnglish,
                $missingGrade,
                $unclassified,
                $orphanChapter,
            ];
        }

        $this->table(
            ['Collection', 'Total', 'No Arabic', 'No English', 'No Grade', 'Unclassified', 'Orphan Chapter'],
            $rows
        );

        return 0;
    }
}

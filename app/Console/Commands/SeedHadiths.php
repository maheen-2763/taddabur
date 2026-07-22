<?php

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use App\Services\HadithApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeedHadiths extends Command
{
    protected $signature = 'hadith:seed {collection}';
    protected $description = 'Fetch and seed hadiths for a given collection (defensive version)';

    private int $emptyTextSkipped = 0;
    private array $emptyTextNumbers = [];

    private int $chapterMismatchSkipped = 0;
    private array $chapterMismatchNumbers = [];

    public function handle(HadithApiService $api)
    {
        $slug = $this->argument('collection');

        $collection = HadithCollection::where('slug', $slug)->first();
        if (!$collection) {
            $this->error("Collection '{$slug}' nahi mila.");
            return 1;
        }

        $arabicData = $api->getEdition("ara-{$slug}");
        $englishData = $api->getEdition("eng-{$slug}");

        if (!$arabicData || !$englishData) {
            $this->error("API se data nahi mila.");
            return 1;
        }

        if (empty($arabicData['hadiths']) || !is_array($arabicData['hadiths'])) {
            $this->error("Arabic hadiths data khaali ya invalid hai. Seeding rok di.");
            return 1;
        }

        $sections = $arabicData['metadata']['sections'] ?? [];
        $sectionDetails = $arabicData['metadata']['section_details'] ?? [];

        if (empty($sectionDetails)) {
            $this->error("section_details khaali hai — chapter mapping possible nahi. Seeding rok di.");
            return 1;
        }

        ksort($sectionDetails, SORT_NUMERIC);

        $chapterModels = [];
        foreach ($sectionDetails as $num => $range) {
            if (!isset($range['hadithnumber_first'], $range['hadithnumber_last'])) {
                $this->warn("⚠️  Section {$num} ka range data missing hai, skip kar rahe hain.");
                continue;
            }

            if ($range['hadithnumber_first'] > $range['hadithnumber_last']) {
                $this->warn("⚠️  Section {$num} ka range invalid hai (first > last), skip kar rahe hain.");
                continue;
            }

            $title = trim($sections[$num] ?? '');
            $chapterModels[$num] = HadithChapter::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $num],
                ['title' => $title !== '' ? $title : "Chapter {$num}"]
            );
        }

        if (empty($chapterModels)) {
            $this->error("Koi bhi valid chapter nahi bana. Seeding rok di — data source check karo.");
            return 1;
        }

        $englishByNumber = collect($englishData['hadiths'] ?? [])->keyBy('hadithnumber');

        $totalCount = count($arabicData['hadiths']);
        $bar = $this->output->createProgressBar($totalCount);

        foreach ($arabicData['hadiths'] as $arabicHadith) {
            $number = $arabicHadith['hadithnumber'] ?? null;
            $arabicText = trim($arabicHadith['text'] ?? '');

            // ✅ Case A: number hi missing hai — ye asli corrupt entry hai
            if ($number === null) {
                $bar->advance();
                continue;
            }

            // ✅ Case B: Arabic text khaali hai (chapter-heading/placeholder jaisa,
            // genuine source-data gap — bug nahi). Alag se track karo.
            if ($arabicText === '') {
                $this->emptyTextSkipped++;
                $this->emptyTextNumbers[] = $number;
                $bar->advance();
                continue;
            }

            $englishHadith = $englishByNumber->get($number);
            $englishText = trim($englishHadith['text'] ?? '');

            if ($englishText === '') {
                Log::warning("Hadith #{$number} ({$slug}): English translation missing.");
            }

            $grade = $arabicHadith['grades'][0] ?? null;

            // Narrowest-range-wins: overlapping Book/Chapter ranges ko sahi handle karta hai
            $chapterId = null;
            $bestRangeSize = PHP_INT_MAX;

            foreach ($sectionDetails as $num => $range) {
                if (!isset($chapterModels[$num])) {
                    continue;
                }

                if ($number >= $range['hadithnumber_first'] && $number <= $range['hadithnumber_last']) {
                    $rangeSize = $range['hadithnumber_last'] - $range['hadithnumber_first'];

                    if ($rangeSize < $bestRangeSize) {
                        $bestRangeSize = $rangeSize;
                        $chapterId = $chapterModels[$num]->id;
                    }
                }
            }

            // ✅ Case C: Text valid hai lekin koi chapter range match nahi hua —
            // ye asli investigate-karne-wala issue hai, alag se track karo
            if ($chapterId === null) {
                $this->chapterMismatchSkipped++;
                $this->chapterMismatchNumbers[] = $number;
                Log::warning("Hadith #{$number} ({$slug}): koi chapter match nahi hua (valid text ke bawajood).");
            }

            Hadith::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $number],
                [
                    'chapter_id' => $chapterId,
                    'arabic' => $arabicText,
                    'english' => $englishText !== '' ? $englishText : null,
                    'grade' => $grade['grade'] ?? null,
                    'grade_source' => $grade['name'] ?? null,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $finalCount = Hadith::where('collection_id', $collection->id)->count();
        $collection->update(['total_hadith' => $finalCount]);

        $this->info("✅ {$slug} seeded! Total hadiths in DB: {$finalCount}");

        // ✅ Ab dono clearly separate dikhenge — confusion nahi hoga
        if ($this->emptyTextSkipped > 0) {
            $this->line("ℹ️  {$this->emptyTextSkipped} hadiths ka Arabic text source data me hi khaali tha (genuine gap, code bug nahi).");
            $this->line("   Numbers: " . implode(', ', array_slice($this->emptyTextNumbers, 0, 20)) .
                ($this->emptyTextSkipped > 20 ? ' ...' : ''));
        }

        if ($this->chapterMismatchSkipped > 0) {
            $this->warn("⚠️  {$this->chapterMismatchSkipped} hadiths ka valid text hai lekin chapter match nahi hua (INVESTIGATE karo).");
            $this->warn("   Numbers: " . implode(', ', array_slice($this->chapterMismatchNumbers, 0, 20)) .
                ($this->chapterMismatchSkipped > 20 ? ' ... (poori list ke liye storage/logs/laravel.log dekho)' : ''));
        }

        if ($this->emptyTextSkipped === 0 && $this->chapterMismatchSkipped === 0) {
            $this->info("✅ Koi bhi issue nahi mila, sab kuch clean hai.");
        }

        return 0;
    }
}

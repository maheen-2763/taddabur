<?php

namespace App\Console\Commands;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use App\Services\HadithApiService;
use Illuminate\Console\Command;

class SeedHadiths extends Command
{
    protected $signature = 'hadith:seed {collection}';
    protected $description = 'Fetch and seed hadiths for a given collection';

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

        // ✅ metadata edition file ke andar hi hai — info.json ki zaroorat nahi
        $sections = $arabicData['metadata']['sections'] ?? [];
        $sectionDetails = $arabicData['metadata']['section_details'] ?? [];

        // ✅ Chapters pehले seed karo
        $chapterModels = [];
        foreach ($sectionDetails as $num => $range) {
            if ($num == 0) continue; // section 0 khali hoती hai
            $chapterModels[$num] = HadithChapter::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $num],
                ['title' => $sections[$num] ?? 'Untitled']
            );
        }

        $englishByNumber = collect($englishData['hadiths'])->keyBy('hadithnumber');
        $bar = $this->output->createProgressBar(count($arabicData['hadiths']));

        foreach ($arabicData['hadiths'] as $arabicHadith) {
            $number = $arabicHadith['hadithnumber'];
            $englishHadith = $englishByNumber->get($number);
            $grade = $arabicHadith['grades'][0] ?? null;

            $chapterId = null;
            foreach ($sectionDetails as $num => $range) {
                if ($num == 0) continue;
                if ($number >= $range['hadithnumber_first'] && $number <= $range['hadithnumber_last']) {
                    $chapterId = $chapterModels[$num]->id ?? null;
                    break;
                }
            }

            Hadith::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $number],
                [
                    'chapter_id' => $chapterId,
                    'arabic' => $arabicHadith['text'],
                    'english' => $englishHadith['text'] ?? null,
                    'grade' => $grade['grade'] ?? null,
                    'grade_source' => $grade['name'] ?? null,
                    'total_hadith' => Hadith::where('collection_id', $collection->id)->count()
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$slug} seeded with chapters!");
        return 0;
    }
}

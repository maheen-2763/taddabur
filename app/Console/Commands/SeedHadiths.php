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

        $info = $api->getInfo();
        $sections = $info[$slug]['metadata']['sections'] ?? [];
        $sectionDetails = $info[$slug]['metadata']['section_details'] ?? [];

        // ✅ Chapters pehले seed karo
        $chapterModels = [];
        foreach ($sectionDetails as $num => $range) {
            if ($num == 0) continue; // section 0 khali hoती hai, skip
            $chapterModels[$num] = HadithChapter::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $num],
                ['title' => $sections[$num] ?? 'Untitled']
            );
        }

        // ✅ Ab hadiths seed karo, chapter assign karte hue
        $arabicData = $api->getEdition("ara-{$slug}");
        $englishData = $api->getEdition("eng-{$slug}");
        $englishByNumber = collect($englishData['hadiths'])->keyBy('hadithnumber');

        $bar = $this->output->createProgressBar(count($arabicData['hadiths']));

        foreach ($arabicData['hadiths'] as $arabicHadith) {
            $number = $arabicHadith['hadithnumber'];
            $englishHadith = $englishByNumber->get($number);
            $grade = $arabicHadith['grades'][0] ?? null;

            // ✅ Ye number kis chapter range mein aata hai, dhundo
            $chapterId = null;
            foreach ($sectionDetails as $num => $range) {
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

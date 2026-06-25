<?php

// database/seeders/HadithSeeder.php
namespace Database\Seeders;

use App\Models\HadithCollection;
use App\Models\HadithChapter;
use App\Models\Hadith;
use App\Services\HadithApiService;
use Illuminate\Database\Seeder;

class HadithSeeder extends Seeder
{
    public function run(): void
    {

        if (empty(config('services.sunnah.key'))) {
            $this->command->warn('SUNNAH_API_KEY not set — skipping hadith seed. Add your key to .env first.');
            return;
        }
        $api = new HadithApiService();

        $collection = HadithCollection::updateOrCreate(
            ['slug' => 'bukhari'],
            [
                'name' => 'Sahih Bukhari',
                'arabic_name' => 'صحيح البخاري',
                'scholar' => 'Imam Muhammad al-Bukhari',
                'period' => '9th century CE',
            ]
        );

        $chapters = $api->fetchChapters('bukhari');

        foreach ($chapters as $chapterData) {
            $chapter = HadithChapter::updateOrCreate(
                ['collection_id' => $collection->id, 'number' => $chapterData['bookNumber']],
                ['title' => $chapterData['book'][0]['name'] ?? 'Untitled']
            );

            $hadiths = $api->fetchHadiths('bukhari', $chapterData['bookNumber']);

            foreach ($hadiths['data'] ?? [] as $h) {
                $arabicEntry = collect($h['hadith'])->firstWhere('lang', 'ar');
                $englishEntry = collect($h['hadith'])->firstWhere('lang', 'en');

                Hadith::updateOrCreate(
                    ['collection_id' => $collection->id, 'number' => $h['hadithNumber']],
                    [
                        'chapter_id' => $chapter->id,
                        'arabic' => $arabicEntry['body'] ?? '',
                        'english' => $englishEntry['body'] ?? '',
                        'grade' => $englishEntry['grades'][0]['grade'] ?? null,
                        'grade_source' => $englishEntry['grades'][0]['graded_by'] ?? null,
                    ]
                );
            }


            sleep(1); // rate limit safety
        }

        $this->command->info('Bukhari seeded successfully.');
    }
}

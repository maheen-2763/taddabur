<?php

namespace App\Console\Commands;

use App\Models\AllahName;
use App\Services\AllahNameService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportAllahNames extends Command
{
    protected $signature   = 'import:allah-names';
    protected $description = 'Import the 99 Names of Allah from Aladhan API';

    public function handle(AllahNameService $service): int
    {
        try {
            $names = $service->fetchAll();
        } catch (\Exception $e) {
            $this->error('Failed to fetch names: ' . $e->getMessage());
            $this->warn('Check your internet connection or API status.');
            return self::FAILURE;
        }

        if (empty($names)) {
            $this->error('API returned no data.');
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar(count($names));
        $bar->start();

        foreach ($names as $index => $name) {

            $transliteration = $name['transliteration'] ?? '';
            $meaning         = $name['en']['meaning'] ?? null;

            AllahName::updateOrCreate(
                ['position' => $index + 1],
                [
                    'name_ar'         => $name['name'] ?? '',
                    'transliteration' => $transliteration,
                    'english_name'    => $meaning,
                    'meaning'         => $meaning,
                    // ✅ slug generated from transliteration
                    'slug'            => Str::slug($transliteration),
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->info('✅ 99 Names imported successfully.');
        $this->info('   Total in database: ' . AllahName::count());

        return self::SUCCESS;
    }
}

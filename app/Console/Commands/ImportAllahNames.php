<?php

namespace App\Console\Commands;

use App\Models\AllahName;
use App\Services\AllahNameService;
use Illuminate\Console\Command;

class ImportAllahNames extends Command
{
    protected $signature = 'import:allah-names';

    protected $description = 'Import Allah Names';

    public function handle(AllahNameService $service)
    {
        $names = $service->fetchAll();

        foreach ($names as $index => $name) {

            AllahName::updateOrCreate(
                [
                    'position' => $index + 1,
                ],
                [
                    'name_ar' => $name['name'] ?? '',
                    'transliteration' => $name['transliteration'] ?? '',
                    'english_name' => $name['en']['meaning'] ?? null,
                    'meaning' => $name['en']['meaning'] ?? null,
                ]
            );
        }

        $this->info('99 Names imported successfully.');

        return self::SUCCESS;
    }
}

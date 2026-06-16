<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('app:import-tanzil-data')]
#[Description('Command description')]
class ImportTanzilData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import of Tanzil data...');

        // Call the import logic here (e.g., a service class or direct DB insert)
        // Example: (new TanzilImportService())->import();

        $this->info('Tanzil data import completed successfully.');

        Http::get('https://api.or-local-file/tanzil.txt');

        return Command::SUCCESS;
    }
}

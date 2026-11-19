<?php

namespace App\Console\Commands;

use App\Helpers\ArabicHelper;
use App\Models\Hadith; // apne model ka naam
use Illuminate\Console\Command;

class CompareHadith extends Command
{
    protected $signature = 'hadith:compare {slug} {number}';
    protected $description = 'Taddabur ka Arabic sunnah.com ke Arabic se milata hai';

    public function handle(): int
    {
        $c = \App\Models\HadithCollection::where('slug', $this->argument('slug'))->first();
        $hadith = $c
            ? \App\Models\Hadith::where('collection_id', $c->id)->where('number', $this->argument('number'))->first()
            : null;

        if (!$hadith) {
            $this->error('Hadith nahi mila.');
            return self::FAILURE;
        }

        $path = storage_path('app/compare/sunnah.txt');
        if (!file_exists($path)) {
            $this->error('Pehle storage/app/compare/sunnah.txt banayein.');
            return self::FAILURE;
        }

        $a = ArabicHelper::normalizeForCompare($hadith->arabic); // column ka naam badlein
        $b = ArabicHelper::normalizeForCompare(file_get_contents($path));

        if ($a === $b) {
            $this->info('MATCH: alfaaz same hain. Harakat aankh se dekhein.');
            return self::SUCCESS;
        }

        $this->error('MISMATCH');
        $this->line('Sirf Taddabur mein: ' . implode(' ', array_diff(explode(' ', $a), explode(' ', $b))));
        $this->line('Sirf sunnah.com mein: ' . implode(' ', array_diff(explode(' ', $b), explode(' ', $a))));

        return self::FAILURE;
    }
}

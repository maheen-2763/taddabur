<?php

namespace App\Console\Commands;

use App\Models\DailyContent;
use App\Models\Hadith;
use App\Models\HadithCollection;
use Illuminate\Console\Command;

class GenerateDailyHadith extends Command
{
    protected $signature = 'daily:generate-hadith';
    protected $description = 'Pick next hadith in sequence (Bukhari then Muslim) and schedule it as today\'s Daily Hadith.';

    // ✅ Sirf yeh do collections — dono 100% authentic, koi grade-filter ki zaroorat nahi
    private const ALLOWED_COLLECTIONS = ['Sahih Bukhari', 'Sahih Muslim'];

    public function handle(): int
    {
        $alreadyExists = DailyContent::where('type', 'hadith')->today()->exists();

        if ($alreadyExists) {
            $this->info('Today\'s hadith already exists — skipping.');
            return self::SUCCESS;
        }

        $nextHadith = $this->determineNextHadith();

        if (!$nextHadith) {
            $this->error('Could not determine next hadith — check collection/chapter data.');
            return self::FAILURE;
        }

        DailyContent::create([
            'type'          => 'hadith',
            'hadith_id'     => $nextHadith->id,
            'scheduled_for' => today(),
            'reflection'    => null,
            'is_sent'       => false,
        ]);

        $this->info("✅ Daily hadith set: {$nextHadith->collection->name} #{$nextHadith->number}");

        return self::SUCCESS;
    }

    private function determineNextHadith(): ?Hadith
    {
        $lastContent = DailyContent::where('type', 'hadith')
            ->whereNotNull('hadith_id')
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->with('hadith.collection')
            ->first();

        // ✅ Pehli baar — Bukhari se shuru karo
        if (!$lastContent || !$lastContent->hadith) {
            return $this->firstHadithInCollection(self::ALLOWED_COLLECTIONS[0]);
        }

        $lastHadith     = $lastContent->hadith;
        $lastCollection = $lastHadith->collection;

        // Case 1: Same collection me agla hadith
        $next = Hadith::where('collection_id', $lastHadith->collection_id)
            ->where('id', '>', $lastHadith->id)
            ->orderBy('id')
            ->first();

        if ($next) {
            return $next;
        }

        // Case 2: Collection khatam — agli allowed collection pe jao (wrap-around bhi)
        $currentIndex = array_search($lastCollection->name, self::ALLOWED_COLLECTIONS);
        $nextIndex    = ($currentIndex + 1) % count(self::ALLOWED_COLLECTIONS);

        return $this->firstHadithInCollection(self::ALLOWED_COLLECTIONS[$nextIndex]);
    }

    private function firstHadithInCollection(string $collectionName): ?Hadith
    {
        $collection = HadithCollection::where('name', $collectionName)->first();

        if (!$collection) {
            return null;
        }

        return Hadith::where('collection_id', $collection->id)
            ->orderBy('id')
            ->first();
    }
}

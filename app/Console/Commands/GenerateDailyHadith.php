<?php

namespace App\Console\Commands;

use App\Models\DailyContent;
use App\Models\Hadith;
use App\Models\HadithCollection;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class GenerateDailyHadith extends Command
{
    protected $signature = 'daily:generate-hadith';
    protected $description = "Pick next eligible hadith in sequence (Bukhari then Muslim) as today's Daily Hadith.";

    // Order yahi rahega: pehle Bukhari, phir Muslim
    private const ALLOWED_COLLECTIONS = ['Sahih Bukhari', 'Sahih Muslim'];

    public function handle(): int
    {
        if (DailyContent::where('type', 'hadith')->today()->exists()) {
            $this->info("Today's hadith already exists, skipping.");

            return self::SUCCESS;
        }

        $nextHadith = $this->determineNextHadith();

        if (! $nextHadith) {
            $this->error('Could not determine next hadith. Check collection/chapter data.');

            return self::FAILURE;
        }

        DailyContent::create([
            'type'          => 'hadith',
            'hadith_id'     => $nextHadith->id,
            'scheduled_for' => today(),
            'reflection'    => null,
            'is_sent'       => false,
        ]);

        $this->info("Daily hadith set: {$nextHadith->collection->name} #{$nextHadith->number}");

        return self::SUCCESS;
    }

    private function determineNextHadith(): ?Hadith
    {
        $idsByName = HadithCollection::whereIn('name', self::ALLOWED_COLLECTIONS)->pluck('id', 'name');

        // Collection ids allowed order mein
        $ordered = collect(self::ALLOWED_COLLECTIONS)
            ->map(fn(string $name) => $idsByName[$name] ?? null)
            ->filter()
            ->values();

        if ($ordered->isEmpty()) {
            return null;
        }

        // Sirf auto rows (reflection NULL) sequence tay karti hain
        $lastAuto = DailyContent::where('type', 'hadith')
            ->whereNotNull('hadith_id')
            ->whereNull('reflection')
            ->whereHas('hadith', fn($q) => $q->whereIn('collection_id', $ordered))
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->with('hadith')
            ->first();

        if (! $lastAuto?->hadith) {
            return $this->firstEligible($ordered->first());
        }

        $last = $lastAuto->hadith;

        $next = $this->eligible()
            ->where('collection_id', $last->collection_id)
            ->where('id', '>', $last->id)
            ->orderBy('id')
            ->first();

        if ($next) {
            return $next;
        }

        // Collection khatam: agli collection (wrap-around)
        $index = $ordered->search($last->collection_id);

        return $this->firstEligible($ordered[($index + 1) % $ordered->count()]);
    }

    private function firstEligible(int $collectionId): ?Hadith
    {
        return $this->eligible()
            ->where('collection_id', $collectionId)
            ->orderBy('id')
            ->first();
    }

    /** Daily pick mein sirf poori aur reviewed hadith aani chahiye. */
    private function eligible(): Builder
    {
        return Hadith::query()
            ->whereNotNull('english')
            ->whereRaw("TRIM(english) != ''")
            ->where(fn($q) => $q->where('needs_review', false)->orWhereNull('needs_review'))
            ->where(fn($q) => $q->where('translation_incomplete', false)->orWhereNull('translation_incomplete'));
    }
}

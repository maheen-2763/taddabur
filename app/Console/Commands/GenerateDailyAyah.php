<?php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\DailyContent;
use App\Models\Surah;
use Illuminate\Console\Command;

class GenerateDailyAyah extends Command
{
    protected $signature = 'daily:generate-ayah';
    protected $description = 'Pick the next ayah in revelation-order sequence and schedule it as today\'s Daily Ayah.';

    public function handle(): int
    {
        // ✅ Idempotent — agar aaj ke liye already ban chuka hai, dobara mat banao
        $alreadyExists = DailyContent::where('type', 'ayah')->today()->exists();

        if ($alreadyExists) {
            $this->info('Today\'s ayah already exists — skipping.');
            return self::SUCCESS;
        }

        $nextAyah = $this->determineNextAyah();

        if (!$nextAyah) {
            $this->error('Could not determine next ayah — check revelation_order data.');
            return self::FAILURE;
        }

        DailyContent::create([
            'type'          => 'ayah',
            'ayah_id'       => $nextAyah->id,
            'scheduled_for' => today(),
            'reflection'    => null,
            'is_sent'       => false,
        ]);

        $this->info("✅ Daily ayah set: Surah {$nextAyah->surah->number}:{$nextAyah->number}");

        return self::SUCCESS;
    }

    private function determineNextAyah(): ?Ayah
    {
        // Pichli baar jo ayah schedule hui thi, uski based pe "agli" nikalo
        $lastContent = DailyContent::where('type', 'ayah')
            ->whereNotNull('ayah_id')
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->with('ayah.surah')
            ->first();

        // ✅ Pehli baar chal raha hai — sabse pehli revelation-order surah se shuru karo
        if (!$lastContent || !$lastContent->ayah) {
            $firstSurah = Surah::where('revelation_order', 1)->first();
            return Ayah::where('surah_id', $firstSurah->id)
                ->orderBy('number')
                ->first();
        }

        $lastAyah  = $lastContent->ayah;
        $lastSurah = $lastAyah->surah;

        // Case 1: Same surah mein agli ayah maujood hai
        $nextInSameSurah = Ayah::where('surah_id', $lastSurah->id)
            ->where('number', $lastAyah->number + 1)
            ->first();

        if ($nextInSameSurah) {
            return $nextInSameSurah;
        }

        // Case 2: Surah khatam ho gayi — revelation-order mein agli surah pe jao
        $nextRevelationOrder = $lastSurah->revelation_order + 1;

        // ✅ Wrap-around — 114 ke baad wapas 1 pe (cycle complete, phir se shuru)
        if ($nextRevelationOrder > 114) {
            $nextRevelationOrder = 1;
        }

        $nextSurah = Surah::where('revelation_order', $nextRevelationOrder)->first();

        if (!$nextSurah) {
            return null;
        }

        return Ayah::where('surah_id', $nextSurah->id)
            ->orderBy('number')
            ->first();
    }
}

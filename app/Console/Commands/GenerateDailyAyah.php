<?php

namespace App\Console\Commands;

use App\Models\Ayah;
use App\Models\DailyContent;
use App\Models\Surah;
use Illuminate\Console\Command;

class GenerateDailyAyah extends Command
{
    protected $signature = 'daily:generate-ayah';
    protected $description = "Pick the next ayah in revelation-order sequence and schedule it as today's Daily Ayah.";

    public function handle(): int
    {
        // Idempotent: aaj ki row (auto ya admin override) pehle se hai to kuch mat karo
        if (DailyContent::where('type', 'ayah')->today()->exists()) {
            $this->info("Today's ayah already exists, skipping.");

            return self::SUCCESS;
        }

        $nextAyah = $this->determineNextAyah();

        if (! $nextAyah) {
            $this->error('Could not determine next ayah. Check revelation_order data.');

            return self::FAILURE;
        }

        DailyContent::create([
            'type'          => 'ayah',
            'ayah_id'       => $nextAyah->id,
            'scheduled_for' => today(),
            'reflection'    => null,
            'is_sent'       => false,
        ]);

        $this->info("Daily ayah set: Surah {$nextAyah->surah->number}:{$nextAyah->number}");

        return self::SUCCESS;
    }

    private function determineNextAyah(): ?Ayah
    {
        // Sirf auto-generated rows (reflection NULL) sequence tay karti hain,
        // taaki admin ka override sequence na bigaade.
        $lastAuto = DailyContent::where('type', 'ayah')
            ->whereNotNull('ayah_id')
            ->whereNull('reflection')
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->with('ayah.surah')
            ->first();

        if (! $lastAuto?->ayah) {
            return $this->firstAyahOfRevelationOrder(1);
        }

        $lastAyah  = $lastAuto->ayah;
        $lastSurah = $lastAyah->surah;

        $nextInSurah = Ayah::where('surah_id', $lastSurah->id)
            ->where('number', $lastAyah->number + 1)
            ->first();

        if ($nextInSurah) {
            return $nextInSurah;
        }

        // Surah khatam: revelation order mein agli surah (114 ke baad wapas 1)
        $nextOrder = $lastSurah->revelation_order >= 114 ? 1 : $lastSurah->revelation_order + 1;

        return $this->firstAyahOfRevelationOrder($nextOrder);
    }

    private function firstAyahOfRevelationOrder(int $order): ?Ayah
    {
        $surah = Surah::where('revelation_order', $order)->first();

        return $surah
            ? Ayah::where('surah_id', $surah->id)->orderBy('number')->first()
            : null;
    }
}

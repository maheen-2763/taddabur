<?php

namespace App\Console\Commands;

use App\Models\Translation;
use App\Models\Ayah;
use Illuminate\Console\Command;

class VerifyTranslations extends Command
{
    protected $signature = 'quran:verify-translations';
    protected $description = 'Check coverage aur missing ayahs for all active translations';

    public function handle()
    {
        $translations = Translation::where('is_active', true)->get();

        $this->info('Translation Coverage Report');
        $this->line('----------------------------');

        foreach ($translations as $t) {
            $covered = Ayah::whereHas(
                'translations',
                fn($q) =>
                $q->where('translation_id', $t->id)
            )->count();

            $missing = 6236 - $covered;
            $status = $missing === 0 ? '✅ COMPLETE' : "⚠️ MISSING {$missing}";

            $this->line("{$t->name} (ID: {$t->id}): {$covered}/6236 — {$status}");

            // Agar missing hai toh which surahs specifically, wo bhi dikha do
            if ($missing > 0) {
                $missingAyahs = Ayah::whereDoesntHave(
                    'translations',
                    fn($q) =>
                    $q->where('translation_id', $t->id)
                )->pluck('surah_number')->unique();

                $this->line("   → Missing in Surahs: " . $missingAyahs->implode(', '));
            }
        }

        $this->info('Done. Verify complete.');
        return Command::SUCCESS;
    }
}

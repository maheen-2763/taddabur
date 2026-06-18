<?php
// app/Console/Commands/NormalizeQuranReferences.php
//
// ONE-TIME command. Converts existing quran_references from formats like:
//   "Al-Baqarah 2:30"
//   "Al-Hijr 15:28-29"
// into clean numeric arrays like:
//   "2:30"
//   "15:28", "15:29"
//
// Run with: php artisan taddabur:normalize-refs
// Add --dry-run to preview changes without saving.

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeQuranReferences extends Command
{
    protected $signature = 'taddabur:normalize-refs {--dry-run}';
    protected $description = 'Convert quran_references to clean surah:ayah numeric format, expanding ranges';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $chapters = DB::table('story_chapters')->get();

        foreach ($chapters as $chapter) {
            $original = json_decode($chapter->quran_references ?? '[]', true) ?? [];
            $normalized = [];

            foreach ($original as $ref) {
                $normalized = array_merge($normalized, $this->parseReference($ref));
            }

            $normalized = array_values(array_unique($normalized));

            $this->line("Chapter #{$chapter->id} ({$chapter->title})");
            $this->line("  Before: " . json_encode($original));
            $this->line("  After:  " . json_encode($normalized));
            $this->line('');

            if (!$dryRun) {
                DB::table('story_chapters')
                    ->where('id', $chapter->id)
                    ->update(['quran_references' => json_encode($normalized)]);
            }
        }

        if ($dryRun) {
            $this->warn('DRY RUN — no changes saved. Remove --dry-run to apply.');
        } else {
            $this->info('✅ All references normalized and saved.');
        }

        return self::SUCCESS;
    }

    /**
     * Parses formats like:
     *   "Al-Baqarah 2:30"        → ["2:30"]
     *   "Al-Hijr 15:28-29"       → ["15:28", "15:29"]
     *   "2:30"                   → ["2:30"]  (already clean)
     *   "Yunus 10:98"            → ["10:98"]
     */
    private function parseReference(string $ref): array
    {
        // Strip any leading surah name text, keep only "NN:NN" or "NN:NN-NN"
        if (!preg_match('/(\d+):(\d+)(?:-(\d+))?/', $ref, $m)) {
            $this->error("  ⚠️  Could not parse: \"{$ref}\" — skipped, needs manual fix");
            return [];
        }

        $surah = (int) $m[1];
        $startAyah = (int) $m[2];
        $endAyah = isset($m[3]) ? (int) $m[3] : $startAyah;

        $result = [];
        for ($a = $startAyah; $a <= $endAyah; $a++) {
            $result[] = "{$surah}:{$a}";
        }

        return $result;
    }
}

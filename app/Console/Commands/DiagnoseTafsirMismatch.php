<?php
// app/Console/Commands/DiagnoseTafsirMismatch.php
//
// Run with: php artisan taddabur:diagnose-tafsir
//
// READ-ONLY. Scans every row in ayah_tafsirs, extracts the surah name
// the TEXT claims to belong to (via "This is the end of the Tafsir of
// Surat X"), and compares it against the surah_name column on that row.
// Reports every mismatch with row id, so we know the exact scope before
// writing any fix.

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseTafsirMismatch extends Command
{
    protected $signature = 'taddabur:diagnose-tafsir';
    protected $description = 'Find ayah_tafsirs rows where surah_name/surah_number does not match the surah named inside the text itself';

    public function handle(): int
    {
        $rows = DB::table('ayah_tafsirs')
            ->select('id', 'tafsir_row_id', 'surah_number', 'surah_name', 'ayah_number', 'text')
            ->orderBy('id')
            ->get();

        $this->info("Total rows in ayah_tafsirs: {$rows->count()}");

        $mismatches = [];
        $noClosingLine = 0;

        foreach ($rows as $r) {
            if (!preg_match('/Tafsir of Surat\s+([A-Za-z\-\' ]+)/u', (string) $r->text, $m)) {
                $noClosingLine++;
                continue;
            }

            $textSaysSurah = trim($m[1]);
            $labeled = trim((string) $r->surah_name);

            // Normalize: strip "Al-", lowercase, strip spaces/hyphens for comparison
            $normalize = fn($s) => strtolower(str_replace(['al-', 'al ', '-', ' '], '', strtolower($s)));

            if ($normalize($textSaysSurah) !== $normalize($labeled)) {
                $mismatches[] = [
                    'id'              => $r->id,
                    'tafsir_row_id'   => $r->tafsir_row_id,
                    'labeled_number'  => $r->surah_number,
                    'labeled_name'    => $labeled,
                    'ayah_number'     => $r->ayah_number,
                    'text_says'       => $textSaysSurah,
                ];
            }
        }

        $this->info("Rows with no 'end of Tafsir of Surat X' marker (can't auto-check): {$noClosingLine}");
        $this->error('Mismatches found: ' . count($mismatches));

        foreach ($mismatches as $row) {
            $this->line(
                "id={$row['id']} (tafsir_row_id={$row['tafsir_row_id']}) | labeled: {$row['labeled_name']} (#{$row['labeled_number']}, ayah {$row['ayah_number']}) | text actually says: {$row['text_says']}"
            );
        }

        // Save full list to storage so we can review/use it for the fix step
        file_put_contents(
            storage_path('app/tafsir_mismatches.json'),
            json_encode($mismatches, JSON_PRETTY_PRINT)
        );
        $this->info('Full list saved to storage/app/tafsir_mismatches.json');

        return self::SUCCESS;
    }
}

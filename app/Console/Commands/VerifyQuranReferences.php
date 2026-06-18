<?php
// app/Console/Commands/VerifyQuranReferences.php
//
// Run with: php artisan taddabur:verify-refs
//
// Scans all story_chapters' quran_references and confirms every
// Surah:Ayah exists in your own verified ayahs table.

namespace App\Console\Commands;

use App\Models\Surah;
use App\Models\Ayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyQuranReferences extends Command
{
    protected $signature = 'taddabur:verify-refs';
    protected $description = 'Verify every Quran reference used in story chapters exists in the ayahs table';

    public function handle(): int
    {
        $missing = [];
        $unparsable = [];
        $checked = 0;

        $chapters = DB::table('story_chapters')->get();

        foreach ($chapters as $chapter) {
            $refs = json_decode($chapter->quran_references ?? '[]', true) ?? [];



            foreach ($refs as $ref) {
                // Expect clean "surah:ayah" format only, e.g. "2:30"
                if (!preg_match('/^(\d+):(\d+)$/', trim($ref), $m)) {
                    $unparsable[] = "Chapter #{$chapter->id} ({$chapter->title}) → \"{$ref}\" is not in clean surah:ayah format";
                    continue;
                }

                $checked++;
                $surahNum = (int) $m[1];
                $ayahNum  = (int) $m[2];

                $surah = Surah::where('number', $surahNum)->first();
                if (!$surah) {
                    $missing[] = "Chapter #{$chapter->id} ({$chapter->title}) → Surah {$surahNum} not found";
                    continue;
                }

                $ayah = Ayah::where('surah_id', $surah->id)->where('number', $ayahNum)->first();
                if (!$ayah) {
                    $missing[] = "Chapter #{$chapter->id} ({$chapter->title}) → Ayah {$surahNum}:{$ayahNum} not found";
                }
            }
        }

        $this->info("Checked {$checked} references across {$chapters->count()} chapters.");

        if (!empty($unparsable)) {
            $this->error('❌ References not in clean format — run taddabur:normalize-refs first:');
            foreach ($unparsable as $line) {
                $this->line(" - {$line}");
            }
        }

        if (!empty($missing)) {
            $this->error('❌ Missing references found — DO NOT publish until fixed:');
            foreach ($missing as $line) {
                $this->line(" - {$line}");
            }
        }

        if (empty($missing) && empty($unparsable)) {
            $this->info('✅ All Quran references verified successfully. Safe to publish.');
            return self::SUCCESS;
        }

        return self::FAILURE;
    }
}

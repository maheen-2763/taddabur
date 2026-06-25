<?php
// app/Console/Commands/VerifyQuranReferences.php
//
// Run with: php artisan taddabur:verify-refs
//
// Scans all story_chapters' quran_references and confirms every
// Surah:Ayah exists in your own verified ayahs table AND is actually
// quoted in the chapter content. Also recalculates read_time_minutes.

namespace App\Console\Commands;

use App\Models\Surah;
use App\Models\Ayah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyQuranReferences extends Command
{
    protected $signature = 'taddabur:verify-refs {--fix-read-time : Update read_time_minutes for each chapter}';
    protected $description = 'Verify every Quran reference used in story chapters exists in the ayahs table and is actually quoted in the content';

    /** Words per minute used for reading time estimate */
    private const WPM = 200;

    public function handle(): int
    {
        $missing = [];
        $unparsable = [];
        $notQuoted = [];
        $checked = 0;

        $chapters = DB::table('story_chapters')->get();

        foreach ($chapters as $chapter) {
            $refs = json_decode($chapter->quran_references ?? '[]', true) ?? [];

            // Duplicate ref check
            $duplicates = array_filter(array_count_values($refs), fn($count) => $count > 1);
            foreach ($duplicates as $ref => $count) {
                $this->warn("⚠️  Chapter #{$chapter->id} ({$chapter->title}) → \"{$ref}\" listed {$count} times");
            }

            $normalizedContent = $this->normalizeArabic($chapter->content ?? '');

            foreach ($refs as $ref) {
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
                    continue;
                }

                // Content-integrity check: is the Arabic text actually quoted?
                $normalizedAyah = $this->normalizeArabic($ayah->text_arabic);
                if ($normalizedAyah !== '' && !str_contains($normalizedContent, $normalizedAyah)) {
                    $notQuoted[] = "Chapter #{$chapter->id} ({$chapter->title}) → {$ref} is listed in quran_references but its Arabic text was NOT found in the chapter content";
                }
            }

            // Reading time calculation
            $plainText = trim(strip_tags($chapter->content ?? ''));
            $wordCount = $plainText === '' ? 0 : count(preg_split('/\s+/', $plainText));
            $readTime  = max(1, (int) ceil($wordCount / self::WPM));

            $currentReadTime = $chapter->read_time_minutes ?? null;
            if ($currentReadTime !== $readTime) {
                $this->line("⏱  Chapter #{$chapter->id} ({$chapter->title}): {$wordCount} words → {$readTime} min (currently stored: " . ($currentReadTime ?? 'null') . ')');

                if ($this->option('fix-read-time')) {
                    DB::table('story_chapters')->where('id', $chapter->id)->update(['read_time_minutes' => $readTime]);
                    $this->info("   ↳ Updated read_time_minutes to {$readTime}");
                }
            }
        }

        $this->info("Checked {$checked} references across {$chapters->count()} chapters.");

        if (!empty($unparsable)) {
            $this->error('❌ References not in clean format:');
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

        if (!empty($notQuoted)) {
            $this->error('❌ References listed but Arabic text not found in content — DO NOT publish until fixed:');
            foreach ($notQuoted as $line) {
                $this->line(" - {$line}");
            }
        }

        if (empty($missing) && empty($unparsable) && empty($notQuoted)) {
            $this->info('✅ All Quran references verified and properly quoted. Safe to publish.');
            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    /**
     * Strip Arabic diacritics (tashkeel), tatweel, and normalize whitespace
     * so DB-stored Arabic and HTML-pasted Arabic can be compared reliably.
     */
    private function normalizeArabic(string $text): string
    {
        // Remove HTML tags first (in case we're normalizing chapter content)
        $text = strip_tags($text);

        // Strip Arabic diacritics (tashkeel) — Unicode ranges for combining marks
        $text = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06DC}\x{06DF}-\x{06E8}\x{06EA}-\x{06ED}]/u', '', $text);

        // Strip tatweel (the elongation character ـ)
        $text = preg_replace('/\x{0640}/u', '', $text);

        // Normalize alef variants to bare alef for looser matching
        $text = preg_replace('/[\x{0622}\x{0623}\x{0625}]/u', "\x{0627}", $text);

        // Collapse all whitespace
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}

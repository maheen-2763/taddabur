<?php

namespace App\Console\Commands;

use App\Models\StoryChapter;
use Illuminate\Console\Command;

class ExtractVerifyMarkers extends Command
{
    protected $signature = 'stories:extract-verify {--apply} {--strip} {--export : checklist storage/app/verify-list.txt me likho}';
    protected $description = 'List or move [VERIFY: ...] markers out of chapter content';

    public function handle(): int
    {
        if ($this->option('strip') && !$this->option('apply')) {
            $this->error('Use --apply --strip together so no marker text is lost.');
            return self::FAILURE;
        }

        $rows = [];

        StoryChapter::where('content', 'like', '%[VERIFY%')->each(function ($chapter) use (&$rows) {
            preg_match_all('/\[VERIFY:?\s*(.*?)\]/s', $chapter->content, $m);
            $found = array_map(fn($t) => trim(preg_replace('/\s+/', ' ', $t)), $m[1]);

            foreach ($found as $text) {
                $rows[] = [$chapter->story_id, $chapter->order, $chapter->title, $text];
            }

            if ($this->option('apply')) {
                $sources = $chapter->sources ?? [];
                $sources['pending'] = array_values(array_unique(array_merge($sources['pending'] ?? [], $found)));
                $chapter->sources = $sources;

                if ($this->option('strip')) {
                    $chapter->content = trim(preg_replace('/\s*\[VERIFY:?\s*.*?\]/s', '', $chapter->content));
                }

                $chapter->save();
            }

            if ($this->option('export')) {
                $this->exportChecklist($rows);
            }
        });

        $this->table(['Story ID', '#', 'Chapter', 'Marker'], $rows);
        $this->info(count($rows) . ' markers found.');

        return (!$this->option('apply') && count($rows)) ? self::FAILURE : self::SUCCESS;
    }

    private function exportChecklist(array $rows): void
    {
        $grouped = [];
        foreach ($rows as [$storyId, $order, $title, $text]) {
            $grouped[$storyId][$order]['title'] = $title;
            $grouped[$storyId][$order]['items'][$text] = ($grouped[$storyId][$order]['items'][$text] ?? 0) + 1;
        }
        ksort($grouped);

        $out = $this->checklistHeader();

        foreach ($grouped as $storyId => $chapters) {
            ksort($chapters);
            foreach ($chapters as $order => $data) {
                $out .= "\nSTORY {$storyId} | CHAPTER {$order} - {$data['title']}\n";
                foreach ($data['items'] as $text => $count) {
                    $out .= "  [ ] {$text}" . ($count > 1 ? "  (x{$count})" : '') . "\n";
                    $out .= '      suggested: ' . $this->suggestLabels($text) . "\n";
                    $out .= "      final citation:\n";
                }
            }
        }

        file_put_contents(storage_path('app/verify-list.txt'), $out);
        $this->info('Checklist saved: storage/app/verify-list.txt');
    }

    private function suggestLabels(string $text): string
    {
        $t = mb_strtolower($text);
        $labels = [];
        $notes = [];

        if (preg_match('/weak|mursal|without a chain|reported differently|sources differ|differ on/', $t)) {
            $labels[] = 'tradition';
            $notes[] = 'write the caveat as a plain note';
        }

        if (str_contains($t, 'not narrated in')) {
            $labels[] = 'scholarly';
            $notes[] = 'mention: not in Sahih al-Bukhari / Sahih Muslim';
        } elseif (preg_match('/bukhari|muslim|tirmidhi|abu dawud|ibn majah|nasa|malik|al-adab/', $t)) {
            $labels[] = 'hadith';
        }

        if (preg_match('/ibn ishaq|ibn hisham|sirat|sirah|tabaqat|ibn sa|tabari|ibn kathir|tafsir|asbab/', $t)) {
            $labels[] = 'scholarly';
        }

        $labels = array_values(array_unique($labels)) ?: ['scholarly'];

        return implode(' + ', $labels) . ($notes ? '  [' . implode('; ', $notes) . ']' : '');
    }

    private function checklistHeader(): string
    {
        return <<<'TXT'
VERIFY CHECKLIST - Taddabur Prophet Stories
============================================

HOW TO VERIFY EACH LINE
1. Find the original passage. Hadith: sunnah.com (search by collection + number, note the grade shown there).
   Sirah / tafsir: Ibn Hisham (Sirat Rasul Allah), Ibn Sa'd (Tabaqat), Al-Tabari, Ibn Kathir.
2. Check that the passage supports the EXACT detail written in the chapter (names, dates, wording), not just the topic.
3. Write the citation under "final citation".
4. Mark the line:  [x] verified   [?] needs a second check   [-] remove or soften the claim in the chapter

HOW TO LABEL (one label per source, shown as a badge in the chapter's Sources box)
- hadith     Found in a hadith collection. Write: collection + number + grade.
             Example: Sahih al-Bukhari 3 (Sahih)
- scholarly  Taken from a sirah or tafsir work. Write: book + section or volume.
             Example: Ibn Hisham, Sirat Rasul Allah - the marriage to Khadijah (RA)
- tradition  Widely narrated, but not in the Qur'an or hadith text itself, or the chain is disputed.
             Write a plain-English note, e.g. "Sources differ on her exact age at the time."
Do not label sirah reports as "hadith". Weak, mursal or chain-less reports go under "tradition" with a note.
If you cannot find the exact passage, do not cite it: soften the sentence, label it tradition, or remove it.
The "suggested" line below each item is only an automatic guess. The final decision is yours.

HOW TO ENTER THE RESULT (php artisan tinker, one chapter at a time)
$c = \App\Models\StoryChapter::where('story_id', 26)->where('order', 5)->first();
$c->sources = [
    'hadith'    => [],
    'scholarly' => ['Ibn Hisham, Sirat Rasul Allah - the marriage to Khadijah (RA)'],
    'tradition' => ['Sources differ on her exact age at the time.'],
    'pending'   => [],
];
$c->save();
('pending' => [] clears the unverified marker for that chapter.)
Qur'an references need nothing here: they come from the chapter's quran_references.

============================================
TXT;
    }
}

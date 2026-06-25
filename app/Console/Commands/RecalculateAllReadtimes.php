<?php
// app/Console/Commands/RecalculateAllReadtimes.php
//
// Run with: php artisan taddabur:recalculate-readtimes
//
// Recalculates estimated read time (in minutes) for every story_chapter
// based on word count in the `content` field, at a configurable WPM rate.

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateAllReadtimes extends Command
{
    protected $signature = 'taddabur:recalculate-readtimes
                            {--wpm=200 : Words per minute reading speed to base the calculation on}
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Recalculate and update read_time_minutes for all story chapters based on content word count';

    public function handle(): int
    {
        $wpm = (int) $this->option('wpm');
        $dryRun = (bool) $this->option('dry-run');

        if ($wpm <= 0) {
            $this->error('WPM must be a positive number.');
            return self::FAILURE;
        }

        $chapters = DB::table('story_chapters')->get();
        $updated = 0;
        $unchanged = 0;
        $rows = [];

        foreach ($chapters as $chapter) {
            $plainText = trim(strip_tags($chapter->content ?? ''));
            $wordCount = $plainText === '' ? 0 : count(preg_split('/\s+/', $plainText));

            // Round up so a short chapter still shows "1 min read" rather than "0 min read"
            $readTime = max(1, (int) ceil($wordCount / $wpm));

            $oldReadTime = $chapter->read_time_minutes ?? null;

            $rows[] = [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'words' => $wordCount,
                'old' => $oldReadTime,
                'new' => $readTime,
            ];

            if ($oldReadTime === $readTime) {
                $unchanged++;
                continue;
            }

            if (!$dryRun) {
                DB::table('story_chapters')
                    ->where('id', $chapter->id)
                    ->update(['read_time_minutes' => $readTime]);
            }

            $updated++;
        }

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Processed {$chapters->count()} chapters at {$wpm} WPM.");
        $this->line('');

        $this->table(
            ['ID', 'Title', 'Words', 'Old', 'New', 'Changed'],
            array_map(fn($r) => [
                $r['id'],
                $r['title'],
                $r['words'],
                $r['old'] ?? '—',
                $r['new'],
                $r['old'] === $r['new'] ? '' : '✓',
            ], $rows)
        );

        $this->line('');
        $this->info("✅ {$updated} chapter(s) " . ($dryRun ? 'would be updated' : 'updated') . ", {$unchanged} unchanged.");

        return self::SUCCESS;
    }
}

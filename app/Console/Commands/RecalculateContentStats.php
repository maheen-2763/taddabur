<?php

namespace App\Console\Commands;

use App\Models\Story;
use App\Support\ReadingStats;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateContentStats extends Command
{
    protected $signature = 'content:recalc-stats
        {--dry-run : Show what would change without saving}
        {--all : Recalculate even where a value already exists}';

    protected $description = 'Fill missing read_time_minutes (chapters) and word_count (stories) from chapter content';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $all = (bool) $this->option('all');

        // ── Chapters: read_time_minutes ──
        $chapterCount = 0;

        DB::table('story_chapters')
            ->when(! $all, fn($q) => $q->whereNull('read_time_minutes'))
            ->select('id', 'content')
            ->chunkById(50, function ($rows) use ($dry, &$chapterCount) {
                foreach ($rows as $row) {
                    $chapterCount++;

                    if (! $dry) {
                        DB::table('story_chapters')->where('id', $row->id)->update([
                            'read_time_minutes' => ReadingStats::minutes(ReadingStats::words($row->content)),
                        ]);
                    }
                }
            });

        // ── Stories: word_count ──
        $stories = Story::withTrashed()
            ->when(! $all, fn($q) => $q->where(
                fn($q) => $q->whereNull('word_count')->orWhere('word_count', 0)
            ))
            ->orderBy('id')
            ->get(['id', 'title']);

        $rows = [];

        foreach ($stories as $story) {
            $contents = $story->chapters()->pluck('content');
            $words    = $contents->sum(fn($content) => ReadingStats::words($content));

            $rows[] = [$story->id, mb_strimwidth($story->title, 0, 40, '…'), $contents->count(), number_format($words)];

            if (! $dry) {
                DB::table('stories')->where('id', $story->id)->update(['word_count' => $words]);
            }
        }

        $this->table(['Story ID', 'Title', 'Chapters', 'Words'], $rows);
        $this->info(($dry ? 'DRY RUN, nothing saved. ' : 'Saved. ')
            . "{$chapterCount} chapter(s) and " . count($rows) . ' story(ies) processed.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recitation;
use App\Models\ReciterWordTiming;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportReciterWordTimings extends Command
{
    protected $signature = 'timings:import {reciter_slug} {json_path}';
    protected $description = 'Import quran-align word timing JSON for a reciter';

    public function handle()
    {
        $slug = $this->argument('reciter_slug');
        $path = $this->argument('json_path');

        $reciter = Recitation::where('slug', $slug)->first();

        if (!$reciter) {
            $this->error("No reciter found with slug: {$slug}");
            return 1;
        }

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info("Importing " . count($data) . " ayah records for {$reciter->name}...");

        $bar = $this->output->createProgressBar(count($data));
        $rowsToInsert = [];

        foreach ($data as $ayahEntry) {
            $surah = $ayahEntry['surah'];
            $ayah = $ayahEntry['ayah'];

            if (!$surah || !$ayah || !isset($ayahEntry['segments']) || !is_array($ayahEntry['segments'])) {
                $this->warn("Skipping incomplete entry: surah={$surah}, ayah={$ayah} — missing or invalid segments");
                $bar->advance();
                continue;
            }

            foreach ($ayahEntry['segments'] as $segment) {
                [$wordStart, $wordEnd, $startMs, $endMs] = $segment;

                // Expand multi-word segments into individual word rows.
                // Split the time range evenly across the words in this segment —
                // not perfect, but far better than treating them as one giant word.
                $wordCount = $wordEnd - $wordStart + 1;
                $segmentDuration = $endMs - $startMs;
                $perWordDuration = intdiv($segmentDuration, $wordCount);

                for ($i = 0; $i < $wordCount; $i++) {
                    $wordIndex = $wordStart + $i;
                    $wStart = $startMs + ($i * $perWordDuration);
                    $wEnd = ($i === $wordCount - 1)
                        ? $endMs // last word in segment gets exact end, avoids rounding drift
                        : $wStart + $perWordDuration;

                    $rowsToInsert[] = [
                        'reciter_id'   => $reciter->id,
                        'surah_number' => $surah,
                        'ayah_number'  => $ayah,
                        'word_index'   => $wordIndex,
                        'start_ms'     => $wStart,
                        'end_ms'       => $wEnd,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            }

            $skippedCount = 0;

            // after the loop, before $bar->finish():
            if ($skippedCount > 0) {
                $this->warn("{$skippedCount} ayahs had no usable segment data and were skipped.");
                Log::channel('single')->warning("Timing import skipped ayahs for {$reciter->slug}", [
                    'skipped_surah_ayah' => $skippedList ?? [], // collect these in the skip branch: $skippedList[] = "{$surah}:{$ayah}";
                ]);
            }


            $bar->advance();

            // Batch insert every 500 ayahs to avoid memory issues on full-Quran files
            if (count($rowsToInsert) >= 2000) {
                $this->upsertBatch($rowsToInsert);
                $rowsToInsert = [];
            }
        }

        if (!empty($rowsToInsert)) {
            $this->upsertBatch($rowsToInsert);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Mark this reciter as verified_segments now.");


        return 0;
    }

    private function upsertBatch(array $rows)
    {
        DB::table('reciter_word_timings')->upsert(
            $rows,
            ['reciter_id', 'surah_number', 'ayah_number', 'word_index'],
            ['start_ms', 'end_ms', 'updated_at']
        );
    }
}

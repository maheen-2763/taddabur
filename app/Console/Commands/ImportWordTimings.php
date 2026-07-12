<?php

namespace App\Console\Commands;

use App\Models\Recitation;
use App\Models\ReciterWordTiming;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWordTimings extends Command
{
    protected $signature = 'timing:import {--path=timings}';
    protected $description = 'Import reciter word-timing JSON files into reciter_word_timings';

    public function handle(): int
    {
        $folder = storage_path('app/' . $this->option('path'));

        if (!is_dir($folder)) {
            $this->error("Folder not found: {$folder}");
            return self::FAILURE;
        }

        // Match each JSON filename to a reciter by finding its folder
        // segment inside audio_url_pattern — e.g. "Alafasy_128kbps" from
        // ".../data/Alafasy_128kbps/{surah_padded}{ayah_padded}.mp3"
        $reciterByFileKey = [];
        foreach (Recitation::all() as $reciter) {
            if (preg_match('#/data/([^/]+)/#', $reciter->audio_url_pattern, $m)) {
                $reciterByFileKey[$m[1]] = $reciter;
            }
        }

        $files = glob($folder . '/*.json');
        if (empty($files)) {
            $this->warn("No .json files found in {$folder}");
            return self::SUCCESS;
        }

        foreach ($files as $filePath) {
            $fileKey = pathinfo($filePath, PATHINFO_FILENAME);
            $reciter = $reciterByFileKey[$fileKey] ?? null;

            if (!$reciter) {
                $this->warn("⚠️  Skipped {$fileKey}.json — no reciter's audio_url_pattern matches this filename.");
                continue;
            }

            $this->info("Importing {$fileKey}.json → {$reciter->name} (id {$reciter->id})...");

            $entries = json_decode(file_get_contents($filePath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("  ✗ Invalid JSON: " . json_last_error_msg());
                continue;
            }

            $rows = [];
            $ayahsCovered = [];
            $skippedSegments = 0;

            foreach ($entries as $entry) {
                $surah = $entry['surah'] ?? null;
                $ayah  = $entry['ayah'] ?? null;
                $segments = $entry['segments'] ?? [];

                if (!$surah || !$ayah || empty($segments)) {
                    continue;
                }

                $ayahsCovered[$surah . ':' . $ayah] = true;

                foreach ($segments as $segment) {
                    if (count($segment) !== 4) {
                        $skippedSegments++;
                        continue;
                    }

                    [$wordStart, $wordEnd, $startMs, $endMs] = $segment;

                    $rows[] = [
                        'reciter_id'       => $reciter->id,
                        'surah_number'     => $surah,
                        'ayah_number'      => $ayah,
                        'word_start_index' => $wordStart,
                        'word_end_index'   => $wordEnd,
                        'start_ms'         => $startMs,
                        'end_ms'           => $endMs,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            DB::transaction(function () use ($rows, $reciter) {
                // Wipe this reciter's old rows first — makes re-running
                // the import always safe, never creates duplicates or
                // leaves stale data behind from a previous run.
                ReciterWordTiming::where('reciter_id', $reciter->id)->delete();

                foreach (array_chunk($rows, 500) as $chunk) {
                    ReciterWordTiming::insert($chunk);
                }
            });

            $this->info("  ✓ Imported " . count($rows) . " segments across " . count($ayahsCovered) . " ayahs.");
            if ($skippedSegments > 0) {
                $this->warn("  ⚠️  Skipped {$skippedSegments} malformed segments (wrong array shape).");
            }
        }

        return self::SUCCESS;
    }
}

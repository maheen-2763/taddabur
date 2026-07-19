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

        // ✅ CHANGE 1: normalize karke key banao (bitrate hata ke)
        $reciterByFileKey = [];
        foreach (Recitation::all() as $reciter) {
            if (preg_match('#/data/([^/]+)/#', $reciter->audio_url_pattern, $m)) {
                $normalizedKey = preg_replace('/_\d+kbps$/i', '', $m[1]);
                $reciterByFileKey[$normalizedKey] = $reciter;
            }
        }

        $files = glob($folder . '/*.json');
        if (empty($files)) {
            $this->warn("No .json files found in {$folder}");
            return self::SUCCESS;
        }

        foreach ($files as $filePath) {
            $fileKey = pathinfo($filePath, PATHINFO_FILENAME);

            // ✅ CHANGE 2: file ka naam bhi normalize karke match karo
            $normalizedFileKey = preg_replace('/_\d+kbps$/i', '', $fileKey);
            $reciter = $reciterByFileKey[$normalizedFileKey] ?? null;

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

            // ✅ CHANGE 3: memory-safe buffer + accurate reporting (pehle wala fix)
            $totalImported = 0;
            $ayahsCovered = [];
            $skippedSegments = 0;

            DB::transaction(function () use ($entries, $reciter, &$totalImported, &$ayahsCovered, &$skippedSegments) {
                ReciterWordTiming::where('reciter_id', $reciter->id)->delete();

                $buffer = [];

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

                        $buffer[] = [
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

                        $totalImported++;

                        if (count($buffer) >= 500) {
                            ReciterWordTiming::insert($buffer);
                            $buffer = [];
                        }
                    }
                }

                if (!empty($buffer)) {
                    ReciterWordTiming::insert($buffer);
                }
            });

            $this->info("  ✓ Imported {$totalImported} segments across " . count($ayahsCovered) . " ayahs.");
            if ($skippedSegments > 0) {
                $this->warn("  ⚠️  Skipped {$skippedSegments} malformed segments (wrong array shape).");
            }
        }

        return self::SUCCESS;
    }
}

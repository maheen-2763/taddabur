<?php

namespace App\Console\Commands;

use App\Models\Recitation;
use Illuminate\Console\Command;

class VerifyTimingMatch extends Command
{
    protected $signature = 'timings:verify {reciter_slug} {json_path} {surah=1} {ayah=1}';
    protected $description = 'Verify quran-align word timing JSON for a reciter';

    public function handle()
    {
        $reciter = Recitation::where('slug', $this->argument('reciter_slug'))->first();

        if (!$reciter) {
            $this->error("No reciter found with slug: {$this->argument('reciter_slug')}");
            return 1;
        }

        $path = $this->argument('json_path');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $data = json_decode(file_get_contents($path), true);
        $surah = (int) $this->argument('surah');
        $ayah = (int) $this->argument('ayah');

        $entry = collect($data)->firstWhere(fn($e) => $e['surah'] == $surah && $e['ayah'] == $ayah);

        if (!$entry || !isset($entry['segments'])) {
            $this->error("No valid entry found for surah {$surah}, ayah {$ayah}");
            return 1;
        }

        $lastSegmentEndMs = end($entry['segments'])[3];

        $url = str_replace(
            ['{surah_padded}', '{ayah_padded}'],
            [str_pad($surah, 3, '0', STR_PAD_LEFT), str_pad($ayah, 3, '0', STR_PAD_LEFT)],
            $reciter->audio_url_pattern
        );

        $this->info("Timing file says ayah ends at: {$lastSegmentEndMs}ms");
        $this->info("Now manually check actual MP3 duration at: {$url}");
        $this->info("If they're within ~200ms, the bitrate variant is safe to import.");

        return 0;
    }
}

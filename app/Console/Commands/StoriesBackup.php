<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Command;

class StoriesBackup extends Command
{
    protected $signature = 'stories:backup';
    protected $description = 'Prophet stories aur unke chapters ka JSON backup banata hai (seed_scripts folder mein)';

    public function handle(): int
    {
        $folder = database_path('seed_scripts');

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $data = Story::with('chapters')->get()->toArray();

        if (empty($data)) {
            $this->error('❌ Koi story nahi mili — backup cancel kiya.');
            return 1;
        }

        $filename = $folder . '/stories_backup_' . now()->format('Y_m_d_His') . '.json';

        $bytes = file_put_contents(
            $filename,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if ($bytes === false) {
            $this->error('❌ Backup fail ho gaya — file write nahi hui.');
            return 1;
        }

        $storyCount = count($data);
        $chapterCount = array_sum(array_map(fn($s) => count($s['chapters'] ?? []), $data));

        $this->info("✅ Backup ho gaya: {$storyCount} stories, {$chapterCount} chapters ({$bytes} bytes)");
        $this->line("📁 File: {$filename}");

        return 0;
    }
}

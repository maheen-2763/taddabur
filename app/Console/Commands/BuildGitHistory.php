<?php

namespace App\Console\Commands;

use App\Support\GitHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BuildGitHistory extends Command
{
    protected $signature = 'changelog:build-git {--limit=50 : Number of recent commits to export}';

    protected $description = 'Export recent git commit subjects to storage for the admin Changelog page';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        if ($limit < 1 || $limit > 200) {
            $this->error('The limit must be between 1 and 200.');

            return self::FAILURE;
        }

        $result = Process::path(base_path())->timeout(30)->run([
            'git',
            '-c',
            'core.quotepath=off',
            'log',
            '--no-merges',
            "-n{$limit}",
            '--name-status',
            GitHistory::LOG_FORMAT,
        ]);

        // On failure the previous file is left untouched.
        if ($result->failed()) {
            $this->error('git log failed: ' . trim($result->errorOutput()));

            return self::FAILURE;
        }

        $commits = GitHistory::parse($result->output());

        Storage::put(GitHistory::FILE, json_encode($commits, JSON_THROW_ON_ERROR));

        $this->info(count($commits) . ' commits exported.');

        return self::SUCCESS;
    }
}

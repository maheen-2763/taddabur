<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PruneActivityLog extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'activity-log:prune {--days=180}';
    protected $description = 'Delete activity logs older than N days';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days < 30) {
            $this->error('Minimum 30 days.');
            return self::FAILURE;
        }

        $deleted = ActivityLog::query()->where('created_at', '<', now()->subDays($days))->delete();
        $this->info("Deleted {$deleted} rows.");

        return self::SUCCESS;
    }
}

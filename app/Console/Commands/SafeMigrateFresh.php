<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Backup;
use Illuminate\Support\Facades\Auth;

class SafeMigrateFresh extends Command
{
    protected $signature = 'db:safe-fresh';
    protected $description = 'Backup lekar phir migrate:fresh --seed chalata hai';

    public function handle()
    {
        if (!$this->confirm('⚠️ Ye SAARA data delete karega! Continue karein?')) {
            $this->error('Cancelled.');
            return;
        }

        $filename = 'taddabur_backup_' . now()->format('Ymd_His') . '.sqlite';
        $backupPath = storage_path('app/backups/' . $filename);

        copy(database_path('taddabur'), $backupPath);

        Backup::create([
            'filename' => $filename,
            'size' => round(filesize($backupPath) / 1024 / 1024, 2) . ' MB',
            'created_by' => Auth::id() ?? null,
        ]);

        $this->info("Backup ban gaya: {$filename}");

        $this->call('migrate:fresh', ['--seed' => true]);
    }
}

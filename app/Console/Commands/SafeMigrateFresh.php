<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SafeMigrateFresh extends Command
{

    // php artisan db:safe-fresh always need to use this, not PHP artisan migrate:fresh

    protected $signature = 'db:safe-fresh';
    protected $description = 'Backup lekar phir migrate:fresh --seed chalata hai';

    public function handle()
    {
        $dbPath = database_path('taddabur');

        if (!$this->confirm('⚠️ Ye SAARA data delete karega! Confirm karo backup chahiye ya nahi?')) {
            $this->error('Cancelled. Manually backup lo pehle.');
            return;
        }

        $backupPath = database_path('backup_' . now()->format('Ymd_His') . '.sqlite');
        copy($dbPath, $backupPath);
        $this->info("Backup ban gaya: {$backupPath}");

        $this->call('migrate:fresh', ['--seed' => true]);
    }
}

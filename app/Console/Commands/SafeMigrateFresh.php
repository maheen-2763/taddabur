<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SafeMigrateFresh extends Command
{
    protected $signature = 'db:safe-fresh';
    protected $description = 'Backup lekar phir migrate:fresh --seed chalata hai (⚠️ POORA data delete karta hai)';

    public function handle()
    {
        $dbPath = database_path('taddabur.sqlite');   // 👈 exact filename confirm kar lena apni config/database.php se

        // ✅ Guard 1 — DB file exist hi nahi karti to aage mat badho
        if (!file_exists($dbPath)) {
            $this->error("Database file nahi mili: {$dbPath}");
            return 1;
        }

        // ✅ Guard 2 — Double confirmation, kyunki ye IRREVERSIBLE hai
        $this->warn('⚠️  Ye command SAARA data delete karega — users, bookmarks, notes, reads, sab kuch.');
        $this->warn('⚠️  Ye sirf full dev-reset ke liye hai, kisi single collection ko re-seed karne ke liye NAHI.');

        if (!$this->confirm('Kya aap bilkul confirm hain?')) {
            $this->info('Cancelled — koi change nahi hua.');
            return 0;
        }

        if ($this->option('no-interaction') === false && !$this->confirm('Sach mein? Ye undo nahi ho sakta (backup ke alawa). Type "yes" to continue:')) {
            $this->info('Cancelled — koi change nahi hua.');
            return 0;
        }

        // ✅ Backup folder khud na bana ho to bana do
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'taddabur_backup_' . now()->format('Ymd_His') . '.sqlite';
        $backupPath = $backupDir . '/' . $filename;

        // ✅ Guard 3 — copy() fail hone pe turant rok do, aage mat badho
        if (!copy($dbPath, $backupPath)) {
            $this->error('Backup fail ho gaya — migrate:fresh NAHI chalaya. Data safe hai.');
            return 1;
        }

        $this->info("✅ Backup ban gaya: {$filename} (" . round(filesize($backupPath) / 1024 / 1024, 2) . " MB)");

        // ✅ Fix — pehle migrate:fresh, USKE BAAD Backup-record save karo
        // (warna record khud hi turant wipe ho jata tha)
        $this->call('migrate:fresh', ['--seed' => true]);

        Backup::create([
            'filename' => $filename,
            'size' => round(filesize($backupPath) / 1024 / 1024, 2) . ' MB',
            'created_by' => Auth::id() ?? null,
        ]);

        $this->info('✅ migrate:fresh complete + backup record saved.');

        return 0;
    }
}

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
        $this->warn('⚠️  Ye command SAARA data delete karega — users, bookmarks, notes, reads, sab kuch.');
        $this->warn('⚠️  Ye sirf full dev-reset ke liye hai, kisi single collection ko re-seed karne ke liye NAHI.');

        if (!$this->confirm('Kya aap bilkul confirm hain?')) {
            $this->info('Cancelled — koi change nahi hua.');
            return 0;
        }

        if (!$this->confirm('Sach mein? Ye undo nahi ho sakta (backup ke alawa). Type "yes" to continue:')) {
            $this->info('Cancelled — koi change nahi hua.');
            return 0;
        }

        // ✅ Naya backup command call karo (DB + storage dono backup hoga)
        $backupExitCode = $this->call('db:backup', ['--label' => 'pre-fresh-reset']);

        // ✅ Guard — backup fail hua to migrate:fresh mat chalao
        if ($backupExitCode !== 0) {
            $this->error('Backup fail ho gaya — migrate:fresh NAHI chalaya. Data safe hai.');
            return 1;
        }

        $this->call('migrate:fresh', ['--seed' => true]);

        $this->info('✅ migrate:fresh complete.');

        return 0;
    }
}

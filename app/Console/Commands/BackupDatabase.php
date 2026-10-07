<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\Backup;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--label= : Optional label, e.g. "before-prophet-seed"}';
    protected $description = 'Poora backup leta hai — DB + uploaded storage files. Koi data delete NAHI karta.';

    public function handle()
    {
        // Step 1: config se path uthao
        $dbPath = config('database.connections.sqlite.database');

        // Step 2: agar relative path hai (jaise sirf "taddabur"), base_path se resolve karo
        if (!str_starts_with($dbPath, '/') && !preg_match('/^[A-Z]:\\\\/', $dbPath)) {
            $dbPath = base_path($dbPath);
        }


        // Guard 1 — DB file exist honi chahiye
        if (!file_exists($dbPath)) {
            $this->error("Database file nahi mili: {$dbPath}");
            return 1;
        }

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Ymd_His');
        $label = $this->option('label') ? '_' . str_replace(' ', '-', $this->option('label')) : '';
        $folderName = "taddabur_backup_{$timestamp}{$label}";
        $backupPath = $backupDir . '/' . $folderName;

        mkdir($backupPath, 0755, true);

        // Step 1 — DB file copy
        $dbBackupPath = $backupPath . '/taddabur';
        if (!copy($dbPath, $dbBackupPath)) {
            $this->error('DB backup fail ho gaya — kuch save nahi hua.');
            return 1;
        }
        $this->info('✅ Database file backed up.');

        // Step 2 — Uploaded storage files copy (agar exist karte hain)
        $storageSource = storage_path('app/public');
        if (is_dir($storageSource)) {
            $storageDest = $backupPath . '/public_storage';
            $this->copyDirectory($storageSource, $storageDest);
            $this->info('✅ Storage/uploaded files backed up.');
        } else {
            $this->comment('ℹ️  Koi public storage folder nahi mila — skip kiya.');
        }

        // Step 3 — Zip bana do taaki ek hi file mein sab ho (optional but clean)
        $zipPath = $backupPath . '.zip';
        $this->zipDirectory($backupPath, $zipPath);

        // Cleanup: raw folder hata do, sirf zip rakho
        $this->deleteDirectory($backupPath);

        $sizeMb = round(filesize($zipPath) / 1024 / 1024, 2);

        // DB record save karo
        Backup::create([
            'filename' => basename($zipPath),
            'size' => $sizeMb . ' MB',
            'created_by' => Auth::id() ?? null,
        ]);

        $this->info("✅ Backup complete: " . basename($zipPath) . " ({$sizeMb} MB)");

        return 0;
    }

    private function copyDirectory(string $source, string $dest): void
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $items = scandir($source);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $srcPath = $source . '/' . $item;
            $destPath = $dest . '/' . $item;

            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $destPath);
            } else {
                copy($srcPath, $destPath);
            }
        }
    }

    private function zipDirectory(string $sourceDir, string $zipPath): void
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}

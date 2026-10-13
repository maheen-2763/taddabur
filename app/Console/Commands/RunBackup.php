<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use PDO;
use RuntimeException;
use Throwable;
use ZipArchive;

class RunBackup extends Command
{
    protected $signature = 'backup:run
        {--label= : Manual backup label (manual backups are never auto-deleted)}
        {--keep=14 : How many automatic backups to keep}
        {--user= : Admin user id that triggered this backup}';

    protected $description = 'Create a verified SQLite snapshot, zip it and register it in the backups table';

    public function handle(): int
    {
        if (DB::getDriverName() !== 'sqlite') {
            $this->error('backup:run supports SQLite only.');

            return self::FAILURE;
        }

        if (! class_exists(ZipArchive::class)) {
            $this->error('PHP zip extension is not enabled.');

            return self::FAILURE;
        }

        $label    = Str::slug((string) $this->option('label'));
        $filename = sprintf('taddabur_backup_%s_%s.zip', now()->format('Ymd_His'), $label !== '' ? $label : 'auto');

        Storage::makeDirectory('backups');
        $zipPath = Storage::path("backups/{$filename}");
        $tmpPath = storage_path('app/backup_snapshot_' . Str::random(8) . '.sqlite');

        try {
            DB::statement('VACUUM INTO ' . DB::getPdo()->quote($tmpPath));
            $this->verifySnapshot($tmpPath);
            $this->makeZip($tmpPath, $zipPath);
        } catch (Throwable $e) {
            @unlink($zipPath);
            report($e);
            $this->error('Backup failed: ' . $e->getMessage());

            return self::FAILURE;
        } finally {
            @unlink($tmpPath);
        }

        $size = Number::fileSize(filesize($zipPath), 2);

        Backup::create([
            'filename'   => $filename,
            'size'       => $size,
            'created_by' => $this->option('user') ?: null,
        ]);

        $this->rotate((int) $this->option('keep'));

        Log::info('Backup created', ['filename' => $filename, 'size' => $size]);
        $this->info("Backup created and verified: {$filename} ({$size})");

        return self::SUCCESS;
    }

    private function verifySnapshot(string $path): void
    {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $integrity = $pdo->query('PRAGMA integrity_check')->fetchColumn();
        if ($integrity !== 'ok') {
            throw new RuntimeException("Integrity check failed: {$integrity}");
        }

        $copy = (int) $pdo->query('SELECT COUNT(*) FROM ayahs')->fetchColumn();
        $live = (int) DB::table('ayahs')->count();
        if ($copy !== $live) {
            throw new RuntimeException("ayahs count mismatch (backup {$copy}, live {$live}).");
        }

        $pdo = null; // Windows par file handle chhodna zaroori hai
    }

    private function makeZip(string $source, string $zipPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the zip file.');
        }

        $zip->addFile($source, 'database.sqlite');
        $zip->setCompressionName('database.sqlite', ZipArchive::CM_DEFLATE);

        if ($password = config('services.backup.zip_password')) {
            $zip->setPassword($password);
            $zip->setEncryptionName('database.sqlite', ZipArchive::EM_AES_256);
        }

        if (! $zip->close()) {
            throw new RuntimeException('Could not write the zip file.');
        }

        $check = new ZipArchive();
        if ($check->open($zipPath, ZipArchive::CHECKCONS) !== true || $check->numFiles !== 1) {
            throw new RuntimeException('Zip verification failed.');
        }
        $check->close();
    }

    /** Sirf "_auto.zip" backups rotate hote hain. Manual/labelled backups kabhi delete nahi hote. */
    private function rotate(int $keep): void
    {
        Backup::orderByDesc('id')->get()
            ->filter(fn(Backup $b) => str_ends_with($b->filename, '_auto.zip'))
            ->skip(max(1, $keep))
            ->each(function (Backup $b) {
                Storage::delete('backups/' . basename($b->filename));
                $b->delete();
            });
    }
}

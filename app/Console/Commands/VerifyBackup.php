<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;
use ZipArchive;

class VerifyBackup extends Command
{
    protected $signature = 'backup:verify {--file= : Backup filename (default: newest backup whose file exists)}';

    protected $description = 'Extract a backup to a temp file and compare every table with the live database (read-only)';

    /** Ye tables app chalte rehne se badalti hain, ya backup khud inhe badalta hai (backups ki row zip banne ke baad judti hai). */
    private const VOLATILE = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'backups'];

    public function handle(): int
    {
        $name = $this->resolveFilename();
        if (! $name) {
            $this->error('No backup file found on disk.');

            return self::FAILURE;
        }

        $zipPath = Storage::path('backups/' . $name);
        $tmpPath = storage_path('app/backup_verify_' . Str::random(8) . '.sqlite');
        $pdo     = null;

        try {
            $this->extract($zipPath, $tmpPath);

            $pdo = new PDO('sqlite:' . $tmpPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $integrity = $pdo->query('PRAGMA integrity_check')->fetchColumn();
            $rows      = $this->compare($pdo);
        } catch (Throwable $e) {
            $this->error('Verification failed: ' . $e->getMessage());

            return self::FAILURE;
        } finally {
            $pdo = null; // Windows par file handle chhodna zaroori hai
            @unlink($tmpPath);
        }

        $this->info("Backup: {$name}");
        $this->table(
            ['Table', 'Live', 'Backup', 'Status'],
            array_map(fn($r) => [$r['table'], $r['live'] ?? '—', $r['copy'] ?? '—', $r['status']], $rows)
        );

        $bad = collect($rows)->whereIn('status', ['MISSING', 'DIFF'])->count();

        $this->line('Integrity check : ' . $integrity);
        $this->line('Tables checked  : ' . count($rows));
        $this->line('Rows in backup  : ' . number_format(collect($rows)->sum(fn($r) => $r['copy'] ?? 0)));

        if ($integrity !== 'ok' || $bad > 0) {
            $this->warn("{$bad} table(s) MISSING or DIFF. Backup ke turant baad chalaya ho to ye problem hai.");

            return self::FAILURE;
        }

        $this->info('Verified: all tables present and row counts match.');

        return self::SUCCESS;
    }

    private function resolveFilename(): ?string
    {
        if ($file = $this->option('file')) {
            return basename($file);
        }

        return Backup::orderByDesc('id')->get()
            ->map(fn(Backup $b) => basename($b->filename))
            ->first(fn(string $f) => Storage::exists('backups/' . $f));
    }

    private function extract(string $zipPath, string $target): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Could not open the zip file.');
        }

        if ($password = config('services.backup.zip_password')) {
            $zip->setPassword($password);
        }

        $stream = $zip->getStream('database.sqlite');
        if (! $stream) {
            $zip->close();
            throw new RuntimeException('database.sqlite not found inside the zip (wrong password?).');
        }

        $out = fopen($target, 'wb');
        stream_copy_to_stream($stream, $out);
        fclose($out);
        fclose($stream);
        $zip->close();
    }

    private function compare(PDO $backup): array
    {
        $sql          = "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'";
        $liveTables   = collect(DB::select($sql . ' ORDER BY name'))->pluck('name');
        $backupTables = collect($backup->query($sql)->fetchAll(PDO::FETCH_COLUMN));

        return $liveTables->map(function (string $table) use ($backup, $backupTables) {
            $present = $backupTables->contains($table);
            $live    = $this->liveCount($table);
            $copy    = $present ? $this->backupCount($backup, $table) : null;

            $status = match (true) {
                ! $present                             => 'MISSING',
                $live === null && $copy === null       => 'SKIPPED',
                in_array($table, self::VOLATILE, true) => 'VOLATILE',
                $live === $copy                        => 'OK',
                default                                => 'DIFF',
            };

            return compact('table', 'live', 'copy', 'status');
        })->all();
    }

    private function liveCount(string $table): ?int
    {
        try {
            return DB::table($table)->count();
        } catch (Throwable) {
            return null;
        }
    }

    private function backupCount(PDO $pdo, string $table): ?int
    {
        try {
            return (int) $pdo->query('SELECT COUNT(*) FROM "' . str_replace('"', '""', $table) . '"')->fetchColumn();
        } catch (PDOException) {
            return null;
        }
    }
}

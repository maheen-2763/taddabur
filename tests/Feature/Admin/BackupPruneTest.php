<?php

namespace Tests\Feature\Admin;

use App\Actions\DeleteBackup;
use App\Actions\PruneAutoBackups;
use App\Models\ActivityLog;
use App\Models\Backup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesBackups;
use Tests\TestCase;

class BackupPruneTest extends TestCase
{
    use MakesBackups, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_keeps_newest_auto_backups_and_deletes_older_with_files(): void
    {
        $b = collect(range(1, 5))->map(fn() => $this->makeBackup());

        $deleted = app(PruneAutoBackups::class)->handle(2);

        $this->assertSame(3, $deleted);
        foreach ([0, 1, 2] as $i) {
            $this->assertModelMissing($b[$i]);
            Storage::assertMissing($b[$i]->storagePath());
        }
        foreach ([3, 4] as $i) {
            $this->assertModelExists($b[$i]);
            Storage::assertExists($b[$i]->storagePath());
        }
    }

    public function test_manual_backups_are_never_pruned(): void
    {
        foreach (range(1, 3) as $_) {
            $this->makeBackup('manual');
            $this->makeBackup('auto');
        }

        app(PruneAutoBackups::class)->handle(1);

        $this->assertSame(3, Backup::where('filename', 'like', '%_manual.zip')->count());
        $this->assertSame(1, Backup::where('filename', 'like', '%_auto.zip')->count());
    }

    public function test_newest_auto_backup_is_kept_even_if_keep_is_zero(): void
    {
        foreach (range(1, 3) as $_) {
            $this->makeBackup();
        }

        app(PruneAutoBackups::class)->handle(0);

        $this->assertSame(1, Backup::count());
    }

    public function test_row_is_removed_even_if_file_is_already_missing(): void
    {
        $old = $this->makeBackup('auto', withFile: false);
        $this->makeBackup();

        app(PruneAutoBackups::class)->handle(1);

        $this->assertModelMissing($old);
    }

    public function test_prune_logs_only_the_count(): void
    {
        foreach (range(1, 5) as $_) {
            $this->makeBackup();
        }

        app(PruneAutoBackups::class)->handle(2);

        $log = ActivityLog::where('event', 'backup.pruned')->sole();
        $this->assertNull($log->actor_id);
        $this->assertSame(['count' => 3], $log->properties);
    }

    public function test_nothing_is_logged_when_nothing_is_pruned(): void
    {
        $this->makeBackup();

        $this->assertSame(0, app(PruneAutoBackups::class)->handle(14));
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_delete_backup_removes_file_and_row(): void
    {
        $backup = $this->makeBackup();

        $this->assertTrue(app(DeleteBackup::class)->handle($backup));

        $this->assertModelMissing($backup);
        Storage::assertMissing($backup->storagePath());
    }

    public function test_row_is_kept_when_file_delete_fails(): void
    {
        $backup = Backup::create(['filename' => 'x_auto.zip', 'size' => '1 B']);

        Storage::shouldReceive('exists')->once()->andReturnTrue();
        Storage::shouldReceive('delete')->once()->andReturnFalse();

        $this->assertFalse(app(DeleteBackup::class)->handle($backup));
        $this->assertModelExists($backup);
    }
}

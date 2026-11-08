<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesBackups;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class BackupsPageTest extends TestCase
{
    use MakesBackups, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_download_streams_file_and_logs_it(): void
    {
        $admin = User::factory()->admin()->create();
        $backup = $this->makeBackup();

        $this->actingAs($admin)->get(route('admin.backups.download', $backup))->assertOk();

        $log = ActivityLog::sole();
        $this->assertSame('backup.downloaded', $log->event);
        $this->assertSame($admin->id, $log->actor_id);
    }

    public function test_download_of_missing_file_is_404_and_not_logged(): void
    {
        $backup = $this->makeBackup('auto', withFile: false);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.backups.download', $backup))
            ->assertNotFound();

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_created_time_is_shown_in_ist(): void
    {
        $backup = $this->makeBackup();
        $backup->forceFill([
            'created_at' => Carbon::parse('2026-11-07 14:01:05', 'UTC')->setTimezone(config('app.timezone')),
        ])->save();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.backups'))
            ->assertSee('07 Nov 2026, 07:31 PM');
    }

    public function test_guest_and_non_admin_cannot_bulk_delete(): void
    {
        $backup = $this->makeBackup();

        $user = User::factory()->create();
        /** @var User $user */

        $this->delete(route('admin.backups.destroy-many'), ['ids' => [$backup->id]])->assertRedirect();
        $this->actingAs($user)
            ->delete(route('admin.backups.destroy-many'), ['ids' => [$backup->id]])
            ->assertForbidden();

        $this->assertModelExists($backup);
    }

    public function test_admin_deletes_selected_backups_and_logs_count(): void
    {
        $admin = User::factory()->admin()->create();
        $old = $this->makeBackup('manual');
        $mid = $this->makeBackup('auto');
        $new = $this->makeBackup('manual');

        $this->actingAs($admin)
            ->delete(route('admin.backups.destroy-many'), ['ids' => [$old->id, $mid->id]])
            ->assertSessionHas('success');

        $this->assertModelMissing($old);
        $this->assertModelMissing($mid);
        Storage::assertMissing($old->storagePath());
        Storage::assertMissing($mid->storagePath());
        $this->assertModelExists($new);

        $log = ActivityLog::sole();
        $this->assertSame('backup.deleted', $log->event);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame(['count' => 2], $log->properties);
    }

    public function test_newest_backup_is_never_deleted(): void
    {
        $old = $this->makeBackup();
        $new = $this->makeBackup();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.backups.destroy-many'), ['ids' => [$old->id, $new->id]]);

        $this->assertModelMissing($old);
        $this->assertModelExists($new);
        Storage::assertExists($new->storagePath());
        $this->assertSame(['count' => 1], ActivityLog::sole()->properties);
    }

    public function test_selecting_only_the_newest_deletes_and_logs_nothing(): void
    {
        $only = $this->makeBackup();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.backups.destroy-many'), ['ids' => [$only->id]])
            ->assertSessionHas('error');

        $this->assertModelExists($only);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_ids_are_validated(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.backups.destroy-many'))
            ->assertSessionHasErrors('ids');

        $this->actingAs($admin)
            ->delete(route('admin.backups.destroy-many'), ['ids' => [999]])
            ->assertSessionHasErrors('ids.0');
    }

    public function test_index_shows_stats_and_flags_missing_files(): void
    {
        $this->makeBackup();
        $this->makeBackup('manual', withFile: false);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.backups'))
            ->assertOk()
            ->assertSee('Database Backups');

        $this->assertSame(2, $response->viewData('stats')['count']);
        $this->assertSame(1, $response->viewData('stats')['missing']);
    }

    public function test_backup_type_detection(): void
    {
        $this->assertTrue($this->makeBackup('auto')->isAuto());
        $this->assertFalse($this->makeBackup('manual')->isAuto());
    }
}

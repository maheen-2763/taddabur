<?php

namespace Tests\Feature\Admin;

use App\Actions\DeleteUserAccount;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminUserDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_non_admin_cannot_delete(): void
    {
        /** @var User $target */
        $target = User::factory()->create();

        $this->delete(route('admin.users.destroy', $target))->assertRedirect();
        $this->actingAs($target)
            ->delete(route('admin.users.destroy', $target), ['password' => 'password'])
            ->assertForbidden();

        $this->assertModelExists($target);
    }

    public function test_admin_deletes_user_and_both_events_are_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target), ['password' => 'password'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertModelMissing($target);
        $this->assertDatabaseHas('activity_logs', ['event' => 'account.deleted', 'actor_id' => null]);
        $this->assertDatabaseHas('activity_logs', ['event' => 'admin.user_deleted', 'actor_id' => $admin->id]);
    }

    public function test_wrong_password_deletes_nothing(): void
    {
        $target = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.users.destroy', $target), ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');

        $this->assertModelExists($target);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin), ['password' => 'password'])
            ->assertSessionHasErrors('user');

        $this->assertModelExists($admin);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_delete_goes_through_delete_user_account(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->mock(DeleteUserAccount::class, function (MockInterface $mock) use ($target) {
            $mock->shouldReceive('handle')->once()
                ->with(Mockery::on(fn(User $u) => $u->is($target)));
        });

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target), ['password' => 'password'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('activity_logs', ['event' => 'admin.user_deleted', 'actor_id' => $admin->id]);
    }
}

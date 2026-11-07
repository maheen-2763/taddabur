<?php

namespace Tests\Feature\Admin;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LogicException;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_login_is_logged_without_personal_data(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);

        $log = ActivityLog::sole();
        $this->assertSame('auth.login_failed', $log->event);
        $this->assertNull($log->actor_id);
        $this->assertNull($log->properties);
        $this->assertStringNotContainsString($user->email, json_encode(DB::table('activity_logs')->get()));
    }

    public function test_lockout_is_logged_without_ip(): void
    {
        event(new Lockout(Request::create('/login', 'POST')));

        $log = ActivityLog::sole();
        $this->assertSame('auth.lockout', $log->event);
        $this->assertStringNotContainsString('127.0.0.1', json_encode(DB::table('activity_logs')->get()));
    }

    public function test_password_reset_logs_user_id_only(): void
    {
        $user = User::factory()->create();

        event(new PasswordReset($user));

        $log = ActivityLog::sole();
        $this->assertSame('auth.password_reset', $log->event);
        $this->assertSame($user->id, $log->actor_id);
        $this->assertStringNotContainsString($user->email, json_encode(DB::table('activity_logs')->get()));
    }

    public function test_only_whitelisted_scalar_properties_are_stored(): void
    {
        app(RecordActivity::class)->handle(ActivityEvent::LoginFailed, null, [
            'reason' => 'x',
            'count' => ['nested'],
            'email' => 'a@b.c',
            'ip' => '1.1.1.1',
        ]);

        $this->assertSame(['reason' => 'x'], ActivityLog::sole()->properties);
    }

    public function test_logs_cannot_be_updated_or_deleted(): void
    {
        $log = app(RecordActivity::class)->handle(ActivityEvent::Lockout);

        try {
            $log->update(['event' => 'x']);
            $this->fail('Update should throw.');
        } catch (LogicException) {
            $this->assertTrue(true);
        }

        $this->expectException(LogicException::class);
        $log->delete();
    }

    public function test_prune_removes_only_old_rows(): void
    {
        DB::table('activity_logs')->insert([
            ['event' => 'auth.lockout', 'created_at' => now()->subDays(200)],
            ['event' => 'auth.lockout', 'created_at' => now()->subDays(10)],
        ]);

        $this->artisan('activity-log:prune')->assertSuccessful();

        $this->assertSame(1, ActivityLog::count());
    }

    public function test_prune_refuses_less_than_30_days(): void
    {
        DB::table('activity_logs')->insert(['event' => 'auth.lockout', 'created_at' => now()->subDays(40)]);

        $this->artisan('activity-log:prune --days=5')->assertFailed();

        $this->assertSame(1, ActivityLog::count());
    }

    public function test_only_admin_can_view_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->get(route('admin.activity-log.index'))->assertRedirect();
        $this->actingAs($user)
            ->get(route('admin.activity-log.index'))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.activity-log.index'))->assertOk();
    }

    public function test_event_filter_works(): void
    {
        app(RecordActivity::class)->handle(ActivityEvent::Lockout);
        app(RecordActivity::class)->handle(ActivityEvent::LoginFailed);

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.activity-log.index', ['event' => 'auth.lockout']))
            ->assertOk();

        $this->assertSame(1, $response->viewData('logs')->total());
    }

    public function test_invalid_event_filter_is_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.activity-log.index', ['event' => 'hack']))
            ->assertSessionHasErrors('event');
    }
}

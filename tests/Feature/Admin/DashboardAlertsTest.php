<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardAlertsTest extends TestCase
{
    use RefreshDatabase;

    private function failedLogins(int $count, $at): void
    {
        DB::table('activity_logs')->insert(array_fill(0, $count, [
            'event' => 'auth.login_failed',
            'created_at' => $at,
        ]));
    }

    public function test_many_recent_failed_logins_raise_an_alert_linking_to_the_log(): void
    {
        $this->failedLogins(10, now());

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('10 failed login attempt(s) in the last 24 hours')
            ->assertSee('event=auth.login_failed', false);
    }

    public function test_few_failed_logins_do_not_raise_an_alert(): void
    {
        $this->failedLogins(9, now());

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('failed login attempt(s)');
    }

    public function test_failed_logins_older_than_a_day_are_not_counted(): void
    {
        $this->failedLogins(10, now()->subDays(2));

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('failed login attempt(s)');
    }


    public function test_blocked_admin_sign_ins_raise_an_alert_linking_to_the_log(): void
    {
        DB::table('activity_logs')->insert(['event' => 'auth.admin_login_blocked', 'created_at' => now()]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('1 blocked admin sign-in attempt(s) in the last 24 hours')
            ->assertSee('event=auth.admin_login_blocked', false);
    }

    public function test_old_blocked_sign_ins_do_not_raise_an_alert(): void
    {
        DB::table('activity_logs')->insert(['event' => 'auth.admin_login_blocked', 'created_at' => now()->subDays(2)]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('blocked admin sign-in');
    }

    public function test_production_without_database_sessions_raises_an_alert(): void
    {
        $this->app->detectEnvironment(fn() => 'production');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Single-session protection is off');
    }
}

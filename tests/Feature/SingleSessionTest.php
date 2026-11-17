<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\SingleSession;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SingleSessionTest extends TestCase
{
    use RefreshDatabase;

    private const BLOCKED_MESSAGE = 'This admin account is already signed in on another device. Sign out there first, or wait until that session has been idle for '
        . SingleSession::ADMIN_IDLE_MINUTES . ' minutes.';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function insertSession(int $userId, string $id, ?Carbon $lastActivity = null): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $userId,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'payload' => 'x',
            'last_activity' => ($lastActivity ?? now())->getTimestamp(),
        ]);
    }

    private function login(User $user, array $extra = [])
    {
        return $this->post('/login', ['email' => $user->email, 'password' => 'password'] + $extra);
    }

    public function test_admin_second_sign_in_is_blocked_while_another_session_is_active(): void
    {
        $admin = User::factory()->admin()->create();
        $this->insertSession($admin->id, 'other-session', now()->subMinutes(5));

        $this->login($admin)->assertSessionHasErrors(['email' => self::BLOCKED_MESSAGE]);

        $this->assertGuest();
        $this->assertDatabaseHas('sessions', ['id' => 'other-session']);
        $this->assertDatabaseHas('activity_logs', ['event' => 'auth.admin_login_blocked', 'actor_id' => $admin->id]);
        $this->assertDatabaseMissing('activity_logs', ['event' => 'auth.admin_login']);
    }

    public function test_admin_can_sign_in_when_the_other_session_has_been_idle_too_long(): void
    {
        $admin = User::factory()->admin()->create();
        $this->insertSession($admin->id, 'stale-session', now()->subMinutes(SingleSession::ADMIN_IDLE_MINUTES + 15));

        $this->login($admin)->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseMissing('sessions', ['id' => 'stale-session']);
        $this->assertDatabaseHas('activity_logs', ['event' => 'auth.admin_login', 'actor_id' => $admin->id]);
        $this->assertDatabaseMissing('activity_logs', ['event' => 'auth.admin_login_blocked']);
    }

    public function test_a_wrong_password_never_reveals_that_the_admin_is_signed_in_elsewhere(): void
    {
        $admin = User::factory()->admin()->create();
        $this->insertSession($admin->id, 'other-session');

        $this->post('/login', ['email' => $admin->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors(['email' => trans('auth.failed')]);

        $this->assertDatabaseMissing('activity_logs', ['event' => 'auth.admin_login_blocked']);
    }

    public function test_blocked_attempts_count_towards_the_login_rate_limit(): void
    {
        $admin = User::factory()->admin()->create();
        $this->insertSession($admin->id, 'other-session');

        foreach (range(1, 5) as $_) {
            $this->login($admin)->assertSessionHasErrors(['email' => self::BLOCKED_MESSAGE]);
        }

        $this->login($admin)->assertSessionHasErrors(['email' => 'Too many login attempts.']);
    }

    public function test_users_newest_sign_in_ends_older_sessions_and_leaves_other_accounts_alone(): void
    {
        $user = User::factory()->create();
        $someoneElse = User::factory()->create();
        $this->insertSession($user->id, 'phone', now()->subMinute());
        $this->insertSession($user->id, 'laptop', now()->subMinutes(70));
        $this->insertSession($someoneElse->id, 'someone-else');

        $this->login($user)->assertRedirect();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('sessions', ['id' => 'phone']);
        $this->assertDatabaseMissing('sessions', ['id' => 'laptop']);
        $this->assertDatabaseHas('sessions', ['id' => 'someone-else']);
    }

    public function test_user_sign_in_rotates_the_remember_token(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token-value']);

        $this->login($user);

        $this->assertNotSame('old-token-value', $user->fresh()->remember_token);
    }

    public function test_admin_never_gets_a_remember_me_cookie_but_users_do(): void
    {
        $cookie = Auth::guard('web')->getRecallerName();

        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        $this->login($admin, ['remember' => 'on'])->assertCookieMissing($cookie);

        Auth::guard('web')->logout();

        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->login($user, ['remember' => 'on']);
        $this->assertNotNull($response->getCookie($cookie, false));
    }

    public function test_admin_is_signed_out_after_the_idle_limit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->withSession(['last_seen_at' => now()->subMinutes(SingleSession::ADMIN_IDLE_MINUTES + 1)->getTimestamp()])
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_activity_inside_the_limit_refreshes_the_timer(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->withSession(['last_seen_at' => now()->subMinutes(SingleSession::ADMIN_IDLE_MINUTES - 1)->getTimestamp()])
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertGreaterThan(now()->subMinute()->getTimestamp(), session('last_seen_at'));
    }

    public function test_regular_users_are_not_subject_to_the_admin_idle_limit(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user)
            ->withSession(['last_seen_at' => now()->subHours(3)->getTimestamp()])
            ->get(route('profile.edit'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_active_session_helper_respects_the_window_and_the_excluded_session(): void
    {
        $admin = User::factory()->admin()->create();
        $this->assertFalse(SingleSession::hasOtherActiveSession($admin));

        $this->insertSession($admin->id, 'old', now()->subMinutes(SingleSession::ADMIN_IDLE_MINUTES + 15));
        $this->assertFalse(SingleSession::hasOtherActiveSession($admin));

        $this->insertSession($admin->id, 'recent', now()->subMinutes(5));
        $this->assertTrue(SingleSession::hasOtherActiveSession($admin));
        $this->assertFalse(SingleSession::hasOtherActiveSession($admin, 'recent'));
    }

    public function test_password_change_ends_other_sessions_and_remember_cookies(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token']);
        /** @var User $user */
        $this->insertSession($user->id, 'other-device');

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'N3w-Strong-Pass!2026',
            'password_confirmation' => 'N3w-Strong-Pass!2026',
        ])->assertSessionHas('status', 'password-updated');

        $this->assertDatabaseMissing('sessions', ['id' => 'other-device']);
        $this->assertNotSame('old-token', $user->fresh()->remember_token);
    }

    public function test_a_failed_password_change_leaves_other_sessions_alone(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token']);
        $this->insertSession($user->id, 'other-device');

        /** @var User $user */
        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'N3w-Strong-Pass!2026',
            'password_confirmation' => 'N3w-Strong-Pass!2026',
        ])->assertSessionHasErrors('current_password', null, 'updatePassword');

        $this->assertDatabaseHas('sessions', ['id' => 'other-device']);
        $this->assertSame('old-token', $user->fresh()->remember_token);
    }

    public function test_password_reset_ends_every_session_of_that_user_only(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token']);
        $other = User::factory()->create();
        $this->insertSession($user->id, 'device-a');
        $this->insertSession($user->id, 'device-b');
        $this->insertSession($other->id, 'unrelated');

        event(new PasswordReset($user));

        $this->assertDatabaseMissing('sessions', ['id' => 'device-a']);
        $this->assertDatabaseMissing('sessions', ['id' => 'device-b']);
        $this->assertDatabaseHas('sessions', ['id' => 'unrelated']);
        $this->assertNotSame('old-token', $user->fresh()->remember_token);
    }
}

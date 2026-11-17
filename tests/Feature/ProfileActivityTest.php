<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class ProfileActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();
    }

    public function test_email_change_is_logged_with_the_user_id_and_no_email_values(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        /** @var User $user */

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'password',
        ])->assertSessionHasNoErrors();

        $log = ActivityLog::sole();
        $this->assertSame('account.email_changed', $log->event);
        $this->assertSame($user->id, $log->actor_id);
        $this->assertNull($log->properties);

        $raw = json_encode(DB::table('activity_logs')->get());
        $this->assertStringNotContainsString('old@example.com', $raw);
        $this->assertStringNotContainsString('new@example.com', $raw);
    }

    public function test_changing_only_the_name_is_not_logged(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'A New Name',
            'email' => $user->email,
        ])->assertSessionHasNoErrors();

        /** @var User $user */
        $this->assertSame('A New Name', $user->fresh()->name);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_email_change_with_the_wrong_current_password_is_not_logged(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        /** @var User $user */

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame('old@example.com', $user->fresh()->email);
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_password_change_is_logged_with_the_user_id_and_no_password_values(): void
    {
        $user = User::factory()->create();
        /** @var User $user */

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'N3w-Strong-Pass!2026',
            'password_confirmation' => 'N3w-Strong-Pass!2026',
        ])->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('N3w-Strong-Pass!2026', $user->fresh()->password));

        $log = ActivityLog::sole();
        $this->assertSame('account.password_changed', $log->event);
        $this->assertSame($user->id, $log->actor_id);
        $this->assertNull($log->properties);
        $this->assertStringNotContainsString('N3w-Strong-Pass', json_encode(DB::table('activity_logs')->get()));
    }

    public function test_password_change_with_the_wrong_current_password_is_not_logged(): void
    {
        $user = User::factory()->create();

        /** @var User $user */

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'N3w-Strong-Pass!2026',
            'password_confirmation' => 'N3w-Strong-Pass!2026',
        ])->assertSessionHasErrors('current_password', null, 'updatePassword');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_a_password_that_fails_validation_is_not_logged(): void
    {
        $user = User::factory()->create();
        /** @var User $user */
        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password', null, 'updatePassword');

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_admin_user_page_shows_the_user_id(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee('ID: ' . $user->id);
    }
}

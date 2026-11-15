<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

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
}

<?php

namespace Tests\Feature;

use App\Actions\DeleteUserAccount;
use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserAccountActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_account_deleted_and_anonymizes_old_actor_ids(): void
    {
        $user = User::factory()->create();
        app(RecordActivity::class)->handle(ActivityEvent::PasswordReset, $user->id);

        app(DeleteUserAccount::class)->handle($user);

        $this->assertSame(0, ActivityLog::where('actor_id', $user->id)->count());
        $this->assertSame(1, ActivityLog::where('event', 'auth.password_reset')->count());
        $this->assertSame(1, ActivityLog::where('event', 'account.deleted')->whereNull('actor_id')->count());
    }
}

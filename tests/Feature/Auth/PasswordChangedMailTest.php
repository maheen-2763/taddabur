<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordChangedMailTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'current_password' => 'OldPass123',
            'password' => 'NewPass456',
            'password_confirmation' => 'NewPass456',
        ];
    }

    public function test_password_update_sends_exactly_one_notice_when_submitted_twice(): void
    {
        Mail::fake();
        /** @var User $user */
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);


        $this->actingAs($user)->put(route('password.update'), $this->payload())->assertSessionHasNoErrors();
        // Double-click: dusri request ab purane password se aayi, to nakaam honi chahiye
        $this->actingAs($user)->put(route('password.update'), $this->payload());

        Mail::assertSent(PasswordChangedMail::class, 1);
        $this->assertTrue(Hash::check('NewPass456', $user->fresh()->password));
    }

    public function test_a_racing_request_that_read_the_old_password_does_not_send_a_second_mail(): void
    {
        Mail::fake();
        $user = User::factory()->create(['password' => Hash::make('OldPass123')]);

        // Dusri request ne purana hash padh liya tha...
        $stale = User::find($user->id);

        // ...par tab tak pehli request password badal chuki thi
        User::where('id', $user->id)->update(['password' => Hash::make('Winner789')]);

        $this->actingAs($stale)->put(route('password.update'), $this->payload());

        Mail::assertNotSent(PasswordChangedMail::class);
        $this->assertTrue(Hash::check('Winner789', $user->fresh()->password));
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailChangedMail;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(): User
    {
        return User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
            'welcome_email_sent_at' => now(),
        ]);
    }

    public function test_changing_email_unverifies_it_and_notifies_old_and_new_address(): void
    {
        Mail::fake();
        Notification::fake();
        $user = $this->verifiedUser();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'password',
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        // Purane address ko security notice
        Mail::assertSent(EmailChangedMail::class, fn($mail) => $mail->hasTo('old@example.com'));

        // Naye address ko "Confirm new email" mail (isChange = true)
        $viaNotification = Notification::sent($user, VerifyEmail::class)->count();
        $viaMail = Mail::sent(VerifyEmailMail::class, fn($mail) => $mail->hasTo('new@example.com') && $mail->isChange)->count();
        $this->assertSame(1, $viaNotification + $viaMail);
    }

    public function test_changing_email_requires_the_current_password(): void
    {
        Mail::fake();
        $user = $this->verifiedUser();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame('old@example.com', $user->fresh()->email);
        Mail::assertNothingSent();
    }

    public function test_saving_profile_without_changing_email_needs_no_password_and_sends_nothing(): void
    {
        Mail::fake();
        Notification::fake();
        $user = $this->verifiedUser();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Another Name',
            'email' => $user->email,
        ])->assertSessionHasNoErrors();

        $this->assertNotNull($user->fresh()->email_verified_at);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }
}

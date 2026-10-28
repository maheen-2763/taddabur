<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordChangedMail;
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyEmailMail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class LoginModuleTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'Secret123';

    private function validRegistration(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'age_confirmed' => '1',
            'hp_trap' => '',
        ], $overrides);
    }

    private function verifiedUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make(self::PASSWORD),
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function assertVerificationSentTo(User $user, int $times = 1): void
    {
        $viaNotification = Notification::sent($user, VerifyEmail::class)->count();
        $viaMail = Mail::sent(VerifyEmailMail::class, fn($mail) => $mail->hasTo($user->email))->count();

        $this->assertSame($times, $viaNotification + $viaMail, 'Verification email send count mismatch.');
    }

    // ───────────── Pages ─────────────

    public function test_auth_pages_load(): void
    {
        foreach (['login', 'register', 'password.request'] as $name) {
            $this->get(route($name))->assertOk();
        }
    }

    // ───────────── Registration ─────────────

    public function test_user_can_register_and_receives_verification_mail(): void
    {
        Notification::fake();
        Mail::fake();

        $response = $this->post(route('register'), $this->validRegistration());

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        $user = User::where('email', 'test.user@example.com')->firstOrFail();
        $this->assertNotNull($user->age_confirmed_at);
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertNull($user->email_verified_at);

        $this->assertVerificationSentTo($user);
    }

    public function test_registration_requires_age_confirmation(): void
    {
        $this->post(route('register'), $this->validRegistration(['age_confirmed' => null]))
            ->assertSessionHasErrors('age_confirmed');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_blocks_honeypot(): void
    {
        $this->post(route('register'), $this->validRegistration(['hp_trap' => 'spam']))
            ->assertSessionHasErrors('hp_trap');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_weak_or_mismatched_passwords(): void
    {
        $this->post(route('register'), $this->validRegistration([
            'password' => 'onlyletters',
            'password_confirmation' => 'onlyletters',
        ]))->assertSessionHasErrors('password');

        $this->post(route('register'), $this->validRegistration([
            'password_confirmation' => 'Different123',
        ]))->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $this->verifiedUser(['email' => 'test.user@example.com']);

        $this->post(route('register'), $this->validRegistration())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_stores_email_in_lowercase(): void
    {
        Notification::fake();

        $this->post(route('register'), $this->validRegistration(['email' => 'New.User@Example.COM']));

        $this->assertDatabaseHas('users', ['email' => 'new.user@example.com']);
    }

    public function test_verify_page_shows_resend_countdown_right_after_registration(): void
    {
        Notification::fake();
        $this->post(route('register'), $this->validRegistration());
        $user = User::where('email', 'test.user@example.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertViewHas('resendWait', fn($seconds) => $seconds > 0);
    }

    // ───────────── Email verification ─────────────

    public function test_unverified_user_is_kept_out_of_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verification_link_verifies_user_and_sends_welcome_mail_once(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->actingAs($user)->get($url);
        $this->actingAs($user)->get($url); // dusri baar kholne par dobara mail nahi jani chahiye

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNotNull($user->fresh()->welcome_email_sent_at);
        Mail::assertSent(WelcomeEmail::class, 1);
    }

    public function test_verification_link_with_wrong_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1('someone-else@example.com'),
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_resend_is_rate_limited_with_countdown(): void
    {
        Notification::fake();
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('status', 'verification-link-sent');

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHasErrors('email');

        $this->assertVerificationSentTo($user, 1);
        $this->actingAs($user)->get(route('verification.notice'))
            ->assertViewHas('resendWait', fn($seconds) => $seconds > 0);
    }

    // ───────────── Forgot / reset password ─────────────

    public function test_forgot_password_gives_same_answer_for_known_and_unknown_email(): void
    {
        Notification::fake();
        $user = $this->verifiedUser();
        $message = 'If an account exists for this email, a reset link has been sent.';

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status', $message);

        $this->post(route('password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHas('status', $message);

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertCount(1);
    }

    public function test_password_can_be_reset_once_and_confirmation_mail_is_sent(): void
    {
        Mail::fake();
        $user = $this->verifiedUser();
        $token = Password::createToken($user);

        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewSecret456',
            'password_confirmation' => 'NewSecret456',
        ];

        $this->post(route('password.store'), $payload)->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewSecret456', $user->fresh()->password));
        Mail::assertSent(PasswordChangedMail::class, 1);

        // Wahi token dobara kaam nahi karna chahiye
        $this->post(route('password.store'), $payload)->assertSessionHasErrors('email');
    }

    public function test_reset_with_invalid_token_fails(): void
    {
        $user = $this->verifiedUser();

        $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'NewSecret456',
            'password_confirmation' => 'NewSecret456',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
    }

    // ───────────── Login ─────────────

    public function test_user_can_login_and_is_sent_to_dashboard(): void
    {
        $user = $this->verifiedUser();

        $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_is_sent_to_admin_dashboard(): void
    {
        $admin = $this->verifiedUser(['is_admin' => true]);

        $this->post(route('login'), ['email' => $admin->email, 'password' => self::PASSWORD])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_email_is_case_insensitive(): void
    {
        $user = $this->verifiedUser(['email' => 'mixed@example.com']);

        $this->post(route('login'), ['email' => 'MIXED@Example.com', 'password' => self::PASSWORD]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = $this->verifiedUser();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_locks_after_five_failed_attempts_and_stores_countdown(): void
    {
        $user = $this->verifiedUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        // 6th try: sahi password ke saath bhi lock rehna chahiye
        $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD])
            ->assertSessionHasErrors(['email' => 'Too many login attempts.']);

        $this->assertGuest();
        $lock = session('login_lock');
        $this->assertSame($user->email, $lock['email']);
        $this->assertGreaterThan(time(), $lock['until']);
    }

    public function test_login_page_receives_countdown_from_session(): void
    {
        $this->withSession(['login_lock' => ['email' => 'a@example.com', 'until' => time() + 45]])
            ->get(route('login'))
            ->assertOk()
            ->assertViewHas('lockSeconds', fn($seconds) => $seconds > 0 && $seconds <= 45)
            ->assertViewHas('lockEmail', 'a@example.com');
    }

    // ───────────── Mail templates ─────────────

    public function test_mail_templates_render(): void
    {
        $user = $this->verifiedUser(['name' => 'Mohammed Maheen']);

        (new VerifyEmailMail('https://taddabur.test/verify/demo', 'Mohammed Maheen'))
            ->assertSeeInHtml('Verify Email Address')
            ->assertSeeInHtml('Assalamu alaikum Mohammed')
            ->assertSeeInText('https://taddabur.test/verify/demo');

        (new WelcomeEmail($user))
            ->assertSeeInHtml('Welcome to Taddabur')
            ->assertSeeInHtml(route('quran.index'))
            ->assertSeeInText(route('hadith.index'));

        (new ResetPasswordMail('https://taddabur.test/reset/demo', 'Mohammed Maheen', 60))
            ->assertSeeInHtml('Reset Password')
            ->assertSeeInText('https://taddabur.test/reset/demo');

        (new PasswordChangedMail('Mohammed Maheen', '01 Jan 2027, 10:00 UTC'))
            ->assertSeeInHtml('Your password was changed')
            ->assertSeeInText('01 Jan 2027, 10:00 UTC');
    }
}

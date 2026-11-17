<?php

namespace App\Http\Requests\Auth;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Models\User;
use App\Support\SingleSession;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => Str::lower(trim((string) $this->input('email')))]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $user = $this->userWithValidPassword($credentials);

        $this->ensureAdminIsNotSignedInElsewhere($user);

        $isAdmin = (bool) $user?->is_admin;

        // Newest sign-in wins for regular users: rotating the token invalidates remember-me cookies on other devices.
        if ($user && ! $isAdmin) {
            SingleSession::rotateRememberToken($user);
        }

        // Admins never get a remember-me cookie.
        if (! Auth::attempt($credentials, $this->boolean('remember') && ! $isAdmin)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->session()->forget('login_lock');
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Kept in the session so the live countdown survives a page refresh.
        $this->session()->put('login_lock', [
            'email' => (string) $this->input('email'),
            'until' => time() + $seconds,
        ]);

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->input('email')) . '|' . $this->ip());
    }

    /** Returns the user only when both the email and the password are correct. */
    private function userWithValidPassword(array $credentials): ?User
    {
        $provider = Auth::getProvider();
        $user = $provider->retrieveByCredentials($credentials);

        return $user && $provider->validateCredentials($user, $credentials) ? $user : null;
    }

    /**
     * Only reached with a correct password, so the message never reveals anything to someone who does not know it.
     *
     * @throws ValidationException
     */
    private function ensureAdminIsNotSignedInElsewhere(?User $user): void
    {
        if (! $user?->is_admin || ! SingleSession::hasOtherActiveSession($user)) {
            return;
        }

        RateLimiter::hit($this->throttleKey());

        app(RecordActivity::class)->handle(ActivityEvent::AdminLoginBlocked, $user->id);

        throw ValidationException::withMessages([
            'email' => 'This admin account is already signed in on another device. Sign out there first, or wait until that session has been idle for ' . SingleSession::ADMIN_IDLE_MINUTES . ' minutes.',
        ]);
    }
}

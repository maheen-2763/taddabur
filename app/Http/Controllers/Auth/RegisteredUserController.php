<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name'  => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'age_confirmed' => ['accepted'],
            'hp_trap' => ['prohibited'],
        ], [
            'age_confirmed.accepted' => 'Please confirm you are 18 or older and accept the Terms and Privacy Policy.',
        ]);

        $user = new User([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->forceFill([
            'age_confirmed_at'  => now(),
            'terms_accepted_at' => now(),
        ])->save();

        // Mail fail ho to bhi account bana rahe; user "Resend" se dobara bhej sakta hai.
        try {
            event(new Registered($user));
        } catch (Throwable $e) {
            report($e);
        }
        RateLimiter::hit('verify-resend:' . $user->id, 60);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $wait = RateLimiter::availableIn('verify-resend:' . $user->id);

        if (RateLimiter::tooManyAttempts('verify-resend-hour:' . $user->id, 6)) {
            $wait = max($wait, RateLimiter::availableIn('verify-resend-hour:' . $user->id));
        }

        return view('auth.verify-email', ['resendWait' => $wait]);
    }
}

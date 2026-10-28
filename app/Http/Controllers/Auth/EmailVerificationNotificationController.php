<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $minuteKey = 'verify-resend:' . $user->id;
        $hourKey = 'verify-resend-hour:' . $user->id;

        if (RateLimiter::tooManyAttempts($minuteKey, 1) || RateLimiter::tooManyAttempts($hourKey, 6)) {
            return back()->withErrors(['email' => 'Please wait before requesting another email.']);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['email' => 'We could not send the email right now. Please try again in a few minutes.']);
        }

        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($hourKey, 3600);

        return back()->with('status', 'verification-link-sent');
    }
}

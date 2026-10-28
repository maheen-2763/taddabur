<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($request->user()->email)->send(
                new \App\Mail\PasswordChangedMail($request->user()->name, now()->format('d M Y, H:i T'))
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'password-updated');
    }
}

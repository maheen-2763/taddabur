<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Mail\PasswordChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Throwable;
use Illuminate\Support\Facades\Mail;

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

        $user = $request->user();
        $oldHash = $user->getAuthPassword();

        // Atomic: sirf wahi request jeetegi jisne abhi tak purana hash dekha hai
        $updated = User::where('id', $user->id)
            ->where('password', $oldHash)
            ->update(['password' => Hash::make($validated['password'])]);

        if ($updated === 1) {
            try {
                Mail::to($user->email)->send(
                    new PasswordChangedMail($user->name, now()->format('d M Y, H:i T'))
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'password-updated');
    }
}

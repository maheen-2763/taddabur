<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use App\Actions\DeleteUserAccount;
use App\Mail\AccountDeletedMail;
use Illuminate\Support\Facades\Mail;
use Throwable;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function __construct(private \App\Services\QuranService $quranService) {}
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'translations' => $this->quranService->getAllTranslations(),
            'tafsirs' => $this->quranService->getAllTafsirs(),
            'reciters' => $this->quranService->getAllReciters(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email;
        $user->fill($request->safe()->only(['name', 'email']));
        $emailChanged = $user->isDirty('email');
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        $user->save();

        if ($emailChanged) {
            try {
                $user->sendEmailVerificationNotification();
                \Illuminate\Support\Facades\Mail::to($oldEmail)->send(
                    new \App\Mail\EmailChangedMail($user->name, $user->email, now()->format('d M Y, H:i T'))
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, DeleteUserAccount $deleteAccount): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['password' => 'The only admin account cannot be deleted.'], 'userDeletion');
        }

        $name = $user->name;
        $email = $user->email;

        Auth::logout();

        $deleteAccount->handle($user); // fail hua to exception, kuch nahi hatega

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        try {
            Mail::to($email)->send(new AccountDeletedMail($name, now()->format('d M Y, H:i T')));
        } catch (Throwable $e) {
            report($e);
        }

        return Redirect::to('/');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'preferred_translation' => 'nullable|string|exists:translations,slug',
            'preferred_tafsir'      => 'nullable|string|exists:tafsirs,slug',
            'preferred_reciter'     => 'nullable|string|exists:recitations,slug',
            'preferred_language'    => 'nullable|string|in:en,ur,ar',
        ]);

        $request->user()->update($request->only([
            'preferred_translation',
            'preferred_tafsir',
            'preferred_reciter',
            'preferred_language',
        ]));

        return back()->with('message', 'Preferences saved.');
    }
}

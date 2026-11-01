<?php

namespace App\Http\Controllers;

use App\Actions\StoreUserAvatar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileAvatarController extends Controller
{
    public function update(Request $request, StoreUserAvatar $avatars): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.mimes' => 'Please upload a JPG, PNG or WebP image.',
            'avatar.max' => 'The photo must be smaller than 2 MB.',
            'avatar.uploaded' => 'The upload failed. The photo may be too large.',
        ]);

        $avatars->handle($request->user(), $request->file('avatar'));

        return redirect()->route('profile.edit')->with('status', 'avatar-updated');
    }

    public function destroy(Request $request, StoreUserAvatar $avatars): RedirectResponse
    {
        $avatars->remove($request->user());

        return redirect()->route('profile.edit')->with('status', 'avatar-removed');
    }
}

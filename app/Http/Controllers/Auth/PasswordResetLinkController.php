<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $request->validate(['email' => ['required', 'email']]);

        // Hamesha same jawab: email registered hai ya nahi, ye leak nahi hona chahiye.
        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'If an account exists for this email, a reset link has been sent.');
    }
}

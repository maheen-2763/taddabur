<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        $lock = $request->session()->get('login_lock');
        $seconds = 0;
        $email = '';

        if (is_array($lock)) {
            $seconds = max(0, (int) ($lock['until'] ?? 0) - time());
            $email = (string) ($lock['email'] ?? '');

            if ($seconds === 0) {
                $request->session()->forget('login_lock');
            }
        }

        return view('auth.login', [
            'lockSeconds' => $seconds,
            'lockEmail' => $email,
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

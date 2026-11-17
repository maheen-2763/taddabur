<?php

namespace App\Http\Middleware;

use App\Support\SingleSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAdminIdleTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_admin) {
            $lastSeen = (int) $request->session()->get('last_seen_at', 0);

            if ($lastSeen > 0 && now()->getTimestamp() - $lastSeen > SingleSession::ADMIN_IDLE_MINUTES * 60) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your admin session expired after ' . SingleSession::ADMIN_IDLE_MINUTES . ' minutes of inactivity. Please sign in again.',
                ]);
            }

            $request->session()->put('last_seen_at', now()->getTimestamp());
        }

        return $next($request);
    }
}

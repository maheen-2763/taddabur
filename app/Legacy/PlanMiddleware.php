<?php
// app/Http/Middleware/PlanMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the authenticated user's plan
     * has a specific feature enabled. If not, redirect to upgrade.
     *
     * Usage in routes:
     *   ->middleware('plan:has_tafsir')
     *   ->middleware('plan:has_audio')
     *   ->middleware('plan:has_notes')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        // Get the user's plan from the database
        $plan = \App\Models\Plan::where('slug', $user->plan)->first();

        // If plan doesn't exist or feature is disabled
        if (!$plan || !$plan->$feature) {
            // AJAX request? Return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'upgrade_required',
                    'message' => 'This feature requires a paid plan.',
                    'upgrade_url' => route('subscription.upgrade'),
                ], 403);
            }

            // Regular request? Redirect to upgrade page
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', 'Upgrade your plan to access this feature.');
        }

        return $next($request);
    }
}

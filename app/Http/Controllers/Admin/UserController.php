<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    // List all users with search
    public function index(Request $request): View
    {
        $users = User::query()
            ->when(
                $request->search,
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when(
                $request->plan,
                fn($q) =>
                $q->where('plan', $request->plan)
            )
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // Show single user
    public function show(User $user): View
    {
        $user->load('subscription.plan');
        $plans = Plan::active()->get();

        return view('admin.users.show', compact('user', 'plans'));
    }

    // Update user plan manually
    public function updatePlan(Request $request, User $user)
    {
        $request->validate([
            'plan' => 'required|in:free,basic,premium',
        ]);

        $expiresAt = match ($request->plan) {
            'free'    => null,
            'basic'   => now()->addMonth(),
            'premium' => now()->addMonth(),
        };

        $user->update([
            'plan'            => $request->plan,
            'plan_expires_at' => $expiresAt,
        ]);

        return back()->with('success', "Plan updated to {$request->plan} for {$user->name}");
    }
}

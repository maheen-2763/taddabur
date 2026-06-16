<?php
// app/Services/SubscriptionService.php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService
{
    // GET ALL ACTIVE PLANS
    public function getActivePlans(): Collection
    {
        return Plan::active()->get();
    }

    // GET PAID PLANS ONLY (for upgrade page)
    public function getPaidPlans(): Collection
    {
        return Plan::active()
            ->where('slug', '!=', 'free')
            ->get();
    }

    // UPGRADE USER TO A PLAN
    public function upgradeTo(User $user, Plan $plan, string $billing): void
    {
        $expiresAt = match ($billing) {
            'monthly'  => now()->addMonth(),
            'yearly'   => now()->addYear(),
            'lifetime' => null,
            default    => now()->addMonth(),
        };

        $user->update([
            'plan'            => $plan->slug,
            'plan_expires_at' => $expiresAt,
        ]);
    }

    // CANCEL SUBSCRIPTION
    public function cancel(User $user): void
    {
        $user->update([
            'plan'            => 'free',
            'plan_expires_at' => null,
        ]);
    }

    // EXPIRE OVERDUE SUBSCRIPTIONS (called by scheduler)
    public function expireOverdueSubscriptions(): int
    {
        $expired = User::where('plan', '!=', 'free')
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', now())
            ->get();

        foreach ($expired as $user) {
            $user->update([
                'plan'            => 'free',
                'plan_expires_at' => null,
            ]);
        }

        return $expired->count();
    }
}

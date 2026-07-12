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

            // Keep the subscriptions table honest too — otherwise
            // it would still say "active" after the user has actually
            // lapsed back to free, the same dual-source-of-truth
            // problem we fixed earlier for isPremium().
            $user->activeSubscription?->update(['status' => 'expired']);
        }

        return $expired->count();
    }
}

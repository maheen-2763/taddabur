<?php

// app/Console/Commands/ExpireOverdueSubscriptions.php
namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ExpireOverdueSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-overdue';
    protected $description = 'Downgrade users whose plan_expires_at has passed, and mark their subscription as expired';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $count = $subscriptionService->expireOverdueSubscriptions();

        $this->info("✅ Expired {$count} overdue subscription(s).");

        return self::SUCCESS;
    }
}

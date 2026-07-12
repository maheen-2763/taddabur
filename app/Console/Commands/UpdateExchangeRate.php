<?php

// app/Console/Commands/UpdateExchangeRate.php
namespace App\Console\Commands;

use App\Models\ExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateExchangeRate extends Command
{
    protected $signature = 'exchange-rate:update';
    protected $description = 'Fetch and store the latest USD to INR exchange rate';

    public function handle(): int
    {
        try {
            // exchangerate-api.com free tier — no key needed for this endpoint
            $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');

            if (!$response->successful()) {
                throw new \Exception('Exchange rate API returned status ' . $response->status());
            }

            $rate = $response->json('rates.INR');

            if (!$rate || !is_numeric($rate)) {
                throw new \Exception('INR rate missing or invalid in API response');
            }

            ExchangeRate::updateOrCreate(
                ['from_currency' => 'USD', 'to_currency' => 'INR'],
                ['rate' => round($rate, 4), 'fetched_at' => now()]
            );

            $this->info("Exchange rate updated: 1 USD = {$rate} INR");
            return self::SUCCESS;
        } catch (\Exception $e) {
            // Never let this crash silently — log it so you notice,
            // but don't throw further, since a failed rate update
            // should never take down the scheduler itself.
            Log::error('Exchange rate update failed: ' . $e->getMessage());
            $this->error('Failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Donor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Throwable;

class ReconcileDonations extends Command
{
    protected $signature = 'donations:reconcile
        {--apply : Actually mark captured donations as paid (default is a read-only dry run)}';

    protected $description = 'Check unconfirmed donations against Razorpay and optionally mark the captured ones as paid';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $api = new Api(
            Config::get('services.razorpay.key'),
            Config::get('services.razorpay.secret')
        );

        $donors = Donor::where('status', '!=', Donor::STATUS_PAID)
            ->whereNotNull('razorpay_payment_id')
            ->orderBy('id')
            ->get();

        if ($donors->isEmpty()) {
            $this->info('Nothing to reconcile.');

            return self::SUCCESS;
        }

        $rows      = [];
        $confirmed = 0;

        foreach ($donors as $donor) {
            $before = $donor->status;
            [$verdict, $razorpayStatus] = $this->check($api, $donor);

            if ($verdict === 'CAPTURED') {
                $confirmed++;

                if ($apply) {
                    $donor->update(['status' => Donor::STATUS_PAID]);
                    Log::info('Donation reconciled as paid', ['donor_id' => $donor->id]);
                }
            }

            $rows[] = [
                $donor->id,
                $donor->name,
                '₹' . number_format($donor->amount, 2),
                $before,
                $razorpayStatus,
                $verdict === 'CAPTURED' ? ($apply ? 'MARKED PAID' : 'WOULD MARK PAID') : $verdict,
            ];
        }

        $this->table(['ID', 'Name', 'Amount', 'DB status', 'Razorpay', 'Result'], $rows);

        $this->line($apply
            ? "{$confirmed} donation(s) marked as paid."
            : "DRY RUN, nothing changed. {$confirmed} donation(s) would be marked paid. Re-run with --apply to save.");

        return self::SUCCESS;
    }

    /** @return array{0: string, 1: string} [verdict, razorpay status] */
    private function check(Api $api, Donor $donor): array
    {
        try {
            $payment = $api->payment->fetch($donor->razorpay_payment_id);
        } catch (Throwable $e) {
            return ['API ERROR: ' . mb_strimwidth($e->getMessage(), 0, 60, '...'), '-'];
        }

        $status = (string) ($payment['status'] ?? 'unknown');

        if (($payment['order_id'] ?? null) !== $donor->razorpay_order_id) {
            return ['ORDER MISMATCH', $status];
        }

        if ((int) ($payment['amount'] ?? 0) !== (int) round($donor->amount * 100)) {
            return ['AMOUNT MISMATCH', $status];
        }

        return match ($status) {
            'captured'   => ['CAPTURED', $status],
            'authorized' => ['NOT CAPTURED YET', $status],
            default      => [strtoupper($status), $status], // failed, refunded...
        };
    }
}

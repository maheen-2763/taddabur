<?php
// app/Services/RazorpayService.php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\Plan;
use App\Models\PaymentOrder;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder(User $user, Plan $plan, string $billing): array
    {
        $amountUsd = match ($billing) {
            'monthly'  => $plan->price_monthly,
            'yearly'   => $plan->price_yearly,
            'lifetime' => $plan->price_lifetime,
            default    => throw new \InvalidArgumentException('Invalid billing type.'),
        };

        // Guard: don't create a ₹0 order for a billing type this plan doesn't sell
        if ($amountUsd <= 0) {
            throw new \InvalidArgumentException("This plan has no price set for {$billing} billing.");
        }

        $rate = ExchangeRate::usdToInr();
        $amountInInr = round($amountUsd * $rate, 2);
        $amountInPaise = (int) round($amountInInr * 100); // round(), not truncate

        $order = $this->api->order->create([
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'receipt'         => 'order_' . uniqid(),
            'payment_capture' => 1,
            'notes'           => [
                'user_id' => (string) $user->id,
                'plan'    => $plan->slug,
                'billing' => $billing,
            ],
        ]);

        // This row is the real record of what this order is for.
        // Everything after this point reads from here — never from session.
        PaymentOrder::create([
            'user_id'            => $user->id,
            'plan_id'            => $plan->id,
            'billing'            => $billing,
            'razorpay_order_id'  => $order->id,
            'amount'             => $amountInPaise,
            'currency'           => 'INR',
            'exchange_rate_used' => $rate,
            'status'             => 'created',
        ]);

        return [
            'order_id' => $order->id,
            'amount'   => $amountInPaise,
            'currency' => 'INR',
            'plan'     => $plan->slug,
            'billing'  => $billing,
        ];
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, config('services.razorpay.secret'));
        return hash_equals($expected, $signature);
    }

    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        // Uses a DIFFERENT secret than checkout — set separately in
        // your Razorpay Dashboard under Webhooks, not the same as
        // services.razorpay.secret.
        $expected = hash_hmac('sha256', $rawPayload, config('services.razorpay.webhook_secret'));
        return hash_equals($expected, $signature);
    }

    /**
     * Activates a plan for a verified, completed payment.
     * Safe to call more than once with the same payment_id —
     * it will never double-activate (idempotent).
     * Called from BOTH the client-side verify endpoint AND the webhook —
     * whichever arrives first "wins," the other becomes a safe no-op.
     */
    public function activateFromPayment(string $razorpayOrderId, string $paymentId): ?Subscription
    {
        return DB::transaction(function () use ($razorpayOrderId, $paymentId) {

            $existing = Subscription::where('razorpay_payment_id', $paymentId)->first();
            if ($existing) {
                return $existing; // already processed — nothing to do
            }

            $order = PaymentOrder::where('razorpay_order_id', $razorpayOrderId)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                Log::error("Payment activation failed: no local order found for {$razorpayOrderId}");
                return null;
            }

            $plan = $order->plan;
            $user = $order->user;

            $expiresAt = match ($order->billing) {
                'monthly'  => now()->addMonth(),
                'yearly'   => now()->addYear(),
                'lifetime' => null,
                default    => now()->addMonth(),
            };

            $user->update([
                'plan'            => $plan->slug,
                'plan_expires_at' => $expiresAt,
            ]);

            $order->update(['status' => 'paid']);

            return Subscription::create([
                'user_id'             => $user->id,
                'plan_id'             => $plan->id,
                'payment_order_id'    => $order->id,
                'type'                => $order->billing,
                'razorpay_payment_id' => $paymentId,
                'status'              => 'active',
                'ends_at'             => $expiresAt,
            ]);
        });
    }

    public function getKey(): string
    {
        return config('services.razorpay.key');
    }
}

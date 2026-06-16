<?php
// app/Services/RazorpayService.php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Razorpay\Api\Api;

class RazorpayService
{
    private Api $api;

    public function __construct()
    {
        // Initialize Razorpay API with your keys from config
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    // -------------------------------------------------------
    // CREATE ORDER
    // Razorpay requires you to create an "order" first
    // Then the frontend uses the order ID to open the payment popup
    // -------------------------------------------------------
    public function createOrder(Plan $plan, string $billing): array
    {
        // Get the price based on billing type
        $amount = match ($billing) {
            'monthly'  => $plan->price_monthly,
            'yearly'   => $plan->price_yearly,
            'lifetime' => $plan->price_lifetime,
            default    => $plan->price_monthly,
        };

        // Razorpay requires amount in PAISE (1 INR = 100 paise)
        // Also convert USD to INR (approximate — use a real rate in production)
        $amountInINR   = $amount * 83; // 1 USD ≈ 83 INR
        $amountInPaise = (int) ($amountInINR * 100);

        // Create the order via Razorpay API
        $order = $this->api->order->create([
            'amount'          => $amountInPaise,
            'currency'        => 'INR',
            'receipt'         => 'order_' . time(),
            'payment_capture' => 1, // Auto capture payment
            'notes'           => [
                'plan'    => $plan->slug,
                'billing' => $billing,
            ],
        ]);

        return [
            'order_id' => $order->id,
            'amount'   => $amountInPaise,
            'currency' => 'INR',
            'plan'     => $plan->slug,
            'billing'  => $billing,
        ];
    }

    // -------------------------------------------------------
    // VERIFY PAYMENT
    // After user pays, Razorpay sends back 3 IDs
    // We verify they are authentic using our secret key
    // This prevents fake/tampered payment confirmations
    // -------------------------------------------------------
    public function verifyPayment(
        string $orderId,
        string $paymentId,
        string $signature
    ): bool {
        try {
            // Razorpay signature verification
            // It hashes orderId + paymentId with your secret
            // If the hash matches → payment is real
            $expectedSignature = hash_hmac(
                'sha256',
                $orderId . '|' . $paymentId,
                config('services.razorpay.secret')
            );

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(
                'Razorpay verification failed: ' . $e->getMessage()
            );
            return false;
        }
    }

    // -------------------------------------------------------
    // ACTIVATE SUBSCRIPTION
    // Called after payment is verified
    // Updates user plan and creates subscription record
    // -------------------------------------------------------
    public function activateSubscription(
        User $user,
        Plan $plan,
        string $billing,
        string $paymentId
    ): Subscription {

        // Calculate expiry date
        $expiresAt = match ($billing) {
            'monthly'  => now()->addMonth(),
            'yearly'   => now()->addYear(),
            'lifetime' => null, // Never expires
            default    => now()->addMonth(),
        };

        // Update user plan
        $user->update([
            'plan'            => $plan->slug,
            'plan_expires_at' => $expiresAt,
        ]);

        // Create subscription record for history/audit
        return Subscription::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'type'           => $billing,
            'stripe_id'      => $paymentId, // Reusing stripe_id column for razorpay payment ID
            'stripe_status'  => 'active',
            'ends_at'        => $expiresAt,
        ]);
    }

    // -------------------------------------------------------
    // GET RAZORPAY KEY (for frontend)
    // The publishable key is safe to expose to frontend
    // -------------------------------------------------------
    public function getKey(): string
    {
        return config('services.razorpay.key');
    }
}

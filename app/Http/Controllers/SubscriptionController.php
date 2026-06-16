<?php
// app/Http/Controllers/SubscriptionController.php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\RazorpayService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(
        private RazorpayService $razorpay,
        private SubscriptionService $subscriptionService,
    ) {}

    // -------------------------------------------------------
    // PRICING — Public page
    // GET /pricing
    // -------------------------------------------------------
    public function pricing(): View
    {
        $plans = $this->subscriptionService->getActivePlans();
        $currentPlan = Auth::user()?->plan ?? 'free'; // null safe — guest users default to 'free'

        return view('subscription.pricing', compact('plans', 'currentPlan'));
    }

    // -------------------------------------------------------
    // UPGRADE — Show upgrade page with plans
    // GET /subscription/upgrade
    // -------------------------------------------------------
    public function upgrade(): View
    {
        $plans       = $this->subscriptionService->getPaidPlans();
        $currentPlan = Auth::user()->plan;
        $razorpayKey = $this->razorpay->getKey();

        return view('subscription.upgrade', compact('plans', 'currentPlan', 'razorpayKey'));
    }

    // -------------------------------------------------------
    // CREATE ORDER — Called via AJAX when user clicks "Pay"
    // POST /subscription/create-order
    // Returns Razorpay order details to frontend
    // -------------------------------------------------------
    public function createOrder(Request $request)
    {
        $request->validate([
            'plan_slug' => 'required|exists:plans,slug',
            'billing'   => 'required|in:monthly,yearly,lifetime',
        ]);

        $plan = Plan::where('slug', $request->plan_slug)->firstOrFail();

        // Can't subscribe to free plan
        if ($plan->slug === 'free') {
            return response()->json(['error' => 'Invalid plan.'], 400);
        }

        try {
            $order = $this->razorpay->createOrder($plan, $request->billing);

            // Store plan and billing in session for verification step
            session([
                'pending_plan'    => $plan->slug,
                'pending_billing' => $request->billing,
            ]);

            return response()->json($order);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Could not create order. Please try again.'
            ], 500);
        }
    }

    // -------------------------------------------------------
    // VERIFY PAYMENT — Called after Razorpay popup closes
    // POST /subscription/verify
    // Verifies signature → activates plan
    // -------------------------------------------------------
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        // Verify payment is authentic
        $isValid = $this->razorpay->verifyPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            return response()->json([
                'error' => 'Payment verification failed. Please contact support.'
            ], 400);
        }

        // Get plan from session
        $planSlug = session('pending_plan');
        $billing  = session('pending_billing');

        if (!$planSlug || !$billing) {
            return response()->json(['error' => 'Session expired. Please try again.'], 400);
        }

        $plan = Plan::where('slug', $planSlug)->firstOrFail();
        $user = Auth::user();

        // Activate the subscription
        $this->razorpay->activateSubscription(
            $user,
            $plan,
            $billing,
            $request->razorpay_payment_id
        );

        // Clear session
        session()->forget(['pending_plan', 'pending_billing']);

        return response()->json([
            'status'      => 'success',
            'redirect_url' => route('subscription.success'),
        ]);
    }

    // -------------------------------------------------------
    // SUCCESS — After successful payment
    // GET /subscription/success
    // -------------------------------------------------------
    public function success(): View
    {
        $user = Auth::user();
        $plan = Plan::where('slug', $user->plan)->first();

        return view('subscription.success', compact('user', 'plan'));
    }

    // -------------------------------------------------------
    // CANCEL — Downgrade to free
    // POST /subscription/cancel
    // -------------------------------------------------------
    public function cancel(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $user->update([
            'plan'            => 'free',
            'plan_expires_at' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('message', 'Your subscription has been cancelled.');
    }
}

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

        if ($plan->slug === 'free') {
            return response()->json(['error' => 'Invalid plan.'], 400);
        }

        try {
            $order = $this->razorpay->createOrder(Auth::user(), $plan, $request->billing);
            return response()->json($order);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not create order. Please try again.'], 500);
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

        if (!$this->razorpay->verifySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        )) {
            return response()->json(['error' => 'Payment verification failed. Please contact support.'], 400);
        }

        $subscription = $this->razorpay->activateFromPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id
        );

        if (!$subscription) {
            return response()->json(['error' => 'Could not match this payment to an order. Please contact support.'], 400);
        }

        return response()->json([
            'status'       => 'success',
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
        $user = Auth::user();
        $subscription = $user->activeSubscription;

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
        }

        // Nothing auto-renews in this model, so there's no Razorpay
        // mandate to stop. The person already paid for their current
        // period — they keep access until it naturally expires.
        // isPremium() already checks plan_expires_at, so access will
        // lapse correctly on its own without any further action here.

        return redirect()->route('dashboard')->with(
            'message',
            'Your plan will not renew. You keep access until ' .
                optional($user->plan_expires_at)->format('M d, Y')
        );
    }
}

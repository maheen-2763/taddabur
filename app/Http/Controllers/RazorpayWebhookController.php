<?php

// app/Http/Controllers/RazorpayWebhookController.php
namespace App\Http\Controllers;

use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function __construct(private RazorpayService $razorpay) {}

    public function handle(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawPayload = $request->getContent();

        if (!$signature || !$this->razorpay->verifyWebhookSignature($rawPayload, $signature)) {
            Log::warning('Razorpay webhook: signature mismatch — possibly not really from Razorpay.');
            return response()->json(['status' => 'invalid signature'], 400);
        }

        $event = $request->input('event');

        if ($event === 'payment.captured') {
            $payment = $request->input('payload.payment.entity', []);
            $orderId = $payment['order_id'] ?? null;
            $paymentId = $payment['id'] ?? null;

            if ($orderId && $paymentId) {
                $this->razorpay->activateFromPayment($orderId, $paymentId);
            } else {
                Log::error('Razorpay webhook: payment.captured missing order_id or payment_id.', $payment);
            }
        }

        // Acknowledge every event, even ones we don't act on —
        // otherwise Razorpay will keep retrying it.
        return response()->json(['status' => 'ok']);
    }
}

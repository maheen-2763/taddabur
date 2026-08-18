<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhookSecret = Config::get('services.razorpay.webhook_secret');
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        // Signature verify karo — proof ki ye asli Razorpay hai, koi fake request nahi
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Razorpay webhook: invalid signature');
            return Response::json(['status' => 'invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? null;

        if ($event === 'payment.captured') {
            $payment = $data['payload']['payment']['entity'];

            $donor = Donor::where('razorpay_order_id', $payment['order_id'])->first();

            if ($donor && $donor->status !== 'success') {
                $donor->update([
                    'razorpay_payment_id' => $payment['id'],
                    'status' => 'success',
                ]);
            }
        }

        if ($event === 'qr_code.credited') {
            $payment = $data['payload']['payment']['entity'];

            Donor::firstOrCreate(
                ['razorpay_payment_id' => $payment['id']],
                [
                    'name' => 'Anonymous',
                    'amount' => $payment['amount'] / 100,
                    'payment_method' => 'razorpay_qr',
                    'status' => 'success',
                    'is_public' => true,
                ]
            );
        }

        if ($event === 'payment.failed') {
            $payment = $data['payload']['payment']['entity'];
            Donor::where('razorpay_order_id', $payment['order_id'])
                ->update(['status' => 'failed']);
        }

        return Response::json(['status' => 'ok']);
    }
}

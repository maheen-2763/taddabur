<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret    = (string) Config::get('services.razorpay.webhook_secret');
        $signature = (string) $request->header('X-Razorpay-Signature');
        $payload   = $request->getContent();

        // Secret khali ho to koi bhi khali key se valid signature bana sakta hai. Isliye rok do.
        if ($secret === '') {
            Log::error('Razorpay webhook: webhook secret is not configured');

            return response()->json(['status' => 'not configured'], 500);
        }

        if (! hash_equals(hash_hmac('sha256', $payload, $secret), $signature)) {
            Log::warning('Razorpay webhook: invalid signature');

            return response()->json(['status' => 'invalid signature'], 400);
        }

        $data = json_decode($payload, true) ?? [];

        match ($data['event'] ?? null) {
            'payment.captured' => $this->paymentCaptured($data),
            'payment.failed'   => $this->paymentFailed($data),
            'qr_code.credited' => $this->qrCredited($data),
            default            => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function paymentCaptured(array $data): void
    {
        $payment = data_get($data, 'payload.payment.entity');
        $orderId = $payment['order_id'] ?? null;

        // QR payments ke paas order_id nahi hota, wo qr_code.credited se handle hote hain
        if (! $payment || ! $orderId) {
            return;
        }

        $donor = Donor::where('razorpay_order_id', $orderId)->first();

        if (! $donor || $donor->status === Donor::STATUS_PAID) {
            return; // anjaan order, ya pehle se paid (duplicate webhook)
        }

        // Sanity check: captured amount hamare order se match kare
        if ((int) ($payment['amount'] ?? 0) !== (int) round($donor->amount * 100)) {
            Log::error('Razorpay webhook: amount mismatch', [
                'order_id' => $orderId,
                'expected' => (int) round($donor->amount * 100),
                'received' => $payment['amount'] ?? null,
            ]);

            return;
        }

        $donor->update([
            'razorpay_payment_id' => $payment['id'],
            'status'              => Donor::STATUS_PAID,
        ]);
    }

    private function paymentFailed(array $data): void
    {
        $orderId = data_get($data, 'payload.payment.entity.order_id');

        if (! $orderId) {
            return;
        }

        // Sirf pending rows. Pehle ka failed attempt kisi successful payment ko overwrite na kare.
        Donor::where('razorpay_order_id', $orderId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }

    private function qrCredited(array $data): void
    {
        $payment = data_get($data, 'payload.payment.entity');

        if (! $payment || empty($payment['id'])) {
            return;
        }

        Donor::firstOrCreate(
            ['razorpay_payment_id' => $payment['id']],
            [
                'name'           => 'Anonymous',
                'amount'         => ($payment['amount'] ?? 0) / 100,
                'payment_method' => 'razorpay_qr',
                'status'         => Donor::STATUS_PAID,
                'is_public'      => false, // QR par koi consent nahi, isliye private
            ]
        );
    }
}

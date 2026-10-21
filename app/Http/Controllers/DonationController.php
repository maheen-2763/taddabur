<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Throwable;

class DonationController extends Controller
{
    protected Api $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            Config::get('services.razorpay.key'),
            Config::get('services.razorpay.secret')
        );
    }

    // Step A: frontend se amount aata hai, hum Razorpay order banate hain
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'    => ['required', 'integer', 'min:1', 'max:100000'], // rupees
            'name'      => ['nullable', 'string', 'max:60'],
            'message'   => ['nullable', 'string', 'max:250'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $name = trim((string) ($validated['name'] ?? ''));

        // Naam ke bina public list mein dikhane ka matlab nahi. Default hamesha private.
        $isPublic = $name !== '' && (bool) ($validated['is_public'] ?? false);

        // Razorpay ko amount paise mein chahiye (₹1 = 100 paise)
        $amountInPaise = $validated['amount'] * 100;

        try {
            $order = $this->razorpay->order->create([
                'amount'   => $amountInPaise,
                'currency' => 'INR',
                'receipt'  => 'don_' . uniqid(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Could not start the payment. Please try again in a moment.',
            ], 502);
        }

        // Pending record: paisa abhi confirm nahi hua
        Donor::create([
            'name'              => $name !== '' ? $name : 'Anonymous',
            'amount'            => $validated['amount'],
            'message'           => $validated['message'] ?? null,
            'is_public'         => $isPublic,
            'razorpay_order_id' => $order['id'],
            'payment_method'    => 'razorpay',
            'status'            => 'pending',
        ]);

        return response()->json([
            'order_id' => $order['id'],
            'amount'   => $amountInPaise,
            'key'      => Config::get('services.razorpay.key'),
        ]);
    }

    // Step B: payment ke baad frontend ye call karta hai. Ye sirf signature check karta hai.
    // Final status webhook se hi confirm hota hai.
    public function verifyPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id'   => ['required', 'string', 'max:64'],
            'razorpay_payment_id' => ['required', 'string', 'max:64'],
            'razorpay_signature'  => ['required', 'string', 'max:128'],
        ]);

        $donor = Donor::where('razorpay_order_id', $validated['razorpay_order_id'])->first();

        if (! $donor) {
            return response()->json(['verified' => false], 404);
        }

        try {
            $this->razorpay->utility->verifyPaymentSignature($validated);
        } catch (Throwable $e) {
            Log::warning('Donation signature verification failed', [
                'order_id' => $validated['razorpay_order_id'],
            ]);

            return response()->json(['verified' => false], 400);
        }

        $donor->update(['razorpay_payment_id' => $validated['razorpay_payment_id']]);

        return response()->json(['verified' => true]);
    }
}

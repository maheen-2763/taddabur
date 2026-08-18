<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Config;

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

    // Step A: Frontend se amount aata hai, hum Razorpay order banate hain
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:100000', // rupees
            'name' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        // Amount hamesha paise mein bhejna padta hai (₹1 = 100 paise)
        $amountInPaise = $validated['amount'] * 100;

        $order = $this->razorpay->order->create([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => 'don_' . uniqid(),
        ]);

        // Pending donor record banao — abhi tak paisa confirm nahi hua
        $donor = Donor::create([
            'name' => $validated['name'] ?? 'Anonymous',
            'amount' => $validated['amount'],
            'message' => $validated['message'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
            'razorpay_order_id' => $order['id'],
            'payment_method' => 'razorpay',
            'status' => 'pending',
        ]);

        return Response::json([
            'order_id' => $order['id'],
            'amount' => $amountInPaise,
            'key' => Config::get('services.razorpay.key'),
            'donor_id' => $donor->id,
        ]);
    }

    // Step B: Payment ke baad frontend ye call karta hai — lekin ye sirf UI update ke liye,
    // asli confirmation webhook se hi aayega (neeche dekho)
    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $attributes = [
            'razorpay_order_id' => $validated['razorpay_order_id'],
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'],
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            return Response::json(['verified' => false], 400);
        }

        // Signature sahi hai — lekin final status webhook confirm karega
        $donor = Donor::where('razorpay_order_id', $validated['razorpay_order_id'])->first();
        if ($donor) {
            $donor->update(['razorpay_payment_id' => $validated['razorpay_payment_id']]);
        }

        return Response::json(['verified' => true]);
    }
}

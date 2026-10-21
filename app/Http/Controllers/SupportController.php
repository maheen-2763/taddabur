<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        $qrDataUri = config('services.razorpay.qr_image_url');

        // Sirf confirmed payments, jinhone khud naam dikhana chuna. Same naam ek hi baar.
        $donors = Donor::supporters()
            ->latest()
            ->limit(60)
            ->get(['id', 'name'])
            ->unique(fn(Donor $donor) => mb_strtolower(trim($donor->name)))
            ->take(20)
            ->values();

        return view('support', compact('qrDataUri', 'donors'));
    }
}

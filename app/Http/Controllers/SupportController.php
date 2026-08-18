<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Donor;
use App\Models\Hadith;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        $qrDataUri = config('services.razorpay.qr_image_url');

        $lampAyah = Ayah::whereHas('surah', fn($q) => $q->where('number', 24))
            ->where('number', 35)
            ->with('translations')
            ->first();

        $donors = Donor::public()->latest()->take(20)->get();
        $gratitudeHadith = Hadith::find(1450);

        return view('support', compact('qrDataUri', 'lampAyah', 'donors', 'gratitudeHadith'));
    }
}

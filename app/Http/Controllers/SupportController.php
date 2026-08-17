<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Hadith;
use App\Models\Donor;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        $upiId = config('services.upi.id', 'your-upi-id@bank');
        $upiName = 'Taddabur';

        $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($upiName) . "&cu=INR";

        $qrCode = new QrCode($upiString);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrDataUri = $result->getDataUri();

        $lampAyah = Ayah::whereHas('surah', fn($q) => $q->where('number', 24))
            ->where('number', 35)
            ->with('translations')
            ->first();

        $donors = Donor::public()->latest()->take(20)->get();
        $gratitudeHadith = \App\Models\Hadith::find(1450);

        return view('support', compact('qrDataUri', 'lampAyah', 'donors', 'gratitudeHadith'));
    }
}

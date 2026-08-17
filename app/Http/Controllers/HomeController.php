<?php

namespace App\Http\Controllers;

use App\Models\AllahName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Ayah;
use App\Models\Hadith;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $allahNames = AllahName::inRandomOrder()
            ->take(20)
            ->get();

        $hadithCount = (int) str_replace(',', '', config('content_sources.stats.1.number'));

        $heroAyahs = Ayah::whereHas('surah', fn($q) => $q->where('number', 96))
            ->whereIn('number', [1, 5])
            ->with('translations')
            ->orderBy('number')
            ->get();

        $reflectionAyah = Ayah::whereHas('surah', fn($q) => $q->where('number', 38))
            ->where('number', 29)
            ->with('translations')
            ->first();


        return view('welcome', compact('allahNames', 'hadithCount', 'reflectionAyah', 'heroAyahs'));
    }
}

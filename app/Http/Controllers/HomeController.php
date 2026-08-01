<?php

namespace App\Http\Controllers;

use App\Models\AllahName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;

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

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        $hadithCount = (int) str_replace(',', '', config('content_sources.stats.1.number'));

        return view('welcome', compact('allahNames', 'plans', 'hadithCount'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AllahName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

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

        return view('welcome', compact('allahNames'));
    }
}

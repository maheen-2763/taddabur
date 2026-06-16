<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ayah;
use App\Models\Story;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // User stats
        $totalUsers   = User::count();
        $freeUsers    = User::where('plan', 'free')->count();
        $basicUsers   = User::where('plan', 'basic')->count();
        $premiumUsers = User::where('plan', 'premium')->count();

        // Revenue estimate
        $monthlyRevenue = ($basicUsers * 1.99) + ($premiumUsers * 3.99);

        // Content stats
        $totalStories  = Story::count();
        $totalAyahs    = Ayah::count();
        $totalSubs     = Subscription::count();

        // Recent users
        $recentUsers = User::latest()->take(5)->get();

        // Recent subscriptions
        $recentSubs = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        // Users expiring soon (next 7 days)
        $expiringSoon = User::where('plan', '!=', 'free')
            ->whereBetween('plan_expires_at', [now(), now()->addDays(7)])
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'freeUsers',
            'basicUsers',
            'premiumUsers',
            'monthlyRevenue',
            'totalStories',
            'totalAyahs',
            'totalSubs',
            'recentUsers',
            'recentSubs',
            'expiringSoon'
        ));
    }
}

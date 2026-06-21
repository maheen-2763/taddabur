<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    // GET /dashboard
    public function index(): View
    {


        return view('dashboard', [
            'dashboard' => $this->dashboardService->forUser(Auth::user())
        ]);
    }
}

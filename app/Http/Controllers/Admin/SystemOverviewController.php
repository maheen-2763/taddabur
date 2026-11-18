<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AppOverview;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class SystemOverviewController extends Controller
{
    public function index(): View
    {
        return view('admin.system.index', ['overview' => AppOverview::cached()]);
    }

    public function refresh(): RedirectResponse
    {
        AppOverview::cached(refresh: true);

        return redirect()->route('admin.system.index')->with('success', 'Overview refreshed.');
    }

    public function export(): Response
    {
        $filename = 'taddabur-overview-' . now()->format('Ymd') . '.md';

        return response(AppOverview::toMarkdown(AppOverview::cached()), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

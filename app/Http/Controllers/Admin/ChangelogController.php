<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Changelog;
use Illuminate\Contracts\View\View;
use App\Support\GitHistory;
use Illuminate\Support\Carbon;

class ChangelogController extends Controller
{
    public function index(): View
    {
        $commitsByDate = collect(GitHistory::recent(30))->groupBy(
            fn(array $commit) => Carbon::parse($commit['date'])->timezone('Asia/Kolkata')->format('d M Y'),
        );

        return view('admin.changelog.index', [
            'html' => Changelog::html(),
            'commitsByDate' => $commitsByDate,
        ]);
    }
}

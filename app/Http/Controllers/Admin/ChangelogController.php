<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Changelog;
use App\Support\GitHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ChangelogController extends Controller
{
    private const COMMITS_PER_PAGE = 10;

    public function index(Request $request): View
    {
        $all = collect(GitHistory::recent(GitHistory::MAX_COMMITS));
        $lastPage = max(1, (int) ceil($all->count() / self::COMMITS_PER_PAGE));
        // Out-of-range page numbers fall back to the last page.
        $page = min(LengthAwarePaginator::resolveCurrentPage(), $lastPage);

        $commits = new LengthAwarePaginator(
            $all->forPage($page, self::COMMITS_PER_PAGE)->values(),
            $all->count(),
            self::COMMITS_PER_PAGE,
            $page,
            ['path' => $request->url()],
        );

        return view('admin.changelog.index', [
            'html' => Changelog::html(),
            'builtAt' => GitHistory::builtAt(),
            'latestCommit' => $all->first(),
            'commits' => $commits,
            'commitsByDate' => $commits->getCollection()->groupBy(
                fn(array $commit) => Carbon::parse($commit['date'])->timezone('Asia/Kolkata')->format('d M Y'),
            ),
        ]);
    }
}

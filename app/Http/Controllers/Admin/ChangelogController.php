<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Changelog;
use Illuminate\Contracts\View\View;

class ChangelogController extends Controller
{
    public function index(): View
    {
        return view('admin.changelog.index', ['html' => Changelog::html()]);
    }
}

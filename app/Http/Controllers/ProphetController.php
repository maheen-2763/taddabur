<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prophet;
use App\Services\StoryService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ProphetController extends Controller
{
    public function journey(Prophet $prophet, StoryService $storyService)
    {
        $stories = $storyService->getProphetJourney($prophet, Auth::user());

        return view('prophets.journey', compact('prophet', 'stories'));
    }
}

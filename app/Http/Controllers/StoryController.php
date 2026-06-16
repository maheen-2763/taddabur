<?php

namespace App\Http\Controllers;

use App\Models\Prophet;
use App\Models\Story;
use App\Models\StoryChapter;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function __construct(private StoryService $storyService) {}

    // GET /stories
    public function index(Request $request): View
    {
        // ✅ Service handles filtering, plan checks, pagination
        $stories  = $this->storyService->getStoriesForListing(
            Auth::user(),
            $request->get('category'),
            $request->get('difficulty'),
            $request->get('prophet')
        );

        // ✅ Service handles prophet listing
        $prophets = $this->storyService->getAllProphets();

        return view('stories.index', compact('stories', 'prophets'));
    }

    // GET /stories/{story:slug}
    public function show(Story $story)
    {
        $user = Auth::user();

        // ✅ Service handles access check
        if (!$this->storyService->userCanAccessStory($user, $story)) {
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', 'Upgrade to read "' . $story->title . '"');
        }

        $firstChapter = $story->chapters()->first();

        if (!$firstChapter) abort(404);

        return redirect()->route('stories.chapter', [$story->slug, $firstChapter->slug]);
    }



    // GET /stories/{story:slug}/{chapter}
    public function chapter(Story $story, StoryChapter $chapter): View
    {

        abort_if($chapter->story_id !== $story->id, 404);

        $user = Auth::user();

        // ✅ Service handles access check
        if (!$this->storyService->userCanAccessStory($user, $story)) {
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', 'Upgrade to continue reading.');
        }

        // ✅ Service loads all chapter context (allChapters, prev, next)
        $data = $this->storyService->getChapterWithContext($story, $chapter);

        // ✅ Service saves progress and streak
        if ($user) {
            $data['progress'] = $this->storyService->saveStoryProgress($user, $story, $chapter);
        }

        return view('stories.chapter', $data);
    }

    // GET /prophets
    public function prophets(): View
    {
        // ✅ Service handles prophet listing with story counts
        $prophets = $this->storyService->getAllProphets();

        return view('prophets.index', compact('prophets'));
    }

    // GET /prophets/{prophet:slug}
    public function prophetStories(Prophet $prophet): View
    {
        // ✅ Service filters by user plan automatically
        $stories = $this->storyService->getStoriesForProphet($prophet, Auth::user());

        return view('prophets.show', compact('prophet', 'stories'));
    }

    // POST /stories/{story}/chapters/{chapter}/complete (AJAX)
    public function markComplete(Story $story, StoryChapter $chapter): JsonResponse
    {
        // ✅ Service saves progress
        $this->storyService->saveStoryProgress(Auth::user(), $story, $chapter);

        // ✅ Service calculates percentage
        $percentage = $this->storyService->getCompletionPercentage($story, $chapter);

        return response()->json([
            'status'     => 'completed',
            'percentage' => $percentage,
            'message'    => $percentage === 100
                ? 'MashaAllah! You completed this story.'
                : "Progress: {$percentage}%",
        ]);
    }
}

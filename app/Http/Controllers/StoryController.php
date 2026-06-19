<?php

namespace App\Http\Controllers;

use App\Models\Prophet;
use App\Models\Story;
use App\Models\ReadingProgress;
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
    public function show(Story $story): View
    {
        $user = Auth::user();

        if (!$this->storyService->userCanAccessStory($user, $story)) {
            abort(403);
        }

        $story->load(['chapters' => fn($q) => $q->orderBy('order'), 'prophet']);

        $progress = $user
            ? ReadingProgress::where('user_id', $user->id)
            ->where('story_id', $story->id)
            ->first()
            : null;

        $completedChapterIds = $user
            ? $this->storyService->getCompletedChapterIds($user, $story)
            : [];

        return view('stories.show', compact('story', 'progress', 'completedChapterIds'));
    }



    // GET /stories/{story:slug}/{chapter}
    public function chapter(Story $story, StoryChapter $chapter): View
    {
        abort_if($chapter->story_id !== $story->id, 404);

        $user = Auth::user();

        if (!$this->storyService->userCanAccessStory($user, $story)) {
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', 'Upgrade to continue reading.');
        }

        $data = $this->storyService->getChapterWithContext($story, $chapter);

        if ($user) {
            $data['progress'] = $this->storyService->saveStoryProgress($user, $story, $chapter);
            $data['isChapterCompleted'] = $this->storyService->isChapterCompleted($user, $chapter);
        } else {
            $data['isChapterCompleted'] = false;
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
        // ✅ Load stories_count so the view can display it correctly
        $prophet->loadCount('stories');

        // ✅ Service filters by user plan automatically
        $stories = $this->storyService->getStoriesForProphet($prophet, Auth::user());

        return view('prophets.show', compact('prophet', 'stories'));
    }

    // POST /stories/{story}/chapters/{chapter}/complete (AJAX)
    public function markComplete(Story $story, StoryChapter $chapter): JsonResponse
    {
        $user = Auth::user();

        // ✅ Explicit completion record — only created by this button
        $this->storyService->markChapterComplete($user, $story, $chapter);

        // Still save general reading progress too
        $this->storyService->saveStoryProgress($user, $story, $chapter);

        $percentage = $this->storyService->getCompletionPercentage($story, $chapter);

        return response()->json([
            'status'     => 'completed',
            'percentage' => $percentage,
            'message'    => $percentage === 100
                ? 'Alhamdulillah! You completed this story.'
                : "Progress: {$percentage}%",
        ]);
    }
}

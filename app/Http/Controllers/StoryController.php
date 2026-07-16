<?php

namespace App\Http\Controllers;

use App\Models\Prophet;
use App\Models\Story;
use App\Models\ReadingProgress;
use App\Models\StoryChapter;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class StoryController extends Controller
{
    public function __construct(private StoryService $storyService) {}

    // GET /stories
    public function index(Request $request): View|RedirectResponse
    {
        // ─────────────────────────────────────────────
        // Multi-part prophets (currently only Muhammad ﷺ) don't belong
        // in this general grid — send them to the locked journey page
        // instead, before we even touch the story query below.
        // ─────────────────────────────────────────────
        if ($prophetSlug = $request->get('prophet')) {
            $prophet = Prophet::where('slug', $prophetSlug)->first();

            if ($prophet) {
                $publishedCount = Story::where('prophet_id', $prophet->id)
                    ->published()
                    ->count();

                if ($publishedCount > 1) {
                    return redirect()->route('prophets.journey', $prophet->slug);
                }
            }
        }

        // ✅ Service handles filtering, plan checks, pagination
        $stories  = $this->storyService->getStoriesForListing(
            Auth::user(),
            $request->get('category'),
            $request->get('difficulty'),
            $request->get('prophet')
        );

        // ✅ New line — attach progress data if logged in
        $this->storyService->attachUserProgress($stories, Auth::user());
        // ✅ Service handles prophet listing
        $prophets = $this->storyService->getAllProphets();

        return view('stories.index', compact('stories', 'prophets'));
    }

    // GET /stories/{story:slug}

    public function show(Story $story): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$this->storyService->userCanAccessStory($user, $story)) {
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', "Upgrade to {$story->min_plan_slug} plan to unlock \"{$story->title}\".");
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

        $totalChapters = $story->chapters->count();
        $isStoryCompleted = $user && $totalChapters > 0 && count($completedChapterIds) === $totalChapters;

        return view('stories.show', compact('story', 'progress', 'completedChapterIds', 'isStoryCompleted', 'totalChapters'));
    }



    // GET /stories/{story:slug}/{chapter}
    public function chapter(Story $story, StoryChapter $chapter): View | RedirectResponse
    {
        abort_if($chapter->story_id !== $story->id, 404);

        $user = Auth::user();

        if (!$this->storyService->userCanAccessStory($user, $story)) {
            return redirect()
                ->route('subscription.upgrade')
                ->with('upgrade_message', 'Upgrade to continue reading.');
        }

        $data = $this->storyService->getChapterWithContext($story, $chapter);
        $totalChapters = $data['allChapters']->count();
        $data['totalChapters'] = $totalChapters;

        if ($user) {
            $data['progress'] = $this->storyService->saveStoryProgress($user, $story, $chapter);
            $data['isChapterCompleted'] = $this->storyService->isChapterCompleted($user, $chapter);

            $completedIds = $this->storyService->getCompletedChapterIds($user, $story);
            $data['completedChapterIds'] = $completedIds;
            $data['completedChaptersCount'] = count($completedIds);
            $data['isStoryCompleted'] = count($completedIds) === $totalChapters;
        } else {
            $data['isChapterCompleted'] = false;
            $data['completedChapterIds'] = [];
            $data['completedChaptersCount'] = 0;
            $data['isStoryCompleted'] = false;
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

        $this->storyService->markChapterComplete($user, $story, $chapter);
        $this->storyService->saveStoryProgress($user, $story, $chapter);

        $percentage = $this->storyService->getCompletionPercentage($user, $story);
        $isStoryCompleted = $percentage === 100;

        return response()->json([
            'status'           => 'completed',
            'percentage'       => $percentage,
            'isStoryCompleted' => $isStoryCompleted,
            'message'          => $isStoryCompleted
                ? 'Alhamdulillah! You completed this story.'
                : "Progress: {$percentage}%",
        ]);
    }
    //wipes the progress, then sends the user straight to Chapter 1 to begin again.
    public function resetProgress(Story $story)
    {
        $user = Auth::user();

        $this->storyService->resetStoryProgress($user, $story);

        $firstChapter = $story->chapters()->orderBy('order')->first();

        return redirect()
            ->route('stories.chapter', [$story->slug, $firstChapter->slug])
            ->with('success', 'Progress reset. Starting fresh, In shaa Allah.');
    }
}

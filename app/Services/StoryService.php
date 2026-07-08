<?php
// app/Services/StoryService.php

namespace App\Services;

use App\Models\Prophet;
use App\Models\ReadingProgress;
use App\Models\Story;
use App\Models\StoryChapter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ChapterCompletion;

class StoryService
{
    // -------------------------------------------------------
    // GET STORIES FOR LISTING PAGE
    // Filters based on user plan, category, difficulty
    // -------------------------------------------------------
    public function getStoriesForListing(
        ?User $user,
        ?string $category = null,
        ?string $difficulty = null,
        ?string $prophet = null,
        int $perPage = 12
    ): LengthAwarePaginator {

        $query = Story::published()
            ->with('prophet')
            ->orderBy('sort_order');

        // Free plan users only see free stories
        if (!$user || !$user->isPremium()) {
            $query->free();
        }

        // Apply filters
        if ($category) {
            $query->ofCategory($category);
        }

        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }
        if ($prophet && !$difficulty) {
            $query->whereHas('prophet', function ($q) use ($prophet) {
                $q->where('slug', $prophet);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    // -------------------------------------------------------
    // CHECK IF USER CAN ACCESS STORY
    // Returns true/false — controller decides what to do
    // -------------------------------------------------------
    public function userCanAccessStory(?User $user, Story $story): bool
    {
        // Free stories — anyone can read
        if ($story->is_free) {
            return true;
        }

        // Paid stories — must be logged in AND have a paid plan
        if (!$user) {
            return false;
        }

        return $user->isPremium();
    }

    // -------------------------------------------------------
    // GET CHAPTER WITH ALL CONTEXT
    // Loads everything needed for the chapter reading page
    // -------------------------------------------------------
    public function getChapterWithContext(Story $story, StoryChapter $chapter): array
    {
        // All chapters for the sidebar navigation
        $allChapters = $story->chapters; // Already ordered by 'order' via relationship

        // Previous and next chapter using model accessors
        $prevChapter = $chapter->previous_chapter;
        $nextChapter = $chapter->next_chapter;

        return compact('story', 'chapter', 'allChapters', 'prevChapter', 'nextChapter');
    }

    // -------------------------------------------------------
    // SAVE STORY READING PROGRESS
    // Called when user visits a chapter
    // -------------------------------------------------------
    public function saveStoryProgress(User $user, Story $story, StoryChapter $chapter): ReadingProgress
    {
        $progress = ReadingProgress::updateOrCreate(
            [
                'user_id'  => $user->id,
                'story_id' => $story->id,
            ],
            [
                'last_chapter_id' => $chapter->id,
                'last_read_date'  => today(),
            ]
        );

        $progress->updateStreak();

        return $progress;
    }

    // -------------------------------------------------------
    // CALCULATE STORY COMPLETION PERCENTAGE
    // Used by the "Mark as Read" AJAX response
    // -------------------------------------------------------
    public function getCompletionPercentage(User $user, Story $story): int
    {
        $totalChapters = $story->chapters()->count();

        if ($totalChapters === 0) return 0;

        $completedCount = ChapterCompletion::where('user_id', $user->id)
            ->where('story_id', $story->id)
            ->count();

        return (int) round(($completedCount / $totalChapters) * 100);
    }

    // -------------------------------------------------------
    // GET ALL PROPHETS WITH STORY COUNTS
    // -------------------------------------------------------
    public function getAllProphets(): Collection
    {
        return Prophet::withCount(['stories' => function ($q) {
            $q->published();
        }])
            ->orderBy('order')
            ->get();
    }

    // -------------------------------------------------------
    // GET PROPHET'S STORIES (filtered by user plan)
    // -------------------------------------------------------
    public function getStoriesForProphet(Prophet $prophet, ?User $user): Collection
    {
        return Story::published()
            ->where('prophet_id', $prophet->id)
            ->when(!$user?->isPremium(), fn($q) => $q->free())
            ->orderBy('sort_order')
            ->get();
    }

    // -------------------------------------------------------
    // GET USER'S IN-PROGRESS STORIES
    // Used on the dashboard
    // -------------------------------------------------------
    public function getInProgressStories(User $user, int $limit = 3): Collection
    {
        $progressRecords = ReadingProgress::where('user_id', $user->id)
            ->whereHas('story')
            ->with(['story', 'lastChapter'])
            ->latest()
            ->take($limit)
            ->get();

        // Attach real completion numbers so "Continue Learning" shows
        // actual progress instead of always falling back to 0
        $stories = $progressRecords->pluck('story')->filter();
        $this->attachUserProgress($stories, $user);

        return $progressRecords;
    }

    // -------------------------------------------------------
    // GET RECOMMENDED STORIES
    // Stories the user hasn't started yet
    // -------------------------------------------------------
    public function getRecommendedStories(User $user, int $limit = 4): Collection
    {
        // Get IDs of stories the user has already started
        $startedIds = ReadingProgress::where('user_id', $user->id)
            ->whereNotNull('story_id')
            ->pluck('story_id');

        return Story::published()
            ->when(!$user->isPremium(), fn($q) => $q->free())
            ->whereNotIn('id', $startedIds)
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
    public function markChapterComplete(User $user, Story $story, StoryChapter $chapter): ChapterCompletion
    {
        return ChapterCompletion::firstOrCreate([
            'user_id'          => $user->id,
            'story_chapter_id' => $chapter->id,
        ], [
            'story_id'     => $story->id,
            'completed_at' => now(),
        ]);
    }

    // -------------------------------------------------------
    // GET SET OF COMPLETED CHAPTER IDS FOR A USER + STORY
    // Used by the story show page to render checkmarks accurately
    // -------------------------------------------------------
    public function getCompletedChapterIds(User $user, Story $story): array
    {
        return ChapterCompletion::where('user_id', $user->id)
            ->where('story_id', $story->id)
            ->pluck('story_chapter_id')
            ->toArray();
    }

    // -------------------------------------------------------
    // CHECK IF A SPECIFIC CHAPTER IS COMPLETED
    // Used on the chapter reading page itself (e.g. to disable
    // the "Mark as Read" button if already marked)
    // -------------------------------------------------------
    public function isChapterCompleted(User $user, StoryChapter $chapter): bool
    {
        return ChapterCompletion::where('user_id', $user->id)
            ->where('story_chapter_id', $chapter->id)
            ->exists();
    }


    public function attachUserProgress($stories, ?User $user): void
    {
        if (!$user) {
            return;
        }

        $storyIds = $stories->pluck('id');

        // Total chapters per story
        $chapterCounts = Story::whereIn('id', $storyIds)
            ->withCount('chapters')
            ->get()
            ->pluck('chapters_count', 'id');

        // Completed chapters per story, for this user only
        $completedCounts = ChapterCompletion::where('user_id', $user->id)
            ->whereIn('story_id', $storyIds)
            ->selectRaw('story_id, count(*) as cnt')
            ->groupBy('story_id')
            ->pluck('cnt', 'story_id');

        foreach ($stories as $story) {
            $total = $chapterCounts[$story->id] ?? 0;
            $completed = $completedCounts[$story->id] ?? 0;

            $story->user_progress = [
                'completed'  => $completed,
                'total'      => $total,
                'percentage' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                'started'    => $completed > 0,
            ];
        }
    }

    public function resetStoryProgress(User $user, Story $story): void
    {
        ChapterCompletion::where('user_id', $user->id)
            ->where('story_id', $story->id)
            ->delete();

        ReadingProgress::where('user_id', $user->id)
            ->where('story_id', $story->id)
            ->delete();
    }




    /**
     * Get a prophet's stories in order, with progress + lock status attached.
     * Built for Muhammad ﷺ's 6-part journey, but works for any prophet
     * with more than one Story record.
     */
    public function getProphetJourney(Prophet $prophet, ?User $user): Collection
    {
        $stories = Story::where('prophet_id', $prophet->id)
            ->published()
            ->orderBy('sort_order')
            ->get();

        // Reuses your existing progress logic — same numbers as everywhere else in the app
        $this->attachUserProgress($stories, $user);

        $previousComplete = true; // Part 1 is always unlocked

        foreach ($stories as $story) {
            // Guests (not logged in) don't get user_progress attached at all —
            // give them a safe default so the view doesn't break
            if (!isset($story->user_progress)) {
                $story->user_progress = [
                    'completed'  => 0,
                    'total'      => 0,
                    'percentage' => 0,
                    'started'    => false,
                ];
            }

            $story->is_locked = !$previousComplete;

            // This story's result decides if the NEXT one unlocks
            $previousComplete = $story->user_progress['percentage'] === 100;
        }

        return $stories;
    }
}

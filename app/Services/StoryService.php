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
    public function getCompletionPercentage(Story $story, StoryChapter $chapter): int
    {
        $totalChapters = $story->chapters()->count();

        if ($totalChapters === 0) return 0;

        return (int) round(($chapter->order / $totalChapters) * 100);
    }

    // -------------------------------------------------------
    // GET ALL PROPHETS WITH STORY COUNTS
    // -------------------------------------------------------
    public function getAllProphets(): Collection
    {
        return Prophet::withCount('stories')
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
        return ReadingProgress::where('user_id', $user->id)
            ->whereHas('story')
            ->with(['story', 'lastChapter'])
            ->latest()
            ->take($limit)
            ->get();
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
}

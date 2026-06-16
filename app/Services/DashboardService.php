<?php

namespace App\Services;

use App\Models\DailyContent;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\UserReadAyah;
use App\Models\SurahProgress;

class DashboardService
{
    public function __construct(
        private StoryService $storyService,
        private QuranService $quranService,
    ) {}



    public function forUser(User $user): array
    {
        $quranProgress = $this->quranService->getQuranProgress($user);
        $storyProgress = $this->storyService->getInProgressStories($user);


        return [
            'dailyContent' => $this->getDailyContent(),
            'quranProgress' => $quranProgress,
            'storyProgress' => $storyProgress,
            'achievement' => $this->getAchievement(
                $this->getTotalAyahsRead($user)
            ),

            'stats' => [
                'streak' => $this->getCurrentStreak($user),
                'totalAyahsRead' => $this->getTotalAyahsRead($user),
                'storyCount' => $this->storyService->getInProgressStories($user)->count(),
                'quranProgress' => $this->quranService->getQuranProgress($user)?->quran_progress_percentage ?? 0,

                'completedSurahs' => $this->getCompletedSurahsCount($user),
            ],
        ];
    }

    // --------------------------
    // DAILY CONTENT
    // --------------------------
    public function getDailyContent(): ?DailyContent
    {
        return DailyContent::today()
            ->with([
                'ayah.surah',
                'ayah.translations.translation'
            ])
            ->first()
            ?? DailyContent::latest()
            ->with([
                'ayah.surah',
                'ayah.translations.translation'
            ])
            ->first();
    }

    // --------------------------
    // STREAK
    // --------------------------
    public function getCurrentStreak(User $user): int
    {
        return ReadingProgress::where('user_id', $user->id)
            ->max('reading_streak_days') ?? 0;
    }

    // --------------------------
    // TOTAL AYAHS
    // --------------------------
    public function getTotalAyahsRead(User $user): int
    {
        return UserReadAyah::where('user_id', $user->id)->count();
    }


    private function getAchievement(int $ayahsRead): array
    {
        $levels = [
            500 => 'Beginning the Journey',
            1000 => 'Knowledge Seeker',
            3000 => 'Ayah Explorer',
            5000 => 'Advanced Seeker',
            6236 => 'Quran Completer',
        ];

        $title = 'Beginning the Journey';
        $nextGoal = null;

        foreach ($levels as $goal => $name) {

            if ($ayahsRead >= $goal) {
                $title = $name;
            } else {
                $nextGoal = $goal;
                break;
            }
        }

        return [
            'title' => $title,
            'ayahsRead' => $ayahsRead,
            'nextGoal' => $nextGoal,
            'remaining' => $nextGoal
                ? max(0, $nextGoal - $ayahsRead)
                : 0,
        ];
    }

    private function getCompletedSurahsCount(User $user): int
    {
        return SurahProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
    }
}

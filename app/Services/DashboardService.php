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
            ->whereNull('story_id')
            ->value('reading_streak_days') ?? 0;
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
            [
                'goal' => 0,
                'title' => 'Beginning the Journey',
                'arabic' => 'بِدَايَةُ الرِّحْلَة',
                'icon' => '🕌',
            ],
            ['goal' => 10, 'title' => 'First Steps', 'arabic' => 'بِسْمِ اللّٰهِ', 'icon' => '🌱'],
            ['goal' => 50, 'title' => 'Seeker of Guidance', 'arabic' => 'طَالِبُ الْهُدَى', 'icon' => '🕊️'],
            ['goal' => 100, 'title' => 'Companion of the Quran', 'arabic' => 'صَاحِبُ الْقُرْآن', 'icon' => '📖'],
            ['goal' => 500, 'title' => 'Student of Revelation', 'arabic' => 'طَالِبُ الْوَحْي', 'icon' => '🌙'],
            ['goal' => 1000, 'title' => 'Keeper of Ayat', 'arabic' => 'حَافِظُ الْآيَات', 'icon' => '⭐'],
            ['goal' => 3000, 'title' => 'Walker of Light', 'arabic' => 'سَالِكُ النُّور', 'icon' => '✨'],
            ['goal' => 5000, 'title' => 'Bearer of Wisdom', 'arabic' => 'حَامِلُ الْحِكْمَة', 'icon' => '🏆'],
            ['goal' => 6236, 'title' => 'Quran Completer', 'arabic' => 'خَاتِمُ الْقُرْآن', 'icon' => '👑'],
        ];

        $current = $levels[0];
        $next = null;
        $previousGoal = 0;

        foreach ($levels as $index => $level) {
            if ($ayahsRead >= $level['goal']) {
                $current = $level;
                $previousGoal = $level['goal'];
            } else {
                $next = $level;
                break;
            }
        }

        $progress = 100;

        if ($next) {
            $range = $next['goal'] - $previousGoal;

            $progress = $range > 0
                ? round((($ayahsRead - $previousGoal) / $range) * 100)
                : 100;

            $progress = max(0, min(100, $progress));
        }

        return [
            'title' => $current['title'],
            'arabic' => $current['arabic'],
            'icon' => $current['icon'],
            'ayahsRead' => $ayahsRead,
            'nextGoal' => $next['goal'] ?? null,
            'remaining' => $next
                ? max(0, $next['goal'] - $ayahsRead)
                : 0,
            'progress' => $progress,
        ];
    }
    private function getCompletedSurahsCount(User $user): int
    {
        return SurahProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();
    }
}

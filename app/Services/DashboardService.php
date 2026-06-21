<?php

namespace App\Services;

use App\Models\DailyContent;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\Note;
use App\Models\UserReadAyah;
use App\Models\SurahProgress;
use App\Models\AllahName;

class DashboardService
{
    public function __construct(
        private StoryService $storyService,
        private QuranService $quranService,
    ) {}



    public function forUser(User $user): array
    {
        // ✅ Fetch each piece of data ONCE, store it, reuse it
        $quranProgress  = $this->quranService->getQuranProgress($user);
        $quranReadCount = null;
        if ($quranProgress?->lastAyah) {
            $quranReadCount = $this->quranService->getReadAyahsCount(
                $user,
                $quranProgress->lastAyah->surah
            );
        }
        $storyProgress  = $this->storyService->getInProgressStories($user);
        $totalAyahsRead = $this->getTotalAyahsRead($user);

        return [
            'dailyContent'      => $this->getDailyContent(),
            'quranProgress'     => $quranProgress,
            'quranReadCount' => $quranReadCount,
            'storyProgress'     => $storyProgress,
            'achievement'       => $this->getAchievement($totalAyahsRead),

            // ✅ Correct level — sibling of dailyContent, not nested
            'allahNamesPreview' => AllahName::inRandomOrder()->take(5)->get(),
            'recentNotes' => Note::where('user_id', $user->id)
                ->quranNotes()
                ->latest()
                ->take(5)
                ->with('ayah.surah')
                ->get(),

            'stats' => [
                'streak'          => $this->getCurrentStreak($user),
                'totalAyahsRead'  => $totalAyahsRead,        // ✅ reused
                'storyCount'      => $storyProgress->count(), // ✅ reused, no new query
                'quranProgress'   => $quranProgress?->quran_progress_percentage ?? 0, // ✅ reused
                'completedSurahs' => $this->getCompletedSurahsCount($user),
            ],
        ];
    }

    // --------------------------
    // DAILY CONTENT
    // --------------------------
    public function getDailyContent(): ?DailyContent
    {
        return DailyContent::with([
            'ayah.surah',
            'ayah.translations.translation'
        ])
            ->today()
            ->first()
            ?? DailyContent::with([
                'ayah.surah',
                'ayah.translations.translation'
            ])
            ->latest('scheduled_for')
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

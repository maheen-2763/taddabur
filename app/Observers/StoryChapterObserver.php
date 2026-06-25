<?php

namespace App\Observers;

use App\Models\StoryChapter;

class StoryChapterObserver
{
    /**
     * Words per minute used for read-time calculation.
     */
    protected int $wordsPerMinute = 200;

    public function created(StoryChapter $chapter): void
    {
        $this->recalculate($chapter);
    }

    public function updated(StoryChapter $chapter): void
    {
        $this->recalculate($chapter);
    }

    public function deleted(StoryChapter $chapter): void
    {
        $this->recalculate($chapter);
    }

    /**
     * Recalculate and persist read_time_minutes on the parent Story.
     */
    protected function recalculate(StoryChapter $chapter): void
    {
        $story = $chapter->story;

        if (!$story) {
            return;
        }

        $totalWords = $story->chapters->sum(function ($chapter) {
            return $this->countEnglishWords($chapter->content);
        });

        $readTimeMinutes = (int) ceil($totalWords / $this->wordsPerMinute);

        // Avoid unnecessary writes (and re-triggering observers elsewhere)
        if ($story->read_time_minutes !== $readTimeMinutes) {
            $story->update(['read_time_minutes' => $readTimeMinutes]);
        }
    }

    /**
     * Strip HTML and Arabic script, then count remaining English words.
     */
    protected function countEnglishWords(?string $content): int
    {
        if (empty($content)) {
            return 0;
        }

        // Remove HTML tags first
        $plainText = strip_tags($content);

        // Remove Arabic script characters (Unicode range for Arabic + presentation forms + diacritics)
        $plainText = preg_replace('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]+/u', '', $plainText);

        // Collapse leftover whitespace
        $plainText = preg_replace('/\s+/', ' ', $plainText);
        $plainText = trim($plainText);

        return $plainText === '' ? 0 : str_word_count($plainText);
    }
}

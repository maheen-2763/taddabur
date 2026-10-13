<?php

namespace App\Support;

use App\Models\Story;

final class ReadingStats
{
    /** Arabic aur English dono ke liye sahi word count (str_word_count Arabic ko ginta hi nahi). */
    public static function words(?string $html): int
    {
        $text = trim(strip_tags(html_entity_decode((string) $html)));

        return $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
    }

    public static function minutes(int $words): int
    {
        return max(1, (int) ceil($words / 200));
    }

    /** Story ka word_count uske saare chapters se dobara nikalta hai. */
    public static function syncStory(Story $story): void
    {
        $story->forceFill([
            'word_count' => $story->chapters()->pluck('content')->sum(fn($c) => self::words($c)),
        ])->save();
    }
}

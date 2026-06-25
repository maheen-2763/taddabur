<?php


use App\Models\Story;

$stories = Story::with('chapters')->get();

foreach ($stories as $story) {
    $totalWords = $story->chapters->sum(function ($chapter) {
        // Strip HTML tags first
        $text = strip_tags($chapter->content);

        // Strip Arabic script characters (Unicode range) and diacritics/tashkeel
        $text = preg_replace('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', '', $text);

        // Clean up extra whitespace left behind after removing Arabic
        $text = preg_replace('/\s+/', ' ', trim($text));

        return str_word_count($text);
    });

    $readTimeMinutes = (int) ceil($totalWords / 200);

    $story->update(['read_time_minutes' => $readTimeMinutes]);

    echo "{$story->title}: {$totalWords} words | {$readTimeMinutes} min" . PHP_EOL;
}

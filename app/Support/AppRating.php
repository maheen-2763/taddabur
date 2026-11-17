<?php

namespace App\Support;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;

final class AppRating
{
    public const CACHE_KEY = 'app-rating-summary';

    public const MIN_RATINGS = 2;

    private const TTL_MINUTES = 10;

    /** @return array{count: int, average: float}|null Null until there are enough ratings to be meaningful. */
    public static function summary(): ?array
    {
        // The footer appears on every page, so a cache or database problem must never break it.
        return rescue(function () {
            $summary = Cache::remember(self::CACHE_KEY, now()->addMinutes(self::TTL_MINUTES), fn() => [
                'count' => Review::count(),
                'average' => round((float) Review::avg('rating'), 1),
            ]);

            return $summary['count'] >= self::MIN_RATINGS ? $summary : null;
        }, null);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

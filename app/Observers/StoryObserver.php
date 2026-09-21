<?php

namespace App\Observers;

use App\Models\Story;
use Illuminate\Support\Facades\Cache;

class StoryObserver
{
    public function saved(Story $story): void
    {
        Cache::forget('stories_stats');
    }

    public function deleted(Story $story): void
    {
        Cache::forget('stories_stats');
    }
}

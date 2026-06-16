<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\BookmarkService;
use App\Services\DashboardService;
use App\Services\NoteService;
use App\Services\QuranApiService;
use App\Services\QuranService;
use App\Services\StoryService;
use App\Services\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register as singletons — Laravel creates ONE instance
        // and reuses it everywhere it's injected (efficient)
        $this->app->singleton(QuranApiService::class);
        $this->app->singleton(QuranService::class);
        $this->app->singleton(StoryService::class);
        $this->app->singleton(BookmarkService::class);
        $this->app->singleton(NoteService::class);
        $this->app->singleton(SubscriptionService::class);
        $this->app->singleton(DashboardService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::bind('surah', function ($value) {
            return \App\Models\Surah::where('number', $value)->firstOrFail();
        });
    }
}

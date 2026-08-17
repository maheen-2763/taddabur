<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\BookmarkService;
use App\Services\DashboardService;
use App\Services\NoteService;
use App\Services\QuranApiService;
use App\Services\QuranService;
use App\Services\StoryService;
use Illuminate\Support\ServiceProvider;
use App\Models\StoryChapter;
use App\Observers\StoryChapterObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Support\Facades\Event;
<<<<<<< HEAD
use Illuminate\Support\Facades\View;
use App\Models\Hadith;
=======
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;
>>>>>>> e3e8ecc7a73d97f2b75dc728ecefd979783fc075

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
        $this->app->singleton(DashboardService::class);
    }

    public function boot(): void
    {

    Storage::extend('google', function ($app, $config) {
        $client = new GoogleClient();
        $client->setClientId($config['clientId']);
        $client->setClientSecret($config['clientSecret']);
        $client->refreshToken($config['refreshToken']);

        $service = new GoogleServiceDrive($client);
        $adapter = new GoogleDriveAdapter($service, $config['folderId'] ?? null);

        return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    });

        if (config('app.force_https', false)) {
            URL::forceScheme('https');
        }
        StoryChapter::observe(StoryChapterObserver::class);
        \Illuminate\Support\Facades\Route::bind('surah', function ($value) {
            return \App\Models\Surah::where('number', $value)->firstOrFail();
        });

        View::composer('layouts.app', function ($view) {
            $view->with('devNoteHadith', Hadith::find(14516));
        });

        Event::listen(Verified::class, SendWelcomeEmail::class);

        Paginator::useBootstrapFive();
    }
}

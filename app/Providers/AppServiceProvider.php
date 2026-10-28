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
use App\Models\Story;
use App\Observers\StoryObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Models\Hadith;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleServiceDrive;
use App\Mail\{ResetPasswordMail, VerifyEmailMail};
use Illuminate\Auth\Notifications\{ResetPassword, VerifyEmail};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;


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
        Story::observe(StoryObserver::class);

        StoryChapter::observe(StoryChapterObserver::class);
        \Illuminate\Support\Facades\Route::bind('surah', function ($value) {
            return \App\Models\Surah::where('number', $value)->firstOrFail();
        });

        View::composer('layouts.app', function ($view) {
            $view->with('devNoteHadith', Hadith::find(14516));
        });

        Event::listen(Verified::class, SendWelcomeEmail::class);

        Paginator::useBootstrapFive();

        Password::defaults(fn() => Password::min(8)->letters()->numbers());

        VerifyEmail::toMailUsing(fn($user, string $url) => (new VerifyEmailMail($url, $user->name))->to($user->email));

        ResetPassword::toMailUsing(function ($user, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false));
            $broker = config('auth.defaults.passwords');

            return (new ResetPasswordMail($url, $user->name, (int) config("auth.passwords.$broker.expire", 60)))
                ->to($user->email);
        });

        RateLimiter::for('login', fn(Request $r) => Limit::perMinute(20)->by($r->ip()));
        RateLimiter::for('register', fn(Request $r) => Limit::perHour(10)->by($r->ip()));
        RateLimiter::for('password-email', fn(Request $r) => [
            Limit::perMinute(3)->by($r->ip()),
            Limit::perHour(5)->by(Str::lower((string) $r->input('email'))),
        ]);
        RateLimiter::for('password-reset', fn(Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('verification-send', fn(Request $r) => [
            Limit::perMinute(1)->by($r->user()?->id ?: $r->ip()),
            Limit::perHour(6)->by($r->user()?->id ?: $r->ip()),
        ]);
    }
}

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\VerifyChapterGaps;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Re-import any missing translations weekly (catches new additions)
Schedule::command('quran:import-translations --all')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->runInBackground()
    ->emailOutputOnFailure(config('mail.from.address'));

Schedule::command('backup:run --keep=14')->dailyAt('02:00')->timezone('Asia/Kolkata')->withoutOverlapping();

Schedule::command('daily:generate-ayah')
    ->hourlyAt(5)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/daily-ayah.log'));

Schedule::command('daily:generate-hadith')
    ->hourlyAt(5)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/daily-hadith.log'));

Schedule::command(VerifyChapterGaps::class)
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/hadith-gaps.log'));

Schedule::command('quran:verify-translations')->weekly();

Schedule::command('stories:backup')->weekly();

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\VerifyChapterGaps;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('reflection:send')
    ->dailyAt('05:00');

Schedule::command(VerifyChapterGaps::class)
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/hadith-gaps.log'));


Schedule::command('quran:verify-translations')->weekly();

Schedule::command('stories:backup')->weekly();

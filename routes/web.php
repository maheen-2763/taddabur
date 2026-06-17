<?php

use App\Http\Controllers\Admin\AdminDailyReflectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReflectionController;


// Re-import any missing translations weekly (catches new additions)
Schedule::command('quran:import-translations --all')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->runInBackground()
    ->emailOutputOnFailure(config('mail.from.address'));

// ============================================================
// PUBLIC ROUTES — No login required
// ============================================================

// Landing page

Route::get('/', [HomeController::class, 'index'])
    ->name('home');




// Public Quran browsing (reading only, no tafsir/audio)
Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
// ✅ ADD THIS — search must come BEFORE /{surah}
Route::get('/quran/search', [QuranController::class, 'search'])
    ->name('quran.search');
// ✅ This comes AFTER search
Route::get('/quran/{surah}', [QuranController::class, 'show'])->name('quran.show');

Route::get(
    '/quran/{surah}/{ayah}/tafsir-page',
    [QuranController::class, 'tafsirPage']
)
    ->middleware(['auth', 'verified'])
    ->name('quran.tafsir');

// Public stories listing (shows free stories only)
Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{story:slug}', [StoryController::class, 'show'])->name('stories.show');
//Route::get('/stories/{story:slug}/{chapter}', [StoryController::class, 'chapter'])->name('stories.chapter');
Route::get(
    '/stories/{story:slug}/{chapter:slug}',
    [StoryController::class, 'chapter']
)->name('stories.chapter');
// Prophets listing
Route::get('/prophets', [StoryController::class, 'prophets'])->name('prophets.index');
Route::get('/prophets/{prophet:slug}', [StoryController::class, 'prophetStories'])->name('prophets.show');


// Pricing page
Route::get('/pricing', [SubscriptionController::class, 'pricing'])->name('pricing');

// ============================================================
// AUTHENTICATED ROUTES — Must be logged in
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // --------------------------------------------------------
    // QURAN ROUTES
    // --------------------------------------------------------
    Route::prefix('quran')->name('quran.')->group(function () {

        Route::get('/{surah}', [QuranController::class, 'show'])
            ->name('show');

        // ✅ Removed middleware — controller handles plan check
        Route::get('/{surah}/{ayah}/tafsir', [QuranController::class, 'tafsir'])
            ->name('tafsir');

        // ✅ Removed middleware — controller handles plan check
        Route::get('/{surah}/{ayah}/audio', [QuranController::class, 'audio'])
            ->name('audio');

        Route::get('/{surah}/{ayah}/translation', [QuranController::class, 'translation'])
            ->name('translation');

        Route::post('/progress', [QuranController::class, 'saveProgress'])
            ->name('progress.save');

        Route::post('/{surah}/complete', [QuranController::class, 'markSurahComplete'])
            ->name('complete');

        Route::post(
            '/quran/{ayah}/audio-completed',
            [QuranController::class, 'audioCompleted']
        )->name('quran.audio.completed');
    });
    // --------------------------------------------------------
    // STORY ROUTES
    // --------------------------------------------------------
    Route::prefix('stories')->name('stories.')->group(function () {

        // Mark chapter as read (AJAX)
        Route::post('/{story}/chapters/{chapter}/complete', [StoryController::class, 'markComplete'])
            ->name('chapter.complete');
    });

    // --------------------------------------------------------
    // BOOKMARKS ROUTES
    // --------------------------------------------------------
    Route::prefix('bookmarks')->name('bookmarks.')->group(function () {
        Route::get('/', [BookmarkController::class, 'index'])->name('index');
        Route::post('/', [BookmarkController::class, 'store'])->name('store');
        Route::delete('/{bookmark}', [BookmarkController::class, 'destroy'])->name('destroy');
    });

    // --------------------------------------------------------
    // NOTES ROUTES — Requires 'has_notes' plan feature
    // --------------------------------------------------------
    Route::middleware('plan:has_notes')
        ->prefix('notes')
        ->name('notes.')
        ->group(function () {
            Route::get('/', [NoteController::class, 'index'])->name('index');
            Route::post('/', [NoteController::class, 'store'])->name('store');
            Route::put('/{note}', [NoteController::class, 'update'])->name('update');
            Route::delete('/{note}', [NoteController::class, 'destroy'])->name('destroy');
        });

    // --------------------------------------------------------
    // SUBSCRIPTION ROUTES
    // --------------------------------------------------------
    Route::prefix('subscription')
        ->name('subscription.')
        ->middleware(['auth', 'verified'])
        ->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::get('/upgrade', [SubscriptionController::class, 'upgrade'])->name('upgrade');
            Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
            Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
            Route::post('/create-order', [SubscriptionController::class, 'createOrder'])->name('create-order');
            Route::post('/verify', [SubscriptionController::class, 'verifyPayment'])->name('verify');
            Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');
            Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
        });
    // --------------------------------------------------------
    // PROFILE ROUTES
    // --------------------------------------------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences');
    });
});

Route::get('/test-name', function () {

    return view('test');
});

// ============================================================
// ADMIN ROUTES
// ============================================================

// ── ADMIN ROUTES ──────────────────────────────────────────
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // Users
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])
            ->name('users.show');
        Route::patch('/users/{user}/plan', [App\Http\Controllers\Admin\UserController::class, 'updatePlan'])
            ->name('users.update-plan');

        // ── STORIES ──────────────────────────────────────────
        Route::get('/stories', [App\Http\Controllers\Admin\StoryController::class, 'index'])
            ->name('stories.index');
        Route::get('/stories/create', [App\Http\Controllers\Admin\StoryController::class, 'create'])
            ->name('stories.create');
        Route::post('/stories', [App\Http\Controllers\Admin\StoryController::class, 'store'])
            ->name('stories.store');
        Route::get('/stories/{story}/edit', [App\Http\Controllers\Admin\StoryController::class, 'edit'])
            ->name('stories.edit');
        Route::patch('/stories/{story}', [App\Http\Controllers\Admin\StoryController::class, 'update'])
            ->name('stories.update');
        Route::delete('/stories/{story}', [App\Http\Controllers\Admin\StoryController::class, 'destroy'])
            ->name('stories.destroy');

        // ── CHAPTERS (nested under stories) ──────────────────
        // ⚠️ These MUST come before /chapters/{chapter} routes
        Route::get('/stories/{story}/chapters/create', [App\Http\Controllers\Admin\ChapterController::class, 'create'])
            ->name('chapters.create');
        Route::post('/stories/{story}/chapters', [App\Http\Controllers\Admin\ChapterController::class, 'store'])
            ->name('chapters.store');

        // ── CHAPTERS (standalone) ─────────────────────────────
        Route::get('/chapters/{chapter}/edit', [App\Http\Controllers\Admin\ChapterController::class, 'edit'])
            ->name('chapters.edit');
        Route::patch('/chapters/{chapter}', [App\Http\Controllers\Admin\ChapterController::class, 'update'])
            ->name('chapters.update');
        Route::delete('/chapters/{chapter}', [App\Http\Controllers\Admin\ChapterController::class, 'destroy'])
            ->name('chapters.destroy');

        // Prophets
        Route::get('/prophets', [App\Http\Controllers\Admin\ProphetController::class, 'index'])
            ->name('prophets.index');
        Route::get('/prophets/{prophet}/edit', [App\Http\Controllers\Admin\ProphetController::class, 'edit'])
            ->name('prophets.edit');
        Route::patch('/prophets/{prophet}', [App\Http\Controllers\Admin\ProphetController::class, 'update'])
            ->name('prophets.update');



        Route::resource(
            'daily-reflections',
            AdminDailyReflectionController::class
        )->only([
            'index',
            'create',
            'store',
            'destroy',
        ]);

        Route::get(
            'ayahs/{surah}',
            [AdminDailyReflectionController::class, 'ayahs']
        )->name('daily-reflections.ayahs');
    });

Route::get(
    '/reflections/{dailyContent}',
    [ReflectionController::class, 'show']
)->name('reflections.show');


Route::get('/time-test', function () {
    return now();
});

require __DIR__ . '/auth.php';

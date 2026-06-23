<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ayah;
use App\Models\AllahName;
use App\Models\Bookmark;
use App\Models\Note;
use App\Models\Prophet;
use App\Models\Story;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ═══════════════════════════════════════════════
        // USERS  (your original queries — untouched)
        // ═══════════════════════════════════════════════

        $totalUsers   = User::count();
        $freeUsers    = User::where('plan', 'free')->count();
        $basicUsers   = User::where('plan', 'basic')->count();
        $premiumUsers = User::where('plan', 'premium')->count();

        // Verified vs unverified
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        // New registrations this calendar month
        $newUsersMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->count();

        // 7-day sparkline — oldest day first, today last
        $dailySignups = collect(range(6, 0))
            ->map(fn($d) => User::whereDate('created_at', now()->subDays($d))->count())
            ->values()
            ->toArray();

        // ═══════════════════════════════════════════════
        // REVENUE  (your original calculation — untouched)
        // plan_expires_at lives on users table
        // ═══════════════════════════════════════════════

        $monthlyRevenue = ($basicUsers * 1.99) + ($premiumUsers * 3.99);

        // Expiring soon — your original query, untouched
        $expiringSoon = User::where('plan', '!=', 'free')
            ->whereBetween('plan_expires_at', [now(), now()->addDays(7)])
            ->count();

        // Churned in last 30 days — users whose plan_expires_at passed recently
        $churned = User::where('plan', '!=', 'free')
            ->whereBetween('plan_expires_at', [now()->subDays(30), now()])
            ->count();

        // ═══════════════════════════════════════════════
        // ACTIVE USERS
        // Proxy via reading_progress until user_sessions is live
        // ═══════════════════════════════════════════════

        $activeUsers7d = DB::table('reading_progress')
            ->where('updated_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Session time — null until user_sessions migration is run
        $hasSessionTable   = DB::getSchemaBuilder()->hasTable('user_sessions');
        $avgSessionMin     = null;
        $avgSessionFree    = null;
        $avgSessionPremium = null;

        if ($hasSessionTable) {
            $base = fn() => DB::table('user_sessions')
                ->where('user_sessions.created_at', '>=', now()->subDays(30));

            $avgSessionMin = round($base()->avg('duration_minutes') ?? 0);

            $avgSessionFree = round(
                $base()
                    ->join('users', 'users.id', '=', 'user_sessions.user_id')
                    ->where('users.plan', 'free')
                    ->avg('user_sessions.duration_minutes') ?? 0
            );

            $avgSessionPremium = round(
                $base()
                    ->join('users', 'users.id', '=', 'user_sessions.user_id')
                    ->where('users.plan', 'premium')
                    ->avg('user_sessions.duration_minutes') ?? 0
            );
        }

        // ═══════════════════════════════════════════════
        // QURAN CONTENT
        // Your Ayah model used directly (as in your original)
        // Real table names from your migrations:
        //   ayah_tafsirs, ayah_translations, recitations
        // ═══════════════════════════════════════════════

        $totalAyahs        = Ayah::count();                          // your original
        $totalTafsir       = DB::table('ayah_tafsirs')->count();
        $totalTranslations = DB::table('ayah_translations')->count();
        $totalReciters     = DB::table('recitations')->count();
        $totalBookmarks    = Bookmark::count();
        $totalNotes        = Note::count();

        $avgBookmarks = $totalUsers > 0
            ? round($totalBookmarks / $totalUsers, 1)
            : 0;

        $avgNotesPremium = $premiumUsers > 0
            ? round(
                Note::whereHas('user', fn($q) => $q->where('plan', 'premium'))->count()
                    / $premiumUsers,
                1
            )
            : 0;

        // Most bookmarked surah name
        // $mostBookmarkedSurah = DB::table('bookmarks')
        //     ->join('surahs', 'surahs.number', '=', 'bookmarks.surah_number')
        //     ->select('surahs.name_simple', DB::raw('count(*) as total'))
        //     ->groupBy('surahs.number', 'surahs.name_simple')
        //     ->orderByDesc('total')
        //     ->first()
        //     ?->name_simple;

        // // Most read surahs — reading_progress → surahs join
        // $mostReadSurahs = DB::table('reading_progress')
        //     ->join('surahs', 'surahs.number', '=', 'reading_progress.surah_number')
        //     ->select(
        //         'surahs.name_simple as name',
        //         'reading_progress.surah_number as number',
        //         DB::raw('COUNT(DISTINCT reading_progress.user_id) as reads')
        //     )
        //     ->groupBy('reading_progress.surah_number', 'surahs.name_simple')
        //     ->orderByDesc('reads')
        //     ->limit(7)
        //     ->get()
        //     ->toArray();

        // ═══════════════════════════════════════════════
        // BONUS QURAN ENGAGEMENT
        // From your extra tables: user_read_ayahs,
        // listened_ayahs, chapter_completions, juzs
        // ═══════════════════════════════════════════════

        $totalReadAyahs          = DB::table('user_read_ayahs')->count();
        $totalListenedAyahs      = DB::table('listened_ayahs')->count();
        $totalChapterCompletions = DB::table('chapter_completions')->count();
        $totalJuzs               = DB::table('juzs')->count();

        // ═══════════════════════════════════════════════
        // CONTENT LIBRARY
        // ═══════════════════════════════════════════════

        $totalStories    = Story::count();                           // your original
        $totalProphets   = Prophet::count();
        $totalAllahNames = AllahName::count();

        $totalSahabas = DB::getSchemaBuilder()->hasTable('sahabas')
            ? DB::table('sahabas')->count()
            : 0;

        $totalImams = DB::getSchemaBuilder()->hasTable('imams')
            ? DB::table('imams')->count()
            : 0;

        // ═══════════════════════════════════════════════
        // TOP ENGAGED USERS
        // Uses user_sessions when available,
        // falls back to user_read_ayahs count
        // ═══════════════════════════════════════════════

        if ($hasSessionTable) {
            $topEngagedUsers = DB::table('user_sessions')
                ->join('users', 'users.id', '=', 'user_sessions.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.plan',
                    DB::raw('SUM(user_sessions.duration_minutes) as total_minutes'),
                    DB::raw('COUNT(user_sessions.id) as sessions')
                )
                ->where('user_sessions.created_at', '>=', now()->subDays(30))
                ->groupBy('users.id', 'users.name', 'users.email', 'users.plan')
                ->orderByDesc('total_minutes')
                ->limit(5)
                ->get();
        } else {
            // Fallback: ranked by ayahs read this month
            $topEngagedUsers = DB::table('user_read_ayahs')
                ->join('users', 'users.id', '=', 'user_read_ayahs.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.plan',
                    DB::raw('COUNT(user_read_ayahs.id) as total_minutes'),
                    DB::raw('COUNT(DISTINCT DATE(user_read_ayahs.created_at)) as sessions')
                )
                ->where('user_read_ayahs.created_at', '>=', now()->subDays(30))
                ->groupBy('users.id', 'users.name', 'users.email', 'users.plan')
                ->orderByDesc('total_minutes')
                ->limit(5)
                ->get();
        }

        // ═══════════════════════════════════════════════
        // RECENT DATA  (your originals — untouched)
        // ═══════════════════════════════════════════════

        $recentUsers = User::latest()->take(10)->get();              // was take(5)

        $totalSubs  = Subscription::count();                         // your original
        $recentSubs = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(15)                                               // was take(5)
            ->get();

        // ═══════════════════════════════════════════════
        // COMPLAINTS & REVIEWS
        // Returns empty safely if tables not yet migrated
        // ═══════════════════════════════════════════════

        $hasComplaints    = DB::getSchemaBuilder()->hasTable('complaints');
        $openComplaints   = $hasComplaints
            ? DB::table('complaints')->where('status', 'open')->count()
            : 0;
        $recentComplaints = $hasComplaints
            ? \App\Models\Complaint::with('user')->latest()->limit(5)->get()
            : collect();

        $hasReviews      = DB::getSchemaBuilder()->hasTable('reviews');
        $avgRating       = $hasReviews ? round(DB::table('reviews')->avg('rating'), 1) : 0;
        $totalReviews    = $hasReviews ? DB::table('reviews')->count() : 0;
        $ratingBreakdown = $hasReviews
            ? DB::table('reviews')
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray()
            : [];
        $recentReviews = $hasReviews
            ? Review::with('user')->latest()->limit(3)->get()->toArray()
            : [];

        // ═══════════════════════════════════════════════
        // PASS TO VIEW
        // ═══════════════════════════════════════════════

        return view('admin.dashboard', compact(
            // Users
            'totalUsers',
            'freeUsers',
            'basicUsers',
            'premiumUsers',
            'verifiedUsers',
            'newUsersMonth',
            'dailySignups',
            'activeUsers7d',
            'avgSessionMin',
            'avgSessionFree',
            'avgSessionPremium',
            'churned',

            // Revenue
            'monthlyRevenue',
            'expiringSoon',

            // Quran content
            'totalAyahs',
            'totalTafsir',
            'totalTranslations',
            'totalReciters',
            'totalBookmarks',
            'totalNotes',
            'avgBookmarks',
            'avgNotesPremium',
            // 'mostBookmarkedSurah',
            // 'mostReadSurahs',

            // Bonus quran engagement
            'totalReadAyahs',
            'totalListenedAyahs',
            'totalChapterCompletions',
            'totalJuzs',

            // Content library
            'totalStories',
            'totalProphets',
            'totalAllahNames',
            'totalSahabas',
            'totalImams',

            // Subscriptions
            'totalSubs',
            'recentSubs',

            // Engagement
            'topEngagedUsers',
            'recentUsers',

            // Feedback
            'openComplaints',
            'recentComplaints',
            'avgRating',
            'totalReviews',
            'ratingBreakdown',
            'recentReviews',
        ));
    }
}

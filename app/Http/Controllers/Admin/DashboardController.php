<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ayah;
use App\Models\Complaint;
use App\Models\Donor;
use App\Models\Note;
use App\Models\Review;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Backup;
use Illuminate\Support\Facades\Storage;
use App\Models\DailyContent;

class DashboardController extends Controller
{
    private const EXPECTED_SURAHS      = 114;
    private const EXPECTED_AYAHS       = 6236;
    private const EXPECTED_JUZS        = 30;
    private const EXPECTED_ALLAH_NAMES = 99;

    // Donor.status ki "paid" value — tinker se verify karna (neeche instructions)
    private const DONATION_PAID_STATUS = DonorController::PAID_STATUS;

    public function index(): View
    {
        $users     = $this->users();
        $sources   = $this->sources();
        $feedback  = $this->feedback();
        $donations = $this->donations();

        return view('admin.dashboard', [
            'users'      => $users,
            'quran'      => $this->quran(),
            'library'    => $this->library(),
            'topSurahs'  => $this->mostReadSurahs(),
            'topReaders' => $this->topReaders(),
            'feedback'   => $feedback,
            'donations'  => $donations,
            'sources'    => $sources,
            'alerts'     => $this->alerts($sources, $feedback, $donations),
        ]);
    }

    private function users(): array
    {
        $total    = User::count();
        $verified = User::whereNotNull('email_verified_at')->count();

        $perDay = User::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->pluck('created_at')
            ->countBy(fn($date) => $date->toDateString());

        // Active = pichhle 7 din mein koi bhi real activity
        $since  = now()->subDays(7);
        $active = DB::query()->fromSub(
            DB::table('user_read_ayahs')->where('created_at', '>=', $since)->select('user_id')
                ->union(DB::table('listened_ayahs')->where('created_at', '>=', $since)->select('user_id'))
                ->union(DB::table('chapter_completions')->where('created_at', '>=', $since)->select('user_id'))
                ->union(DB::table('hadith_reads')->where('read_at', '>=', $since)->select('user_id'))
                ->union(DB::table('reading_progress')->where('updated_at', '>=', $since)->select('user_id')),
            'activity'
        )->count();

        return [
            'total'        => $total,
            'verified'     => $verified,
            'unverified'   => $total - $verified,
            'verifiedRate' => $total > 0 ? (int) round($verified / $total * 100) : 0,
            'newThisMonth' => User::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'daily'        => collect(range(6, 0))->map(fn($i) => [
                'label' => now()->subDays($i)->format('d M'),
                'count' => $perDay[now()->subDays($i)->toDateString()] ?? 0,
            ])->all(),
            'active7d'     => $active,
            'recent'       => User::latest()->take(10)
                ->get(['id', 'name', 'email', 'email_verified_at', 'created_at']),
        ];
    }

    private function quran(): array
    {
        $bookmarks = DB::table('bookmarks')->count();
        $totalUsers = User::count();

        $topBookmarked = DB::table('bookmarks')
            ->join('ayahs', 'ayahs.id', '=', 'bookmarks.bookmarkable_id')
            ->join('surahs', 'surahs.id', '=', 'ayahs.surah_id')
            ->where('bookmarks.bookmarkable_type', (new Ayah)->getMorphClass())
            ->select('surahs.name_transliteration', DB::raw('COUNT(*) as total'))
            ->groupBy('surahs.id', 'surahs.name_transliteration')
            ->orderByDesc('total')
            ->first();

        return [
            'surahs'              => DB::table('surahs')->count(),
            'ayahs'               => Ayah::count(),
            'juzs'                => DB::table('juzs')->count(),
            'bookmarks'           => $bookmarks,
            'notes'               => Note::count(),
            'read'                => DB::table('user_read_ayahs')->count(),
            'listened'            => DB::table('listened_ayahs')->count(),
            'chapters'            => DB::table('chapter_completions')->count(),
            'hadithReads'         => DB::table('hadith_reads')->count(),
            'avgBookmarks'        => $totalUsers > 0 ? round($bookmarks / $totalUsers, 1) : 0,
            'mostBookmarkedSurah' => $topBookmarked?->name_transliteration,
        ];
    }

    private function library(): array
    {
        return [
            'prophets'    => DB::table('prophets')->count(),
            'stories'     => Story::count(),
            'published'   => Story::where('is_published', true)->count(),
            'chapters'    => DB::table('story_chapters')->whereIn('story_id', Story::select('id'))->count(),
            'hadiths'     => DB::table('hadiths')->count(),
            'collections' => DB::table('hadith_collections')->count(),
            'allahNames'  => DB::table('allah_names')->count(),
            'scholars'    => DB::table('scholars')->count(),
        ];
    }

    private function mostReadSurahs(): Collection
    {
        return DB::table('user_read_ayahs')
            ->join('ayahs', 'ayahs.id', '=', 'user_read_ayahs.ayah_id')
            ->join('surahs', 'surahs.id', '=', 'ayahs.surah_id')
            ->select('surahs.number', 'surahs.name_transliteration as name', DB::raw('COUNT(*) as reads'))
            ->groupBy('surahs.id', 'surahs.number', 'surahs.name_transliteration')
            ->orderByDesc('reads')
            ->limit(7)
            ->get();
    }

    private function topReaders(): Collection
    {
        return DB::table('user_read_ayahs')
            ->join('users', 'users.id', '=', 'user_read_ayahs.user_id')
            ->where('user_read_ayahs.created_at', '>=', now()->subDays(30))
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as ayahs_read'),
                DB::raw('COUNT(DISTINCT DATE(user_read_ayahs.created_at)) as active_days')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('ayahs_read')
            ->limit(5)
            ->get();
    }

    private function feedback(): array
    {
        return [
            'openComplaints' => Complaint::where('status', 'open')->count(),
            'complaints'     => Complaint::with('user:id,name')->latest()->limit(5)->get(),
            'reviewCount'    => Review::count(),
            'avgRating'      => round((float) Review::avg('rating'), 1),
            'breakdown'      => Review::select('rating', DB::raw('COUNT(*) as total'))
                ->groupBy('rating')
                ->pluck('total', 'rating'),
            'reviews'        => Review::with('user:id,name')->latest()->limit(3)->get(),
        ];
    }

    private function donations(): array
    {
        $paid = fn() => Donor::where('status', self::DONATION_PAID_STATUS);

        return [
            'paidStatus' => self::DONATION_PAID_STATUS,
            'attempts'   => Donor::count(),
            'paidCount'  => $paid()->count(),
            'paidTotal'  => (float) $paid()->sum('amount'),
            'byStatus'   => Donor::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status'),
            'recent'     => Donor::latest()->limit(5)->get(),
        ];
    }

    private function alerts(array $sources, array $feedback, array $donations): array
    {
        $alerts = [];

        $failedChecks = collect($sources['checks'])->where('ok', false)->count();
        if ($failedChecks > 0) {
            $alerts[] = ['danger', "{$failedChecks} Quran data integrity check(s) failed", '#data-sources'];
        }

        foreach (['tafsirs' => 'Tafsir', 'translations' => 'Translation'] as $key => $label) {
            $partial = $sources[$key]->filter(fn($row) => $row->is_active && $row->percent < 100)->count();
            if ($partial > 0) {
                $alerts[] = ['warning', "{$partial} active {$label} source(s) do not cover all ayahs", '#data-sources'];
            }
        }

        $needsReview = (int) $sources['hadith']->sum('needs_review');
        if ($needsReview > 0) {
            $alerts[] = ['warning', number_format($needsReview) . ' hadiths are marked "needs review"', '#data-sources'];
        }

        $partialReciters = $sources['reciters']->where('sync', 'partial')->count();
        if ($partialReciters > 0) {
            $alerts[] = ['warning', "{$partialReciters} reciter(s) have incomplete word timings", '#data-sources'];
        }

        if ($feedback['openComplaints'] > 0) {
            $alerts[] = ['danger', "{$feedback['openComplaints']} open complaint(s)", '#feedback'];
        }

        $notCompleted = $donations['attempts'] - $donations['paidCount'];
        if ($notCompleted > 0) {
            $alerts[] = ['info', "{$notCompleted} donation attempt(s) not completed (pending or failed)", route('admin.donations.index')];
        }

        $latest = Backup::latest()->first();
        if (! $latest) {
            $alerts[] = ['warning', 'No backup recorded yet', route('admin.backups')];
        } elseif (! Storage::exists('backups/' . basename($latest->filename))) {
            $alerts[] = ['danger', 'Latest backup file is missing on disk', route('admin.backups')];
        } elseif ($latest->created_at->lt(now()->subDays(7))) {
            $alerts[] = ['warning', 'Last backup is older than 7 days', route('admin.backups')];
        }

        foreach (['ayah', 'hadith'] as $type) {
            $exists = DailyContent::where('type', $type)->whereDate('scheduled_for', today())->exists();

            if (! $exists) {
                $alerts[] = ['danger', "Today's daily {$type} has not been generated", route('admin.daily-reflections.index')];
            }
        }

        return $alerts;
    }

    private function sources(): array
    {
        $totalAyahs = Ayah::count();

        $surahMismatch = DB::table('surahs')
            ->leftJoin('ayahs', 'ayahs.surah_id', '=', 'surahs.id')
            ->groupBy('surahs.id', 'surahs.ayah_count')
            ->havingRaw('COUNT(ayahs.id) != surahs.ayah_count')
            ->select('surahs.id')
            ->get()
            ->count();

        $checks = collect([
            ['label' => 'Surahs',                         'actual' => DB::table('surahs')->count(),          'expected' => self::EXPECTED_SURAHS],
            ['label' => 'Ayahs',                          'actual' => $totalAyahs,                           'expected' => self::EXPECTED_AYAHS],
            ['label' => 'Sum of surah ayah counts',       'actual' => (int) DB::table('surahs')->sum('ayah_count'), 'expected' => self::EXPECTED_AYAHS],
            ['label' => 'Surahs with ayah count mismatch', 'actual' => $surahMismatch,                       'expected' => 0],
            ['label' => 'Juz divisions',                  'actual' => DB::table('juzs')->count(),            'expected' => self::EXPECTED_JUZS],
            ['label' => 'Asma-ul-Husna (Allah names)',    'actual' => DB::table('allah_names')->count(),     'expected' => self::EXPECTED_ALLAH_NAMES],
        ])->map(fn($c) => $c + ['ok' => $c['actual'] === $c['expected']])->all();

        $hadith = DB::table('hadith_collections')
            ->leftJoin('hadiths', 'hadiths.collection_id', '=', 'hadith_collections.id')
            ->select(
                'hadith_collections.id',
                'hadith_collections.name',
                'hadith_collections.scholar',
                'hadith_collections.total_hadith',
                DB::raw('COUNT(hadiths.id) as actual'),
                DB::raw('COALESCE(SUM(CASE WHEN hadiths.needs_review = 1 THEN 1 ELSE 0 END), 0) as needs_review'),
                DB::raw('COALESCE(SUM(CASE WHEN hadiths.translation_incomplete = 1 THEN 1 ELSE 0 END), 0) as incomplete')
            )
            ->groupBy(
                'hadith_collections.id',
                'hadith_collections.name',
                'hadith_collections.scholar',
                'hadith_collections.total_hadith'
            )
            ->orderBy('hadith_collections.id')
            ->get();

        return [
            'totalAyahs'   => $totalAyahs,
            'checks'       => $checks,
            'translations' => $this->coverage(
                'translations',
                'ayah_translations',
                'translation_id',
                ['id', 'name', 'author', 'language_name', 'source', 'is_active', 'sort_order'],
                $totalAyahs
            ),
            'tafsirs'      => $this->coverage(
                'tafsirs',
                'ayah_tafsirs',
                'tafsir_id',
                ['id', 'name', 'scholar', 'language_name', 'source', 'is_active', 'sort_order'],
                $totalAyahs
            ),
            'hadith'       => $hadith,
            'reciters'     => $this->reciterSync($totalAyahs),
        ];
    }

    private function reciterSync(int $totalAyahs): Collection
    {
        $timings = DB::table('reciter_word_timings')
            ->select(
                'reciter_id',
                DB::raw('COUNT(*) as timing_rows'),
                DB::raw('COUNT(DISTINCT surah_number * 1000 + ayah_number) as ayahs_covered')
            )
            ->groupBy('reciter_id')
            ->get()
            ->keyBy('reciter_id');

        return DB::table('recitations')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'style', 'is_active'])
            ->map(function ($reciter) use ($timings, $totalAyahs) {
                $t = $timings->get($reciter->id);

                $reciter->timing_rows   = (int) ($t->timing_rows ?? 0);
                $reciter->ayahs_covered = (int) ($t->ayahs_covered ?? 0);
                $reciter->coverage      = $totalAyahs > 0 ? round($reciter->ayahs_covered / $totalAyahs * 100, 1) : 0;
                $reciter->missing_ayahs = max(0, $totalAyahs - $reciter->ayahs_covered);
                $reciter->sync          = match (true) {
                    $reciter->ayahs_covered >= $totalAyahs && $totalAyahs > 0 => 'word',
                    $reciter->ayahs_covered > 0                               => 'partial',
                    default                                                   => 'approximate',
                };

                return $reciter;
            });
    }

    /** Har translation/tafsir ki ayah-coverage (kitni ayahs ke liye entry maujood hai). */
    private function coverage(string $table, string $pivot, string $foreignKey, array $columns, int $totalAyahs): Collection
    {
        $qualified = array_map(fn($c) => "{$table}.{$c}", $columns);

        return DB::table($table)
            ->leftJoin($pivot, "{$pivot}.{$foreignKey}", '=', "{$table}.id")
            ->select(array_merge($qualified, [DB::raw("COUNT({$pivot}.id) as entries")]))
            ->groupBy($qualified)
            ->orderBy("{$table}.sort_order")
            ->get()
            ->map(function ($row) use ($totalAyahs) {
                $row->percent = $totalAyahs > 0 ? round($row->entries / $totalAyahs * 100, 1) : 0;

                return $row;
            });
    }
}

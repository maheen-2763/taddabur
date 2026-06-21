<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\UserReadAyah;
use App\Models\Ayah;
use App\Models\AyahTafsir;
use App\Models\AyahTranslation;
use App\Models\Recitation;
use App\Models\Surah;
use App\Models\SurahProgress;
use App\Models\Tafsir;
use App\Models\Translation;
use App\Services\BookmarkService;
use App\Services\QuranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ListenedAyah;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class QuranController extends Controller
{
    public function __construct(
        private QuranService    $quranService,
        private BookmarkService $bookmarkService,
    ) {}

    // ── GET /quran ────────────────────────────────────────
    public function index(): View
    {
        $surahs       = $this->quranService->getAllSurahs();
        $meccanCount  = $surahs->where('revelation_type', 'meccan')->count();
        $medinanCount = $surahs->where('revelation_type', 'medinan')->count();

        $completedSurahIds = [];
        $completedCount    = 0;

        if (Auth::check()) {
            $completedSurahIds = SurahProgress::where('user_id', Auth::id())
                ->where('is_completed', true)
                ->pluck('surah_id')
                ->toArray();

            $completedCount = count($completedSurahIds);
        }

        return view('quran.index', compact(
            'surahs',
            'meccanCount',
            'medinanCount',
            'completedSurahIds',
            'completedCount'
        ));
    }

    // ── GET /quran/{surah} ───────────────────────────────
    public function show(Request $request, Surah $surah): View
    {
        $user            = Auth::user();
        $translationSlug = $request->get(
            'translation',
            $user?->preferred_translation ?? 'sahih-international'
        );

        $data = $this->quranService->getSurahForReading($surah, $user, $translationSlug);
        $data['readAyahsCount'] = $user
            ? $this->quranService->getReadAyahsCount($user, $surah)
            : 0;

        // After $this->quranService->getSurahForReading(...) is called
        $data['userNotes'] = $user
            ? Note::where('user_id', $user->id)
            ->whereIn('ayah_id', $data['ayahs']->pluck('id'))
            ->get()
            ->keyBy('ayah_id')
            : collect();

        $data['readAyahIds'] = $user
            ? UserReadAyah::where('user_id', $user->id)
            ->whereIn('ayah_id', $data['ayahs']->pluck('id'))
            ->pluck('ayah_id')
            ->toArray()
            : [];

        // ✅ Plan info passed to view
        $data['isPremium']      = $this->quranService->userIsPremium($user);
        $data['canAccessTafsir'] = $this->quranService->userCanAccessTafsir($user);
        $data['upgradeUrl']     = route('subscription.upgrade');

        // ✅ Reading progress
        $data['quranProgress']  = $user
            ? $this->quranService->getQuranProgress($user)
            : null;

        // ✅ Consistent variable name — $isSurahCompleted
        $data['isSurahCompleted'] = $user
            ? SurahProgress::where('user_id', $user->id)
            ->where('surah_id', $surah->id)
            ->where('is_completed', true)
            ->exists()
            : false;

        return view('quran.show', $data);
    }

    // ── GET /quran/{surah}/{ayah}/tafsir (AJAX) ──────────
    public function tafsir(Request $request, Surah $surah, Ayah $ayah): JsonResponse
    {
        // ✅ Server-side plan check — cannot bypass via direct URL
        if (!$this->quranService->userCanAccessTafsir(Auth::user())) {
            return response()->json([
                'error'       => 'upgrade_required',
                'message'     => 'Tafsir requires a paid plan.',
                'upgrade_url' => route('subscription.upgrade'),
            ], 403);
        }

        $tafsirSlug = $request->get('tafsir', Auth::user()?->preferred_tafsir ?? 'ibn-kathir-en');
        $tafsir     = Tafsir::where('slug', $tafsirSlug)->first();

        // Fallback to first available tafsir
        if (!$tafsir) {
            $tafsir = Tafsir::where('is_active', true)->first();
        }

        if (!$tafsir) {
            return response()->json(['error' => 'No tafsir available.'], 404);
        }

        $ayahTafsir = AyahTafsir::where('ayah_id', $ayah->id)
            ->where('tafsir_id', $tafsir->id)
            ->first();

        // Not cached — fetch from API
        if (!$ayahTafsir) {
            $ayahTafsir = $this->fetchAndStoreTafsir($ayah, $tafsir);
        }

        if (!$ayahTafsir) {
            return response()->json(['error' => 'Tafsir not available for this ayah.'], 404);
        }

        return response()->json([
            'ayah_reference' => "{$surah->number}:{$ayah->number}",
            'ayah_arabic'    => $ayah->text_arabic,
            'tafsir_name'    => $tafsir->name,
            'scholar'        => $tafsir->scholar,
            'text'           => $ayahTafsir->text,
        ]);
    }

    // ── GET /quran/{surah}/{ayah}/tafsir-page ────────────
    public function tafsirPage(Surah $surah, Ayah $ayah): View | RedirectResponse
    {
        // Plan check
        if (!$this->quranService->userCanAccessTafsir(Auth::user())) {
            return redirect()->route('subscription.upgrade')
                ->with('message', 'Tafsir requires an upgrade.');
        }

        $user        = Auth::user();
        $tafsirs     = $this->quranService->getAllTafsirs();
        $translation = $this->quranService->resolveTranslation(
            $user?->preferred_translation ?? QuranService::DEFAULT_TRANSLATION,
            $user
        );

        // Load translation for this ayah
        $ayah->load([
            'translations' => fn($q) => $q
                ->where('translation_id', $translation?->id)
        ]);

        // Default tafsir
        $selectedTafsir = Tafsir::where('slug', QuranService::DEFAULT_TAFSIR)
            ->where('is_active', true)
            ->first() ?? $tafsirs->first();

        // Adjacent ayahs for prev/next navigation
        $prevAyah = Ayah::where('surah_id', $surah->id)
            ->where('number', $ayah->number - 1)
            ->first();

        $nextAyah = Ayah::where('surah_id', $surah->id)
            ->where('number', $ayah->number + 1)
            ->first();

        return view('quran.tafsir', compact(
            'surah',
            'ayah',
            'tafsirs',
            'selectedTafsir',
            'prevAyah',
            'nextAyah'
        ));
    }



    // ── GET /quran/{surah}/{ayah}/translation (AJAX) ─────
    public function translation(Request $request, Surah $surah, Ayah $ayah): JsonResponse
    {
        $translationSlug = $request->get('translation', 'sahih-international');

        $translation = Translation::where('slug', $translationSlug)
            ->where('is_active', true)
            ->first();

        // ✅ Fallback to Sahih International if not found
        if (!$translation) {
            $translation = Translation::where('slug', 'sahih-international')
                ->first();
        }

        // Plan check
        if (!$this->quranService->userCanAccessTranslation(Auth::user(), $translation)) {
            return response()->json([
                'error'       => 'upgrade_required',
                'message'     => 'This translation requires a paid plan.',
                'upgrade_url' => route('subscription.upgrade'),
            ], 403);
        }

        // Check if translation data exists
        $ayahTranslation = AyahTranslation::where('ayah_id', $ayah->id)
            ->where('translation_id', $translation->id)
            ->first();

        // ✅ If no data → try to fetch from API
        if (!$ayahTranslation) {
            $ayahTranslation = $this->fetchAndStoreTranslation($ayah, $translation);
        }

        // ✅ Still no data → fallback to Sahih International
        if (!$ayahTranslation) {
            $sahih           = Translation::where('slug', 'sahih-international')->first();
            $ayahTranslation = AyahTranslation::where('ayah_id', $ayah->id)
                ->where('translation_id', $sahih?->id)
                ->first();

            return response()->json([
                'translation_name' => $sahih?->name . ' (Fallback)',
                'author'           => $sahih?->author,
                'language'         => $sahih?->language_name,
                'text'             => $ayahTranslation?->text ?? 'Translation not available.',
                'fallback'         => true,
            ]);
        }

        return response()->json([
            'translation_name' => $translation->name,
            'author'           => $translation->author,
            'language'         => $translation->language_name,
            'text'             => $ayahTranslation->text,
            'fallback'         => false,
        ]);
    }

    // ── GET /quran/{surah}/{ayah}/audio (AJAX) ───────────
    public function audio(Request $request, Surah $surah, Ayah $ayah): JsonResponse
    {
        $requestedReciter = $request->get('reciter', 'mishary-rashid');
        $recitation       = Recitation::where('slug', $requestedReciter)
            ->where('is_active', true)
            ->first();

        // ✅ Server-side plan check
        // Free users can only use free reciters
        if ($recitation && !$recitation->is_free) {
            if (!$this->quranService->userIsPremium(Auth::user())) {
                // Silently fall back to free reciter
                $requestedReciter = 'mishary-rashid';
                $recitation = Recitation::where('slug', $requestedReciter)->first();
            }
        }

        // Fallback to free reciter if not found
        if (!$recitation) {
            $recitation = Recitation::where('is_free', true)
                ->where('is_active', true)
                ->first();
        }

        if (!$recitation) {
            return response()->json(['error' => 'No reciter available.'], 404);
        }

        $audioUrl = $this->quranService->getAudioUrl($surah, $ayah, $recitation->slug);

        if (!$audioUrl) {
            return response()->json(['error' => 'Audio not available.'], 404);
        }

        return response()->json([
            'audio_url'   => $audioUrl,
            'ayah'        => "{$surah->number}:{$ayah->number}",
            'reciter'     => $recitation->name,
            'surah_name'  => $surah->name_transliteration,
            'ayah_number' => $ayah->number,
        ]);
    }

    // ── GET /quran/search ─────────────────────────────────
    public function search(Request $request): View
    {
        $query   = trim($request->get('q', ''));
        $results = $query ? $this->quranService->search($query) : null;

        return view('quran.search', compact('query', 'results'));
    }

    // ── POST /quran/progress ──────────────────────────────
    public function saveProgress(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'skipped']);
        }

        $request->validate(['ayah_id' => 'required|exists:ayahs,id']);

        $ayah = Ayah::find($request->ayah_id);
        $this->quranService->saveReadingProgress(Auth::user(), $ayah);

        // ✅ Return live counts so the banner can update
        //    instantly WITHOUT a page reload
        $readCount = $this->quranService->getReadAyahsCount(Auth::user(), $ayah->surah);

        return response()->json([
            'status'      => 'saved',
            'ayah_number' => $ayah->number,
            'read_count'  => $readCount,
            'total_ayahs' => $ayah->surah->ayah_count,
        ]);
    }

    // ── POST /quran/{surah}/complete ──────────────────────
    public function markSurahComplete(Surah $surah): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthenticated.'
            ], 401);
        }

        $progress = SurahProgress::where([
            'user_id' => Auth::id(),
            'surah_id' => $surah->id,
        ])->first();

        if ($progress?->is_completed) {
            return response()->json([
                'status' => 'already_completed'
            ]);
        }

        SurahProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'surah_id' => $surah->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'completed'
        ]);
    }

    // ── PRIVATE HELPERS ───────────────────────────────────

    private function fetchAndStoreTafsir(Ayah $ayah, Tafsir $tafsir): ?AyahTafsir
    {
        try {
            $url      = "https://api.quran.com/api/v4/tafsirs/{$tafsir->source}/by_ayah/{$ayah->number_in_quran}";
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) return null;

            $text = $response->json()['tafsir']['text'] ?? null;
            if (!$text) return null;

            // Strip HTML tags and footnotes
            $text = preg_replace('/<sup[^>]*>.*?<\/sup>/i', '', $text);
            $text = strip_tags($text);
            $text = trim(preg_replace('/\s+/', ' ', $text));

            return AyahTafsir::create([
                'ayah_id'   => $ayah->id,
                'tafsir_id' => $tafsir->id,
                'text'      => $text,
            ]);
        } catch (\Exception $e) {
            Log::error('Tafsir fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchAndStoreTranslation(Ayah $ayah, Translation $translation): ?AyahTranslation
    {
        try {
            $verseKey = "{$ayah->surah->number}:{$ayah->number}";
            $url      = "https://api.quran.com/api/v4/quran/translations/{$translation->source}?verse_key={$verseKey}";
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) return null;

            $text = $response->json()['translations'][0]['text'] ?? null;
            if (!$text) return null;

            // Clean footnotes
            $text = preg_replace('/<sup[^>]*>.*?<\/sup>/i', '', $text);
            $text = strip_tags($text);
            $text = trim(preg_replace('/\s+/', ' ', $text));

            return AyahTranslation::create([
                'ayah_id'        => $ayah->id,
                'translation_id' => $translation->id,
                'text'           => $text,
            ]);
        } catch (\Exception $e) {
            Log::error('Translation fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    // POST /quran/audio-completed
    public function audioCompleted(Request $request): JsonResponse
    {
        $request->validate(['ayah_id' => 'required|exists:ayahs,id']);

        $ayah = Ayah::findOrFail($request->ayah_id);
        $user = Auth::user();

        // ✅ Record this ayah as listened — unique per user+ayah
        // firstOrCreate means listening to ayah 3 twice
        // still only counts ONCE — exactly what you asked for
        ListenedAyah::firstOrCreate(
            ['user_id' => $user->id, 'ayah_id' => $ayah->id],
            ['surah_id' => $ayah->surah_id]
        );

        // ✅ Count UNIQUE ayahs listened for this surah — ever,
        // across ALL sessions, not just this page visit
        $listenedCount = ListenedAyah::where('user_id', $user->id)
            ->where('surah_id', $ayah->surah_id)
            ->count();

        $totalAyahs       = Ayah::where('surah_id', $ayah->surah_id)->count();
        $isFullyListened  = $listenedCount >= $totalAyahs;
        $newlyCompleted   = false;

        if ($isFullyListened) {
            $progress = SurahProgress::where('user_id', $user->id)
                ->where('surah_id', $ayah->surah_id)
                ->first();

            // ✅ Only mark complete + flag "newly" if not already done
            // This stops the modal showing again on re-listens
            if (!$progress?->is_completed) {
                SurahProgress::updateOrCreate(
                    ['user_id' => $user->id, 'surah_id' => $ayah->surah_id],
                    ['is_completed' => true, 'completed_at' => now()]
                );
                $newlyCompleted = true;
            }
        }

        return response()->json([
            'listened_count'  => $listenedCount,
            'total_ayahs'     => $totalAyahs,
            'completed'       => $isFullyListened,
            'newly_completed' => $newlyCompleted,
        ]);
    }


    public function sajdas(): View
    {
        $sajdaAyahs = Ayah::where('sajda', true)
            ->join('surahs', 'surahs.id', '=', 'ayahs.surah_id')
            ->orderBy('surahs.number')
            ->orderBy('ayahs.number')
            ->select('ayahs.*')
            ->with('surah:id,number,name_arabic,name_transliteration,name_english')
            ->get();

        return view('quran.sajdas', compact('sajdaAyahs'));
    }
}

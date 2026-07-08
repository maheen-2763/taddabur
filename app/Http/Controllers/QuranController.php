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
use App\Services\QuranApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ListenedAyah;
use App\Services\Quran\QuranIndexService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class QuranController extends Controller
{
    public function __construct(
        private QuranService      $quranService,
        private BookmarkService   $bookmarkService,
        private QuranIndexService $quranIndexService,
        private QuranApiService   $api,
    ) {}

    // ── GET /quran ────────────────────────────────────────
    public function index(): View
    {
        return view('quran.index', $this->quranIndexService->get(Auth::id()));
    }

    // ── GET /quran/{surah} ───────────────────────────────
    public function show(Request $request, Surah $surah): View
    {
        $user            = Auth::user();
        $translationSlug = $request->get('translation', $user?->preferred_translation ?? 'sahih-international');

        $data = $this->quranService->getSurahForReading($surah, $user, $translationSlug);

        $data['readAyahsCount'] = $user
            ? $this->quranService->getReadAyahsCount($user, $surah)
            : 0;

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

        $data['isPremium']       = $this->quranService->userIsPremium($user);
        $data['canAccessTafsir'] = $this->quranService->userCanAccessTafsir($user);
        $data['upgradeUrl']      = route('subscription.upgrade');
        $data['quranProgress']   = $user ? $this->quranService->getQuranProgress($user) : null;

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
        if (!$this->quranService->userCanAccessTafsir(Auth::user())) {
            return response()->json([
                'error'       => 'upgrade_required',
                'message'     => 'Tafsir requires a paid plan.',
                'upgrade_url' => route('subscription.upgrade'),
            ], 403);
        }

        $tafsirSlug = $request->get('tafsir', Auth::user()?->preferred_tafsir ?? 'ibn-kathir-en');
        $tafsir     = Tafsir::where('slug', $tafsirSlug)->first()
            ?? Tafsir::where('is_active', true)->first();

        if (!$tafsir) {
            return response()->json(['error' => 'No tafsir available.'], 404);
        }

        $ayahTafsir = AyahTafsir::where('ayah_id', $ayah->id)
            ->where('tafsir_id', $tafsir->id)
            ->first();

        if (!$ayahTafsir) {
            $ayahTafsir = $this->fetchSingleAyahTafsir($surah, $ayah, $tafsir);
        }

        if (!$ayahTafsir) {
            $ayahTafsir = AyahTafsir::whereIn('ayah_id', $surah->ayahs()->pluck('id'))
                ->where('tafsir_id', $tafsir->id)
                ->first();
        }

        if (!$ayahTafsir) {
            return response()->json(['error' => 'Tafsir not available for this ayah.'], 404);
        }

        $result = $this->buildFallbackNote($ayah, $surah, $ayahTafsir);

        if (!$result['text']) {
            return response()->json([
                'error'   => 'not_available_separately',
                'message' => $result['note'] ?? 'Tafsir not available for this ayah.',
            ], 404);
        }

        return response()->json([
            'ayah_reference' => "{$surah->number}:{$ayah->number}",
            'ayah_arabic'    => $ayah->text_arabic,
            'tafsir_name'    => $tafsir->name,
            'scholar'        => $tafsir->scholar,
            'text' => $result['text'],
            'note' => $result['note'],
        ]);
    }

    // ── GET /quran/{surah}/{ayah}/tafsir-page ────────────
    public function tafsirData(Request $request, Surah $surah, Ayah $ayah)
    {
        if (!$this->quranService->userCanAccessTafsir(Auth::user())) {
            return response()->json(['error' => 'premium_required'], 403);
        }

        $slug = $request->get('source', QuranService::DEFAULT_TAFSIR);
        $selectedTafsir = Tafsir::where('slug', $slug)->where('is_active', true)->first();

        if (!$selectedTafsir) {
            return response()->json(['error' => 'No tafsir available.'], 404);
        }

        $ayahTafsir = AyahTafsir::where('ayah_id', $ayah->id)
            ->where('tafsir_id', $selectedTafsir->id)
            ->first();

        if (!$ayahTafsir) {
            $ayahTafsir = AyahTafsir::whereIn('ayah_id', $surah->ayahs()->pluck('id'))
                ->where('tafsir_id', $selectedTafsir->id)
                ->first();
        }

        // ✅ FIX — array-based result use karo, purana wala nahi
        $result = $this->buildFallbackNote($ayah, $surah, $ayahTafsir);

        $arabicText = $ayah->text_arabic;
        $showBismillahTop = !in_array($surah->number, [1, 9]);

        if ($showBismillahTop && $ayah->number === 1) {
            $arabicText = \App\Helpers\ArabicHelper::stripBismillah($arabicText);
        }

        return response()->json([
            'surah_name'   => $surah->name_transliteration,
            'surah_number' => $surah->number,
            'ayah_number'  => $ayah->number,
            'arabic'       => $arabicText,
            'translation'  => $ayah->translations->first()?->text,
            'tafsir_name'  => $selectedTafsir?->name,
            'author'       => $selectedTafsir?->name,
            'text'         => $result['text'], // ✅ null rehne do, fallback mat karo
            'note'         => $result['note'],
        ]);
    }

    // ── GET /quran/{surah}/{ayah}/translation (AJAX) ─────
    public function translation(Request $request, Surah $surah, Ayah $ayah): JsonResponse
    {
        $translationSlug = $request->get('translation', 'sahih-international');

        $translation = Translation::where('slug', $translationSlug)
            ->where('is_active', true)
            ->first() ?? Translation::where('slug', 'sahih-international')->first();

        if (!$this->quranService->userCanAccessTranslation(Auth::user(), $translation)) {
            return response()->json([
                'error'       => 'upgrade_required',
                'message'     => 'This translation requires a paid plan.',
                'upgrade_url' => route('subscription.upgrade'),
            ], 403);
        }

        $ayahTranslation = AyahTranslation::where('ayah_id', $ayah->id)
            ->where('translation_id', $translation->id)
            ->first();

        if (!$ayahTranslation) {
            $ayahTranslation = $this->fetchSingleAyahTranslation($surah, $ayah, $translation);
        }

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
        $recitation       = Recitation::where('slug', $requestedReciter)->where('is_active', true)->first();

        if ($recitation && !$recitation->is_free && !$this->quranService->userIsPremium(Auth::user())) {
            $requestedReciter = 'mishary-rashid';
            $recitation = Recitation::where('slug', $requestedReciter)->first();
        }

        if (!$recitation) {
            $recitation = Recitation::where('is_free', true)->where('is_active', true)->first();
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

        $ayah = Ayah::with('surah')->find($request->ayah_id);
        $user = Auth::user();

        $this->quranService->saveReadingProgress($user, $ayah);

        $readCount  = $this->quranService->getReadAyahsCount($user, $ayah->surah);
        $totalAyahs = $ayah->surah->ayah_count;
        $newlyCompleted = false;

        if ($readCount >= $totalAyahs) {
            $progress = SurahProgress::where('user_id', $user->id)
                ->where('surah_id', $ayah->surah_id)
                ->first();

            if (!$progress?->is_completed) {
                SurahProgress::updateOrCreate(
                    ['user_id' => $user->id, 'surah_id' => $ayah->surah_id],
                    ['is_completed' => true, 'completed_at' => now()]
                );
                $newlyCompleted = true;
            }
        }

        return response()->json([
            'status'          => 'saved',
            'ayah_number'     => $ayah->number,
            'read_count'      => $readCount,
            'total_ayahs'     => $totalAyahs,
            'newly_completed' => $newlyCompleted,
        ]);
    }

    // ── POST /quran/{surah}/complete ──────────────────────
    public function markSurahComplete(Surah $surah): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $progress = SurahProgress::where(['user_id' => Auth::id(), 'surah_id' => $surah->id])->first();

        if ($progress?->is_completed) {
            return response()->json(['status' => 'already_completed']);
        }

        SurahProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'surah_id' => $surah->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        return response()->json(['status' => 'completed']);
    }

    // ── POST /quran/audio-completed ───────────────────────
    public function audioCompleted(Request $request): JsonResponse
    {
        $request->validate(['ayah_id' => 'required|exists:ayahs,id']);

        $ayah = Ayah::findOrFail($request->ayah_id);
        $user = Auth::user();

        ListenedAyah::firstOrCreate(
            ['user_id' => $user->id, 'ayah_id' => $ayah->id],
            ['surah_id' => $ayah->surah_id]
        );

        $listenedCount   = ListenedAyah::where('user_id', $user->id)->where('surah_id', $ayah->surah_id)->count();
        $totalAyahs      = Ayah::where('surah_id', $ayah->surah_id)->count();
        $isFullyListened = $listenedCount >= $totalAyahs;
        $newlyCompleted  = false;

        if ($isFullyListened) {
            $progress = SurahProgress::where('user_id', $user->id)->where('surah_id', $ayah->surah_id)->first();

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

    // ── GET /quran/sajdas ──────────────────────────────────
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

    // ── GET /quran/my-progress ─────────────────────────────
    public function myProgress(): View
    {
        $progress       = $this->quranService->getAllSurahsProgress(Auth::user());
        $totalAyahs     = 6236;
        $totalRead      = $progress->sum('read_count');
        $totalCompleted = $progress->where('is_completed', true)->count();

        return view('quran.my-progress', compact('progress', 'totalAyahs', 'totalRead', 'totalCompleted'));
    }

    // ── PRIVATE HELPERS ───────────────────────────────────

    private function fetchSingleAyahTafsir(Surah $surah, Ayah $ayah, Tafsir $tafsir): ?AyahTafsir
    {
        if (!is_numeric($tafsir->source)) {
            Log::error("Non-numeric tafsir source: {$tafsir->source}");
            return null;
        }

        try {
            $verses = $this->api->fetchTafsirForSurah($surah->number, (int) $tafsir->source);

            if (empty($verses)) {
                Log::warning("Empty tafsir fetch: surah={$surah->number}, tafsir={$tafsir->source}");
                return null;
            }

            $saved = null;

            foreach ($verses as $item) {
                if (empty($item['verse_key']) || !str_contains($item['verse_key'], ':')) {
                    continue;
                }

                [, $aNum] = explode(':', $item['verse_key']);

                $targetAyah = Ayah::where('surah_id', $surah->id)
                    ->where('number', (int) $aNum)
                    ->first();

                if (!$targetAyah) {
                    continue;
                }

                $text = $this->cleanText($item['tafsirs'][0]['text'] ?? '');
                if ($text === '') {
                    continue;
                }

                $record = AyahTafsir::updateOrCreate(
                    ['ayah_id' => $targetAyah->id, 'tafsir_id' => $tafsir->id],
                    ['text' => $text, 'api_source_version' => 'qf_oauth2_v1']
                );

                if ($targetAyah->id === $ayah->id) {
                    $saved = $record;
                }
            }

            return $saved;
        } catch (\Exception $e) {
            Log::error("Tafsir on-demand fetch failed: surah={$surah->number}, tafsir={$tafsir->id} — " . $e->getMessage());
            return null;
        } finally {
            $this->api->pause();
        }
    }

    private function fetchSingleAyahTranslation(Surah $surah, Ayah $ayah, Translation $translation): ?AyahTranslation
    {
        if (!is_numeric($translation->source)) {
            Log::error("Non-numeric translation source: {$translation->source}");
            return null;
        }

        try {
            $verses = $this->api->fetchTranslationForSurah($surah->number, (int) $translation->source);

            if (empty($verses)) {
                Log::warning("Empty translation fetch: surah={$surah->number}, translation={$translation->source}");
                return null;
            }

            $saved = null;

            foreach ($verses as $item) {
                if (empty($item['verse_key']) || !str_contains($item['verse_key'], ':')) {
                    continue;
                }

                [, $aNum] = explode(':', $item['verse_key']);

                $targetAyah = Ayah::where('surah_id', $surah->id)
                    ->where('number', (int) $aNum)
                    ->first();

                if (!$targetAyah) {
                    continue;
                }

                $text = $this->cleanText($item['text'] ?? '');
                if ($text === '') {
                    continue;
                }

                $record = AyahTranslation::updateOrCreate(
                    ['ayah_id' => $targetAyah->id, 'translation_id' => $translation->id],
                    ['text' => $text]
                );

                if ($targetAyah->id === $ayah->id) {
                    $saved = $record;
                }
            }

            return $saved;
        } catch (\Exception $e) {
            Log::error("Translation on-demand fetch failed: surah={$surah->number}, translation={$translation->id} — " . $e->getMessage());
            return null;
        } finally {
            $this->api->pause();
        }
    }

    private function cleanText(string $html): string
    {
        if (trim($html) === '') return '';

        $withBreaks = preg_replace(
            ['/<\/p>/i', '/<br\s*\/?>/i', '/<\/h[1-6]>/i', '/<\/div>/i', '/<div[^>]*>/i'],
            ["\n\n", "\n", "\n\n", "\n\n", "\n\n"],
            $html
        );

        $plain = strip_tags($withBreaks);
        $plain = preg_replace("/\n{3,}/", "\n\n", $plain);
        $plain = preg_replace('/[ \t]+/', ' ', $plain);

        return trim($plain);
    }
    public function loadAyahs(Request $request, $surahNumber)
    {
        $ayahs = Ayah::where('surah_id', $surahNumber)  // ✅ sahi column
            ->orderBy('number')
            ->skip(($request->get('page', 1) - 1) * 30)
            ->take(30)
            ->get(['number', 'text_arabic']);

        return response()->json([
            'ayahs' => $ayahs,
            'has_more' => $ayahs->count() === 30,
        ]);
    }


    private function buildFallbackNote(Ayah $requestedAyah, Surah $requestedSurah, ?AyahTafsir $ayahTafsir): array
    {
        if (!$ayahTafsir) {
            return ['text' => null, 'note' => null];
        }

        if (str_starts_with($ayahTafsir->api_source_version ?? '', 'cross_surah')) {
            // Format: cross_surah_104_105 — dusra number nikaalo
            $parts = explode('_', $ayahTafsir->api_source_version);
            $combinedWithSurahNumber = $parts[3] ?? null; // 'cross', 'surah', '104', '105'

            $combinedSurah = $combinedWithSurahNumber
                ? Surah::where('number', $combinedWithSurahNumber)->first()
                : null;

            return [
                'text' => null,
                'note' => $combinedSurah
                    ? "This tafsir edition does not have separate commentary for Surah {$requestedSurah->name_transliteration}. It is discussed together with Surah {$combinedSurah->name_transliteration}."
                    : "This tafsir edition does not have separate commentary for Surah {$requestedSurah->name_transliteration}.",
            ];
        }

        if ($ayahTafsir->ayah_id === $requestedAyah->id) {
            return ['text' => $ayahTafsir->text, 'note' => null];
        }

        $sourceAyah = Ayah::find($ayahTafsir->ayah_id);

        if ($sourceAyah && $sourceAyah->surah_id !== $requestedSurah->id) {
            $sourceSurah = Surah::find($sourceAyah->surah_id);
            return [
                'text' => null,
                'note' => "This tafsir edition does not have separate commentary for Surah {$requestedSurah->name_transliteration}. It is discussed together with Surah {$sourceSurah?->name_transliteration}.",
            ];
        }

        // Al-Fil jaisa case — same surah ke andar combined
        return [
            'text' => $ayahTafsir->text,
            'note' => "This tafsir covers the complete surah (verses combined into {$requestedSurah->number}:{$sourceAyah->number}).",
        ];
    }
}

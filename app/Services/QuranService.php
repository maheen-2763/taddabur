<?php
// app/Services/QuranService.php

namespace App\Services;

use App\Models\Ayah;
use App\Models\AyahTranslation;
use App\Models\ReadingProgress;
use App\Models\Recitation;
use App\Models\Surah;
use App\Models\Tafsir;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Models\UserReadAyah;
use Illuminate\Support\Facades\Log;

class QuranService
{
    public const DEFAULT_TRANSLATION = 'sahih-international';
    public const DEFAULT_TAFSIR      = 'ibn-kathir-en';
    public const DEFAULT_RECITER     = 'mishary-rashid';
    // -------------------------------------------------------
    // GET ALL SURAHS
    // -------------------------------------------------------
    public function getAllSurahs(): Collection
    {
        return Surah::select([
            'id',
            'number',
            'name_arabic',
            'name_english',
            'name_transliteration',
            'ayah_count',
            'revelation_type',
        ])->orderBy('number')->get();
    }

    // -------------------------------------------------------
    // GET SURAH FOR READING
    // -------------------------------------------------------
    public function getSurahForReading(
        Surah $surah,
        ?User $user,
        string $translationSlug
    ): array {
        $translation = $this->resolveTranslation($translationSlug, $user);

        $ayahs = Ayah::where('surah_id', $surah->id)
            ->with([
                'translations' => fn($q) => $q
                    ->where('translation_id', $translation?->id)
                    ->select(['ayah_id', 'translation_id', 'text']) // ✅ Only needed columns
            ])
            ->orderBy('number')
            ->get();

        // ✅ Always return ALL options — lock logic handled in view + server checks
        $translations = $this->getAllTranslations();
        $tafsirs      = $this->getAllTafsirs();
        $reciters     = $this->getAllReciters();

        $prevSurah = Surah::where('number', $surah->number - 1)->first();
        $nextSurah = Surah::where('number', $surah->number + 1)->first();

        return compact(
            'surah',
            'ayahs',
            'translation',
            'translations',
            'tafsirs',
            'reciters',
            'prevSurah',
            'nextSurah'
        );
    }

    // -------------------------------------------------------
    // RESOLVE TRANSLATION
    // Always falls back to free if user cannot access premium
    // -------------------------------------------------------
    public function resolveTranslation(string $slug, ?User $user): ?Translation
    {
        // Try requested translation
        $translation = Translation::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        // ✅ Check if translation has actual data in database
        if ($translation) {
            $hasData = AyahTranslation::where('translation_id', $translation->id)
                ->exists();

            // No data → fallback to Sahih International
            if (!$hasData) {
                $translation = Translation::where('slug', 'sahih-international')
                    ->where('is_active', true)
                    ->first();
            }
        }

        // Fallback if not found at all
        if (!$translation) {
            $translation = Translation::where('slug', 'sahih-international')
                ->where('is_active', true)
                ->first();
        }

        // Plan check — free user requesting premium translation
        if ($translation && !$translation->is_free && !$this->userIsPremium($user)) {
            $translation = Translation::where('is_free', true)
                ->where('is_active', true)
                ->first();
        }

        return $translation;
    }

    // -------------------------------------------------------
    // GET ALL TRANSLATIONS (for dropdown — ALL plans see all)
    // Lock is handled in view and server endpoint
    // -------------------------------------------------------
    public function getAllTranslations(): Collection
    {
        return Translation::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    // -------------------------------------------------------
    // GET ALL TAFSIRS (for dropdown — ALL plans see all)
    // -------------------------------------------------------
    public function getAllTafsirs(): Collection
    {
        return Tafsir::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // -------------------------------------------------------
    // GET ALL RECITERS (for dropdown — ALL plans see all)
    // -------------------------------------------------------
    public function getAllReciters(): Collection
    {
        return Recitation::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // -------------------------------------------------------
    // PLAN ACCESS HELPERS
    // -------------------------------------------------------

    // Can user access premium features (Basic or Premium plan)
    public function userIsPremium(?User $user): bool
    {
        if (!$user) return false;
        return $user->isPremium();
    }

    // Can user access tafsir
    public function userCanAccessTafsir(?User $user): bool
    {
        if (!$user) return false;
        return $user->canAccess('has_tafsir');
    }

    // Can user access premium audio (multiple reciters)
    public function userIsPremiumAudio(?User $user): bool
    {
        if (!$user) return false;
        return $user->isPremium();
    }

    // Can user access a specific translation
    public function userCanAccessTranslation(?User $user, Translation $translation): bool
    {
        if ($translation->is_free) return true;
        return $this->userIsPremium($user);
    }

    // Can user access a specific reciter
    public function userCanAccessReciter(?User $user, Recitation $reciter): bool
    {
        if ($reciter->is_free) return true;
        return $this->userIsPremium($user);
    }

    // -------------------------------------------------------
    // SEARCH QURAN
    // -------------------------------------------------------
    public function search(string $query, int $perPage = 20)
    {
        if (strlen(trim($query)) < 3) {
            return null;
        }

        $translation = Translation::where('is_free', true)
            ->where('is_active', true)
            ->first();

        return AyahTranslation::where('translation_id', $translation?->id)
            ->where('text', 'LIKE', "%{$query}%")
            ->with(['ayah' => fn($q) => $q->with('surah')])
            ->paginate($perPage)
            ->withQueryString();
    }

    // -------------------------------------------------------
    // SAVE READING PROGRESS
    // -------------------------------------------------------
    public function saveReadingProgress(User $user, Ayah $ayah): void
    {
        UserReadAyah::firstOrCreate([
            'user_id' => $user->id,
            'ayah_id' => $ayah->id,
        ]);

        $progress = ReadingProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'story_id' => null,
            ],
            [
                'last_read_at' => now(),
                'last_ayah_id' => $ayah->id,
            ]
        );

        $progress->update([
            'last_ayah_id' => $ayah->id,
        ]);

        $progress->updateStreak();
    }

    // -------------------------------------------------------
    // GET USER'S QURAN PROGRESS
    // -------------------------------------------------------
    public function getQuranProgress(User $user): ?ReadingProgress
    {
        return ReadingProgress::where('user_id', $user->id)
            ->whereNull('story_id')
            ->with('lastAyah.surah')
            ->first();
    }

    // -------------------------------------------------------
    // GET AUDIO URL FOR AYAH
    // -------------------------------------------------------
    public function getAudioUrl(Surah $surah, Ayah $ayah, string $reciterSlug): ?string
    {
        $recitation = Recitation::where('slug', $reciterSlug)
            ->where('is_active', true)
            ->first();

        if (!$recitation) return null;

        return $recitation->audioUrlFor($surah, $ayah);
    }
}

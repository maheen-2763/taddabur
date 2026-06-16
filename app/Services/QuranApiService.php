<?php
// app/Services/QuranApiService.php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuranApiService
{
    // -------------------------------------------------------
    // The two free APIs we use:
    // 1. alquran.cloud  → Arabic text, surah info
    // 2. api.quran.com  → Translations, tafsirs
    // Both are completely free, no API key needed.
    // -------------------------------------------------------

    private string $alquranBase = 'https://api.alquran.cloud/v1';
    private string $quranComBase = 'https://api.quran.com/api/v4';

    // How long to wait between API requests (in seconds)
    // This prevents us from being rate-limited or banned
    private int $delayBetweenRequests = 1;

    // -------------------------------------------------------
    // FETCH ALL SURAHS
    // Returns array of all 114 surahs with metadata
    // -------------------------------------------------------
    public function fetchAllSurahs(): array
    {
        $response = $this->get($this->alquranBase . '/surah');

        if (!$response || !isset($response['data'])) {
            throw new \Exception('Failed to fetch surahs from alquran.cloud');
        }

        return $response['data'];
    }

    // -------------------------------------------------------
    // FETCH ONE SURAH WITH ALL AYAHS
    // $surahNumber = 1 to 114
    // $edition = which text to fetch (e.g. 'quran-uthmani' for proper Arabic)
    // -------------------------------------------------------
    public function fetchSurahWithAyahs(int $surahNumber, string $edition = 'quran-uthmani'): array
    {
        $url = "{$this->alquranBase}/surah/{$surahNumber}/{$edition}";
        $response = $this->get($url);

        if (!$response || !isset($response['data'])) {
            throw new \Exception("Failed to fetch surah {$surahNumber}");
        }

        return $response['data'];
    }

    // -------------------------------------------------------
    // FETCH TRANSLATION FOR ONE SURAH
    // Uses Quran.com API which has the most translations
    // -------------------------------------------------------
    public function fetchTranslationForSurah(int $surahNumber, string $translationId): array
    {
        // Quran.com uses verse keys like "1:1" to "114:6"
        $url = "{$this->quranComBase}/verses/by_chapter/{$surahNumber}";

        $response = $this->get($url, [
            'translations' => $translationId,
            'per_page'     => 300,  // max ayahs per surah is 286
        ]);

        if (!$response || !isset($response['verses'])) {
            return [];
        }

        // Reformat to match what our import command expects
        return $response['verses'];;
    }

    // -------------------------------------------------------
    // FETCH TAFSIR FOR ONE SURAH
    // -------------------------------------------------------
    public function fetchTafsirForSurah(int $surahNumber, string $tafsirKey): array
    {
        $url = "{$this->quranComBase}/tafsirs/{$tafsirKey}";
        $url .= "?chapter_number={$surahNumber}";

        $response = $this->get($url);

        if (!$response || !isset($response['tafsirs'])) {
            return [];
        }

        return $response['tafsirs'];
    }

    // -------------------------------------------------------
    // FETCH SIMPLE ARABIC (without tashkeel/diacritics)
    // Used for search (easier to search without diacritics)
    // -------------------------------------------------------
    public function fetchSimpleArabicForSurah(int $surahNumber): array
    {
        $url = "{$this->alquranBase}/surah/{$surahNumber}/quran-simple";
        $response = $this->get($url);

        if (!$response || !isset($response['data']['ayahs'])) {
            return [];
        }

        return $response['data']['ayahs'];
    }

    // -------------------------------------------------------
    // PRIVATE HELPER: Make HTTP GET request with retry logic
    // -------------------------------------------------------
    private function get(string $url, array $params = []): ?array
    {
        $maxRetries = 3;
        $attempt    = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->get($url, $params);  // ← pass params here

                if ($response->successful()) {
                    return $response->json();
                }

                if ($response->status() === 429) {
                    sleep(5);
                    $attempt++;
                    continue;
                }

                Log::warning("API request failed: {$url} — Status: {$response->status()}");
                return null;
            } catch (\Exception $e) {
                Log::error("API request exception: {$url} — {$e->getMessage()}");
                $attempt++;
                sleep(2);
            }
        }

        return null;
    }

    // -------------------------------------------------------
    // Sleep between requests (be polite to free APIs!)
    // -------------------------------------------------------
    public function pause(): void
    {
        sleep($this->delayBetweenRequests);
    }
}

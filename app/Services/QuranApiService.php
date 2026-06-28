<?php
// app/Services/QuranApiService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuranApiService
{
    // -------------------------------------------------------
    // Three APIs in use:
    // 1. alquran.cloud        → Arabic text, surah info (no auth needed)
    // 2. oauth2.quran.foundation → OAuth2 token endpoint
    // 3. apis.quran.foundation  → Translations, tafsirs (auth required)
    // -------------------------------------------------------

    private string $alquranBase;
    private string $authBase;
    private string $contentBase;
    private string $clientId;
    private string $clientSecret;

    private int $delayBetweenRequests = 1;

    public function __construct()
    {
        $this->alquranBase  = 'https://api.alquran.cloud/v1';
        $this->authBase     = config('services.quran_foundation.auth_base');
        $this->contentBase  = config('services.quran_foundation.content_base');
        $this->clientId     = config('services.quran_foundation.client_id');
        $this->clientSecret = config('services.quran_foundation.client_secret');
    }

    // -------------------------------------------------------
    // GET OAUTH2 ACCESS TOKEN (cached)
    // Quran Foundation tokens expire — we cache it and only
    // request a new one when it's missing or expired.
    // -------------------------------------------------------
    private function getAccessToken(): ?string
    {
        return Cache::remember('quran_foundation_access_token', 3000, function () {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post("{$this->authBase}/oauth2/token", [
                    'grant_type' => 'client_credentials',
                    'scope'      => 'content',
                ]);

            if (!$response->successful()) {
                Log::error('Quran Foundation token request failed: ' . $response->body());
                return null;
            }

            $data = $response->json();

            if (empty($data['access_token'])) {
                Log::error('Quran Foundation token response missing access_token');
                return null;
            }

            // Cache for slightly less than the actual expiry to be safe
            $ttl = max(60, ($data['expires_in'] ?? 3600) - 60);
            Cache::put('quran_foundation_access_token', $data['access_token'], $ttl);

            return $data['access_token'];
        });
    }

    // -------------------------------------------------------
    // FETCH ALL SURAHS (alquran.cloud — no auth needed)
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
    // FETCH ONE SURAH WITH ALL AYAHS (alquran.cloud — no auth needed)
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
    // FETCH TRANSLATION FOR ONE SURAH (Quran Foundation — auth required)
    // -------------------------------------------------------
    public function fetchTranslationForSurah(int $surahNumber, string $translationId): array
    {
        $url = "{$this->contentBase}/content/api/v4/verses/by_chapter/{$surahNumber}";

        $response = $this->getAuthenticated($url, [
            'translations' => $translationId,
            'per_page'     => 300,
        ]);

        if (!$response || !isset($response['verses'])) {
            return [];
        }

        return $response['verses'];
    }

    // -------------------------------------------------------
    // FETCH TAFSIR FOR ONE SURAH (Quran Foundation — auth required)
    // NOTE: This endpoint requires the numeric resource_id,
    // NOT the slug. Slugs return 404 even though /resources/tafsirs
    // itself accepts and returns slugs for display purposes.
    // -------------------------------------------------------
    /**
     * FETCH TAFSIR FOR ONE SURAH (Quran Foundation — auth required)
     * Uses /verses/by_chapter endpoint with tafsirs param (like translations).
     */
    public function fetchTafsirForSurah(int $surahNumber, string $tafsirResourceId): array
    {
        // Use /verses/by_chapter endpoint with tafsirs parameter
        $url = "{$this->contentBase}/content/api/v4/verses/by_chapter/{$surahNumber}";

        $response = $this->getAuthenticated($url, [
            'tafsirs'  => $tafsirResourceId,
            'per_page' => 300,
        ]);

        if (!$response || !isset($response['verses'])) {
            return [];
        }

        // Extract tafsirs from each verse
        $tafsirs = [];
        foreach ($response['verses'] as $verse) {
            if (!empty($verse['tafsirs'])) {
                foreach ($verse['tafsirs'] as $tafsir) {
                    $tafsirs[] = $tafsir;
                }
            }
        }

        return $tafsirs;
    }
    // -------------------------------------------------------
    // FETCH SIMPLE ARABIC (alquran.cloud — no auth needed)
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
    // FETCH LIST OF AVAILABLE TAFSIRS (for verification/debugging)
    // -------------------------------------------------------
    public function fetchAvailableTafsirs(?string $language = null): array
    {
        $url = "{$this->contentBase}/content/api/v4/resources/tafsirs";
        $params = $language ? ['language' => $language] : [];

        $response = $this->getAuthenticated($url, $params);

        return $response['tafsirs'] ?? [];
    }

    // -------------------------------------------------------
    // PRIVATE HELPER: Authenticated GET request to Quran Foundation
    // Adds x-auth-token and x-client-id headers automatically.
    // -------------------------------------------------------
    private function getAuthenticated(string $url, array $params = []): ?array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            Log::error("Cannot call {$url} — no valid access token.");
            return null;
        }

        $maxRetries = 3;
        $attempt    = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->withHeaders([
                        'x-auth-token' => $token,
                        'x-client-id'  => $this->clientId,
                    ])
                    ->get($url, $params);

                if ($response->successful()) {
                    return $response->json();
                }

                // Token may have expired early — clear cache and retry once
                if ($response->status() === 401 && $attempt === 0) {
                    Cache::forget('quran_foundation_access_token');
                    $token = $this->getAccessToken();
                    if (!$token) {
                        return null;
                    }
                    $attempt++;
                    continue;
                }

                if ($response->status() === 429) {
                    sleep(5);
                    $attempt++;
                    continue;
                }

                Log::warning("API request failed: {$url} — Status: {$response->status()} — Body: {$response->body()}");
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
    // PRIVATE HELPER: Plain (no-auth) GET request — for alquran.cloud
    // -------------------------------------------------------
    private function get(string $url, array $params = []): ?array
    {
        $maxRetries = 3;
        $attempt    = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->get($url, $params);

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

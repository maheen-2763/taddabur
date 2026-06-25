<?php
// app/Services/HadithApiService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HadithApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.sunnah.base_url');
        $this->apiKey = config('services.sunnah.key');
    }

    protected function request(string $endpoint): ?array
    {
        $response = Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->get("{$this->baseUrl}{$endpoint}");

        if ($response->failed()) {
            Log::error("Sunnah API failed: {$endpoint}", ['status' => $response->status()]);
            return null;
        }

        return $response->json();
    }

    public function fetchCollections(): array
    {
        return $this->request('/collections')['data'] ?? [];
    }

    public function fetchChapters(string $collectionSlug): array
    {
        return $this->request("/collections/{$collectionSlug}/books")['data'] ?? [];
    }

    public function fetchHadiths(string $collectionSlug, int $bookNumber, int $page = 1): array
    {
        return $this->request("/collections/{$collectionSlug}/books/{$bookNumber}/hadiths?page={$page}&limit=50") ?? [];
    }
}

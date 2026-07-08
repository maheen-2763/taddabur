<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HadithApiService
{
    const BASE_URL = 'https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1';

    public function getEdition(string $editionSlug): ?array
    {
        $response = Http::get(self::BASE_URL . "/editions/{$editionSlug}.json");

        if (!$response->successful()) {
            return null;
        }

        return $response->json();
    }

    public function getAllEditions(): ?array
    {
        $response = Http::get(self::BASE_URL . "/editions.json");
        return $response->successful() ? $response->json() : null;
    }


    public function getInfo(): ?array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('hadith_api_info', function () {
            $response = Http::timeout(60)->get(self::BASE_URL . '/info.json');
            return $response->successful() ? $response->json() : null;
        });
    }
}

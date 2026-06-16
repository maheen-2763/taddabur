<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AllahNameService
{
    public function fetchAll()
    {
        $response = Http::timeout(30)
            ->get('https://api.aladhan.com/v1/asmaAlHusna');

        if (! $response->successful()) {
            throw new \Exception('Unable to fetch Allah Names.');
        }



        return $response->json('data');
    }
}

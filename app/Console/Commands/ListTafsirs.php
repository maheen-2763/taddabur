<?php
// app/Console/Commands/ListTafsirs.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ListTafsirs extends Command
{
    protected $signature = 'quran:list-tafsirs
                            {--language= : Filter by language code (e.g. en, ar, ur)}
                            {--search= : Filter by name containing this text (e.g. "kathir", "jalalayn", "muyassar")}';

    protected $description = 'Fetch the live list of available tafsirs from api.quran.com (source of truth — no guessing)';

    public function handle(): int
    {
        $this->info('');
        $this->info('📚 Fetching live tafsir list from api.quran.com...');
        $this->info('================================================');

        $url = 'https://api.quran.com/api/v4/resources/tafsirs';
        $params = [];

        if ($language = $this->option('language')) {
            $params['language'] = $language;
        }

        $response = Http::timeout(30)->get($url, $params);

        if (!$response->successful()) {
            $this->error("API request failed. Status: {$response->status()}");
            $this->error($response->body());
            return self::FAILURE;
        }

        $data = $response->json();

        if (!isset($data['tafsirs']) || empty($data['tafsirs'])) {
            $this->warn('No tafsirs returned. Try without --language filter to see all.');
            return self::FAILURE;
        }

        $tafsirs = $data['tafsirs'];

        // Optional name-based filter, since the API has no search param
        if ($search = $this->option('search')) {
            $tafsirs = array_filter($tafsirs, function ($t) use ($search) {
                return str_contains(strtolower($t['name'] ?? ''), strtolower($search))
                    || str_contains(strtolower($t['author_name'] ?? ''), strtolower($search));
            });
        }

        if (empty($tafsirs)) {
            $this->warn("No tafsirs matched search: '{$search}'");
            return self::FAILURE;
        }

        $this->newLine();
        $this->table(
            ['ID', 'Slug', 'Name', 'Author', 'Language'],
            array_map(fn($t) => [
                $t['id'] ?? '—',
                $t['slug'] ?? '—',
                $t['name'] ?? '—',
                $t['author_name'] ?? '—',
                $t['language_name'] ?? '—',
            ], $tafsirs)
        );

        $this->newLine();
        $this->info('Total found: ' . count($tafsirs));
        $this->info('👉 Copy the exact "Slug" column value into TafsirSeeder.php — do not retype by hand.');

        return self::SUCCESS;
    }
}

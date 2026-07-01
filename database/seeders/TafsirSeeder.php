<?php
// database/seeders/TafsirSeeder.php

namespace Database\Seeders;

use App\Models\Tafsir;
use Illuminate\Database\Seeder;

class TafsirSeeder extends Seeder
{
    public function run(): void
    {
        $tafsirs = [
            [
                'name'          => 'Tafsir Ibn Kathir',
                'scholar'       => 'Ibn Kathir',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'ibn-kathir-en',
                'source'        => '169', // Quran Foundation resource_id — verified via live fetch
                'description'   => 'One of the most widely used tafsirs in the world, known for its authentic hadith references.',
                'is_active'     => true,
                'sort_order'    => 1,
            ],
            [
                'name'          => 'Tafsir Al-Jalalayn',
                'scholar'       => 'Jalal ad-Din al-Mahalli & Jalal ad-Din as-Suyuti',
                'language_code' => 'ar',
                'language_name' => 'Arabic',
                'slug'          => 'al-jalalayn-en',
                'source'        => '926',
                'description'   => 'A concise classical tafsir covering the meaning of each verse briefly and clearly.',
                'is_active'     => true,
                'sort_order'    => 2,
            ],
            [
                'name'          => 'Tafsir al-Muyassar',
                'scholar'       => 'King Fahd Complex',
                'language_code' => 'ar',
                'language_name' => 'Arabic',
                'slug'          => 'al-muyassar-ar',
                'source'        => '16',
                'description'   => 'A simplified Arabic tafsir designed for modern readers.',
                'is_active'     => true,
                'sort_order'    => 3,
            ],
            [
                'name'          => 'Tafsir Ibn Kathir (Urdu)',
                'scholar'       => 'Ibn Kathir',
                'language_code' => 'ur',
                'language_name' => 'Urdu',
                'slug'          => 'ibn-kathir-ur',
                'source'        => '160',
                'description'   => 'Urdu translation of the famous Ibn Kathir tafsir.',
                'is_active'     => true,
                'sort_order'    => 4,
            ],
        ];

        $seededCount = 0;

        foreach ($tafsirs as $tafsir) {
            // Guard: skip if slug missing — prevents accidental empty/duplicate rows
            if (empty($tafsir['slug'])) {
                $this->command->warn("⚠️ Skipped tafsir with missing slug: " . ($tafsir['name'] ?? 'unknown'));
                continue;
            }

            // Detect source (resource_id) change BEFORE updating —
            // because cached text in ayah_tafsirs won't auto-refresh after this.
            $existing = Tafsir::where('slug', $tafsir['slug'])->first();

            if ($existing && $existing->source !== $tafsir['source']) {
                $this->command->warn(
                    "⚠️ Source changed for '{$tafsir['slug']}': "
                        . "{$existing->source} → {$tafsir['source']}. "
                        . "Run TafsirImportCommand for this tafsir to refresh cached ayah_tafsirs text."
                );
            }

            // Explicit whitelist — only known columns are written.
            // Prevents silent overwrite if $tafsirs array structure changes later.
            Tafsir::updateOrCreate(
                ['slug' => $tafsir['slug']],
                [
                    'name'          => $tafsir['name'],
                    'scholar'       => $tafsir['scholar'],
                    'language_code' => $tafsir['language_code'],
                    'language_name' => $tafsir['language_name'],
                    'source'        => $tafsir['source'],
                    'description'   => $tafsir['description'],
                    'is_active'     => $tafsir['is_active'] ?? true,
                    'sort_order'    => $tafsir['sort_order'] ?? 0,
                ]
            );

            $seededCount++;
        }

        $this->command->info("✅ Tafsirs seeded/updated: {$seededCount} collections — using verified numeric resource IDs");
    }
}

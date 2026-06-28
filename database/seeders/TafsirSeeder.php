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
                'source'        => '169', // Quran Foundation numeric resource_id (NOT slug — verified via live tafsir fetch)
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
                'source'        => '926', // numeric resource_id
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
                'source'        => '16', // numeric resource_id
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
                'source'        => '160', // numeric resource_id — confirmed working via live test above
                'description'   => 'Urdu translation of the famous Ibn Kathir tafsir.',
                'is_active'     => true,
                'sort_order'    => 4,
            ],
        ];

        foreach ($tafsirs as $tafsir) {
            Tafsir::updateOrCreate(
                ['slug' => $tafsir['slug']],
                $tafsir
            );
        }

        $this->command->info('✅ Tafsirs seeded/updated: ' . count($tafsirs) . ' collections — using verified numeric resource IDs');
    }
}

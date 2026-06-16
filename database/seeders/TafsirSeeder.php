<?php
// database/seeders/TafsirSeeder.php

namespace Database\Seeders;

use App\Models\Tafsir;
use Illuminate\Database\Seeder;

class TafsirSeeder extends Seeder
{
    public function run(): void
    {
        Tafsir::truncate();

        $tafsirs = [
            [
                'name'          => 'Tafsir Ibn Kathir',
                'scholar'       => 'Ibn Kathir',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'ibn-kathir-en',
                'source'        => 'en-tafisr-ibn-kathir', // Quran.com tafsir key
                'description'   => 'One of the most widely used tafsirs in the world, known for its authentic hadith references.',
                'is_active'     => true,
                'sort_order'    => 1,
            ],
            [
                'name'          => 'Tafsir Al-Jalalayn',
                'scholar'       => 'Jalal ad-Din al-Mahalli & Jalal ad-Din as-Suyuti',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'al-jalalayn-en',
                'source'        => 'en-tafsir-al-jalalayn',
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
                'source'        => 'ar-tafsir-muyassar',
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
                'source'        => 'ur-tafsir-ibn-kathir',
                'description'   => 'Urdu translation of the famous Ibn Kathir tafsir.',
                'is_active'     => true,
                'sort_order'    => 4,
            ],
        ];

        foreach ($tafsirs as $tafsir) {
            Tafsir::create($tafsir);
        }

        $this->command->info('✅ Tafsirs seeded: ' . count($tafsirs) . ' collections');
    }
}

<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters! Later seeders may depend on earlier ones.
        // e.g. StorySeeder needs ProphetSeeder to have run first.

        $this->call([
            PlanSeeder::class,          // Pricing plans (Free, Basic, Premium)
            TranslationSeeder::class,   // Available Quran translations
            TafsirSeeder::class,        // Available tafsir collections
            RecitationSeeder::class,    // Available Qaris (reciters)
            SurahSeeder::class,         // All 114 Surahs
            ProphetSeeder::class,       // All 25 Prophets
            StorySeeder::class,         // Sample prophet stories
            AdminSeeder::class,         // Admin user
            AllahNameSeeder::class,     // 99 Names of Allah
            ProphetTimelineSeeder::class, // Timeline of Prophets
            JuzSeeder::class,           // All 30 Juzs
            JuzSurahSeeder::class,      // Mapping of which Surahs are in which Juzs
        ]);
    }
}

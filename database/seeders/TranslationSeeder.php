<?php
// database/seeders/TranslationSeeder.php
//
// PURPOSE: Seed translation LABELS only.
//          Never seeds actual ayah text.
//          Never uses truncate() — safe to run anytime.
//
// ACTUAL AYAH TEXT is imported by:
//   php artisan quran:import-translations --all
//
// WHY updateOrCreate:
//   truncate() cascades to ayah_translations table
//   and wipes 6,236+ records. updateOrCreate is safe.

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [

            // ── ENGLISH ──────────────────────────────────
            [
                'name'          => 'Sahih International',
                'author'        => 'Saheeh International',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'sahih-international',
                'source'        => '20',   // quran.com translation ID
                'is_free'       => true,   // Free for all users
                'is_active'     => true,
                'sort_order'    => 1,
            ],
            [
                'name'          => 'The Clear Quran',
                'author'        => 'Dr. Mustafa Khattab',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'the-clear-quran',
                'source'        => '131',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 2,
            ],
            [
                'name'          => 'Pickthall',
                'author'        => 'Mohammed Marmaduke Pickthall',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'pickthall',
                'source'        => '95',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 3,
            ],
            [
                'name'          => 'Yusuf Ali',
                'author'        => 'Abdullah Yusuf Ali',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'yusuf-ali',
                'source'        => '85',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 4,
            ],
            [
                'name'          => 'T. Usmani',
                'author'        => 'Mufti Taqi Usmani',
                'language_code' => 'en',
                'language_name' => 'English',
                'slug'          => 'en-taqi-usmani',
                'source'        => '84',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 5,
            ],

            // ── URDU ─────────────────────────────────────
            [
                'name'          => 'Fateh Muhammad Jalandhari',
                'author'        => 'Fateh Muhammad Jalandhari',
                'language_code' => 'ur',
                'language_name' => 'Urdu',
                'slug'          => 'jalandhari-ur',
                'source'        => '82',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 6,
            ],

            // ── ARABIC ───────────────────────────────────
            [
                'name'          => 'King Fahad Complex',
                'author'        => 'King Fahad Complex',
                'language_code' => 'ar',
                'language_name' => 'Arabic',
                'slug'          => 'king-fahad-ar',
                'source'        => '97',
                'is_free'       => false,
                'is_active'     => true,
                'sort_order'    => 7,
            ],

        ];

        foreach ($translations as $data) {
            // ✅ updateOrCreate — NEVER destroys existing data
            Translation::updateOrCreate(
                ['slug' => $data['slug']],  // Find by slug
                $data                        // Update or create with this data
            );
        }

        $this->command->info('✅ Translations seeded safely.');
        $this->command->info('   Run: php artisan quran:import-translations --all');
        $this->command->info('   to import actual ayah text.');
    }
}

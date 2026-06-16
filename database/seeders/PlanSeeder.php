<?php
// database/seeders/PlanSeeder.php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [

            // ─────────────────────────────────────────
            // FREE — Hook them with the beauty of Quran
            // ─────────────────────────────────────────
            [
                'name'              => 'Free',
                'slug'              => 'free',
                'description'       => 'Begin your journey with the Book of Allah.',
                'price_monthly'     => 0.00,
                'price_yearly'      => 0.00,
                'price_lifetime'    => 0.00,
                'story_limit'       => 5,      // 5 free stories
                'translation_limit' => 1,      // Sahih International only
                'has_tafsir'        => false,  // No tafsir
                'has_audio'         => true,   // ✅ Mishary only (free reciter)
                'has_notes'         => false,  // No personal notes
                'has_progress'      => true,   // ✅ Track reading progress
                'has_downloads'     => false,  // No offline
                'features'          => [
                    'Full Quran — Arabic text',
                    'Sahih International translation',
                    'Audio recitation — Mishary Rashid',
                    'Reading progress tracker',
                    '5 Prophet stories',
                    'Basic bookmarks',
                ],
                'is_active'  => true,
                'sort_order' => 1,
            ],

            // ─────────────────────────────────────────
            // BASIC — Deepen their connection
            // ─────────────────────────────────────────
            [
                'name'              => 'Basic',
                'slug'              => 'basic',
                'description'       => 'Deepen your understanding of the Quran.',
                'price_monthly'     => 1.99,
                'price_yearly'      => 19.99,  // ~2 months free
                'price_lifetime'    => 0.00,
                'story_limit'       => null,   // All stories
                'translation_limit' => null,   // All translations
                'has_tafsir'        => true,   // ✅ Tafsir unlocked
                'has_audio'         => true,   // ✅ All reciters
                'has_notes'         => false,  // No notes yet
                'has_progress'      => true,   // Full progress
                'has_downloads'     => false,  // No offline
                'features'          => [
                    'Everything in Free',
                    'All Quran translations',
                    'All Qari recitations',
                    'Tafsir — Ibn Kathir, Maariful Quran',
                    'All Prophet stories',
                    'Sahaba & Khulafa stories',
                    'Unlimited bookmarks',
                    'Full reading progress',
                ],
                'is_active'  => true,
                'sort_order' => 2,
            ],

            // ─────────────────────────────────────────
            // PREMIUM — Complete spiritual experience
            // ─────────────────────────────────────────
            [
                'name'              => 'Premium',
                'slug'              => 'premium',
                'description'       => 'The complete spiritual learning experience.',
                'price_monthly'     => 3.99,
                'price_yearly'      => 35.99,  // ~3 months free
                'price_lifetime'    => 29.00,  // One time forever
                'story_limit'       => null,
                'translation_limit' => null,
                'has_tafsir'        => true,
                'has_audio'         => true,
                'has_notes'         => true,   // ✅ Personal notes
                'has_progress'      => true,
                'has_downloads'     => true,   // ✅ Offline access
                'features'          => [
                    'Everything in Basic',
                    'Personal notes on every ayah',
                    'Four Imams library',
                    'Daily Hadith collection',
                    'Offline Quran download',
                    'Islamic calendar & events',
                    'AI Taddabur Assistant (Coming Soon)',
                ],
                'is_active'  => true,
                'sort_order' => 3,
            ],

        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('✅ Plans seeded: Free, Basic, Premium');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                    => 'Free',
                'slug'                    => 'free',
                'description'             => 'Begin your journey with the Book of Allah.',
                'price_monthly'           => 0,
                'price_yearly'            => 0,
                'price_lifetime'          => 0,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id'  => null,
                'features' => [
                    'Full Quran — Arabic text',
                    'Sahih International translation',
                    'Audio recitation — Mishary Rashid',
                    'Smart reading progress tracking',
                    '5 Prophet stories',
                    'Up to 7 bookmarks',
                ],
                'story_limit'       => 5,
                'translation_limit' => 1,
                'bookmark_limit'    => 7,
                'has_tafsir'        => false,
                'has_audio'         => true,
                'has_notes'         => false,
                'has_progress'      => true,
                'has_downloads'     => false,
                'has_hadith'        => false,
                'is_active'         => true,
                'sort_order'        => 1,
            ],
            [
                'name'                    => 'Basic',
                'slug'                    => 'basic',
                'description'             => 'Deepen your understanding of the Quran.',
                'price_monthly'           => 1.99,
                'price_yearly'            => 19.99,
                'price_lifetime'          => 0,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id'  => null,
                'features' => [
                    'Everything in Free',
                    'All Quran translations',
                    'All Qari recitations',
                    'Tafsir — Ibn Kathir, Al-Jalalayn',
                    '12 Prophet stories',
                    'Unlimited bookmarks',
                ],
                'story_limit'       => 12,
                'translation_limit' => null,
                'bookmark_limit'    => null,
                'has_tafsir'        => true,
                'has_audio'         => true,
                'has_notes'         => false,
                'has_progress'      => true,
                'has_downloads'     => false,
                'has_hadith'        => false,
                'is_active'         => true,
                'sort_order'        => 2,
            ],
            [
                'name'                    => 'Premium',
                'slug'                    => 'premium',
                'description'             => 'The complete spiritual learning experience.',
                'price_monthly'           => 3.99,
                'price_yearly'            => 35.99,
                'price_lifetime'          => 29,
                'stripe_monthly_price_id' => null,
                'stripe_yearly_price_id'  => null,
                'features' => [
                    'Everything in Basic',
                    'All 25 Prophet stories',
                    'Sahaba & Khulafa stories',
                    'Personal notes on every ayah',
                    'Four Imams library',
                    'Daily Hadith collection',
                    'Offline Quran download',
                ],
                'story_limit'       => null,
                'translation_limit' => null,
                'bookmark_limit'    => null,
                'has_tafsir'        => true,
                'has_audio'         => true,
                'has_notes'         => true,
                'has_progress'      => true,
                'has_downloads'     => true,
                'has_hadith'        => true,
                'is_active'         => true,
                'sort_order'        => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}

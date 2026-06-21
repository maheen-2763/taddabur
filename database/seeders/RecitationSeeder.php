<?php
// database/seeders/RecitationSeeder.php

namespace Database\Seeders;

use App\Models\Recitation;
use Illuminate\Database\Seeder;

class RecitationSeeder extends Seeder
{
    public function run(): void
    {
        $recitations = [

            // ── Already have this one — kept for completeness ──
            [
                'name'               => 'Mishary Rashid Alafasy',
                'slug'               => 'mishary-rashid',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Alafasy_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => true,  // Free plan default reciter
                'is_active'          => true,
                'sort_order'         => 1,
            ],

            [
                'name'               => 'Abdur-Rahman As-Sudais',
                'slug'               => 'as-sudais',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Abdurrahmaan_As-Sudais_192kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 2,
            ],

            [
                'name'               => 'Saad Al-Ghamdi',
                'slug'               => 'al-ghamdi',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Ghamadi_40kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 3,
            ],

            [
                'name'               => 'Abdul Basit Abdul Samad',
                'slug'               => 'abdul-basit',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Abdul_Basit_Murattal_192kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 4,
            ],

            [
                'name'               => 'Mahmoud Khalil Al-Husary',
                'slug'               => 'al-husary',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Husary_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 5,
            ],

            [
                'name'               => 'Ali Al-Hudhaify',
                'slug'               => 'al-hudhaify',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Hudhaify_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 6,
            ],

            [
                'name'               => 'Saud Al-Shuraim',
                'slug'               => 'al-shuraim',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Saood_ash-Shuraym_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 7,
            ],

            [
                'name'               => 'Maher Al Muaiqly',
                'slug'               => 'al-muaiqly',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/MaherAlMuaiqly128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 8,
            ],

            [
                'name'               => 'Yasser Al-Dosari',
                'slug'               => 'al-dosari',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Yasser_Ad-Dussary_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 9,
            ],

            [
                'name'               => 'Muhammad Siddiq Al-Minshawi',
                'slug'               => 'al-minshawi',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Minshawy_Murattal_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 10,
            ],

            [
                'name'               => 'Hani Ar-Rifai',
                'slug'               => 'ar-rifai',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Hani_Rifai_192kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 11,
            ],

            [
                'name'               => 'Abdullah Basfar',
                'slug'               => 'basfar',
                'style'              => 'Murattal',
                'audio_url_pattern'  => 'https://everyayah.com/data/Abdullah_Basfar_192kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'            => false,
                'is_active'          => true,
                'sort_order'         => 12,
            ],

        ];

        foreach ($recitations as $data) {
            // ✅ updateOrCreate — never destroys existing data
            Recitation::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('✅ ' . count($recitations) . ' reciters seeded.');
    }
}

<?php
// database/seeders/RecitationSeeder.php

namespace Database\Seeders;

use App\Models\Recitation;
use Illuminate\Database\Seeder;

class RecitationSeeder extends Seeder
{
    public function run(): void
    {
        Recitation::truncate();

        // Audio URL pattern explained:
        // {surah_padded} → surah number padded to 3 digits: "001"
        // {ayah_padded}  → ayah number padded to 3 digits:  "001"
        // Together they form the filename: "001001.mp3" = Surah 1, Ayah 1

        $recitations = [
            [
                'name'              => 'Mishary Rashid Al-Afasy',
                'slug'              => 'mishary-rashid',
                'style'             => 'murattal',
                'audio_url_pattern' => 'https://everyayah.com/data/Alafasy_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'           => true,  // Free plan can listen to this reciter
                'is_active'         => true,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Abdul Basit Abdul Samad',
                'slug'              => 'abdul-basit',
                'style'             => 'mujawwad',
                'audio_url_pattern' => 'https://everyayah.com/data/Abdul_Basit_Murattal_192kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'           => false,
                'is_active'         => true,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'Mahmoud Khalil Al-Husary',
                'slug'              => 'al-husary',
                'style'             => 'murattal',
                'audio_url_pattern' => 'https://everyayah.com/data/Husary_128kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'           => false,
                'is_active'         => true,
                'sort_order'        => 3,
            ],
            [
                'name'              => 'Saad Al-Ghamdi',
                'slug'              => 'saad-ghamdi',
                'style'             => 'murattal',
                'audio_url_pattern' => 'https://everyayah.com/data/Ghamadi_40kbps/{surah_padded}{ayah_padded}.mp3',
                'is_free'           => false,
                'is_active'         => true,
                'sort_order'        => 4,
            ],
        ];

        foreach ($recitations as $recitation) {
            Recitation::create($recitation);
        }

        $this->command->info('✅ Recitations seeded: ' . count($recitations) . ' reciters');
    }
}

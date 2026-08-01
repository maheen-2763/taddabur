<?php

namespace Database\Seeders;

use App\Models\ProphetName;
use App\Models\Hadith;
use App\Models\Ayah;
use Illuminate\Database\Seeder;

class ProphetNameSeeder extends Seeder
{
    public function run(): void
    {
        $hadithId = Hadith::where('collection_id', 1)->where('number', 3532)->value('id');

        $ayahIdMuhammad = Ayah::whereHas('surah', fn($q) => $q->where('number', 3))->where('number', 144)->value('id');
        $ayahIdAhmad    = Ayah::whereHas('surah', fn($q) => $q->where('number', 61))->where('number', 6)->value('id');
        $ayahId3345     = Ayah::whereHas('surah', fn($q) => $q->where('number', 33))->where('number', 45)->value('id');
        $ayahId3346     = Ayah::whereHas('surah', fn($q) => $q->where('number', 33))->where('number', 46)->value('id');
        $ayahId21107    = Ayah::whereHas('surah', fn($q) => $q->where('number', 21))->where('number', 107)->value('id');

        $muhammadRefs = [
            ['surah' => 3, 'ayah' => 144],
            ['surah' => 33, 'ayah' => 40],
            ['surah' => 47, 'ayah' => 2],
            ['surah' => 48, 'ayah' => 29],
        ];

        $names = [
            // ============ TIER 1: NAMES ============
            [
                'name_ar' => 'مُحَمَّد',
                'name_transliteration' => 'Muhammad',
                'meaning' => 'The Praised One',
                'tier' => 'name',
                'source_type' => 'quran',
                'source_reference' => 'Quran 3:144, 33:40, 47:2, 48:29',
                'hadith_id' => null,
                'ayah_id' => $ayahIdMuhammad,
                'all_references' => $muhammadRefs,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'أَحْمَد',
                'name_transliteration' => 'Ahmad',
                'meaning' => 'The Most Praiseworthy',
                'tier' => 'name',
                'source_type' => 'quran',
                'source_reference' => 'Quran 61:6',
                'hadith_id' => null,
                'ayah_id' => $ayahIdAhmad,
                'all_references' => null,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'الْمَاحِي',
                'name_transliteration' => 'Al-Mahi',
                'meaning' => 'The Eraser — through whom Allah eliminates disbelief',
                'tier' => 'name',
                'source_type' => 'hadith',
                'source_reference' => 'Sahih Bukhari #3532',
                'hadith_id' => $hadithId,
                'ayah_id' => null,
                'all_references' => null,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'الْحَاشِر',
                'name_transliteration' => 'Al-Hashir',
                'meaning' => 'The Gatherer — first to be resurrected',
                'tier' => 'name',
                'source_type' => 'hadith',
                'source_reference' => 'Sahih Bukhari #3532',
                'hadith_id' => $hadithId,
                'ayah_id' => null,
                'all_references' => null,
                'sort_order' => 4,
            ],
            [
                'name_ar' => 'الْعَاقِب',
                'name_transliteration' => 'Al-Aqib',
                'meaning' => 'The Last — no prophet after him',
                'tier' => 'name',
                'source_type' => 'hadith',
                'source_reference' => 'Sahih Bukhari #3532',
                'hadith_id' => $hadithId,
                'ayah_id' => null,
                'all_references' => null,
                'sort_order' => 5,
            ],

            // ============ TIER 2: QURANIC TITLES ============
            [
                'name_ar' => 'شَاهِد',
                'name_transliteration' => 'Shahid',
                'meaning' => 'Witness',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 33:45',
                'hadith_id' => null,
                'ayah_id' => $ayahId3345,
                'all_references' => null,
                'sort_order' => 6,
            ],
            [
                'name_ar' => 'مُبَشِّر',
                'name_transliteration' => 'Mubashshir',
                'meaning' => 'Bearer of glad tidings',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 33:45',
                'hadith_id' => null,
                'ayah_id' => $ayahId3345,
                'all_references' => null,
                'sort_order' => 7,
            ],
            [
                'name_ar' => 'نَذِير',
                'name_transliteration' => 'Nadhir',
                'meaning' => 'Warner',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 33:45',
                'hadith_id' => null,
                'ayah_id' => $ayahId3345,
                'all_references' => null,
                'sort_order' => 8,
            ],
            [
                'name_ar' => 'دَاعِيًا إِلَى اللَّه',
                'name_transliteration' => "Da'i ilallah",
                'meaning' => 'Caller to Allah',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 33:46',
                'hadith_id' => null,
                'ayah_id' => $ayahId3346,
                'all_references' => null,
                'sort_order' => 9,
            ],
            [
                'name_ar' => 'سِرَاجًا مُّنِيرًا',
                'name_transliteration' => 'Siraj Munir',
                'meaning' => 'Illuminating Lamp',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 33:46',
                'hadith_id' => null,
                'ayah_id' => $ayahId3346,
                'all_references' => null,
                'sort_order' => 10,
            ],
            [
                'name_ar' => 'رَحْمَةً لِّلْعَالَمِين',
                'name_transliteration' => 'Rahmatan lil-Alamin',
                'meaning' => 'Mercy to the Worlds',
                'tier' => 'title',
                'source_type' => 'quran',
                'source_reference' => 'Quran 21:107',
                'hadith_id' => null,
                'ayah_id' => $ayahId21107,
                'all_references' => null,
                'sort_order' => 11,
            ],
        ];

        foreach ($names as $name) {
            ProphetName::updateOrCreate(
                ['name_transliteration' => $name['name_transliteration']],
                $name
            );
        }
    }
}

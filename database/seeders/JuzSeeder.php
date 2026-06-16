<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Juz;

class JuzSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Juz::truncate();

        $juzs = [
            [1,  'الم',              'Alif Lam Meem',        'alif-lam-meem',        1, 1,   2, 141],
            [2,  'سَيَقُولُ',        'Sayaqool',             'sayaqool',             2, 142, 2, 252],
            [3,  'تِلۡكَ الرُّسُلُ', 'Tilka Rusul',          'tilka-rusul',          2, 253, 3, 92],
            [4,  'لَن تَنَالُوا',    'Lan Tana Loo',         'lan-tana-loo',         3, 93,  4, 23],
            [5,  'وَالمُحۡصَنَات',   'Wal Mohsanat',         'wal-mohsanat',         4, 24,  4, 147],
            [6,  'لَا يُحِبُّ',      'La Yuhibbullah',       'la-yuhibbullah',       4, 148, 5, 81],
            [7,  'وَإِذَا سَمِعُوا', 'Wa Iza Samiu',         'wa-iza-samiu',         5, 82,  6, 110],
            [8,  'وَلَوْ أَنَّنَا',  'Wa Lau Annana',        'wa-lau-annana',        6, 111, 7, 87],
            [9,  'قَالَ الْمَلَأُ',  'Qalal Malao',          'qalal-malao',          7, 88,  8, 40],
            [10, 'وَاعْلَمُوا',      'Wa A‘lamu',            'wa-alamu',             8, 41,  9, 92],
            [11, 'يَعْتَذِرُونَ',    'Yatazeroon',           'yatazeroon',           9, 93,  11, 5],
            [12, 'وَمَا مِن دَآبَّة', 'Wa Mamin Dabbah',      'wa-mamin-dabbah',      11, 6,  12, 52],
            [13, 'وَمَا أُبَرِّئُ',  'Wa Ma Ubarri’u',       'wa-ma-ubarriu',        12, 53, 14, 52],
            [14, 'رُبَمَا',          'Rubama',               'rubama',               15, 1,  16, 128],
            [15, 'سُبْحَانَ',        'Subhanallazi',         'subhanallazi',         17, 1,  18, 74],
            [16, 'قَالَ أَلَمْ',     'Qal Alam',             'qal-alam',             18, 75, 20, 135],
            [17, 'اقْتَرَبَ',        'Aqtaraba',             'aqtaraba',             21, 1,  22, 78],
            [18, 'قَدْ أَفْلَحَ',    'Qadd Aflaha',          'qadd-aflaha',          23, 1,  25, 20],
            [19, 'وَقَالَ الَّذِينَ', 'Wa Qalallazina',       'wa-qalallazina',       25, 21, 27, 55],
            [20, 'أَمَّنْ خَلَقَ',   'Amman Khalaqa',        'amman-khalaqa',        27, 56, 29, 45],
            [21, 'اتْلُ مَا أُوحِيَ', 'Utlu Ma Oohiya',       'utlu-ma-oohiya',       29, 46, 33, 30],
            [22, 'وَمَن يَقْنُتْ',   'Wa Manyaqnut',         'wa-manyaqnut',         33, 31, 36, 27],
            [23, 'وَمَا لِيَ',       'Wa Mali',              'wa-mali',              36, 28, 39, 31],
            [24, 'فَمَنْ أَظْلَمُ',  'Faman Azlam',          'faman-azlam',          39, 32, 41, 46],
            [25, 'إِلَيْهِ يُرَدُّ', 'Ilayhi Yuraddu',       'ilayhi-yuraddu',       41, 47, 45, 37],
            [26, 'حٰمٓ',             'Ha Meem',              'ha-meem',              46, 1,  51, 30],
            [27, 'قَالَ فَمَا',      'Qala Fama',            'qala-fama',            51, 31, 57, 29],
            [28, 'قَدْ سَمِعَ',      'Qad Sami Allah',       'qad-sami-allah',       58, 1,  66, 12],
            [29, 'تَبَارَكَ',        'Tabarakallazi',        'tabarakallazi',        67, 1,  77, 50],
            [30, 'عَمَّ',            'Amma Yatasaa’loon',    'amma-yatasaaloon',     78, 1,  114, 6],
        ];
    }
}

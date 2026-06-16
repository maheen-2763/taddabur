<?php

namespace App\Services\Quran;

class JuzNameService
{
    public static function getAll(): array
    {
        return [
            1 => ['ar' => 'الم', 'en' => 'Alif Lam Meem'],
            2 => ['ar' => 'سَيَقُولُ', 'en' => 'Sayaqool'],
            3 => ['ar' => 'تِلْكَ الرُّسُلُ', 'en' => 'Tilkal Rusul'],
            4 => ['ar' => 'لَنْ تَنَالُوا', 'en' => 'Lan Tanaaloo'],
            5 => ['ar' => 'وَالْمُحْصَنَاتُ', 'en' => 'Wal Muhsanat'],
            6 => ['ar' => 'لَا يُحِبُّ اللَّهُ', 'en' => 'La Yuhibbullah'],
            7 => ['ar' => 'وَإِذَا سَمِعُوا', 'en' => 'Wa Iza Samiu'],
            8 => ['ar' => 'وَلَوْ أَنَّنَا', 'en' => 'Walaw Annana'],
            9 => ['ar' => 'قَالَ الْمَلَأُ', 'en' => 'Qalal Mala'],
            10 => ['ar' => 'وَاعْلَمُوا', 'en' => 'Wa A’lamu'],
            11 => ['ar' => 'يَعْتَذِرُونَ', 'en' => 'Ya’tadhirun'],
            12 => ['ar' => 'وَمَا مِنْ دَابَّةٍ', 'en' => 'Wa Ma Min Dabbah'],
            13 => ['ar' => 'وَمَا أُبَرِّئُ', 'en' => 'Wa Ma Ubarri'],
            14 => ['ar' => 'رُبَمَا', 'en' => 'Rubama'],
            15 => ['ar' => 'سُبْحَانَ الَّذِي', 'en' => 'Subhanallazi'],
            16 => ['ar' => 'قَالَ أَلَمْ', 'en' => 'Qala Alam'],
            17 => ['ar' => 'اقْتَرَبَ لِلنَّاسِ', 'en' => 'Iqtaraba Linnas'],
            18 => ['ar' => 'قَدْ أَفْلَحَ', 'en' => 'Qad Aflaha'],
            19 => ['ar' => 'وَقَالَ الَّذِينَ', 'en' => 'Wa Qalallazina'],
            20 => ['ar' => 'أَمَّنْ خَلَقَ', 'en' => 'Aman Khalaq'],
            21 => ['ar' => 'اتْلُ مَا أُوحِيَ', 'en' => 'Utlu Ma Oohiya'],
            22 => ['ar' => 'وَمَنْ يَقْنُتْ', 'en' => 'Wa Man Yaqnut'],
            23 => ['ar' => 'وَمَا لِيَ', 'en' => 'Wa Maliya'],
            24 => ['ar' => 'فَمَنْ أَظْلَمُ', 'en' => 'Faman Azlam'],
            25 => ['ar' => 'إِلَيْهِ يُرَدُّ', 'en' => 'Ilayhi Yurad'],
            26 => ['ar' => 'حَم', 'en' => 'Haa Meem'],
            27 => ['ar' => 'قَالَ فَمَا خَطْبُكُمْ', 'en' => 'Qala Fama Khatbukum'],
            28 => ['ar' => 'قَدْ سَمِعَ اللَّهُ', 'en' => 'Qad Sami Allah'],
            29 => ['ar' => 'تَبَارَكَ الَّذِي', 'en' => 'Tabarakallazi'],
            30 => ['ar' => 'عَمَّ', 'en' => 'Amma Yatasaaloon'],
        ];
    }

    public static function get(int $juz): array
    {
        return self::getAll()[$juz] ?? [
            'ar' => 'الجزء',
            'en' => 'Sacred Juz'
        ];
    }
}

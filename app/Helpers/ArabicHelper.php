<?php

namespace App\Helpers;

use Alkoumi\LaravelHijriDate\Hijri;

class ArabicHelper
{
    /**
     * Convert Western Arabic numerals to Eastern Arabic numerals
     * 1234567890 → ١٢٣٤٥٦٧٨٩٠
     */
    public static function toEasternArabic(int $number): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        return str_replace($western, $eastern, (string) $number);
    }

    public static function normalizeArabic(string $text): string
    {
        // Step 1: Diacritics (zabar, zer, pesh, sukun, tanween, shadda) hatao
        $text = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{06D6}-\x{06ED}]/u', '', $text);

        // Step 2: Alef variants ko normal Alef mein convert karo
        // أ (hamza-above) إ (hamza-below) آ (madda) ٱ (wasla) → ا
        $text = preg_replace('/[\x{0622}\x{0623}\x{0625}\x{0671}]/u', 'ا', $text);

        // Step 3: Ta Marbuta → Ha (ة → ه) — optional, search ke liye helpful
        $text = str_replace('ة', 'ه', $text);

        // Step 4: Alef Maksura → Ya (ى → ي)
        $text = str_replace('ى', 'ي', $text);

        // Step 5: Extra whitespace clean karo
        return trim(preg_replace('/\s+/', ' ', $text));
    }


    /**
     * Hijri Date
     */
    public static function hijriDate(): string
    {
        return Hijri::Date('j F Y') . ' هـ';
    }

    public static function stripBismillah(string $text): string
    {
        $patterns = [
            'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ',
            'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
            'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ',
        ];

        foreach ($patterns as $pattern) {

            if (str_starts_with($text, $pattern)) {
                return trim(
                    mb_substr($text, mb_strlen($pattern))
                );
            }
        }

        return $text;
    }
}

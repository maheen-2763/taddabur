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

    /**
     * Hijri Date
     */
    public static function hijriDate(): string
    {
        return Hijri::Date('j F Y') . ' هـ';
    }
}

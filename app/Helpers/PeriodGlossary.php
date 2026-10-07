<?php
// app/Helpers/PeriodGlossary.php

namespace App\Helpers;

class PeriodGlossary
{
    public static function explain(string $period): string
    {
        $parts = [];

        // Explain "circa"
        if (str_contains($period, 'circa') || str_contains(strtolower($period), 'circa')) {
            $parts[] = '"Circa" means "around" or "approximately" — the exact date is not certainly known.';
        }

        // Explain "BCE"
        if (str_contains($period, 'BCE')) {
            $parts[] = '"BCE" (Before Common Era) means before year 1 — i.e., that many years before the Christian calendar begins.';
        }

        // Explain "CE"
        if (str_contains($period, 'CE') && !str_contains($period, 'BCE')) {
            $parts[] = '"CE" (Common Era) refers to the widely-used calendar era, equivalent to "AD."';
        }

        // Explain vague/non-numeric eras
        if (str_contains($period, 'Ancient')) {
            $parts[] = 'This era has no scholarly-confirmed exact dates — it refers broadly to a very early historical period.';
        }

        if (str_contains($period, 'Flood')) {
            $parts[] = 'This refers to a time before recorded history — no exact date is established.';
        }

        if (str_contains($period, 'Beginning of humanity')) {
            $parts[] = 'This refers to the very start of human history, before any dating system existed.';
        }

        if (str_contains($period, 'century')) {
            $parts[] = 'A "century" is a 100-year span — used here since the exact year is not certain.';
        }

        // Fallback if nothing matched
        if (empty($parts)) {
            return 'An approximate historical era — exact dates are not confirmed by scholars.';
        }

        return implode(' ', $parts);
    }
}

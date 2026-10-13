<?php

namespace App\Support;

final class ListInput
{
    /** "a, b ,, c" => ['a', 'b', 'c'] (trim + empty hata kar, duplicate hata kar) */
    public static function parse(?string $value): array
    {
        return array_values(array_unique(array_filter(
            array_map('trim', explode(',', (string) $value)),
            fn($item) => $item !== ''
        )));
    }
}

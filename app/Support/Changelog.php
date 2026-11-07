<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Changelog
{
    public static function html(?string $path = null): string
    {
        $path ??= base_path('CHANGELOG.md');

        if (! is_file($path)) {
            return '';
        }

        return Str::markdown(file_get_contents($path), [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}

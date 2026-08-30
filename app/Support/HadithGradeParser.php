<?php

namespace App\Support;

class HadithGradeParser
{
    // Isnad-type words — checked separately, shown as a small neutral badge
    protected const ISNAD_TYPES = [
        'Mauquf' => 'Mawquf',
        'Mawquf' => 'Mawquf',
        'Maqtu' => "Maqtu'",
        'Mursal' => 'Mursal',
        'Marfu' => "Marfu'",
    ];

    // Reliability words — ORDER MATTERS. More specific/severe grades checked first,
    // so e.g. "Very Daif" matches before generic "Daif", and "Munkar" before "Daif"
    // when both appear together in a compound string.
    protected const RELIABILITY_MAP = [
        'Very Daif' => ['label' => 'Very Da\'if', 'css' => 'grade-very-daif'],
        'Mawdu' => ['label' => 'Mawdu', 'css' => 'grade-mawdu'],
        'Batil' => ['label' => 'Batil', 'css' => 'grade-mawdu'],
        'Munkar' => ['label' => 'Munkar', 'css' => 'grade-munkar'],
        'Shadh' => ['label' => 'Shadh', 'css' => 'grade-shadh'],
        'Malool' => ['label' => 'Malool', 'css' => 'grade-daif'],
        'Daif' => ['label' => "Da'if", 'css' => 'grade-daif'],
        'Hasan' => ['label' => 'Hasan', 'css' => 'grade-hasan'],
        'Mutawatir' => ['label' => 'Mutawatir', 'css' => 'grade-sahih'],
        'Sahih' => ['label' => 'Sahih', 'css' => 'grade-sahih'],
    ];

    public static function parse(?string $raw): array
    {
        if (!$raw) {
            return ['isnad_type' => null, 'label' => null, 'css_class' => null, 'raw' => null];
        }

        $isnadType = null;
        foreach (self::ISNAD_TYPES as $needle => $display) {
            if (stripos($raw, $needle) !== false) {
                $isnadType = $display;
                break;
            }
        }

        $label = null;
        $cssClass = null;
        foreach (self::RELIABILITY_MAP as $needle => $info) {
            if (stripos($raw, $needle) !== false) {
                $label = $info['label'];
                $cssClass = $info['css'];
                break;
            }
        }

        // ✅ Fallback CSS class only applies when we found NEITHER an isnad
        // type NOR a reliability label — i.e. genuinely unrecognized text.
        // If we already found an isnad type, that badge alone is enough;
        // showing the raw grade again would just repeat the same word.
        if (!$label && !$isnadType) {
            $cssClass = 'grade-other';
        }

        return [
            'isnad_type' => $isnadType,
            'label' => $label,
            'css_class' => $cssClass,
            'raw' => $raw,
        ];
    }
}

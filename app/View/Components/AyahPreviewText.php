<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;
use App\Helpers\ArabicHelper;

class AyahPreviewText extends Component
{
    public string $text;

    /**
     * @param mixed $ayah - Ayah model instance
     * @param bool $stripBismillah - whether to strip bismillah (only relevant for ayah 1)
     * @param int|null $limit - character limit (null = no truncation, full text)
     */
    public function __construct(
        public $ayah,
        public bool $stripBismillah = false,
        public ?int $limit = null,
    ) {
        $ayahText = $this->ayah->text_arabic;

        if ($this->stripBismillah && $this->ayah->number === 1) {
            $ayahText = ArabicHelper::stripBismillah($ayahText);
        }

        $this->text = $this->limit
            ? Str::limit($ayahText, $this->limit)
            : $ayahText;
    }

    public function render()
    {
        return view('components.ayah-preview-text');
    }
}

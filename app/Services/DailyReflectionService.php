<?php

namespace App\Services;

use App\Models\DailyContent;

class DailyReflectionService
{
    public function today(): ?DailyContent
    {
        return DailyContent::query()
            ->where('type', 'reflection')
            ->whereDate('scheduled_for', today())
            ->first();
    }
}

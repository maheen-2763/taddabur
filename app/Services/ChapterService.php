<?php

namespace App\Services;

use App\Models\Story;
use App\Models\StoryChapter;

class ChapterService
{
    public function create(Story $story, array $data): StoryChapter
    {

        return $story->chapters()->create([

            'title' => $data['title'],
            'content' => $data['content'],
            'order' => $data['order'],
            'quran_references' => $this->parseQuranReferences(
                $data['quran_references'] ?? null
            ),
        ]);
    }

    public function update(StoryChapter $chapter, array $data): bool
    {
        return $chapter->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'order' => $data['order'] ?? $chapter->order,
            'quran_references' => $this->parseQuranReferences(
                $data['quran_references'] ?? null
            ),
        ]);
    }

    private function parseQuranReferences(?string $references): array
    {
        if (blank($references)) {
            return [];
        }

        return collect(explode(',', $references))
            ->map(fn($ref) => trim($ref))
            ->filter()
            ->values()
            ->toArray();
    }
}

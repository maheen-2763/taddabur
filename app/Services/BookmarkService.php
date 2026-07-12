<?php
// app/Services/BookmarkService.php

namespace App\Services;

use App\Models\Ayah;
use App\Models\Bookmark;
use App\Models\StoryChapter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Hadith;

class BookmarkService
{
    // -------------------------------------------------------
    // TOGGLE BOOKMARK
    // If bookmark exists → remove it (returns 'removed')
    // If bookmark doesn't exist → add it (returns 'added')
    // This is called the "toggle" pattern
    // -------------------------------------------------------
    public function toggle(User $user, string $type, int $itemId): array
    {
        // Map the type string to the correct model class
        $modelClass = $this->resolveModelClass($type);

        // Make sure the item actually exists in the database
        $item = $modelClass::findOrFail($itemId);

        // Check if bookmark already exists
        $existing = Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_type', $modelClass)
            ->where('bookmarkable_id', $item->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return ['status' => 'removed', 'message' => 'Bookmark removed.'];
        }

        Bookmark::create([
            'user_id'           => $user->id,
            'bookmarkable_type' => $modelClass,
            'bookmarkable_id'   => $item->id,
        ]);

        return ['status' => 'added', 'message' => 'Bookmarked successfully.'];
    }

    // -------------------------------------------------------
    // GET USER BOOKMARKS PAGINATED
    // Loads the polymorphic related item (Ayah or StoryChapter)
    // -------------------------------------------------------
    // GET USER BOOKMARKS PAGINATED
    public function getUserBookmarks(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return Bookmark::where('user_id', $user->id)
            ->with([
                'bookmarkable' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Ayah::class => ['surah'],
                        \App\Models\Hadith::class => ['collection', 'chapter'],
                        \App\Models\StoryChapter::class => ['story'],
                    ]);
                },
            ])
            ->latest()
            ->paginate($perPage);
    }

    // -------------------------------------------------------
    // CHECK IF AN ITEM IS BOOKMARKED BY USER
    // Used to show the correct button state (filled vs empty)
    // -------------------------------------------------------
    public function isBookmarked(User $user, string $type, int $itemId): bool
    {
        $modelClass = $this->resolveModelClass($type);

        return Bookmark::where('user_id', $user->id)
            ->where('bookmarkable_type', $modelClass)
            ->where('bookmarkable_id', $itemId)
            ->exists();
    }

    // -------------------------------------------------------
    // DELETE BOOKMARK (with ownership check)
    // -------------------------------------------------------
    public function delete(User $user, Bookmark $bookmark): void
    {
        // Security: make sure this bookmark belongs to THIS user
        if ($bookmark->user_id !== $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'You do not own this bookmark.'
            );
        }

        $bookmark->delete();
    }

    // -------------------------------------------------------
    // PRIVATE: Map type string to model class
    // -------------------------------------------------------
    private function resolveModelClass(string $type): string
    {
        return match ($type) {
            'ayah'    => Ayah::class,
            'chapter' => StoryChapter::class,
            'hadith'  => Hadith::class,
            default   => throw new \InvalidArgumentException("Unknown bookmark type: {$type}")
        };
    }
}

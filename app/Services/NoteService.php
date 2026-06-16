<?php
// app/Services/NoteService.php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NoteService
{
    // -------------------------------------------------------
    // CREATE A NOTE
    // -------------------------------------------------------
    public function create(User $user, array $data): Note
    {
        return Note::create([
            'user_id'    => $user->id,
            'ayah_id'    => $data['ayah_id'] ?? null,
            'story_id'   => $data['story_id'] ?? null,
            'title'      => $data['title'] ?? null,
            'content'    => $data['content'],
            'color'      => $data['color'] ?? '#fef3c7',
            'is_private' => true, // Notes are always private
        ]);
    }

    // -------------------------------------------------------
    // UPDATE A NOTE (with ownership check)
    // -------------------------------------------------------
    public function update(User $user, Note $note, array $data): Note
    {
        $this->authorizeOwnership($user, $note);

        $note->update([
            'title'   => $data['title'] ?? $note->title,
            'content' => $data['content'] ?? $note->content,
            'color'   => $data['color'] ?? $note->color,
        ]);

        return $note->fresh(); // Return the updated model from DB
    }

    // -------------------------------------------------------
    // DELETE A NOTE (soft delete — recoverable)
    // -------------------------------------------------------
    public function delete(User $user, Note $note): void
    {
        $this->authorizeOwnership($user, $note);
        $note->delete(); // Uses SoftDeletes — sets deleted_at
    }

    // -------------------------------------------------------
    // GET USER NOTES PAGINATED
    // -------------------------------------------------------
    public function getUserNotes(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return Note::where('user_id', $user->id)
            ->with([
                'ayah.surah', // Load ayah and its surah
                'story',      // Load linked story
            ])
            ->latest()
            ->paginate($perPage);
    }

    // -------------------------------------------------------
    // PRIVATE: Ownership check
    // Throws an exception if user doesn't own the note
    // -------------------------------------------------------
    private function authorizeOwnership(User $user, Note $note): void
    {
        if ($note->user_id !== $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'You do not own this note.'
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class NoteController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display all notes belonging to the authenticated user.
     */
    public function index(Request $request): View
    {
        $notes = Note::query()
            ->forUser(Auth::id())
            ->latest()
            ->paginate(15);

        return view('notes.index', compact('notes'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('notes.create');
    }

    /**
     * Store a new note.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ayah_id' => ['nullable', 'exists:ayahs,id'],
            'story_id' => ['nullable', 'exists:stories,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_private' => ['boolean'],
        ]);

        $validated['user_id'] = Auth::id();

        Note::create($validated);

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    /**
     * Show a note.
     */
    public function show(Note $note): View
    {
        $this->authorize('view', $note);

        return view('notes.show', compact('note'));
    }

    /**
     * Show edit form.
     */
    public function edit(Note $note): View
    {
        $this->authorize('update', $note);

        return view('notes.edit', compact('note'));
    }

    /**
     * Update a note.
     */
    public function update(Request $request, Note $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'ayah_id' => ['nullable', 'exists:ayahs,id'],
            'story_id' => ['nullable', 'exists:stories,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_private' => ['boolean'],
        ]);

        $note->update($validated);

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Soft delete.
     */
    public function destroy(Note $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }
}

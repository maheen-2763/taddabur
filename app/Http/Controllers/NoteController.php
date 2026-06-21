<?php

namespace App\Http\Controllers;

use App\Models\Ayah;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoteController extends Controller
{
    use AuthorizesRequests;

    // ── Display all notes (full page) ─────────────────────
    public function index(Request $request): View
    {
        $notes = Note::query()
            ->forUser(Auth::id())
            ->latest()
            ->paginate(15);

        return view('notes.index', compact('notes'));
    }

    public function create(): View
    {
        return view('notes.create');
    }

    // ── Store — supports BOTH form redirect AND AJAX ──────
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ayah_id'    => ['nullable', 'exists:ayahs,id'],
            'story_id'   => ['nullable', 'exists:stories,id'],
            'title'      => ['nullable', 'string', 'max:255'], // ✅ relaxed
            'content'    => ['required', 'string'],
            'color'      => ['nullable', 'string', 'max:20'],
            'is_private' => ['boolean'],
        ]);

        $validated['user_id'] = Auth::id();

        // ✅ Auto-generate a title when omitted
        if (empty($validated['title'])) {
            $validated['title'] = $this->defaultTitleFor($validated);
        }

        $note = Note::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'created',
                'note'   => $note->only(['id', 'title', 'content']),
            ]);
        }

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    public function show(Note $note): View
    {
        $this->authorize('view', $note);
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note): View
    {
        $this->authorize('update', $note);
        return view('notes.edit', compact('note'));
    }

    // ── Update — supports BOTH form redirect AND AJAX ─────
    public function update(Request $request, Note $note): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'ayah_id'    => ['nullable', 'exists:ayahs,id'],
            'story_id'   => ['nullable', 'exists:stories,id'],
            'title'      => ['nullable', 'string', 'max:255'],
            'content'    => ['required', 'string'],
            'color'      => ['nullable', 'string', 'max:20'],
            'is_private' => ['boolean'],
        ]);

        if (empty($validated['title'])) {
            $validated['title'] = $note->title
                ?: $this->defaultTitleFor($validated + ['ayah_id' => $note->ayah_id]);
        }

        $note->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'updated',
                'note'   => $note->only(['id', 'title', 'content']),
            ]);
        }

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    // ── Destroy — supports BOTH redirect AND AJAX ─────────
    public function destroy(Request $request, Note $note): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'deleted']);
        }

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    // ── Generate a sensible default title ──────────────────
    private function defaultTitleFor(array $data): string
    {
        if (!empty($data['ayah_id'])) {
            $ayah = Ayah::with('surah')->find($data['ayah_id']);
            if ($ayah?->surah) {
                return "{$ayah->surah->name_transliteration} {$ayah->surah->number}:{$ayah->number}";
            }
        }

        return 'Untitled Note';
    }
}

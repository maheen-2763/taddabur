<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryChapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChapterController extends Controller
{
    // Show create form
    public function create(Story $story): View
    {
        $nextOrder = ($story->chapters()->max('order') ?? 0) + 1;

        return view('admin.chapters.create', compact('story', 'nextOrder'));
    }

    // Store new chapter
    public function store(Request $request, Story $story): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'content'          => ['required', 'string'],
            'order'            => ['required', 'integer', 'min:1'],
            'quran_references' => ['nullable', 'string'],
        ]);

        StoryChapter::create([
            'story_id'         => $story->id,
            'title'            => $validated['title'],
            'content'          => $validated['content'],
            'order'            => $validated['order'],
            'quran_references' => $validated['quran_references']
                ? explode(',', $validated['quran_references'])
                : [],
        ]);

        return redirect()
            ->route('admin.stories.edit', $story)
            ->with('success', 'Chapter added successfully.');
    }

    // Show edit form
    public function edit(StoryChapter $chapter): View
    {
        $story = $chapter->story;

        return view('admin.chapters.edit', compact('chapter', 'story'));
    }

    // Update chapter
    public function update(Request $request, StoryChapter $chapter): RedirectResponse
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'content'          => ['required', 'string'],
            'order'            => ['nullable', 'integer', 'min:1'],
            'quran_references' => ['nullable', 'string'],
        ]);

        $chapter->update([
            'title'            => $validated['title'],
            'content'          => $validated['content'],
            'order'            => $validated['order'] ?? $chapter->order,
            'quran_references' => $validated['quran_references']
                ? explode(',', $validated['quran_references'])
                : $chapter->quran_references,
        ]);

        return redirect()->route('admin.stories.edit', $chapter->story)->with('success', 'Chapter updated.');
    }

    // Delete chapter
    public function destroy(StoryChapter $chapter): RedirectResponse
    {
        $story = $chapter->story;
        $chapter->delete();

        return redirect()
            ->route('admin.stories.edit', $story)
            ->with('success', 'Chapter deleted.');
    }
}

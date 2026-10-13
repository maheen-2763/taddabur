<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryChapter;
use App\Support\ListInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChapterController extends Controller
{
    public function create(Story $story): View
    {
        $nextOrder = ($story->chapters()->max('order') ?? 0) + 1;

        return view('admin.chapters.create', compact('story', 'nextOrder'));
    }

    public function store(Request $request, Story $story): RedirectResponse
    {
        $data = $request->validate($this->rules($story->id), $this->messages());

        StoryChapter::create([
            'story_id'         => $story->id,
            'title'            => $data['title'],
            'content'          => $data['content'],
            'order'            => $data['order'],
            'quran_references' => ListInput::parse($data['quran_references'] ?? null),
        ]);

        $target = $request->input('after') === 'another'
            ? route('admin.chapters.create', $story)
            : route('admin.stories.edit', $story);

        return redirect($target)->with('success', 'Chapter added successfully.');
    }

    public function edit(StoryChapter $chapter): View
    {
        $story = $chapter->story;

        return view('admin.chapters.edit', compact('chapter', 'story'));
    }

    public function update(Request $request, StoryChapter $chapter): RedirectResponse
    {
        $data = $request->validate($this->rules($chapter->story_id, $chapter->id), $this->messages());

        $chapter->update([
            'title'            => $data['title'],
            'content'          => $data['content'],
            'order'            => $data['order'],
            'quran_references' => ListInput::parse($data['quran_references'] ?? null),
        ]);

        return redirect()
            ->route('admin.stories.edit', $chapter->story_id)
            ->with('success', 'Chapter updated.');
    }

    public function destroy(StoryChapter $chapter): RedirectResponse
    {
        $storyId = $chapter->story_id;
        $chapter->delete();

        return redirect()
            ->route('admin.stories.edit', $storyId)
            ->with('success', 'Chapter deleted.');
    }

    private function rules(int $storyId, ?int $ignoreChapterId = null): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'content'          => ['required', 'string'],
            'order'            => [
                'required',
                'integer',
                'min:1',
                Rule::unique('story_chapters', 'order')
                    ->where('story_id', $storyId)
                    ->ignore($ignoreChapterId),
            ],
            'quran_references' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return ['order.unique' => 'In this story the following chapter order is already present.'];
    }
}

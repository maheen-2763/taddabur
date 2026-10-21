<?php

namespace App\Http\Controllers\Admin;


use Closure;
use App\Http\Controllers\Controller;
use App\Models\Prophet;
use App\Models\Story;
use App\Support\ListInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StoryController extends Controller
{
    private const CATEGORIES   = ['prophet', 'companion', 'general'];
    private const DIFFICULTIES = ['beginner', 'intermediate', 'advanced'];

    public function index(): View
    {
        $stories = Story::withTrashed()
            ->with('prophet:id,name_transliteration,slug')
            ->withCount('chapters')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.stories.index', compact('stories'));
    }

    public function create(): View
    {
        $prophets = Prophet::orderBy('order')->get();

        return view('admin.stories.create', compact('prophets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $story = Story::create([
            'prophet_id'        => $data['prophet_id'] ?? null,
            'title'             => $data['title'],
            'slug'              => $this->uniqueSlug($data['title']),
            'category'          => $data['category'],
            'summary'           => $data['summary'],
            'difficulty'        => $data['difficulty'],
            'is_free'           => true, // Taddabur hamesha free hai
            'is_published'      => $request->boolean('is_published'),
            'sort_order'        => $data['sort_order'] ?? 0,
            'read_time_minutes' => $data['read_time_minutes'],
            'tags'              => ListInput::parse($data['tags'] ?? null),
        ]);

        return redirect()
            ->route('admin.chapters.create', $story)
            ->with('success', 'Story created! Now add chapters.');
    }

    public function edit(Story $story): View
    {
        $prophets = Prophet::orderBy('order')->get();

        return view('admin.stories.edit', compact('story', 'prophets'));
    }

    public function update(Request $request, Story $story): RedirectResponse
    {
        $data = $request->validate($this->rules($story));

        // Slug update nahi karte: public URLs stable rehne chahiye.
        $story->update([
            'prophet_id'        => $data['prophet_id'] ?? null,
            'title'             => $data['title'],
            'category'          => $data['category'],
            'summary'           => $data['summary'],
            'difficulty'        => $data['difficulty'],
            'is_free'           => true,
            'is_published'      => $request->boolean('is_published'),
            'sort_order'        => $data['sort_order'] ?? $story->sort_order,
            'read_time_minutes' => $data['read_time_minutes'],
            'tags'              => ListInput::parse($data['tags'] ?? null),
        ]);

        return back()->with('success', 'Story updated successfully.');
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete(); // soft delete

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Story hidden (soft deleted). You can restore it from this list.');
    }

    public function restore(int $story): RedirectResponse
    {
        Story::onlyTrashed()->findOrFail($story)->restore();

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Story restored.');
    }

    private function rules(?Story $story = null): array
    {
        return [
            'title'             => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) use ($story) {
                    if ($this->titleExists((string) $value, $story?->id)) {
                        $fail('There is a story available for this title. If it has been deleted earlier restore it from stories list page.
                        ');
                    }
                },
            ],
            'prophet_id'        => ['nullable', 'integer', 'exists:prophets,id'],
            'category'          => ['required', Rule::in(self::CATEGORIES)],
            'difficulty'        => ['required', Rule::in(self::DIFFICULTIES)],
            'summary'           => ['required', 'string'],
            'read_time_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'tags'              => ['nullable', 'string', 'max:500'],
        ];
    }

    /** Case, spaces aur punctuation ignore karke title compare karta hai (deleted stories bhi count hoti hain). */
    private function titleExists(string $title, ?int $ignoreId = null): bool
    {
        $needle = Str::slug($title);

        if ($needle === '') {
            return false;
        }

        return Story::withTrashed()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->pluck('title')
            ->contains(fn($existing) => Str::slug($existing) === $needle);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'story';
        $slug = $base;

        for ($i = 2; Story::withTrashed()->where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}

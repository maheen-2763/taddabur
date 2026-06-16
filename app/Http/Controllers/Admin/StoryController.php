<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prophet;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    // List all stories
    public function index(): View
    {
        $stories = Story::withTrashed()
            ->with('prophet')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.stories.index', compact('stories'));
    }

    // Show create form
    public function create(): View
    {
        $prophets = Prophet::orderBy('order')->get();

        return view('admin.stories.create', compact('prophets'));
    }

    // Store new story
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'category'   => 'required|in:prophet,companion,general',
            'summary'    => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
        ]);

        $story = Story::create([
            'prophet_id'        => $request->prophet_id,
            'title'             => $request->title,
            'slug'              => \Illuminate\Support\Str::slug($request->title),
            'category'          => $request->category,
            'summary'           => $request->summary,
            'difficulty'        => $request->difficulty,
            'is_free'           => $request->boolean('is_free'),
            'is_published'      => $request->boolean('is_published'),
            'sort_order'        => $request->sort_order ?? 0,
            'read_time_minutes' => $request->read_time_minutes,
            'tags'              => $request->tags
                ? explode(',', $request->tags)
                : [],
        ]);

        return redirect()
            ->route('admin.chapters.create', $story)
            ->with('success', 'Story created! Now add chapters.');
    }

    // Show edit form
    public function edit(Story $story): View
    {
        $prophets = Prophet::orderBy('order')->get();

        return view('admin.stories.edit', compact('story', 'prophets'));
    }

    // Update story
    public function update(Request $request, Story $story)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'summary' => 'required|string',
        ]);

        $story->update([
            'prophet_id'        => $request->prophet_id,
            'title'             => $request->title,
            'category'          => $request->category,
            'summary'           => $request->summary,
            'difficulty'        => $request->difficulty,
            'is_free'           => $request->boolean('is_free'),
            'is_published'      => $request->boolean('is_published'),
            'sort_order'        => $request->sort_order ?? $story->sort_order,
            'read_time_minutes' => $request->read_time_minutes,
            'tags'              => $request->tags
                ? explode(',', $request->tags)
                : $story->tags,
        ]);

        return back()->with('success', 'Story updated successfully.');
    }

    // Soft delete story
    public function destroy(Story $story)
    {
        $story->delete();

        return back()->with('success', 'Story deleted.');
    }
}

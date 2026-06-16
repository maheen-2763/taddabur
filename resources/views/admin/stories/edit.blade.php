@extends('admin.layout')
@section('title', 'Edit Story')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.stories.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-heading)">Edit Story</h5>
                    <small class="text-muted">{{ $story->title }}</small>
                </div>
            </div>

            {{-- Story Edit Form --}}
            <div class="admin-table p-4 mb-4">
                <form action="{{ route('admin.stories.update', $story) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Story Title *</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $story->title) }}" required>
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Prophet (optional)</label>
                            <select name="prophet_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach ($prophets as $prophet)
                                    <option value="{{ $prophet->id }}"
                                        {{ $story->prophet_id == $prophet->id ? 'selected' : '' }}>
                                        {{ $prophet->name_transliteration }} (AS)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="prophet" {{ $story->category === 'prophet' ? 'selected' : '' }}>Prophet
                                </option>
                                <option value="companion" {{ $story->category === 'companion' ? 'selected' : '' }}>Companion
                                </option>
                                <option value="general" {{ $story->category === 'general' ? 'selected' : '' }}>General
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Difficulty *</label>
                            <select name="difficulty" class="form-select" required>
                                <option value="beginner" {{ $story->difficulty === 'beginner' ? 'selected' : '' }}>
                                    Beginner</option>
                                <option value="intermediate" {{ $story->difficulty === 'intermediate' ? 'selected' : '' }}>
                                    Intermediate</option>
                                <option value="advanced" {{ $story->difficulty === 'advanced' ? 'selected' : '' }}>
                                    Advanced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Read Time (minutes)</label>
                            <input type="number" name="read_time_minutes" class="form-control"
                                value="{{ old('read_time_minutes', $story->read_time_minutes) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Summary *</label>
                            <textarea name="summary" class="form-control" rows="3" required>{{ old('summary', $story->summary) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control"
                                value="{{ old('tags', implode(', ', $story->tags ?? [])) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                value="{{ old('sort_order', $story->sort_order) }}">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_free" value="1" class="form-check-input" id="isFree"
                                    {{ $story->is_free ? 'checked' : '' }}>
                                <label class="form-check-label" for="isFree">
                                    Free story
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_published" value="1" class="form-check-input"
                                    id="isPublished" {{ $story->is_published ? 'checked' : '' }}>
                                <label class="form-check-label" for="isPublished">
                                    Published
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Changes
                            </button>
                            <a href="{{ route('admin.stories.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Chapters Section --}}
            <div class="admin-table">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-family:var(--font-heading)">
                        Chapters ({{ $story->chapters->count() }})
                    </h6>
                    <a href="{{ route('admin.chapters.create', $story) }}" class="btn btn-sm btn-admin-gold">
                        <i class="bi bi-plus-lg me-1"></i>Add Chapter
                    </a>
                </div>

                @if ($story->chapters->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <p class="mb-2">No chapters yet.</p>
                        <a href="{{ route('admin.chapters.create', $story) }}" class="btn btn-sm btn-admin-primary">
                            Add First Chapter
                        </a>
                    </div>
                @else
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Title</th>
                                <th>Read Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($story->chapters as $chapter)
                                <tr>
                                    <td class="text-muted">{{ $chapter->order }}</td>
                                    <td style="font-size:0.9rem">{{ $chapter->title }}</td>
                                    <td style="font-size:0.8rem; color:#999">{{ $chapter->read_time }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.chapters.edit', $chapter) }}"
                                                class="btn btn-sm btn-admin-primary">Edit</a>
                                            <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST"
                                                onsubmit="return confirm('Delete this chapter?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Danger Zone --}}
            <div class="mt-4 p-3" style="border:1px solid #dc3545; border-radius:12px; background:rgba(220,53,69,0.05)">
                <h6 class="text-danger mb-2">Danger Zone</h6>
                <p class="text-muted mb-2" style="font-size:0.85rem">
                    Deleting this story will also remove all its chapters.
                </p>
                <form action="{{ route('admin.stories.destroy', $story) }}" method="POST"
                    onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Story
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

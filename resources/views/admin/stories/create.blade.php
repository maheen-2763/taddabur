@extends('admin.layout')
@section('title', 'New Story')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.stories.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="mb-0" style="font-family:var(--font-heading)">Create New Story</h5>
            </div>

            <div class="admin-table p-4">
                <form action="{{ route('admin.stories.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Story Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required
                                placeholder="e.g. The Story of Musa (AS)">
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
                                        {{ old('prophet_id') == $prophet->id ? 'selected' : '' }}>
                                        {{ $prophet->name_transliteration }} (AS)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="prophet">Prophet</option>
                                <option value="companion">Companion</option>
                                <option value="general">General</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Difficulty *</label>
                            <select name="difficulty" class="form-select" required>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Read Time (minutes)</label>
                            <input type="number" name="read_time_minutes" class="form-control"
                                value="{{ old('read_time_minutes', 10) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Summary *</label>
                            <textarea name="summary" class="form-control" rows="3" required
                                placeholder="Short description shown in story listing...">{{ old('summary') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control" value="{{ old('tags') }}"
                                placeholder="patience, dua, repentance">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                value="{{ old('sort_order', 0) }}">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_free" value="1" class="form-check-input" id="isFree"
                                    {{ old('is_free') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isFree">
                                    Free story (visible to all users)
                                </label>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_published" value="1" class="form-check-input"
                                    id="isPublished" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="isPublished">
                                    Publish immediately
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-check-lg me-1"></i>Create Story & Add Chapters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

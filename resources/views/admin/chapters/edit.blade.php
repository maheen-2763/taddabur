@extends('admin.layout')
@section('title', 'Edit Chapter')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-heading)">Edit Chapter</h5>
                    <small class="text-muted">{{ $story->title }}</small>
                </div>
            </div>

            <div class="admin-table p-4">
                <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Chapter Title *</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $chapter->title) }}" required>
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Chapter Order *</label>
                            <input type="number" name="order" class="form-control"
                                value="{{ old('order', $chapter->order) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Chapter Content *</label>
                            <textarea name="content" class="form-control" rows="20" required>{{ old('content', $chapter->content) }}</textarea>
                            <small class="text-muted">
                                HTML supported: &lt;p&gt; &lt;em&gt; &lt;strong&gt; &lt;blockquote&gt; &lt;ul&gt; &lt;li&gt;
                            </small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Quran References (comma separated)</label>
                            <input type="text" name="quran_references" class="form-control"
                                value="{{ old('quran_references', implode(', ', $chapter->quran_references ?? [])) }}"
                                placeholder="Al-Baqarah 2:30, Al-A'raf 7:19">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Chapter
                            </button>
                            <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

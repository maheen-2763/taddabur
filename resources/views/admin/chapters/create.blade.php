@extends('admin.layout')
@section('title', 'Add Chapter')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.stories.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0" style="font-family:var(--font-heading)">Add Chapter</h5>
                    <small class="text-muted">{{ $story->title }}</small>
                </div>
            </div>

            <div class="admin-table p-4">
                <form action="{{ route('admin.chapters.store', $story) }}" method="POST">
                    @csrf

                    {{-- ADD THIS --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Chapter Title *</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required
                                placeholder="e.g. The Burning Bush">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Chapter Order *</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $nextOrder) }}"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Chapter Content *</label>
                            <textarea name="content" class="form-control" rows="15" required
                                placeholder="Write the chapter content here... HTML is supported.">{{ old('content') }}</textarea>
                            <small class="text-muted">
                                HTML tags supported: &lt;p&gt; &lt;em&gt; &lt;strong&gt; &lt;blockquote&gt;
                            </small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Quran References (comma separated)</label>
                            <input type="text" name="quran_references" class="form-control"
                                value="{{ old('quran_references') }}" placeholder="Al-Baqarah 2:30, Al-A'raf 7:19">
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Chapter
                            </button>
                            <a href="{{ route('admin.chapters.create', $story) }}" class="btn btn-admin-gold">
                                <i class="bi bi-plus-lg me-1"></i>Add Another Chapter
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Existing chapters list --}}
            @if ($story->chapters->isNotEmpty())
                <div class="admin-table mt-4">
                    <div class="p-3 border-bottom">
                        <h6 class="mb-0" style="font-family:var(--font-heading)">
                            Existing Chapters ({{ $story->chapters->count() }})
                        </h6>
                    </div>
                    <table class="table">
                        <tbody>
                            @foreach ($story->chapters as $chapter)
                                <tr>
                                    <td style="width:40px; color:#999">{{ $chapter->order }}</td>
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
                </div>
            @endif

        </div>
    </div>
@endsection

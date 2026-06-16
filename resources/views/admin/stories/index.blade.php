@extends('admin.layout')
@section('title', 'Stories')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">Prophet Stories</h5>
        <a href="{{ route('admin.stories.create') }}" class="btn btn-admin-gold">
            <i class="bi bi-plus-lg me-1"></i>New Story
        </a>
    </div>

    <div class="admin-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Prophet</th>
                    <th>Chapters</th>
                    <th>Status</th>
                    <th>Access</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stories as $story)
                    <tr style="{{ $story->deleted_at ? 'opacity:0.5' : '' }}">
                        <td>
                            <div style="font-size:0.9rem; font-weight:500">{{ $story->title }}</div>
                            <small class="text-muted">{{ $story->difficulty }}</small>
                        </td>
                        <td style="font-size:0.85rem">
                            {{ $story->prophet?->name_transliteration ?? '—' }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $story->chapters->count() }} chapters
                            </span>
                        </td>
                        <td>
                            @if ($story->deleted_at)
                                <span class="badge bg-danger">Deleted</span>
                            @elseif($story->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning text-dark">Draft</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $story->is_free ? 'bg-info' : 'bg-dark' }}">
                                {{ $story->is_free ? 'Free' : 'Premium' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.stories.edit', $story) }}" class="btn btn-sm btn-admin-primary">
                                    Edit
                                </a>
                                <a href="{{ route('admin.chapters.create', $story) }}" class="btn btn-sm btn-admin-gold">
                                    + Chapter
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No stories yet.
                            <a href="{{ route('admin.stories.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $stories->links() }}</div>
    </div>
@endsection

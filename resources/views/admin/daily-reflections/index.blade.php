@extends('admin.layout')
@section('title', 'Daily Content')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0" style="font-family:var(--font-heading)">Daily Content</h5>
            <small class="text-muted">Scheduled reflections and daily picks</small>
        </div>
        <a href="{{ route('admin.daily-reflections.create') }}" class="btn btn-admin-gold">
            <i class="bi bi-plus-lg me-1"></i>New Reflection
        </a>
    </div>

    <div class="admin-table">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Reference</th>
                    <th>Reflection</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailyContents as $content)
                    <tr>
                        <td>{{ $content->scheduled_for->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($content->type) }}</span></td>
                        <td>
                            @if ($content->ayah)
                                {{ $content->ayah->surah?->name_transliteration }}
                                {{ $content->ayah->surah?->number }}:{{ $content->ayah->number }}
                            @elseif ($content->hadith_id)
                                Hadith #{{ $content->hadith_id }}
                            @elseif ($content->story_id)
                                Story #{{ $content->story_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-muted" style="max-width:320px">
                            {{ \Illuminate\Support\Str::limit((string) $content->reflection, 100) ?: '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $content->is_sent ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $content->is_sent ? 'Sent' : 'Scheduled' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.daily-reflections.destroy', $content) }}" method="POST"
                                onsubmit="return confirm('Delete this entry?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">No entries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $dailyContents->links() }}</div>
    </div>
@endsection

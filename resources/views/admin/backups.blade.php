@extends('admin.layout')
@section('title', 'Backups')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0" style="font-family:var(--font-heading)">Database Backups</h5>
            <small class="text-muted">Verified SQLite snapshots · stored privately on the server</small>
        </div>
        <form action="{{ route('admin.backups.run') }}" method="POST"
            onsubmit="const b = this.querySelector('button'); b.disabled = true; b.innerText = 'Creating backup...';">
            @csrf
            <button class="btn btn-admin-gold"><i class="bi bi-plus-lg me-1"></i>Backup now</button>
        </form>
    </div>

    <div class="admin-table">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Created</th>
                    <th>File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $backup)
                    <tr>
                        <td style="font-family:monospace; font-size:.8rem">{{ $backup->filename }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ str_ends_with($backup->filename, '_auto.zip') ? 'Auto' : 'Manual' }}
                            </span>
                        </td>
                        <td>{{ $backup->size ?: '—' }}</td>
                        <td title="{{ $backup->created_at }}">{{ $backup->created_at->diffForHumans() }}</td>
                        <td>
                            <span class="badge {{ $backup->file_exists ? 'bg-success' : 'bg-danger' }}">
                                {{ $backup->file_exists ? 'OK' : 'Missing' }}
                            </span>
                        </td>
                        <td>
                            @if ($backup->file_exists)
                                <a href="{{ route('admin.backups.download', $backup) }}"
                                    class="btn btn-sm btn-admin-primary">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No backups yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $backups->links() }}</div>
    </div>
@endsection

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

    <div class="d-flex gap-3 mb-3 small text-muted">
        <span><strong>{{ $stats['count'] }}</strong> backups</span>
        <span><strong>{{ $stats['size'] }}</strong> on disk</span>
        @if ($stats['missing'] > 0)
            <span class="text-danger"><strong>{{ $stats['missing'] }}</strong> file missing</span>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('admin.backups.destroy-many') }}" method="POST"
        onsubmit="return confirm('Selected backups will be deleted permanently. Are you sure?')">
        @csrf
        @method('DELETE')

        <div class="mb-2">
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Delete selected
            </button>
        </div>

        <div class="admin-table">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:32px"><input type="checkbox" id="select-all" aria-label="Select all"></th>
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
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $backup->id }}" class="row-check"
                                    @disabled($backup->id === $latestId)
                                @if ($backup->id === $latestId) title="Most new backup cant be deleted" @endif>
                            </td>
                            <td style="font-family:monospace; font-size:.8rem">{{ $backup->filename }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $backup->isAuto() ? 'Auto' : 'Manual' }}</span>
                            </td>
                            <td>{{ $backup->size ?: '—' }}</td>
                            <td>
                                {{ $backup->created_at->diffForHumans() }}
                                <div class="small text-muted">
                                    {{ $backup->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $backup->file_exists ? 'bg-success' : 'bg-danger' }}">
                                    {{ $backup->file_exists ? 'OK' : 'Missing' }}
                                </span>
                            </td>
                            <td>
                                @if ($backup->file_exists)
                                    <a href="{{ route('admin.backups.download', $backup) }}" class="btn btn-sm btn-admin-primary">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No backups yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="p-3">{{ $backups->links() }}</div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function () {
            document.querySelectorAll('.row-check:not(:disabled)').forEach(c => c.checked = this.checked);
        });
    </script>
@endsection
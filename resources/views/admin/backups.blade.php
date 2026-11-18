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
        onsubmit="return confirm('Delete the selected backups permanently?')">
        @csrf
        @method('DELETE')

        <div class="mb-2 d-flex align-items-center gap-2">
    <button type="submit" id="delete-selected" class="btn btn-sm btn-outline-danger" disabled>
        <i class="bi bi-trash me-1"></i>Delete selected
    </button>
    <small class="text-muted">{{ 'The newest backup is protected and cannot be selected.' }}</small>
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
                                   @if ($backup->id === $latestId) title="The newest backup cannot be deleted" @endif>
                            </td>
                            <td>
                                <span style="font-family:monospace; font-size:.8rem">{{ $backup->filename }}</span>
                                @if ($backup->details)
                                    @php
                                        $details = $backup->details;
                                        $tableCount = $details['table_count'] ?? 0;
                                        $summary = number_format($details['total_rows'] ?? 0) . ' rows in ' . $tableCount . ' ' . \Illuminate\Support\Str::plural('table', $tableCount);
                                        $tables = collect($details['tables'] ?? [])->sortDesc();
                                    @endphp
                                    <details class="mt-1">
                                        <summary class="small text-muted" style="cursor:pointer">Contents: {{ $summary }}</summary>
                                        <div class="row row-cols-2 row-cols-md-3 g-1 small mt-2">
                                            @foreach ($tables as $table => $count)
                                                <div class="col d-flex justify-content-between gap-2 border-bottom">
                                                    <span
                                                        class="text-break">{{ $table }}</span><strong>{{ number_format($count) }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if (!empty($details['last_migration']))
                                           <div class="small text-muted mt-2">Last migration:
                                                <code>{{ $details['last_migration'] }}</code></div>
                                        @endif

                                            @if (!empty($details['skipped']))
                                                <div class="small text-warning mt-2">Not counted:
                                                    @foreach ($details['skipped'] as $table => $reason)
                                                        <code>{{ $table }}</code> ({{ $reason }})@if (!$loop->last), @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        
                                    </details>
                                @else
                                    <div class="small text-muted">No contents recorded</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $backup->isAuto() ? 'Auto' : 'Manual' }}</span>
                            </td>
                            <td>{{ $backup->size ?: '—' }}</td>
                            <td>
                                {{ $backup->created_at->diffForHumans() }}
                                <div class="small text-muted">
                                    {{ $backup->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                                </div>
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
    const rowChecks = () => document.querySelectorAll('.row-check:not(:disabled)');
    const deleteButton = document.getElementById('delete-selected');
    const refreshDeleteButton = () => {
        deleteButton.disabled = ![...rowChecks()].some((box) => box.checked);
    };

    document.getElementById('select-all')?.addEventListener('change', function () {
        rowChecks().forEach((box) => { box.checked = this.checked; });
        refreshDeleteButton();
    });
    rowChecks().forEach((box) => box.addEventListener('change', refreshDeleteButton));
    window.addEventListener('pageshow', refreshDeleteButton);
    refreshDeleteButton();
</script>
@endsection
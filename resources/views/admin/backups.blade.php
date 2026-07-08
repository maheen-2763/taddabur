<x-app-layout>
    <div class="container">
        <h2>Database Backups</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($backups as $backup)
                    <tr>
                        <td>{{ $backup->filename }}</td>
                        <td>{{ $backup->size }}</td>
                        <td>{{ $backup->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.backups.download', $backup) }}"
                                class="btn btn-sm btn-primary">Download</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>

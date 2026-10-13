@extends('admin.layout')
@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">All Users <small
                class="text-muted">({{ $users->total() }})</small></h5>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..."
                value="{{ request('search') }}" style="width:220px">
            <select name="status" class="form-select form-select-sm" style="width:140px">
                <option value="">All users</option>
                <option value="verified" @selected(request('status') === 'verified')>Verified</option>
                <option value="unverified" @selected(request('status') === 'unverified')>Unverified</option>
            </select>
            <select name="sort" class="form-select form-select-sm" style="width:140px">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
                <option value="active" @selected(request('sort') === 'active')>Most active</option>
            </select>
            <button class="btn btn-admin-primary btn-sm">Filter</button>
        </form>
    </div>

    <div class="admin-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email verified</th>
                    <th>Ayahs read</th>
                    <th>Bookmarks</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div style="font-size:.9rem; font-weight:500">
                                {{ $user->name }}
                                @if ($user->is_admin)
                                    <span class="badge badge-admin ms-1" style="font-size:.6rem">ADMIN</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        <td style="font-size:.82rem">
                            @if ($user->email_verified_at)
                                <span class="text-success"><i
                                        class="bi bi-check-circle me-1"></i>{{ $user->email_verified_at->format('d M Y') }}</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Not verified</span>
                            @endif
                        </td>
                        <td>{{ number_format($user->ayahs_read) }}</td>
                        <td>{{ number_format($user->bookmarks_count) }}</td>
                        <td style="font-size:.82rem; color:#666">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-admin-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">{{ $users->links() }}</div>
    </div>
@endsection

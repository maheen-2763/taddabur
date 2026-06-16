@extends('admin.layout')
@section('title', 'Users')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">All Users</h5>
        <form class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..."
                value="{{ request('search') }}" style="width:220px">
            <select name="plan" class="form-select form-select-sm" style="width:120px">
                <option value="">All plans</option>
                <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
            </select>
            <button class="btn btn-admin-primary btn-sm">Filter</button>
        </form>
    </div>

    <div class="admin-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Expires</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="font-size:0.9rem; font-weight:500">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->plan }}">
                                {{ strtoupper($user->plan) }}
                            </span>
                        </td>
                        <td style="font-size:0.82rem; color:#666">
                            {{ $user->plan_expires_at
                                ? $user->plan_expires_at->format('d M Y')
                                : ($user->plan === 'free'
                                    ? '—'
                                    : 'Lifetime') }}
                        </td>
                        <td style="font-size:0.82rem; color:#666">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-admin-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-3">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection

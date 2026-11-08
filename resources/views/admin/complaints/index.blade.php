@extends('admin.layout')
@section('title', 'Complaints')

@php
    $tabs = ['open' => 'Open', 'in_progress' => 'In progress', 'resolved' => 'Resolved', 'all' => 'All'];
@endphp

@section('content')
    <div class="mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">
            <i class="bi bi-exclamation-triangle me-2"></i>Complaints
        </h5>
        <small class="text-muted">Users ki complaints dekho, jawab do aur close karo</small>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    <ul class="nav nav-pills mb-3 gap-1">
        @foreach ($tabs as $key => $label)
            <li class="nav-item">
                <a class="nav-link py-1 {{ $status === $key ? 'active' : '' }}"
                    href="{{ route('admin.complaints.index', ['status' => $key]) }}">
                    {{ $label }}
                    <span
                        class="badge bg-light text-dark ms-1">{{ $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0) }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="admin-table">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($complaints as $complaint)
                    <tr>
                        <td class="text-nowrap small">
                            {{ $complaint->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                        <td>{{ $complaint->user?->name ?? '—' }}</td>
                        <td>{{ $complaint->subject }}</td>
                        <td class="small">{{ $complaint->categoryLabel() }}</td>
                        <td><span class="badge {{ $complaint->statusBadge() }}">{{ $complaint->statusLabel() }}</span></td>
                        <td>
                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                                class="btn btn-sm btn-admin-primary">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Is tab me koi complaint nahi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $complaints->links() }}</div>
    </div>
@endsection
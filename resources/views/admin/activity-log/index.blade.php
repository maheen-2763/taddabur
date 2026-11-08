@extends('admin.layout')
@section('title', 'Activity Log')

@section('content')
<div class="mb-4">
    <h5 class="mb-0" style="font-family:var(--font-heading)">Activity Log</h5>
    <small class="text-muted">Who did what · email, IP or password is never stored · time in IST</small>
</div>

@if ($errors->any())
    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
@endif

<form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-md-4">
        <label for="event" class="form-label small text-muted mb-1">Event</label>
        <select name="event" id="event" class="form-select form-select-sm">
            <option value="">All events</option>
            @foreach ($events as $e)
                <option value="{{ $e->value }}" @selected(request('event') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label for="from" class="form-label small text-muted mb-1">From</label>
        <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control form-control-sm">
    </div>
    <div class="col-6 col-md-2">
        <label for="to" class="form-label small text-muted mb-1">To</label>
        <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-admin-primary">Filter</button>
        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="admin-table">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Time</th>
                <th>Event</th>
                <th>By</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
            @php($event = $log->eventEnum())
            <tr>
                <td class="text-nowrap" title="{{ $log->created_at->diffForHumans() }}">
                    {{ $log->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') }}
                </td>
                <td><span
                        class="badge {{ $event?->badge() ?? 'bg-secondary' }}">{{ $event?->label() ?? $log->event }}</span>
                </td>
                <td>
                    @if ($log->actor)
                        <a href="{{ route('admin.users.show', $log->actor_id) }}">{{ $log->actor->name }}</a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @forelse (($log->properties ?? []) as $key => $value)
                        <span class="badge bg-light text-dark border">{{ $key }}: {{ $value }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">No Activity found yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-3">{{ $logs->links() }}</div>
</div>
@endsection
{{-- resources/views/admin/activity-log/index.blade.php --}}
@extends('layouts.admin')
@section('content')
    <h1>Activity Log</h1>
    <form method="GET">
        <select name="event">
            <option value="">All events</option>
            @foreach ($events as $e)
                <option value="{{ $e->value }}" @selected(request('event') === $e->value)>{{ $e->value }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}">
        <input type="date" name="to" value="{{ request('to') }}">
        <button type="submit">Filter</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Event</th>
                <th>Actor ID</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $log->event }}</td>
                    <td>{{ $log->actor_id ?? '—' }}</td>
                    <td>{{ $log->properties ? json_encode($log->properties) : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Koi activity nahi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $logs->links() }}
@endsection
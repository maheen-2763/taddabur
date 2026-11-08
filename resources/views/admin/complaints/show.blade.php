@extends('admin.layout')
@section('title', 'Complaint')

@section('content')
    <a href="{{ route('admin.complaints.index') }}" class="small text-muted">
        <i class="bi bi-arrow-left me-1"></i>Complaints
    </a>

    <div class="admin-table p-4 mt-2">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <h5 class="mb-1">{{ $complaint->subject }}</h5>
            <span class="badge {{ $complaint->statusBadge() }}">{{ $complaint->statusLabel() }}</span>
        </div>
        <div class="small text-muted mb-3">
            @if ($complaint->user)
                <a href="{{ route('admin.users.show', $complaint->user_id) }}">{{ $complaint->user->name }}</a>
            @else
                User
            @endif
            · {{ $complaint->categoryLabel() }}
            · {{ $complaint->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
        </div>

        <p style="white-space:pre-line">{{ $complaint->message }}</p>
        <hr>

        <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3" style="max-width:240px">
                <label for="status" class="form-label small text-muted mb-1">Status</label>
                <select name="status" id="status" class="form-select form-select-sm @error('status') is-invalid @enderror"
                    required>
                    @foreach (['open' => 'Open', 'in_progress' => 'In progress', 'resolved' => 'Resolved'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $complaint->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="admin_reply" class="form-label small text-muted mb-1">
                    Jawab (user ko apne Feedback page par dikhega)
                </label>
                <textarea name="admin_reply" id="admin_reply" rows="5" maxlength="2000"
                    class="form-control @error('admin_reply') is-invalid @enderror">{{ old('admin_reply', $complaint->admin_reply) }}</textarea>
                @error('admin_reply')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if ($complaint->replied_at)
                    <div class="form-text">Pichhla jawab:
                        {{ $complaint->replied_at->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</div>
                @endif
            </div>

            <button type="submit" class="btn btn-admin-gold">Save</button>
        </form>
    </div>
@endsection
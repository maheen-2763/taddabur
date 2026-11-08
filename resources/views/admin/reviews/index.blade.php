@extends('admin.layout')
@section('title', 'Reviews')

@section('content')
    <div class="mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">
            <i class="bi bi-star-fill text-warning me-2"></i>Reviews
        </h5>
        <small class="text-muted">App ke baare me users ke ratings aur comments</small>
    </div>

    <div class="d-flex gap-3 mb-3 small text-muted">
        <span><strong>{{ $stats['count'] }}</strong> visible</span>
        <span><strong>{{ $stats['count'] ? $stats['average'] : '—' }}</strong> / 5 average</span>
        <span><strong>{{ $stats['hidden'] }}</strong> hidden</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
    @endif

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-6 col-md-3">
            <label for="rating" class="form-label small text-muted mb-1">Rating</label>
            <select name="rating" id="rating" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach (range(5, 1) as $n)
                    <option value="{{ $n }}" @selected((int) request('rating') === $n)>{{ $n }} ★</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="visibility" class="form-label small text-muted mb-1">Show</label>
            <select name="visibility" id="visibility" class="form-select form-select-sm">
                <option value="visible" @selected($visibility === 'visible')>Visible</option>
                <option value="hidden" @selected($visibility === 'hidden')>Hidden</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-admin-primary">Filter</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="admin-table">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td class="text-nowrap small">
                            {{ $review->created_at->copy()->timezone('Asia/Kolkata')->format('d M Y') }}</td>
                        <td>
                            @if ($review->user)
                                <a href="{{ route('admin.users.show', $review->user_id) }}">{{ $review->user->name }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-nowrap text-warning">{{ $review->stars() }}</td>
                        <td style="white-space:pre-line">{{ $review->comment ?: '—' }}</td>
                        <td>
                            @if ($review->trashed())
                                <form method="POST" action="{{ route('admin.reviews.restore', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success">Restore</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.hide', $review) }}"
                                    onsubmit="return confirm('Ye review users ke liye hide ho jayega. Pakka?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hide</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Koi review nahi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $reviews->links() }}</div>
    </div>
@endsection
@extends('admin.layout')
@section('title', 'User Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i></a>
                <h5 class="mb-0" style="font-family:var(--font-heading)">User Details</h5>
            </div>

            <div class="admin-table p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div
                        style="width:56px; height:56px; background:var(--emerald); border-radius:50%; display:flex;
                        align-items:center; justify-content:center; font-size:1.4rem; color:#fff; font-family:var(--font-heading)">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $user->name }}</h5>
                        <p class="text-muted mb-0" style="font-size:.85rem">{{ $user->email }}</p>
                    </div>
                    @if ($user->is_admin)
                        <span class="badge badge-admin ms-auto px-3 py-2">ADMIN</span>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:.75rem">JOINED</label>
                        <p class="mb-0">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:.75rem">EMAIL VERIFIED</label>
                        <p class="mb-0">
                            @if ($user->email_verified_at)
                                <span class="text-success"><i
                                        class="bi bi-check-circle me-1"></i>{{ $user->email_verified_at->format('d M Y') }}</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Not verified</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:.75rem">PREFERRED LANGUAGE</label>
                        <p class="mb-0">{{ strtoupper($user->preferred_language ?? 'EN') }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:.75rem">DELETE USER</label>
                        @unless ($user->is(auth()->user()))
    <div class="border border-danger-subtle rounded-3 p-3 mt-4">
        <h6 class="text-danger mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Danger zone</h6>
        <p class="small text-muted mb-3">
            All User data (bookmarks, notes, reviews, complaints, progress) will get deleted permanently. This can't be retreived back.
        </p>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              class="d-flex gap-2 align-items-start flex-wrap"
              onsubmit="return confirm('User will be deleted permanently. Are you sure?')">
            @csrf
            @method('DELETE')
            <div>
                <input type="password" name="password" required autocomplete="current-password"
                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                       placeholder="Your admin password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="bi bi-trash me-1"></i>Delete user
            </button>
        </form>
        @error('user') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
    </div>
@endunless
                    </div>
                </div>
                
            </div>

            

            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Activity</h6>
                </div>
                <div class="row g-0 text-center">
                    @foreach ($stats as $label => $value)
                        <div class="col-4 p-3 border-bottom">
                            <div style="font-family:var(--font-heading); font-size:1.4rem; color:var(--emerald)">
                                {{ number_format($value) }}</div>
                            <div class="text-muted" style="font-size:.72rem">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection

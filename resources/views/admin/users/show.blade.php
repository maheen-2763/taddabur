@extends('admin.layout')
@section('title', 'User Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="mb-0" style="font-family:var(--font-heading)">User Details</h5>
            </div>

            {{-- User Info --}}
            <div class="admin-table p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div
                        style="width:56px; height:56px; background:var(--emerald); border-radius:50%;
                            display:flex; align-items:center; justify-content:center;
                            font-size:1.4rem; color:white; font-family:var(--font-heading)">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $user->name }}</h5>
                        <p class="text-muted mb-0" style="font-size:0.85rem">{{ $user->email }}</p>
                    </div>
                    <div class="ms-auto">
                        <span class="badge badge-{{ $user->plan }} px-3 py-2">
                            {{ strtoupper($user->plan) }}
                        </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:0.75rem">JOINED</label>
                        <p class="mb-0">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:0.75rem">PLAN EXPIRES</label>
                        <p class="mb-0">
                            {{ $user->plan_expires_at
                                ? $user->plan_expires_at->format('d M Y')
                                : ($user->plan === 'free'
                                    ? 'N/A'
                                    : 'Lifetime') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:0.75rem">EMAIL VERIFIED</label>
                        <p class="mb-0">
                            @if ($user->email_verified_at)
                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ $user->email_verified_at->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-danger">
                                    <i class="bi bi-x-circle me-1"></i>Not verified
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted" style="font-size:0.75rem">PREFERRED LANGUAGE</label>
                        <p class="mb-0">{{ strtoupper($user->preferred_language ?? 'EN') }}</p>
                    </div>
                </div>
            </div>

            {{-- Change Plan --}}
            <div class="admin-table p-4 mb-4">
                <h6 class="heading-font mb-3">Change Plan</h6>

                <form action="{{ route('admin.users.update-plan', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="d-flex gap-3 align-items-end">
                        <div class="flex-grow-1">
                            <label class="form-label fw-medium">New Plan</label>
                            <select name="plan" class="form-select">
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->slug }}"
                                        {{ $user->plan === $plan->slug ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                        @if ($plan->price_monthly > 0)
                                            (${{ $plan->price_monthly }}/mo)
                                        @else
                                            (Free)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-admin-primary"
                            onclick="return confirm('Change this user\'s plan?')">
                            Update Plan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Subscription History --}}
            @if ($user->subscription)
                <div class="admin-table">
                    <div class="p-3 border-bottom">
                        <h6 class="mb-0" style="font-family:var(--font-heading)">
                            Subscription History
                        </h6>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Type</th>
                                <th>Payment ID</th>
                                <th>Status</th>
                                <th>Expires</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $user->subscription->plan?->slug }}">
                                        {{ strtoupper($user->subscription->plan?->slug ?? '—') }}
                                    </span>
                                </td>
                                <td style="font-size:0.85rem">
                                    {{ ucfirst($user->subscription->type) }}
                                </td>
                                <td style="font-size:0.75rem; color:#999; font-family:monospace">
                                    {{ $user->subscription->stripe_id ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $user->subscription->stripe_status ?? 'active' }}
                                    </span>
                                </td>
                                <td style="font-size:0.82rem">
                                    {{ $user->subscription->ends_at ? $user->subscription->ends_at->format('d M Y') : 'Lifetime' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
@endsection

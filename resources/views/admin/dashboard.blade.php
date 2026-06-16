@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

    {{-- ── STAT CARDS ──────────────────────────────── --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.75rem">TOTAL USERS</p>
                        <h3 style="font-family:var(--font-heading); color:var(--emerald)">
                            {{ number_format($totalUsers) }}
                        </h3>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-people" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <small class="text-muted">
                    {{ $freeUsers }} free · {{ $basicUsers }} basic · {{ $premiumUsers }} premium
                </small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.75rem">EST. REVENUE</p>
                        <h3 style="font-family:var(--font-heading); color:var(--gold)">
                            ${{ number_format($monthlyRevenue, 2) }}
                        </h3>
                    </div>
                    <div class="stat-icon" style="background:rgba(201,150,58,0.1)">
                        <i class="bi bi-currency-dollar" style="color:var(--gold)"></i>
                    </div>
                </div>
                <small class="text-muted">per month estimate</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.75rem">STORIES</p>
                        <h3 style="font-family:var(--font-heading); color:var(--emerald)">
                            {{ $totalStories }}
                        </h3>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-journal-text" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <small class="text-muted">prophet & companion stories</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.75rem">QURAN AYAHS</p>
                        <h3 style="font-family:var(--font-heading); color:var(--gold)">
                            {{ number_format($totalAyahs) }}
                        </h3>
                    </div>
                    <div class="stat-icon" style="background:rgba(201,150,58,0.1)">
                        <i class="bi bi-book" style="color:var(--gold)"></i>
                    </div>
                </div>
                <small class="text-muted">imported from API</small>
            </div>
        </div>
    </div>

    {{-- ── ALERT: EXPIRING SOON ─────────────────────── --}}
    @if ($expiringSoon > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>
                <strong>{{ $expiringSoon }}</strong> subscription(s) expiring in the next 7 days.
            </span>
        </div>
    @endif

    <div class="row g-4">

        {{-- ── RECENT USERS ─────────────────────────── --}}
        <div class="col-lg-6">
            <div class="admin-table">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-family:var(--font-heading)">Recent Users</h6>
                    <a href="{{ route('admin.users.index') }}" style="font-size:0.8rem; color:var(--emerald)">View all</a>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Plan</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentUsers as $user)
                            <tr>
                                <td>
                                    <div style="font-size:0.9rem">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $user->plan }}">
                                        {{ strtoupper($user->plan) }}
                                    </span>
                                </td>
                                <td style="font-size:0.8rem; color:#666">
                                    {{ $user->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── RECENT SUBSCRIPTIONS ─────────────────── --}}
        <div class="col-lg-6">
            <div class="admin-table">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0" style="font-family:var(--font-heading)">Recent Payments</h6>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubs as $sub)
                            <tr>
                                <td style="font-size:0.85rem">{{ $sub->user->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $sub->plan?->slug }}">
                                        {{ strtoupper($sub->plan?->slug ?? '—') }}
                                    </span>
                                </td>
                                <td style="font-size:0.8rem">{{ ucfirst($sub->type) }}</td>
                                <td style="font-size:0.8rem; color:#666">
                                    {{ $sub->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    No payments yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

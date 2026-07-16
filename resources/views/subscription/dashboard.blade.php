@extends('layouts.app')

@section('title', 'Manage Subscription — Taddabur')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <h3 class="fw-bold mb-4">Manage Subscription</h3>

                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Current Plan Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <p class="text-muted small mb-1 text-uppercase" style="letter-spacing: 0.05em;">
                                    Current Plan
                                </p>
                                <h4 class="fw-bold mb-2">
                                    {{ ucfirst(auth()->user()->plan ?? 'free') }}
                                </h4>

                                @if (auth()->user()->plan_expires_at)
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        @if (auth()->user()->activeSubscription?->status === 'cancelled')
                                            Access ends {{ auth()->user()->plan_expires_at->format('d M Y') }}
                                        @else
                                            Renews on {{ auth()->user()->plan_expires_at->format('d M Y') }}
                                        @endif
                                    </p>
                                @elseif (auth()->user()->isPremium())
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-infinity me-1"></i>
                                        Lifetime access — no expiry
                                    </p>
                                @else
                                    <p class="text-muted small mb-0">Free plan — no expiry</p>
                                @endif
                            </div>

                            @if (auth()->user()->isPremium())
                                <span class="badge text-bg-success px-3 py-2 fs-6">
                                    <i class="bi bi-star-fill me-1"></i> Active
                                </span>
                            @else
                                <span class="badge text-bg-secondary px-3 py-2 fs-6">Free</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                @if (auth()->user()->isPremium())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            @if (auth()->user()->activeSubscription?->status === 'cancelled')
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span class="small">
                                        Your subscription is set to end on
                                        {{ auth()->user()->plan_expires_at?->format('d M Y') }}.
                                        You won't be charged again.
                                    </span>
                                </div>
                            @else
                                <h6 class="fw-bold mb-1 text-danger">Cancel Subscription</h6>
                                <p class="text-muted small mb-3">
                                    You'll keep {{ ucfirst(auth()->user()->plan) }} access until your current billing period
                                    ends —
                                    no further charges after that.
                                </p>
                                <form action="{{ route('subscription.cancel') }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel? You will keep access until your period ends.')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Cancel Subscription
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                @if (auth()->user()->plan !== 'premium')
                    @php
                        $premiumPlan = \App\Models\Plan::where('slug', 'premium')->first();
                    @endphp
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-stars fs-2 text-success mb-2 d-block"></i>
                            <h5 class="fw-bold mb-2">Unlock Premium Features</h5>
                            <p class="text-muted mb-3">
                                Get all 25 prophet stories, Hadith collections, personal notes on every ayah,
                                and offline downloads
                                @if ($premiumPlan)
                                    for just ${{ number_format($premiumPlan->price_monthly, 2) }}/month.
                                @else
                                    with Premium.
                                @endif
                            </p>
                            <a href="{{ route('subscription.upgrade') }}" class="btn btn-success">
                                Upgrade to Premium
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

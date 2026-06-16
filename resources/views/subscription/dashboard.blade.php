@extends('layouts.app')

@section('title', 'Manage Subscription — Taddabur')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <h3 class="fw-bold mb-4">Manage Subscription</h3>

                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Current Plan Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    Current Plan:
                                    <span class="text-success">
                                        {{ ucfirst(auth()->user()->plan ?? 'free') }}
                                    </span>
                                </h5>
                                @if (auth()->user()->plan_expires_at)
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Renews on {{ auth()->user()->plan_expires_at->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-muted small mb-0">Free plan — no expiry</p>
                                @endif
                            </div>
                            @if (auth()->user()->isPremium())
                                <span class="badge text-bg-success px-3 py-2">Active</span>
                            @else
                                <span class="badge text-bg-secondary px-3 py-2">Free</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                @if (auth()->user()->isPremium())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-1 text-danger">Cancel Subscription</h6>
                            <p class="text-muted small mb-3">
                                You'll lose access to Premium features at the end of your billing period.
                            </p>
                            <form action="{{ route('subscription.cancel') }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to cancel?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Cancel Subscription
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold mb-2">Unlock Premium Features</h5>
                            <p class="text-muted mb-3">
                                Get tafsir, audio, notes, and all prophet stories for just $9.99/month.
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

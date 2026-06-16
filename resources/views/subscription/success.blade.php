@extends('layouts.app')
@section('title', 'Payment Successful!')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">

                <div class="card-islamic p-5">
                    {{-- Success icon --}}
                    <div style="font-size:4rem; margin-bottom:1rem">🎉</div>

                    <h2 class="heading-font mb-2">
                        Alhamdulillah!
                    </h2>
                    <p class="text-muted mb-3">
                        Your payment was successful. You now have access to the
                        <strong>{{ $plan->name }}</strong> plan.
                    </p>

                    {{-- Plan badge --}}
                    <div class="mb-4">
                        <span class="badge badge-{{ $user->plan }} px-4 py-2 fs-6">
                            {{ strtoupper($user->plan) }} PLAN
                        </span>
                        @if ($user->plan_expires_at)
                            <p class="text-muted mt-2" style="font-size:0.85rem">
                                Valid until {{ $user->plan_expires_at->format('d M Y') }}
                            </p>
                        @else
                            <p class="text-muted mt-2" style="font-size:0.85rem">
                                Lifetime access — never expires
                            </p>
                        @endif
                    </div>

                    {{-- Features unlocked --}}
                    <div class="text-start mb-4 p-3" style="background:rgba(27,94,59,0.08); border-radius:var(--radius)">
                        <p class="heading-font mb-2" style="font-size:0.9rem; color:var(--emerald)">
                            NOW UNLOCKED:
                        </p>
                        @foreach ($plan->features as $feature)
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-check-circle-fill" style="color:var(--emerald-light)"></i>
                                <span style="font-size:0.9rem">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('quran.index') }}" class="btn-emerald btn">
                            <i class="bi bi-book me-1"></i>Read Quran
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-gold btn">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

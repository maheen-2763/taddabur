@extends('layouts.app')
@section('title', 'Payment Successful!')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <div class="card-islamic p-5 text-center position-relative overflow-hidden">

                    {{-- Success icon --}}
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width:88px; height:88px; background:rgba(27,94,59,0.1);">
                            <i class="bi bi-check-lg" style="font-size:2.5rem; color:var(--emerald);"></i>
                        </div>
                    </div>

                    <h2 class="heading-font mb-2">Alhamdulillah!</h2>
                    <p class="text-muted mb-4">
                        Your payment was successful. You now have access to the
                        <strong>{{ $plan->name }}</strong> plan.
                    </p>

                    {{-- Plan badge --}}
                    <div class="mb-4">
                        <span class="badge px-4 py-2 fs-6" style="background:var(--emerald); color:#fff;">
                            {{ strtoupper($user->plan) }} PLAN
                        </span>

                        @if ($user->plan_expires_at)
                            <p class="text-muted mt-2 mb-0" style="font-size:0.85rem">
                                <i class="bi bi-calendar3 me-1"></i>
                                Valid until {{ $user->plan_expires_at->format('d M Y') }}
                            </p>
                        @else
                            <p class="text-muted mt-2 mb-0" style="font-size:0.85rem">
                                <i class="bi bi-infinity me-1"></i>
                                Lifetime access — never expires
                            </p>
                        @endif
                    </div>

                    {{-- Features unlocked --}}
                    @if ($plan && count($plan->features))
                        <div class="text-start mb-4 p-4"
                            style="background:rgba(27,94,59,0.06); border-radius:var(--radius); border:1px solid rgba(27,94,59,0.12);">
                            <p class="heading-font mb-3"
                                style="font-size:0.85rem; letter-spacing:0.05em; color:var(--emerald);">
                                <i class="bi bi-unlock me-1"></i> NOW UNLOCKED
                            </p>
                            <div class="row row-cols-1 row-cols-sm-2 g-2">
                                @foreach ($plan->features as $feature)
                                    <div class="col d-flex align-items-start gap-2">
                                        <i class="bi bi-check-circle-fill mt-1"
                                            style="color:var(--emerald-light); flex-shrink:0;"></i>
                                        <span style="font-size:0.9rem">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="{{ route('quran.index') }}" class="btn-emerald btn px-4">
                            <i class="bi bi-book me-1"></i> Read Quran
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-gold btn px-4">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </div>

                    <p class="text-muted mt-4 mb-0" style="font-size:0.8rem">
                        <i class="bi bi-receipt me-1"></i>
                        A receipt has been sent to your registered email.
                    </p>

                </div>

            </div>
        </div>
    </div>
@endsection

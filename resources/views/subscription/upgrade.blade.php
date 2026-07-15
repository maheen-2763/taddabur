@extends('layouts.app')
@section('title', 'Upgrade Your Plan')

@push('styles')
    <style>
        .plan-card {
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--cream-dark);
            position: relative;
        }

        .plan-card:hover:not(.unavailable),
        .plan-card.selected {
            border-color: var(--gold);
            background: rgba(201, 150, 58, 0.05);
        }

        .plan-card.selected .plan-radio {
            background: var(--gold);
            border-color: var(--gold);
        }

        .plan-card.unavailable {
            cursor: not-allowed;
            opacity: 0.45;
            filter: grayscale(0.3);
        }

        .plan-radio {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 50%;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .billing-tab {
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
            background: transparent;
        }

        .billing-tab.active {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald);
        }

        .unavailable-tag {
            display: inline-block;
            font-size: 0.75rem;
            background: rgba(0, 0, 0, 0.08);
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            color: var(--text-muted, #777);
        }

        .payment-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .payment-overlay.show {
            display: flex;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-4">
                    <h2 class="heading-font mb-1">Upgrade Your Plan</h2>
                    <p class="text-muted mb-0">
                        Currently on:
                        <span class="badge px-3 py-2" style="background:var(--emerald); color:#fff;">
                            {{ strtoupper($currentPlan) }}
                        </span>
                    </p>
                </div>

                {{-- Billing Toggle --}}
                <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
                    <button class="billing-tab active" data-billing="monthly" onclick="setBilling('monthly', this)">
                        Monthly
                    </button>
                    <button class="billing-tab" data-billing="yearly" onclick="setBilling('yearly', this)">
                        Yearly
                        <span class="badge ms-1" style="background:var(--gold); color:#1A1A2E; font-size:0.65rem">
                            Save 20%
                        </span>
                    </button>
                    <button class="billing-tab" data-billing="lifetime" onclick="setBilling('lifetime', this)">
                        Lifetime
                    </button>
                </div>

                {{-- Plan Cards --}}
                <div class="row g-3 mb-4">
                    @foreach ($plans as $plan)
                        <div class="col-md-6">
                            <div class="plan-card {{ $loop->first ? 'selected' : '' }}"
                                onclick="selectPlan('{{ $plan->slug }}', this)" data-plan="{{ $plan->slug }}"
                                data-monthly="{{ $plan->price_monthly > 0 ? 1 : 0 }}"
                                data-yearly="{{ $plan->price_yearly > 0 ? 1 : 0 }}"
                                data-lifetime="{{ $plan->price_lifetime > 0 ? 1 : 0 }}">

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="plan-radio {{ $loop->first ? '' : '' }}"
                                        style="{{ $loop->first ? 'background:var(--gold);border-color:var(--gold);' : '' }}">
                                    </div>
                                    <div>
                                        <h5 class="heading-font mb-0">{{ $plan->name }}</h5>
                                        <small class="text-muted">{{ $plan->description }}</small>
                                    </div>
                                </div>

                                {{-- Prices --}}
                                <div class="price-display">
                                    <div class="price-monthly">
                                        <span style="font-size:1.8rem; font-family:var(--font-heading); color:var(--gold)">
                                            ${{ number_format($plan->price_monthly, 2) }}
                                        </span>
                                        <small class="text-muted">/month</small>
                                    </div>
                                    <div class="price-yearly" style="display:none">
                                        <span style="font-size:1.8rem; font-family:var(--font-heading); color:var(--gold)">
                                            ${{ number_format($plan->price_yearly, 2) }}
                                        </span>
                                        <small class="text-muted">/year</small>
                                    </div>
                                    <div class="price-lifetime" style="display:none">
                                        @if ($plan->price_lifetime > 0)
                                            <span
                                                style="font-size:1.8rem; font-family:var(--font-heading); color:var(--gold)">
                                                ${{ number_format($plan->price_lifetime, 2) }}
                                            </span>
                                            <small class="text-muted">one time</small>
                                        @else
                                            <span class="unavailable-tag">
                                                <i class="bi bi-x-circle me-1"></i>Not available on Lifetime
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Features --}}
                                <ul class="list-unstyled mt-3 mb-0">
                                    @foreach ($plan->features as $feature)
                                        <li class="mb-1" style="font-size:0.85rem">
                                            <i class="bi bi-check-circle-fill me-2" style="color:var(--emerald-light)"></i>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pay Button --}}
                <div class="text-center">
                    <button id="payBtn" onclick="initiatePayment()" class="btn-gold btn btn-lg px-5">
                        <i class="bi bi-lock-fill me-2"></i>
                        Pay Securely with Razorpay
                    </button>
                    <p class="text-muted mt-2 mb-0" style="font-size:0.8rem">
                        <i class="bi bi-shield-check me-1"></i>
                        Secured by Razorpay · UPI, Cards, NetBanking accepted
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Loading overlay --}}
    <div class="payment-overlay" id="loadingOverlay">
        <div class="text-center text-white">
            <div class="spinner-border text-warning mb-3" role="status"></div>
            <p>Processing your payment...</p>
        </div>
    </div>

    @push('scripts')
        {{-- Razorpay JS SDK --}}
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const RAZORPAY_KEY = "{{ $razorpayKey }}";

            let selectedPlan = "{{ $plans->first()?->slug }}";
            let selectedBilling = 'monthly';

            // ── SELECT PLAN ───────────────────────────────────────
            function selectPlan(slug, card) {
                if (card.classList.contains('unavailable')) return; // can't select a plan not sold on this billing cycle

                document.querySelectorAll('.plan-card').forEach(c => {
                    c.classList.remove('selected');
                    c.querySelector('.plan-radio').style.background = '';
                    c.querySelector('.plan-radio').style.borderColor = '';
                });

                card.classList.add('selected');
                card.querySelector('.plan-radio').style.background = 'var(--gold)';
                card.querySelector('.plan-radio').style.borderColor = 'var(--gold)';

                selectedPlan = slug;
            }

            // ── SET BILLING CYCLE ─────────────────────────────────
            function setBilling(billing, btn) {
                document.querySelectorAll('.billing-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedBilling = billing;

                // Show/hide correct prices
                document.querySelectorAll('.price-monthly, .price-yearly, .price-lifetime').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.price-' + billing).forEach(el => {
                    el.style.display = 'block';
                });

                // Mark cards unavailable for this billing cycle, and auto-switch
                // selection away from any card that just became unavailable —
                // this is what stops a Lifetime + "no lifetime price" combo
                // from ever reaching the Pay button.
                let selectedCardStillValid = false;
                let firstAvailableCard = null;

                document.querySelectorAll('.plan-card').forEach(card => {
                    const available = card.dataset[billing] === '1';
                    card.classList.toggle('unavailable', !available);

                    if (available && !firstAvailableCard) {
                        firstAvailableCard = card;
                    }
                    if (available && card.dataset.plan === selectedPlan) {
                        selectedCardStillValid = true;
                    }
                });

                if (!selectedCardStillValid && firstAvailableCard) {
                    selectPlan(firstAvailableCard.dataset.plan, firstAvailableCard);
                }
            }

            // ── INITIATE PAYMENT ──────────────────────────────────
            async function initiatePayment() {
                const btn = document.getElementById('payBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating order...';

                try {
                    const orderRes = await fetch("{{ route('subscription.create-order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({
                            plan_slug: selectedPlan,
                            billing: selectedBilling,
                        }),
                    });

                    const order = await orderRes.json();

                    if (order.error) {
                        alert(order.error);
                        resetBtn();
                        return;
                    }

                    const options = {
                        key: RAZORPAY_KEY,
                        amount: order.amount,
                        currency: order.currency,
                        order_id: order.order_id,
                        name: 'Taddabur',
                        description: selectedPlan + ' plan — ' + selectedBilling,
                        image: '/images/logo.png',

                        prefill: {
                            name: "{{ auth()->user()->name }}",
                            email: "{{ auth()->user()->email }}",
                        },

                        theme: {
                            color: '#C9963A',
                        },

                        handler: async function(response) {
                            document.getElementById('loadingOverlay').classList.add('show');

                            const verifyRes = await fetch("{{ route('subscription.verify') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                },
                                body: JSON.stringify({
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature,
                                }),
                            });

                            const result = await verifyRes.json();

                            if (result.status === 'success') {
                                window.location.href = result.redirect_url;
                            } else {
                                alert('Payment verification failed: ' + result.error);
                                document.getElementById('loadingOverlay').classList.remove('show');
                            }
                        },

                        modal: {
                            ondismiss: function() {
                                resetBtn();
                            }
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();

                } catch (err) {
                    console.error(err);
                    alert('Something went wrong. Please try again.');
                    resetBtn();
                }
            }

            function resetBtn() {
                const btn = document.getElementById('payBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Pay Securely with Razorpay';
            }
        </script>
    @endpush

@endsection

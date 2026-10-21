@extends('layouts.app')

@section('title', 'Support Taddabur')

@push('styles')
    <style>
        .support-hero {
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0a2a18 100%);
            padding: 4rem 0 3.5rem;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .book-wrap {
            max-width: 260px;
            margin: 0 auto 1rem;
        }

        .support-message {
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            line-height: 1.8;
        }

        .support-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(201, 150, 58, 0.25);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
        }

        .qr-box {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .qr-box img {
            width: 100%;
            max-width: 260px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .support-covers {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .support-covers-label {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.78rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .support-covers-list {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            line-height: 1.8;
        }

        .supporters-strip {
            padding: 3.5rem 0;
            background: var(--cream-dark);
            text-align: center;
        }

        .supporters-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
        }

        .supporter-chip {
            display: inline-flex;
            align-items: center;
            background: var(--cream);
            border: 1px solid rgba(201, 150, 58, 0.3);
            border-radius: 50px;
            padding: 0.45rem 1.1rem;
            font-size: 0.85rem;
            color: var(--ink);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }

        .supporter-chip:hover {
            border-color: var(--gold);
            transform: translateY(-2px);
        }

        [data-bs-theme="dark"] .supporter-chip {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(201, 150, 58, 0.35);
        }

        @keyframes riseFade {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.55;
            }

            50% {
                transform: translateY(-14px) scale(1.08);
                opacity: 0.9;
            }

            100% {
                transform: translateY(-28px) scale(1.15);
                opacity: 0;
            }
        }

        @keyframes softPulse {

            0%,
            100% {
                opacity: 0.35;
            }

            50% {
                opacity: 0.6;
            }
        }

        .ray {
            animation: riseFade 3.2s ease-in-out infinite;
            transform-origin: center;
        }

        .ray:nth-child(2) {
            animation-delay: 0.6s;
        }

        .ray:nth-child(3) {
            animation-delay: 1.2s;
        }

        .ray:nth-child(4) {
            animation-delay: 1.8s;
        }

        .book-glow {
            animation: softPulse 3.2s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {

            .ray,
            .book-glow {
                animation: none;
                opacity: 0.5;
            }
        }

        .support-highlight {
            color: #fff;
            font-weight: 500;
            font-size: 1.2rem;
            max-width: 560px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .payment-toggle {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            padding: 0.3rem;
        }

        .payment-toggle-btn {
            flex: 1;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding: 0.55rem 0.5rem;
            border-radius: 50px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .payment-toggle-btn.active {
            background: var(--gold-light);
            color: #0a2a18;
            font-weight: 600;
        }

        .support-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(201, 150, 58, 0.3);
            color: #fff;
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            font-size: 0.9rem;
        }

        .support-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .support-input:focus {
            outline: none;
            border-color: var(--gold-light);
            background: rgba(255, 255, 255, 0.12);
        }

        .support-legal-links a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: underline;
        }

        .donate-status {
            font-size: 0.85rem;
            min-height: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .donate-status-error {
            color: #ff9b9b;
        }

        .donate-status-success {
            color: #8fd9a8;
        }
    </style>
@endpush

@section('content')

    <section class="support-hero">
        <div class="container-fluid position-relative px-3">

            <div class="book-wrap">
                <svg width="100%" viewBox="0 0 680 380" role="img" aria-label="Open book with rising light">
                    <title>Open book with rising light</title>
                    <desc>An open book illustration with soft rays of light rising from its pages</desc>
                    <defs>
                        <radialGradient id="bookGlowGrad" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#C9963A" stop-opacity="0.4" />
                            <stop offset="100%" stop-color="#C9963A" stop-opacity="0" />
                        </radialGradient>
                    </defs>
                    <ellipse class="book-glow" cx="340" cy="240" rx="220" ry="130" fill="url(#bookGlowGrad)" />
                    <g id="rays">
                        <line class="ray" x1="300" y1="220" x2="298" y2="140" stroke="#C9963A" stroke-width="1.5"
                            stroke-linecap="round" opacity="0.6" />
                        <line class="ray" x1="325" y1="205" x2="322" y2="110" stroke="#E8B85C" stroke-width="2"
                            stroke-linecap="round" opacity="0.75" />
                        <line class="ray" x1="355" y1="205" x2="359" y2="110" stroke="#E8B85C" stroke-width="2"
                            stroke-linecap="round" opacity="0.75" />
                        <line class="ray" x1="382" y1="220" x2="386" y2="140" stroke="#C9963A" stroke-width="1.5"
                            stroke-linecap="round" opacity="0.6" />
                    </g>
                    <path d="M340 235 L340 285" stroke="#1B5E3B" stroke-width="2" />
                    <path
                        d="M340 240 C300 220 250 218 205 232 C198 234 194 240 194 248 L194 288 C194 296 198 300 205 298 C250 284 300 286 340 305 Z"
                        fill="none" stroke="#1B5E3B" stroke-width="2.5" stroke-linejoin="round" />
                    <path
                        d="M340 240 C380 220 430 218 475 232 C482 234 486 240 486 248 L486 288 C486 296 482 300 475 298 C430 284 380 286 340 305 Z"
                        fill="none" stroke="#1B5E3B" stroke-width="2.5" stroke-linejoin="round" />
                    <path d="M215 250 C250 240 280 240 315 252" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <path d="M215 264 C250 254 280 254 315 266" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <path d="M215 278 C250 268 280 268 315 280" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <path d="M365 252 C400 240 430 240 465 250" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <path d="M365 266 C400 254 430 254 465 264" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <path d="M365 280 C400 268 430 268 465 278" fill="none" stroke="#C9963A" stroke-width="1"
                        opacity="0.55" />
                    <ellipse cx="340" cy="308" rx="150" ry="10" fill="#1B5E3B" opacity="0.12" />
                </svg>
            </div>

            <h1 class="heading-font mb-3" style="color:#fff; font-size:2rem">Support Taddabur</h1>

            <p class="support-highlight">
                Knowledge, like light, was never meant to stay behind closed doors.
            </p>

            <div class="mx-auto mb-4" style="max-width: 480px;">
                <p class="support-message">
                    This page exists simply so the option is here — for anyone who wishes to
                    contribute toward what keeps it running. Everything else stays exactly as
                    it is, either way.
                </p>
            </div>

            <!-- Payment section -->
            <div class="mx-auto" style="max-width: 420px; text-align:left">

                <div class="payment-toggle" role="tablist">
                    <button type="button" class="payment-toggle-btn active" data-target="upi-panel">
                        <i class="bi bi-qr-code me-1"></i>UPI
                    </button>
                    <button type="button" class="payment-toggle-btn" data-target="card-panel">
                        <i class="bi bi-credit-card me-1"></i>Card / Netbanking
                    </button>
                </div>

                <div id="upi-panel" class="payment-panel">
                    <div class="support-card text-center">
                        @if ($qrDataUri)
                            <div class="qr-box">
                                <img src="{{ $qrDataUri }}" alt="Razorpay UPI QR code">
                            </div>
                            <p class="mt-3 mb-0" style="color:rgba(255,255,255,0.6); font-size:0.85rem">
                                Scan with any UPI app — Google Pay, PhonePe, Paytm, or your bank's app.
                            </p>
                        @else
                            <p class="mb-0" style="color:rgba(255,255,255,0.7); font-size:0.9rem">
                                The UPI QR code is unavailable right now.
                                Please use the Card / Netbanking option instead.
                            </p>
                        @endif
                    </div>
                </div>

                <div id="card-panel" class="payment-panel d-none">
                    <div class="support-card text-center">
                        <p class="mb-3" style="color:rgba(255,255,255,0.7); font-size:0.9rem">
                            Pay via card, netbanking, or wallet
                        </p>

                        <label for="donation-amount" class="visually-hidden">Amount in rupees</label>
                        <input type="number" id="donation-amount" placeholder="Amount (₹)" min="1" max="100000" step="1"
                            inputmode="numeric" class="support-input mb-2">

                        <label for="donor-name" class="visually-hidden">Your name (optional)</label>
                        <input type="text" id="donor-name" placeholder="Your Name (optional)" maxlength="60"
                            class="support-input mb-2">

                        <label for="donor-message" class="visually-hidden">Message (optional)</label>
                        <textarea id="donor-message" placeholder="Message (optional)" maxlength="250"
                            class="support-input mb-2"></textarea>

                        <div class="form-check d-flex justify-content-center align-items-center gap-2 mb-3">
                            <input type="checkbox" id="donor-public" class="form-check-input">
                            <label class="form-check-label" for="donor-public"
                                style="color:rgba(255,255,255,0.6); font-size:0.85rem">
                                Show my name in the supporters list
                            </label>
                        </div>

                        <button id="razorpay-donate-btn" class="btn btn-outline-light w-100">
                            <i class="bi bi-credit-card me-1"></i>Donate Now
                        </button>
                        <p id="donate-status" class="donate-status mt-3 mb-0" role="status" aria-live="polite"></p>
                    </div>
                </div>

                <div class="support-covers text-center">
                    <p class="support-covers-label">What this helps sustain</p>
                    <p class="support-covers-list">
                        Keeping the content accurate · Reviewing new sources · Fixing what needs fixing ·
                        Building what comes next
                    </p>
                </div>
            </div>

            <p class="mt-4 mb-0" style="color:rgba(255,255,255,0.5); font-size:0.85rem">
                JazakAllahu Khair, either way. 🤲
            </p>

            <p class="text-center mt-3 mb-0" style="font-size:0.8rem">
                <a href="{{ route('about') }}" style="color:rgba(255,255,255,0.5); text-decoration:underline">
                    Learn how Taddabur is built and sourced
                </a>
            </p>

            <p class="support-legal-links text-center mt-2 mb-0" style="font-size:0.75rem">
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <span style="color:rgba(255,255,255,0.3)"> · </span>
                <a href="{{ route('terms') }}">Terms</a>
            </p>
        </div>
    </section>

    @php
        $supporters = $donors->filter(fn($donor) => filled(trim((string) $donor->name)))->values();
    @endphp

    @if ($supporters->isNotEmpty())
        <section class="supporters-strip">
            <div class="container-fluid" style="max-width:700px">
                <p class="text-muted small mb-1" style="letter-spacing:0.05em; text-transform:uppercase; font-size:0.75rem">
                    With Gratitude
                </p>
                <h5 class="heading-font mb-3" style="color:var(--gold-dark)">Those Who Helped Keep This Free</h5>
                <div class="supporters-grid">
                    @foreach ($supporters as $donor)
                        <span class="supporter-chip">
                            <i class="bi bi-heart-fill me-1" style="font-size:0.65rem; color:var(--gold)"></i>
                            {{ $donor->name }}
                        </span>
                    @endforeach
                </div>

                <p class="text-muted mt-3 mb-0" style="font-size:0.8rem; direction:rtl" lang="ar">
                    جَزَاكُمُ اللَّهُ خَيْرًا
                </p>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
    <script>
        window.CONFIG = window.CONFIG || {};
        window.CONFIG.createOrderUrl = @json(route('donate.create-order'));
        window.CONFIG.verifyUrl = @json(route('donate.verify'));
        window.CONFIG.csrfToken = @json(csrf_token());
    </script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('js/donation.js') }}"></script>
@endpush
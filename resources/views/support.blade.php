@extends('layouts.app')

@section('title', 'Support Taddabur')

@push('styles')
    <style>
        .support-hero {
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0a2a18 100%);
            padding: 5rem 0 4rem;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .book-wrap {
            max-width: 260px;
            margin: 0 auto 1rem;
        }

        .support-ayah-arabic {
            font-family: var(--quran-font, var(--font-arabic));
            font-size: clamp(1.3rem, 3vw, 1.75rem);
            line-height: 2.6;
            direction: rtl;
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            word-spacing: 0.08em;
            margin-bottom: 0.75rem;
            max-width: 600px;
            margin-inline: auto;
        }

        .support-ayah-translation {
            font-family: var(--font-body);
            font-size: 0.92rem;
            line-height: 1.85;
            font-style: italic;
            color: rgba(255, 255, 255, 0.65);
            max-width: 480px;
            margin: 0 auto;
        }

        .support-ayah-ref {
            color: var(--gold-light);
            font-size: 0.85rem;
            font-style: italic;
            margin-top: 0.4rem;
        }

        .support-read-context {
            display: inline-block;
            margin-top: 0.75rem;
            margin-bottom: 2rem;
            font-size: 0.8rem;
            color: var(--gold-light);
            text-decoration: none;
            border-bottom: 1px dashed rgba(201, 150, 58, 0.5);
        }

        .support-read-context:hover {
            color: #fff;
            border-color: #fff;
        }

        .support-message {
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            line-height: 1.8;
            max-width: 480px;
            margin: 0 auto;
        }

        .support-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(201, 150, 58, 0.25);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            max-width: 420px;
            margin: 2rem auto 0;
        }

        .qr-box {
            background: #fff;
            padding: 1rem;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .qr-box img {
            width: 220px;
            height: 220px;
            display: block;
        }

        .upi-id-tag {
            font-family: monospace;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.08);
            color: var(--gold-light);
            padding: 0.4rem 0.9rem;
            border-radius: 50px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .support-covers {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 480px;
            margin-inline: auto;
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
    </style>
@endpush

@section('content')

    <section class="support-hero">
        <div class="container-fluid position-relative" style="max-width:600px">

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
                    <ellipse class="book-glow" cx="340" cy="240" rx="220" ry="130"
                        fill="url(#bookGlowGrad)" />
                    <g id="rays">
                        <line class="ray" x1="300" y1="220" x2="298" y2="140" stroke="#C9963A"
                            stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
                        <line class="ray" x1="325" y1="205" x2="322" y2="110" stroke="#E8B85C"
                            stroke-width="2" stroke-linecap="round" opacity="0.75" />
                        <line class="ray" x1="355" y1="205" x2="359" y2="110" stroke="#E8B85C"
                            stroke-width="2" stroke-linecap="round" opacity="0.75" />
                        <line class="ray" x1="382" y1="220" x2="386" y2="140" stroke="#C9963A"
                            stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
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

            @if ($lampAyah)
                <p class="support-ayah-arabic" lang="ar" dir="rtl">
                    {{ $lampAyah->text_arabic }}
                </p>
                <p class="support-ayah-translation mb-1">
                    "{{ Illuminate\Support\Str::limit($lampAyah->translations->first()->text, 140) }}"
                </p>
                <p class="support-ayah-ref">— An-Nur 24:35</p>
                <a href="{{ route('quran.show', 24) }}?highlight=35" class="support-read-context">
                    Open this ayah in the Quran Reader →
                </a>
            @endif

            <p class="support-message">
                Knowledge, like light, was never meant to stay behind closed doors.
            </p>
            <p class="support-message mt-2">
                This page exists simply so the option is here — for anyone who wishes to
                contribute toward what keeps it running. Everything else stays exactly as
                it is, either way.
            </p>

            <div class="support-card">
                <div class="qr-box">
                    <img src="{{ $qrDataUri }}" alt="UPI QR Code">
                </div>
                <div>
                    <span class="upi-id-tag">
                        <i class="bi bi-qr-code me-1"></i>{{ config('services.upi.id') }}
                    </span>
                </div>
                <p class="mt-3 mb-0" style="color:rgba(255,255,255,0.6); font-size:0.85rem">
                    Scan with any UPI app — Google Pay, PhonePe, Paytm, or your bank's app.
                </p>
            </div>

            <div class="support-covers">
                <p class="support-covers-label">What this helps sustain</p>
                <p class="support-covers-list">
                    Keeping the content accurate · Reviewing new sources · Fixing what needs fixing ·
                    Building what comes next
                </p>
            </div>

            <p class="mt-4 mb-0" style="color:rgba(255,255,255,0.5); font-size:0.85rem">
                JazakAllahu Khair, either way. 🤲
            </p>

            <p class="text-center mt-3" style="font-size:0.8rem">
                <a href="{{ route('about') }}" style="color:rgba(255,255,255,0.5); text-decoration:underline">
                    Learn how Taddabur is built and sourced
                </a>
            </p>

            <p class="text-center mt-4 mb-0" style="color:rgba(255,255,255,0.35); font-size:0.78rem">
                You can simply close this page — that's okay too.
            </p>
        </div>
    </section>

    @if ($donors->isNotEmpty())
        <section class="supporters-strip">
            <div class="container-fluid" style="max-width:700px">
                <p class="text-muted small mb-1"
                    style="letter-spacing:0.05em; text-transform:uppercase; font-size:0.75rem">
                    With Gratitude
                </p>
                <h5 class="heading-font mb-3" style="color:var(--gold-dark)">Those Who Helped Keep This Free</h5>
                <div class="supporters-grid">
                    @foreach ($donors as $donor)
                        <span class="supporter-chip">
                            <i class="bi bi-heart-fill me-1" style="font-size:0.65rem; color:var(--gold)"></i>
                            {{ $donor->name }}
                        </span>
                    @endforeach
                </div>

                @if ($gratitudeHadith)
                    <div class="mt-4 pt-3"
                        style="border-top:1px solid var(--border); max-width:520px; margin-inline:auto">
                        <p
                            style="font-size:0.85rem; font-style:italic; color:var(--ink); line-height:1.7; margin-bottom:0.3rem">
                            "Every day two angels come down from Heaven and one of them says, 'O Allah! Compensate every
                            person who spends in Your Cause.'"
                        </p>
                        <small class="text-muted">
                            — Sahih Bukhari, Hadith {{ $gratitudeHadith->number }} (excerpt)
                        </small>
                    </div>
                @endif

                <p class="text-muted mt-3 mb-0" style="font-size:0.8rem; direction:rtl" lang="ar">
                    جَزَاكُمُ اللَّهُ خَيْرًا
                </p>
            </div>
        </section>
    @endif

@endsection

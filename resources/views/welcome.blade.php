@extends('layouts.app')

@section('title', 'Taddabur — Quran, Tafsir & Prophet Stories')

@push('styles')
    <style>
        .hero {
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0a2a18 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        /* Decorative geometric pattern overlay */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z' fill='none' stroke='rgba(201,150,58,0.07)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 60px 60px;
        }

        .hero-ayah {
            font-family: var(--font-arabic);
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            line-height: 2;
            color: rgba(255, 255, 255, 0.85);
            text-align: right;
            direction: rtl;
            border-right: 3px solid var(--gold);
            padding-right: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .hero-ayah-ref {
            color: var(--gold-light);
            font-size: 0.85rem;
            text-align: right;
            font-style: italic;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: clamp(2rem, 6vw, 4rem);
            color: #fff;
            line-height: 1.2;
        }

        .hero-title span {
            color: var(--gold-light);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--emerald), var(--emerald-light));
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--gold-light);
            flex-shrink: 0;
        }

        .prophet-chip {
            background: var(--cream-dark);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--ink);
        }

        .prophet-chip:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: #1A1A2E;
        }

        .stats-box {
            text-align: center;
            padding: 2rem;
            border-right: 1px solid var(--border);
        }

        .stats-box:last-child {
            border-right: none;
        }

        .stats-number {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            color: var(--gold);
            display: block;
            line-height: 1;
        }

        /* ==========================================
                                                                                                                                                                                                   TADDABUR PREMIUM ASMA BACKGROUND
                                                                                                                                                                                                ========================================== */

        .allah-hero-bg {
            position: absolute;
            inset: 0;

            overflow: hidden;

            pointer-events: none;

            z-index: 1;
        }

        /* Allah Watermark */

        .allah-watermark {
            position: absolute;

            top: 62%;
            left: 50%;

            transform: translate(-50%, -50%);

            font-family: 'Amiri', serif;

            font-size: clamp(16rem, 32vw, 36rem);

            line-height: 1.15;

            color: rgba(255, 255, 255, 0.035);

            white-space: nowrap;

            user-select: none;
        }

        /* Current Name */

        .hero-asma {
            position: absolute;

            top: 62%;
            left: 50%;

            transform: translate(-50%, -50%);

            text-align: center;

            width: 100%;

            z-index: 2;
        }

        /* Arabic Name */

        .hero-asma-ar {
            font-family: var(--font-arabic);

            font-size: clamp(2rem, 3vw, 3.5rem);

            color: rgba(201, 150, 58, 0.75);

            text-shadow:
                0 0 30px rgba(201, 150, 58, 0.10);

            transition: opacity 1.5s ease;
        }

        /* Meaning */

        .hero-asma-en {
            margin-top: 0.5rem;

            font-size: 0.85rem;

            letter-spacing: 3px;

            text-transform: uppercase;

            color: rgba(255, 255, 255, 0.45);

            transition: opacity 1.5s ease;
        }

        .hero .container {
            position: relative;
            z-index: 10;
        }

        .hero::after {
            content: '';

            position: absolute;
            inset: 0;

            background:
                radial-gradient(circle at center,
                    rgba(255, 255, 255, 0.03),
                    transparent 55%);

            pointer-events: none;
        }

        .card-islamic {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .card-islamic:hover {
            transform: translateY(-4px);
        }
    </style>
@endpush

@section('content')

    {{-- ============================================================
     HERO SECTION
============================================================ --}}
    <section class="hero">
        <div class="allah-hero-bg">

            <div class="allah-watermark">
                ﷲ
            </div>

            <div class="hero-asma">
                <div id="asma-ar" class="hero-asma-ar">
                    {{ $allahNames->first()->name_ar ?? '' }}
                </div>

                <div id="asma-en" class="hero-asma-en">
                    {{ $allahNames->first()->meaning ?? '' }}
                </div>
            </div>

        </div>

        <div class="container position-relative">
            <div class="row align-items-center gy-5">



                {{-- Left: Text --}}
                <div class="col-lg-6">
                    <h1 class="hero-title mb-3">
                        Read the Quran.<br>
                        Understand the <span>Tafsir</span>.<br>
                        Know the Prophets.
                    </h1>
                    <p class="mb-4"
                        style="color: rgba(255,255,255,0.85); font-size:1.1rem; max-width:480px; line-height: 1.6; ">
                        A simple space to read, reflect, and understand the Qur’an — with Tafsir and prophetic guidance, so
                        every Muslim can access deep Quranic knowledge.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('quran.index') }}" class="btn-gold btn btn-lg">
                            <i class="bi bi-book me-2"></i>Start Reading
                        </a>
                        <a href="{{ route('stories.index') }}" class="btn btn-lg"
                            style="background:transparent; color:white; border:1px solid rgba(255,255,255,0.2)">
                            <i class="bi bi-journal-text me-2"></i>Prophet Stories
                        </a>
                    </div>
                    <p class="mt-3" style="color:rgba(255,255,255,0.5); font-size:0.85rem">
                        <i class="bi bi-check-circle me-1" style="color:var(--gold-light)"></i>
                        Free forever. No credit card needed to start.
                    </p>
                </div>

                {{-- Right: Arabic ayah --}}
                <div class="col-lg-5 offset-lg-1">
                    <div
                        style="background:rgba(255,255,255,0.05); border-radius:var(--radius-lg); padding:2rem; border:1px solid rgba(201,150,58,0.2)">
                        <p class="hero-ayah" style="margin-bottom:1.5rem;">
                            اقْرَأْ بِاسْمِ رَبِّكَ الَّذِي خَلَقَ
                        </p>
                        <p class="hero-ayah-ref">
                            "Read in the name of your Lord who created." — Al-'Alaq 96:1
                        </p>
                        <hr style="border-color:rgba(201,150,58,0.2)">
                        <p class="hero-ayah" style="margin-bottom:1.5rem;">
                            وَعَلَّمَ الْإِنسَانَ مَا لَمْ يَعْلَمْ
                        </p>
                        <p class="hero-ayah-ref">
                            "He taught man what he did not know." — Al-'Alaq 96:5
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
     STATS BAR
============================================================ --}}
    <section class="py-3" style="background:var(--cream-dark); border-bottom:1px solid var(--border)">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3 stats-box">
                    <span class="stats-number">114</span>
                    <small class="text-muted">Surahs</small>
                </div>
                <div class="col-6 col-md-3 stats-box">
                    <span class="stats-number">6,236</span>
                    <small class="text-muted">Ayahs</small>
                </div>
                <div class="col-6 col-md-3 stats-box">
                    <span class="stats-number">25</span>
                    <small class="text-muted">Prophets</small>
                </div>
                <div class="col-6 col-md-3 stats-box">
                    <span class="stats-number">$1.99</span>
                    <small class="text-muted">Starting price</small>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
     FEATURES
============================================================ --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="heading-font mb-2">Everything You Need to Learn</h2>
                <p class="text-muted">One platform. Deep knowledge. Affordable for everyone.</p>
                <hr class="divider-gold">
            </div>

            <div class="row g-4">
                @foreach ([['icon' => 'bi-book-half', 'title' => 'Full Quran Reader', 'desc' => 'Read all 114 Surahs in Arabic with multiple translations. Clean, distraction-free reading.'], ['icon' => 'bi-mortarboard', 'title' => 'Scholar Tafsir', 'desc' => 'Deep explanations from Ibn Kathir, Al-Jalalayn and more — ayah by ayah.'], ['icon' => 'bi-mic', 'title' => 'Audio Recitations', 'desc' => 'Listen to Mishary Al-Afasy, Abdul Basit and others. Follow along as you read.'], ['icon' => 'bi-journal-bookmark', 'title' => 'Prophet Stories', 'desc' => 'All 25 prophets told in beautiful, scholarly chapters. Perfect for adults and children.'], ['icon' => 'bi-bookmark-star', 'title' => 'Bookmarks & Notes', 'desc' => 'Save ayahs and chapters. Write personal notes on any verse or story.'], ['icon' => 'bi-graph-up', 'title' => 'Progress Tracking', 'desc' => 'Track your reading streak and see how much of the Quran you have covered.']] as $feature)
                    <div class="col-md-6 col-lg-4">
                        <div class="card-islamic p-4 h-100 d-flex gap-3">
                            <div class="feature-icon">
                                <i class="bi {{ $feature['icon'] }}"></i>
                            </div>
                            <div>
                                <h5 class="heading-font mb-1" style="font-size:1rem">{{ $feature['title'] }}</h5>
                                <p class="text-muted mb-0" style="font-size:0.9rem">{{ $feature['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
     PROPHETS PREVIEW
============================================================ --}}
    <section class="py-5" style="background:var(--cream-dark)">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="heading-font mb-2">Stories of the Prophets</h2>
                <p class="text-muted">From Adam (AS) to Muhammad (SAW) — every story told in depth.</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                @foreach (['Adam', 'Nuh', 'Ibrahim', 'Musa', 'Yusuf', 'Isa', 'Dawud', 'Sulayman', 'Yunus', 'Muhammad'] as $name)
                    <a href="{{ route('prophets.index') }}" class="prophet-chip">{{ $name }} (AS) →
                        Stories</a>
                @endforeach
                <a href="{{ route('prophets.index') }}" class="prophet-chip" style="border-style:dashed">
                    + 15 more prophets
                </a>
            </div>
            <div class="text-center">
                <a href="{{ route('stories.index') }}" class="btn-emerald btn">
                    Browse All Stories
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================
     PRICING PREVIEW
============================================================ --}}
    <section class="py-5">
        <div class="container text-center">
            <h2 class="heading-font mb-2">Knowledge Should Be Affordable</h2>
            <p class="text-muted small">
                Built for learning the Qur’an — not profit-driven complexity.
            </p>
            <p class="text-muted mb-4">
                Simple pricing. No hidden charges. Cancel anytime.
            </p>
            <div class="row justify-content-center g-4">
                @foreach ([['plan' => 'Free', 'price' => '$0', 'note' => 'forever', 'features' => ['Quran reading', '1 translation', '5 free stories'], 'cta' => 'Start Free', 'href' => route('register'), 'highlight' => false], ['plan' => 'Basic', 'price' => '$1.99', 'note' => 'per month', 'features' => ['All translations', 'Full tafsir', 'All stories'], 'cta' => 'Get Basic', 'href' => route('subscription.upgrade'), 'highlight' => true], ['plan' => 'Premium', 'price' => '$3.99', 'note' => 'per month', 'features' => ['Audio recitations', 'Personal notes', 'Downloads'], 'cta' => 'Go Premium', 'href' => route('subscription.upgrade'), 'highlight' => false]] as $plan)
                    <div class="col-md-4">
                        <div class="card-islamic p-4 h-100 {{ $plan['highlight'] ? 'border-gold' : '' }}"
                            style="{{ $plan['highlight'] ? 'border-color:var(--gold)!important; border-width:2px!important' : '' }}">
                            @if ($plan['highlight'])
                                <div class="badge mb-2" style="background:var(--gold); color:#1A1A2E">Most Popular</div>
                            @endif
                            <h4 class="heading-font">{{ $plan['plan'] }}</h4>
                            <div class="mb-3">
                                <span style="font-size:2rem; font-family:var(--font-heading); color:var(--gold)">
                                    {{ $plan['price'] }}
                                </span>
                                <small class="text-muted"> / {{ $plan['note'] }}</small>
                            </div>
                            <ul class="list-unstyled text-start mb-4">
                                @foreach ($plan['features'] as $f)
                                    <li class="mb-1">
                                        <i class="bi bi-check-circle-fill me-2" style="color:var(--emerald-light)"></i>
                                        {{ $f }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ $plan['href'] }}"
                                class="{{ $plan['highlight'] ? 'btn-gold' : 'btn-emerald' }} btn w-100">
                                {{ $plan['cta'] }}
                            </a>

                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </section>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const names = @json(
                $allahNames->map(fn($name) => [
                        'ar' => $name->name_ar,
                        'en' => $name->meaning,
                    ]));

            if (!names.length) return;

            let current = 0;

            const ar = document.getElementById('asma-ar');
            const en = document.getElementById('asma-en');

            if (!ar || !en) return;

            setInterval(() => {

                ar.style.opacity = 0;
                en.style.opacity = 0;

                setTimeout(() => {

                    current++;

                    if (current >= names.length) {
                        current = 0;
                    }

                    ar.textContent = names[current].ar;
                    en.textContent = names[current].en;

                    ar.style.opacity = 1;
                    en.style.opacity = 1;

                }, 1000);

            }, 8000);

        });
    </script>
@endpush

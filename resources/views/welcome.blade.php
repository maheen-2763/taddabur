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
            border: 1.5px solid transparent;
            border-radius: 50% 50% 6px 6px / 65% 65% 6px 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--gold-light);
            flex-shrink: 0;
            transition: background 0.25s ease, border-color 0.25s ease, color 0.25s ease;
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
               HERO AMBIENT BACKGROUND
               Subtle geometric + watermark texture only.
               The rotating Asma-ul-Husna display now lives
               in the "Coming Soon" card, not here — keeps
               the hero calm and avoids overlapping the
               ayah card on the right.
            ========================================== */

        .allah-hero-bg {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .allah-watermark {
            position: absolute;
            top: 58%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Amiri', serif;
            font-size: clamp(10rem, 18vw, 20rem);
            line-height: 1;
            color: rgba(201, 150, 58, 0.08);
            white-space: nowrap;
            user-select: none;
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
            mask-image: radial-gradient(circle at center, black 40%, transparent 75%);
        }

        .hero .container {
            position: relative;
            z-index: 10;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.03), transparent 55%);
            pointer-events: none;
        }

        .card-islamic {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .card-islamic:hover {
            transform: translateY(-4px);
        }

        .card-islamic:hover .feature-icon {
            background: transparent;
            border-color: var(--gold);
            color: var(--gold);
        }

        /* ==========================================
               COMING SOON CARDS
            ========================================== */

        .card-coming-soon {
            position: relative;
            border: 1px dashed var(--border);
            border-radius: var(--radius);
            background: var(--cream-dark);
            transition: border-color 0.2s ease;
        }

        .card-coming-soon:hover {
            border-color: var(--gold);
        }

        .card-coming-soon:hover .feature-icon-outline {
            background: rgba(201, 150, 58, 0.1);
        }

        .badge-coming-soon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(201, 150, 58, 0.12);
            color: var(--gold);
            border: 1px solid rgba(201, 150, 58, 0.3);
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
        }

        .feature-icon-outline {
            width: 56px;
            height: 56px;
            background: transparent;
            border: 1.5px solid var(--gold);
            border-radius: 50% 50% 6px 6px / 65% 65% 6px 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: var(--gold);
            flex-shrink: 0;
            transition: background 0.25s ease;
        }

        /* Mini Asma-ul-Husna preview, embedded in its Coming Soon card */
        .asma-preview {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border);
        }

        .asma-preview-ar {
            font-family: var(--font-arabic);
            font-size: 1.4rem;
            color: var(--gold);
            transition: opacity 1.2s ease;
        }

        .asma-preview-en {
            font-size: 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--ink);
            opacity: 0.6;
            transition: opacity 1.2s ease;
        }

        /* ==========================================
               ORNAMENTAL DIVIDER — small gold star motif
               used between every major section heading
            ========================================== */

        .divider-ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 1rem auto 0;
            max-width: 220px;
        }

        .divider-ornament .line {
            flex: 1;
            height: 1px;
            background: rgba(201, 150, 58, 0.35);
        }

        .divider-ornament svg {
            flex-shrink: 0;
            color: var(--gold);
        }

        /* Manuscript-style corner brackets on the hero ayah card */
        .ayah-card {
            position: relative;
        }

        .ayah-card::before,
        .ayah-card::after {
            content: '';
            position: absolute;
            width: 26px;
            height: 26px;
            border: 2px solid var(--gold);
            opacity: 0.55;
            pointer-events: none;
        }

        .ayah-card::before {
            top: -1px;
            left: -1px;
            border-right: none;
            border-bottom: none;
        }

        .ayah-card::after {
            bottom: -1px;
            right: -1px;
            border-left: none;
            border-top: none;
        }

        /* ==========================================
               REFLECTION BAND — second dark band, echoes
               the hero, gives the page a mid-scroll pause
            ========================================== */

        .reflect-band {
            position: relative;
            padding: 5rem 0;
            background: linear-gradient(160deg, var(--emerald-dark) 0%, #0a2a18 100%);
            overflow: hidden;
        }

        .reflect-band-bg {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0 L60 30 L30 60 L0 30 Z' fill='none' stroke='rgba(201,150,58,0.06)' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 60px 60px;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')

    {{-- ============================================================
     HERO SECTION
============================================================ --}}
    <section class="hero">
        <div class="allah-hero-bg">
            <div class="allah-watermark">ﷲ</div>
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
                        A simple space to read, reflect, and understand the Qur'an — with Tafsir and prophetic guidance, so
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
                    <div class="ayah-card"
                        style="background:rgba(255,255,255,0.05); border-radius:var(--radius-lg); padding:2rem; border:1px solid rgba(201,150,58,0.2)">
                        <p class="hero-ayah" lang="ar" dir="rtl" style="margin-bottom:1.5rem;">
                            اقْرَأْ بِاسْمِ رَبِّكَ الَّذِي خَلَقَ
                        </p>
                        <p class="hero-ayah-ref">
                            "Read in the name of your Lord who created." — Al-'Alaq 96:1
                        </p>
                        <hr style="border-color:rgba(201,150,58,0.2)">
                        <p class="hero-ayah" lang="ar" dir="rtl" style="margin-bottom:1.5rem;">
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
     STATS BAR — sacred counts only, no pricing mixed in
============================================================ --}}
    <section class="py-3" style="background:var(--cream-dark); border-bottom:1px solid var(--border)">
        <div class="container">
            <div class="row text-center">
                <div class="col-4 stats-box">
                    <span class="stats-number" data-target="114">0</span>
                    <small class="text-muted">Surahs</small>
                </div>
                <div class="col-4 stats-box">
                    <span class="stats-number" data-target="6236" data-format="comma">0</span>
                    <small class="text-muted">Ayahs</small>
                </div>
                <div class="col-4 stats-box">
                    <span class="stats-number" data-target="25">0</span>
                    <small class="text-muted">Prophets</small>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
     FEATURES (live today)
============================================================ --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="heading-font mb-2">Everything You Need to Learn</h2>
                <p class="text-muted">One platform. Deep knowledge. Affordable for everyone.</p>
                <div class="divider-ornament">
                    <span class="line"></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0 L14.5 9.5 L24 12 L14.5 14.5 L12 24 L9.5 14.5 L0 12 L9.5 9.5 Z" />
                    </svg>
                    <span class="line"></span>
                </div>
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
                <p class="text-muted">From Adam (AS) to Muhammad ﷺ — every story told in depth.</p>
                <div class="divider-ornament">
                    <span class="line"></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0 L14.5 9.5 L24 12 L14.5 14.5 L12 24 L9.5 14.5 L0 12 L9.5 9.5 Z" />
                    </svg>
                    <span class="line"></span>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                @foreach (['Adam' => 'AS', 'Nuh' => 'AS', 'Ibrahim' => 'AS', 'Musa' => 'AS', 'Yusuf' => 'AS', 'Isa' => 'AS', 'Dawud' => 'AS', 'Sulayman' => 'AS', 'Yunus' => 'AS', 'Muhammad' => 'ﷺ'] as $name => $honorific)
                    <a href="{{ route('prophets.index') }}" class="prophet-chip">{{ $name }} {{ $honorific }} →
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
     GROWING EVERY MONTH (roadmap — clearly marked Coming Soon)
============================================================ --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="heading-font mb-2">Always Growing</h2>
                <p class="text-muted">
                    More authentic knowledge is on its way — built the same way as everything else here:
                    sourced, reviewed, and free to start.
                </p>
                <div class="divider-ornament">
                    <span class="line"></span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0 L14.5 9.5 L24 12 L14.5 14.5 L12 24 L9.5 14.5 L0 12 L9.5 9.5 Z" />
                    </svg>
                    <span class="line"></span>
                </div>
            </div>

            <div class="row g-4">
                {{-- Sahaba Stories --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card-coming-soon p-4 h-100">
                        <span class="badge-coming-soon">Coming Soon</span>
                        <div class="feature-icon-outline mb-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="heading-font mb-1" style="font-size:1rem">Sahaba Stories</h5>
                        <p class="text-muted mb-0" style="font-size:0.9rem">
                            The companions who carried the message forward — their courage, sacrifice, and lives
                            beside the Prophet ﷺ.
                        </p>
                    </div>
                </div>

                {{-- Tadabbur AI — with live mini preview --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card-coming-soon p-4 h-100">
                        <span class="badge-coming-soon">Coming Soon</span>
                        <div class="feature-icon-outline mb-3">
                            <i class="bi bi-robot"></i>
                        </div>
                        <h5 class="heading-font mb-1" style="font-size:1rem">Tadabbur AI</h5>
                        <p class="text-muted mb-0" style="font-size:0.9rem">
                            Ask questions about any ayah and get clear answers — grounded only in your existing
                            Tafsir sources, built to help you reflect, not replace a scholar.
                        </p>
                    </div>
                </div>

                {{-- Four Imams --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card-coming-soon p-4 h-100">
                        <span class="badge-coming-soon">Coming Soon</span>
                        <div class="feature-icon-outline mb-3">
                            <i class="bi bi-bank"></i>
                        </div>
                        <h5 class="heading-font mb-1" style="font-size:1rem">The Four Imams</h5>
                        <p class="text-muted mb-0" style="font-size:0.9rem">
                            The lives and legacy of Imam Abu Hanifa, Malik, Ash-Shafi'i, and Ahmad ibn Hanbal.
                        </p>
                    </div>
                </div>

                {{-- Hadith Collection --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card-coming-soon p-4 h-100">
                        <span class="badge-coming-soon">Coming Soon</span>
                        <div class="feature-icon-outline mb-3">
                            <i class="bi bi-collection"></i>
                        </div>
                        <h5 class="heading-font mb-1" style="font-size:1rem">Hadith Collection</h5>
                        <p class="text-muted mb-0" style="font-size:0.9rem">
                            Authenticated hadith from Sahih Bukhari, Sahih Muslim and more, explained in context.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="text-muted mb-2" style="font-size:0.9rem">Want to know the moment these launch?</p>
                <a href="{{ route('register') }}" class="btn-emerald btn">
                    Create a Free Account
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================
     REFLECTION BAND — a pause before the pricing ask
============================================================ --}}
    <section class="reflect-band">
        <div class="reflect-band-bg"></div>
        <div class="container position-relative text-center">
            <div class="divider-ornament" style="margin-bottom:1.5rem">
                <span class="line" style="background:rgba(201,150,58,0.4)"></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"
                    style="color:var(--gold-light)">
                    <path d="M12 0 L14.5 9.5 L24 12 L14.5 14.5 L12 24 L9.5 14.5 L0 12 L9.5 9.5 Z" />
                </svg>
                <span class="line" style="background:rgba(201,150,58,0.4)"></span>
            </div>
            <p class="hero-ayah" lang="ar" dir="rtl"
                style="text-align:center; border-right:none; padding-right:0; display:inline-block; margin-bottom:1.5rem; max-width:720px;">
                كِتَابٌ أَنزَلْنَاهُ إِلَيْكَ مُبَارَكٌ لِّيَدَّبَّرُوا آيَاتِهِ وَلِيَتَذَكَّرَ أُولُو الْأَلْبَابِ
            </p>
            <p class="mb-1"
                style="color:rgba(255,255,255,0.8); font-size:1.05rem; max-width:600px; margin-inline:auto;">
                "A blessed Book which We have revealed to you, that they might reflect upon its verses and that
                those of understanding would be reminded."
            </p>
            <p class="hero-ayah-ref" style="text-align:center; margin-bottom:2rem">— Sad 38:29</p>
            <a href="{{ route('quran.index') }}" class="btn btn-lg"
                style="background:transparent; color:var(--gold-light); border:1px solid rgba(201,150,58,0.4)">
                Begin Your Tadabbur
            </a>
        </div>
    </section>

    {{-- ============================================================
     PRICING PREVIEW
============================================================ --}}
    <section class="py-5" style="background:var(--cream-dark)">
        <div class="container text-center">
            <h2 class="heading-font mb-2">Knowledge Should Be Affordable</h2>
            <p class="text-muted small">
                Built for learning the Qur'an — not profit-driven complexity.
            </p>
            <p class="text-muted mb-4">
                Simple pricing. No hidden charges. Cancel anytime.
            </p>
            <div class="divider-ornament" style="margin-bottom:2rem">
                <span class="line"></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0 L14.5 9.5 L24 12 L14.5 14.5 L12 24 L9.5 14.5 L0 12 L9.5 9.5 Z" />
                </svg>
                <span class="line"></span>
            </div>
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

            const counters = document.querySelectorAll('.stats-number[data-target]');
            if (!counters.length) return;

            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            const animateCounter = (el) => {
                const target = parseInt(el.dataset.target, 10);
                const useComma = el.dataset.format === 'comma';

                if (prefersReducedMotion) {
                    el.textContent = useComma ? target.toLocaleString() : target;
                    return;
                }

                const duration = 1200;
                const start = performance.now();

                const step = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const value = Math.round(progress * target);
                    el.textContent = useComma ? value.toLocaleString() : value;
                    if (progress < 1) requestAnimationFrame(step);
                };

                requestAnimationFrame(step);
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            counters.forEach((el) => observer.observe(el));

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const names = @json(
                $allahNames->map(fn($name) => [
                        'ar' => $name->name_ar,
                        'en' => $name->meaning,
                    ]));

            if (!names.length) return;

            let current = 0;

            const ar = document.getElementById('asma-preview-ar');
            const en = document.getElementById('asma-preview-en');

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

            }, 4000);

        });
    </script>
@endpush

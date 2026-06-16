@extends('layouts.app')

@section('title', 'Pricing — Taddabur')

@section('content')
    <section class="py-5">
        <div class="container" style="max-width:1400px">

            {{-- Header --}}
            <div class="text-center mb-4">
                <p class="text-success fw-semibold mb-1"
                    style="letter-spacing: 0.12em; font-size: 0.8rem; text-transform: uppercase;">
                    Simple Pricing
                </p>
                <h1 class="display-5 fw-bold">Invest in your spiritual growth</h1>
                <p class="lead text-muted">Start free. Upgrade when you're ready.</p>

                {{-- Monthly / Yearly toggle --}}
                <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
                    <span class="fw-semibold" id="label-monthly">Monthly</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="billingToggle"
                            style="width: 3rem; height: 1.5rem; cursor: pointer;">
                    </div>
                    <span class="text-muted" id="label-yearly">
                        Yearly
                        <span class="badge bg-success ms-1" style="font-size: 0.7rem;">Save up to 33%</span>
                    </span>
                </div>
            </div>

            {{-- Plan Cards --}}
            <div class="row justify-content-center g-4">
                @foreach ($plans as $plan)
                    {{-- Highlight the middle plan (Basic) --}}
                    @php $isPopular = $plan->slug === 'basic'; @endphp

                    <div class="col-md-4 col-lg-4">
                        <div class="card h-100 border-0 shadow
                {{ $isPopular ? 'border border-success' : '' }}"
                            style="{{ $isPopular ? 'border-width: 2px !important;' : '' }}">

                            {{-- Popular badge --}}
                            @if ($isPopular)
                                <div class="card-header bg-success text-white text-center py-2 border-0">
                                    <small class="fw-semibold" style="letter-spacing: 0.08em; text-transform: uppercase;">
                                        Most Popular
                                    </small>
                                </div>
                            @endif

                            <div class="card-body p-4">

                                {{-- Plan name & description --}}
                                <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
                                <p class="text-muted small mb-4">{{ $plan->description }}</p>

                                {{-- Price --}}
                                <div class="mb-4">
                                    @if ($plan->isFree())
                                        <span class="display-6 fw-bold">Free</span>
                                        <span class="text-muted small d-block mt-1">Forever</span>
                                    @else
                                        {{-- Monthly price (shown by default) --}}
                                        <div class="price-monthly">
                                            <span class="display-6 fw-bold">${{ $plan->price_monthly }}</span>
                                            <span class="text-muted">/month</span>
                                        </div>

                                        {{-- Yearly price (hidden by default) --}}
                                        <div class="price-yearly d-none">
                                            <span class="display-6 fw-bold">${{ $plan->price_yearly }}</span>
                                            <span class="text-muted">/year</span>
                                            @if ($plan->yearly_savings > 0)
                                                <div
                                                    class="badge bg-success bg-opacity-10 text-success mt-2 d-block w-auto">
                                                    Save ${{ number_format($plan->yearly_savings, 2) }} vs monthly
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Features --}}
                                <ul class="list-unstyled mb-4">

                                    {{-- Boolean feature flags --}}
                                    @php
                                        $flags = [
                                            'has_tafsir' => 'Tafsir (verse explanation)',
                                            'has_audio' => 'Audio recitation',
                                            'has_notes' => 'Personal notes',
                                            'has_progress' => 'Reading progress',
                                            'has_downloads' => 'Offline downloads',
                                        ];
                                    @endphp

                                    {{-- Named features from JSON --}}
                                    @foreach ($plan->features as $feature)
                                        <li class="mb-2 d-flex align-items-start gap-2">
                                            <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                                            <span class="small">{{ $feature }}</span>
                                        </li>
                                    @endforeach

                                    {{-- Show locked features for free plan --}}
                                    @if ($plan->isFree())
                                        @foreach ($flags as $key => $label)
                                            @if (!$plan->$key)
                                                <li class="mb-2 d-flex align-items-start gap-2 text-muted">
                                                    <i class="bi bi-x-circle flex-shrink-0 mt-1"></i>
                                                    <span class="small">{{ $label }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif

                                    {{-- Story & translation limits --}}
                                    <li class="mb-2 d-flex align-items-start gap-2 text-muted">
                                        <i class="bi bi-book flex-shrink-0 mt-1 text-success"></i>
                                        <span class="small">
                                            {{ $plan->story_limit_label }} prophet stories
                                        </span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-start gap-2 text-muted">
                                        <i class="bi bi-translate flex-shrink-0 mt-1 text-success"></i>
                                        <span class="small">
                                            {{ $plan->translation_limit_label }}
                                            translation {{ Str::plural('language', $plan->translation_limit ?? 2) }}
                                        </span>
                                    </li>
                                </ul>

                                {{-- CTA Button --}}
                                @auth
                                    @if ($currentPlan === $plan->id)
                                        <button class="btn btn-outline-success w-100" disabled>
                                            <i class="bi bi-check2 me-1"></i> Current Plan
                                        </button>
                                    @elseif (!$plan->isFree())
                                        <a href="{{ route('subscription.upgrade') }}?plan={{ $plan->slug }}"
                                            class="btn {{ $isPopular ? 'btn-success' : 'btn-outline-success' }} w-100">
                                            Upgrade to {{ $plan->name }}
                                        </a>
                                    @endif
                                @else
                                    @if ($plan->isFree())
                                        <a href="{{ route('register') }}" class="btn btn-outline-success w-100">
                                            Get Started Free
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}"
                                            class="btn {{ $isPopular ? 'btn-success' : 'btn-outline-success' }} w-100">
                                            Get Started
                                        </a>
                                    @endif
                                @endauth

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-12">

                    <div class="text-center mb-4">
                        <div class="divider-gold mx-auto" style="max-width:180px;"></div>
                    </div>

                    <div class="text-center mb-4">
                        <h2 class="heading-font mb-3">
                            What Makes Taddabur Different?
                        </h2>

                        <p class="text-muted mx-auto" style="max-width: 650px;">
                            More than a Quran reader. Explore authentic tafsir, learn from the Prophets,
                            Sahaba, Khulafa Rashidun, and the Four Imams — all in one place.
                        </p>
                    </div>

                    {{-- Feature Cards --}}
                    <div class="row g-4">

                        {{-- Quran & Tafsir --}}
                        <div class="col-md-6 col-lg-3">
                            <div class="card-islamic feature-card">

                                <div class="mb-3">
                                    <i class="bi bi-book-half fs-1 text-success"></i>
                                </div>

                                <h5 class="heading-font mb-3">
                                    Quran & Tafsir
                                </h5>

                                <p class="small text-muted mb-0">
                                    Read the Quran with trusted tafsir sources,
                                    translations, and contextual explanations.
                                </p>

                            </div>
                        </div>


                        {{-- Prophets & History --}}
                        <div class="col-md-6 col-lg-3">
                            <div class="card-islamic feature-card">

                                <div class="mb-3">
                                    <i class="bi bi-person-hearts fs-1 text-success"></i>
                                </div>

                                <h5 class="heading-font mb-3">
                                    Prophets & History
                                </h5>

                                <p class="small text-muted mb-0">
                                    Discover authentic stories of the Prophets,
                                    Sahaba, Khulafa Rashidun, and Islamic history.
                                </p>

                            </div>
                        </div>

                        {{-- Reflection --}}
                        <div class="col-md-6 col-lg-3">
                            <div class="card-islamic feature-card">

                                <div class="mb-3">
                                    <i class="bi bi-journal-richtext fs-1 text-success"></i>
                                </div>

                                <h5 class="heading-font mb-3">
                                    Personal Reflection
                                </h5>

                                <p class="small text-muted mb-0">
                                    Save reflections, notes, bookmarks,
                                    and track your learning journey.
                                </p>

                            </div>
                        </div>

                        {{-- AI Assistant --}}
                        <div class="col-md-6 col-lg-3">
                            <div class="card-islamic feature-card">

                                <div class="mb-1">
                                    <i class="bi bi-robot fs-1 text-success"></i>
                                </div>

                                <div class="mb-1">
                                    <span class="badge rounded-pill text-bg-success">
                                        Coming Soon
                                    </span>
                                </div>

                                <h5 class="heading-font mb-3">
                                    AI Study Assistant
                                </h5>

                                <p class="small text-muted mb-0">
                                    Learn from authentic sources, explore tafsir,
                                    and discover relevant Quranic guidance with AI.
                                </p>

                            </div>
                        </div>

                    </div>

                    {{-- Lifetime Section --}}
                    <div class="text-center mt-5 mb-3">
                        <h4 class="heading-font mb-2">
                            One Payment. Lifetime Learning.
                        </h4>

                        <p class="text-muted small mb-0">
                            Unlock every Premium feature forever with a single purchase.
                        </p>
                    </div>

                </div>

            </div>

            {{-- Lifetime Deal --}}
            @php $premium = $plans->firstWhere('slug', 'premium'); @endphp

            @if ($premium && $premium->price_lifetime > 0)
                <div class="row justify-content-center mt-3">
                    <div class="col-md-8 col-lg-6">

                        <span class="badge bg-warning text-dark mb-2">
                            Best Value
                        </span>
                        <div class="card-islamic lifetime-card">

                            <div
                                class="card-body py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

                                <div>
                                    <h5 class="mb-1 text-success fw-bold">
                                        <i class="bi bi-infinity me-2"></i>
                                        Lifetime Access
                                    </h5>

                                    <p class="text-muted small mb-0">
                                        One-time payment. All Premium features. Forever.
                                    </p>
                                    @php
                                        $savings = 12 * $premium->price_monthly - $premium->price_lifetime;
                                    @endphp
                                    <span class="badge bg-success">
                                        Save ${{ number_format($savings, 0) }} in the first year alone!
                                    </span>

                                </div>

                                <div class="text-end">
                                    <div class="fs-2 fw-bold">
                                        ${{ number_format($premium->price_lifetime, 2) }}
                                    </div>

                                    <small class="text-muted">
                                        one-time purchase
                                    </small>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            @endif

            {{-- Trust line --}}
            <div class="text-center mt-5">
                <h5 class="heading-font mb-2">
                    Built Upon Authentic Sources
                </h5>

                <p class="text-muted mx-auto" style="max-width:700px;">
                    Our mission is to make authentic Islamic knowledge accessible through
                    trusted Quran translations, recognized tafsir works, and carefully researched historical sources.
                </p>


            </div>
    </section>

    @push('scripts')
        <script>
            const toggle = document.getElementById('billingToggle');
            const monthlyPrices = document.querySelectorAll('.price-monthly');
            const yearlyPrices = document.querySelectorAll('.price-yearly');
            const labelMonthly = document.getElementById('label-monthly');
            const labelYearly = document.getElementById('label-yearly');

            toggle.addEventListener('change', function() {
                const isYearly = this.checked;

                monthlyPrices.forEach(el => el.classList.toggle('d-none', isYearly));
                yearlyPrices.forEach(el => el.classList.toggle('d-none', !isYearly));

                labelMonthly.classList.toggle('text-muted', isYearly);
                labelMonthly.classList.toggle('fw-semibold', !isYearly);
                labelYearly.classList.toggle('text-muted', !isYearly);
                labelYearly.classList.toggle('fw-semibold', isYearly);
            });
        </script>
    @endpush

@endsection

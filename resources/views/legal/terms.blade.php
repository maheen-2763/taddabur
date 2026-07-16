@extends('layouts.app')

@section('title', 'Terms of Service — Taddabur')

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 780px;">
            <h1 class="heading-font mb-2">Terms of Service</h1>
            <p class="text-muted small mb-5">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-content">

                <h4 class="heading-font mt-4">1. Acceptance of Terms</h4>
                <p>By creating an account or using Taddabur, you agree to these Terms of Service.</p>

                <h4 class="heading-font mt-4">2. Description of Service</h4>
                <p>Taddabur is an Islamic educational platform offering Quran reading, Tafsir, Prophet stories, and related
                    content across Free, Basic, and Premium plans.</p>

                <h4 class="heading-font mt-4">3. Accounts</h4>
                <p>You must be at least 13 years old to create an account. You are responsible for keeping your account
                    credentials secure.</p>

                <h4 class="heading-font mt-4">4. Subscriptions & Billing</h4>
                <p>Paid subscriptions (Basic, Premium) are billed monthly or yearly via Razorpay. {{-- TODO: confirm auto-renewal wording, and what happens on failed payment --}}</p>

                <h4 class="heading-font mt-4">5. Cancellations & Refunds</h4>
                <p>You may request a full refund within 7 days of purchase by contacting us. {{-- TODO: add support contact once ready; confirm if refund applies to renewals too or first purchase only --}}</p>

                <h4 class="heading-font mt-4">6. Content Accuracy Disclaimer</h4>
                <p>Quranic text, translations, and Tafsir are sourced from verified references and reviewed for accuracy.
                    Taddabur is a study aid and does not replace guidance from a qualified scholar. {{-- TODO: add error-report contact/method --}}
                </p>

                <h4 class="heading-font mt-4">7. Acceptable Use</h4>
                {{-- <p>You may not scrape, resell, or redistribute platform content, or misuse the AI Tafsir assistant to
                    generate content unrelated to its intended purpose.</p> --}}

                <h4 class="heading-font mt-4">8. Intellectual Property</h4>
                <p>{{-- TODO: platform code/design ownership statement; Quranic text itself is public domain, note that distinctly --}}</p>

                <h4 class="heading-font mt-4">9. Termination</h4>
                <p>{{-- TODO: grounds for suspending/closing an account --}}</p>

                <h4 class="heading-font mt-4">10. Limitation of Liability</h4>
                <p>{{-- TODO: standard liability cap clause — recommend lawyer review --}}</p>

                <h4 class="heading-font mt-4">11. Changes to These Terms</h4>
                <p>We may update these Terms from time to time. Continued use after changes means you accept the updated
                    Terms.</p>

                <h4 class="heading-font mt-4">12. Contact</h4>
                <p>{{-- TODO: support email once set up --}}</p>

            </div>
        </div>
    </section>
@endsection

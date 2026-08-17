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
                <p>Taddabur is a free Islamic educational platform offering Quran reading, Tafsirs, Hadiths, Prophet
                    stories, and related content to everyone, at no cost.</p>

                <h4 class="heading-font mt-4">3. Accounts</h4>
                <p>You must be at least 13 years old to create an account. Creating an account is optional and is
                    only required to save your reading progress, notes, and bookmarks — all content can be read
                    without one. You are responsible for keeping your account credentials secure.</p>

                <h4 class="heading-font mt-4">4. Content Accuracy Disclaimer</h4>
                <p>Quranic text, translations, and Tafsir are sourced from verified references and reviewed for accuracy.
                    Taddabur is a study aid and does not replace guidance from a qualified scholar. If you notice an error,
                    please let us know via the contact details below.</p>

                <h4 class="heading-font mt-4">5. Acceptable Use</h4>
                <p>You agree not to copy, scrape, resell, or redistribute the platform's content or code without permission.
                    You also agree not to attempt to disrupt, hack, or gain unauthorized access to the platform or other
                    users'
                    accounts.</p>

                <h4 class="heading-font mt-4">6. Intellectual Property</h4>
                <p>The Taddabur platform — including its design, code, and branding — is the property of its developer. The
                    Quranic text itself is not owned by anyone and remains freely available to all; Taddabur simply presents
                    it alongside translations, Tafsir, and related content sourced from established scholarly works.</p>

                <h4 class="heading-font mt-4">7. Termination</h4>
                <p>We may suspend or terminate an account that violates these Terms, misuses the platform, or engages in
                    abusive
                    or harmful behavior toward other users.</p>

                <h4 class="heading-font mt-4">8. Limitation of Liability</h4>
                <p>Taddabur is provided "as is" without warranties of any kind, express or implied. While we take great care
                    to
                    ensure content accuracy, we do not guarantee that the platform will be error-free, uninterrupted, or
                    available at all times. To the maximum extent permitted by law, Taddabur and its developer shall not be
                    liable for any indirect, incidental, or consequential damages arising from your use of the platform.</p>

                <h4 class="heading-font mt-4">9. Changes to These Terms</h4>
                <p>We may update these Terms from time to time. Continued use after changes means you accept the
                    updated Terms.</p>

                <h4 class="heading-font mt-4">10. Contact</h4>
                <p>{{-- TODO: support email once set up --}}</p>

            </div>
        </div>
    </section>
@endsection

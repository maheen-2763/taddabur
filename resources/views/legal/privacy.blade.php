@extends('layouts.app')

@section('title', 'Privacy Policy — Taddabur')

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 780px;">
            <h1 class="heading-font mb-2">Privacy Policy</h1>
            <p class="text-muted small mb-5">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-content">

                <h4 class="heading-font mt-4">1. Information We Collect</h4>
                <p>We collect your name, email address, and reading activity (bookmarks, notes, progress). Payment details
                    are handled entirely by Razorpay — we do not store your card or bank information.</p>

                <h4 class="heading-font mt-4">2. How We Use Your Information</h4>
                <p>Your information is used to manage your account, track your reading progress, and send account-related
                    emails.</p>

                <h4 class="heading-font mt-4">3. Payment Processing</h4>
                <p>All payments are processed by Razorpay. Taddabur does not receive or store your full card or bank account
                    details.</p>

                <h4 class="heading-font mt-4">4. Cookies</h4>
                <p>{{-- TODO: session cookies only, or do you use any analytics tool (Google Analytics etc.)? --}}</p>

                <h4 class="heading-font mt-4">5. Data Sharing</h4>
                <p>We do not sell your data. Information is shared only with Razorpay for payment processing.
                    {{-- TODO: confirm if any other third-party tool touches user data --}}</p>

                <h4 class="heading-font mt-4">6. Data Retention</h4>
                <p>{{-- TODO: how long is data kept after account deletion? --}}</p>

                <h4 class="heading-font mt-4">7. Your Rights</h4>
                <p>{{-- TODO: if you have users outside India, GDPR-style data export/deletion request rights may apply --}}</p>

                <h4 class="heading-font mt-4">8. Children's Privacy</h4>
                <p>Taddabur is intended for users aged 13 and older. We do not knowingly collect data from children under
                    13.</p>

                <h4 class="heading-font mt-4">9. Security</h4>
                <p>We take reasonable technical measures to protect your data, but no online service can guarantee absolute
                    security.</p>

                <h4 class="heading-font mt-4">10. Changes to This Policy</h4>
                <p>We may update this Privacy Policy from time to time. Continued use after changes means you accept the
                    update.</p>

                <h4 class="heading-font mt-4">11. Contact</h4>
                <p>{{-- TODO: support email once set up --}}</p>

            </div>
        </div>
    </section>
@endsection

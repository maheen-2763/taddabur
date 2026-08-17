@extends('layouts.app')

@section('title', 'Privacy Policy — Taddabur')

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 780px;">
            <h1 class="heading-font mb-2">Privacy Policy</h1>
            <p class="text-muted small mb-5">Last updated: {{ date('F j, Y') }}</p>

            <div class="legal-content">

                <h4 class="heading-font mt-4">1. Information We Collect</h4>
                <p>We collect your name, email address, and reading activity (bookmarks, notes, progress) when you
                    create an account. Taddabur is completely free — we do not collect any payment or financial
                    information, because none is ever required.</p>

                <h4 class="heading-font mt-4">2. How We Use Your Information</h4>
                <p>Your information is used only to manage your account, track your reading progress, and send
                    account-related emails (such as verification or password reset).</p>

                <h4 class="heading-font mt-4">3. Cookies & Analytics</h4>
                <p>We use only essential session cookies required to keep you logged in. We do not use Google Analytics,
                    advertising trackers, or any third-party analytics tools.</p>

                <h4 class="heading-font mt-4">4. Data Sharing</h4>
                <p>We do not sell, rent, or share your personal data with any third party.</p>

                <h4 class="heading-font mt-4">5. Data Retention</h4>
                <p>When you delete your account, your personal data — including your profile, notes, bookmarks, and reading
                    progress — is permanently deleted immediately. This action cannot be undone.</p>

                <h4 class="heading-font mt-4">6. Your Rights</h4>
                <p>You may view, update, or delete your personal data at any time directly from your Profile settings. If
                    you
                    need further assistance, you may also contact us.</p>

                <h4 class="heading-font mt-4">7. Children's Privacy</h4>
                <p>Taddabur is intended for users aged 13 and older. We do not knowingly collect data from children
                    under 13.</p>

                <h4 class="heading-font mt-4">8. Security</h4>
                <p>We take reasonable technical measures to protect your data, but no online service can guarantee
                    absolute security.</p>

                <h4 class="heading-font mt-4">9. Changes to This Policy</h4>
                <p>We may update this Privacy Policy from time to time. Continued use after changes means you accept
                    the update.</p>

                <h4 class="heading-font mt-4">10. Contact</h4>
                <p>{{-- TODO: support email once set up --}}</p>

            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Privacy Policy — Taddabur')

@section('content')
    @php
        $updatedAt = 'October 17, 2026';   // policy badalne par ye date zaroor badlein
        $contactEmail = null;              // TODO: launch se pehle support email yahan daalein
    @endphp

    <section class="py-5">
        <div class="container" style="max-width: 780px;">
            <h1 class="heading-font mb-2">Privacy Policy</h1>
            <p class="text-muted small mb-5">Last updated: {{ $updatedAt }}</p>

            <div class="legal-content">

                <h4 class="heading-font mt-4">1. Information We Collect</h4>
                <p>Taddabur is completely free. You never need to pay to use any feature. Depending on how you use the
                    platform, we collect:</p>
                <ul>
                    <li><strong>Account details:</strong> your name, email address and password (stored only in hashed
                        form), plus your preferences (language, translation, tafsir, reciter and font size).</li>
                    <li><strong>Activity:</strong> your reading and listening progress, bookmarks, notes, story progress
                        and the hadith you mark as read.</li>
                    <li><strong>Session data:</strong> your IP address and browser details, stored with your login session
                        for security.</li>
                    <li><strong>Donations (optional):</strong> the name you enter, the amount, an optional message,
                        whether you want your name shown publicly, the payment method and status, and Razorpay's order
                        and payment reference IDs.</li>
                </ul>
                <p>We never see or store your card, UPI or bank details. Those are handled entirely by Razorpay.</p>

                <h4 class="heading-font mt-4">2. How We Use Your Information</h4>
                <p>We use your information to manage your account, track your reading progress, and send
                    account-related emails (such as welcome, verification and password reset). We also use it to process
                    and record donations, confirm payment status, and keep the platform secure and backed up.</p>

                <h4 class="heading-font mt-4">3. Donations and Razorpay</h4>
                <p>Donating is voluntary and never unlocks anything extra. Payments are processed by Razorpay, and
                    Razorpay's own privacy policy applies to them. On the Support page, Razorpay's checkout script loads
                    from Razorpay's servers and may collect information such as your device details, email or phone
                    number as part of completing the payment. We receive only the payment result and reference IDs. If you
                    tick "Show my name in the supporters list", the name you entered appears in the supporters list on the
                    Support page. Your message is never shown publicly. Leave it unticked to stay anonymous.</p>

                <h4 class="heading-font mt-4">4. Cookies &amp; Third-Party Content</h4>
                <p>We use only essential session cookies required to keep you logged in. We do not use Google Analytics,
                    advertising trackers, or any third-party analytics tools. Some fonts and libraries are loaded from
                    third-party content networks (for example Google Fonts and jsDelivr), which may receive your IP
                    address when your browser fetches them. Razorpay's scripts load only on the Support page.</p>

                <h4 class="heading-font mt-4">5. Data Sharing</h4>
                <p>We do not sell or rent your personal data. We share it only with the service providers needed to run
                    Taddabur: Razorpay (donations), our email delivery provider (account emails) and our hosting
                    provider.</p>

                <h4 class="heading-font mt-4">6. Data Retention and Deletion</h4>
                <p>When you delete your account, your profile, notes, bookmarks and reading progress are removed from our
                    live database immediately. This cannot be undone. Private backup copies are kept for a limited period
                    and then deleted. Donation records are stored separately from your account and are not linked to it;
                    we keep them for accounting and payment verification. If you want your name removed from the supporters
                    list or from a donation record, contact us.</p>

                <h4 class="heading-font mt-4">7. Your Rights</h4>
                <p>You may view, update, or delete your personal data at any time from your Profile settings. For
                    anything else, including donation records, you can contact us.</p>

                <h4 class="heading-font mt-4">8. Children's Privacy</h4>
                <p>Taddabur is intended for users aged 13 and older. We do not knowingly collect data from children
                    under 13.</p>

                <h4 class="heading-font mt-4">9. Security</h4>
                <p>We take reasonable technical measures to protect your data, but no online service can guarantee
                    absolute security.</p>

                <h4 class="heading-font mt-4">10. Changes to This Policy</h4>
                <p>We may update this Privacy Policy from time to time. The "Last updated" date above shows when it
                    last changed. Continued use after changes means you accept the update.</p>

                <h4 class="heading-font mt-4">11. Contact</h4>
                <p>
                    @if ($contactEmail)
                        Questions or requests about your data? Email us at
                        <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>.
                    @else
                        <em>Contact email will be added before launch.</em>
                    @endif
                </p>

            </div>
        </div>
    </section>
@endsection
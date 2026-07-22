@extends('layouts.app')

@section('title', 'About — Taddabur')

@push('styles')
    <style>
        .source-card {
            background: var(--cream-dark);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            height: 100%;
        }

        .source-card h5 {
            font-size: 1rem;
            color: var(--ink);
        }

        .source-card .source-tag {
            display: inline-block;
            font-size: 0.75rem;
            color: var(--gold-dark);
            background: rgba(201, 150, 58, 0.1);
            border: 1px solid rgba(201, 150, 58, 0.25);
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            margin-bottom: 0.6rem;
        }
    </style>
@endpush

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 900px;">

            <div class="text-center mb-5">
                <h1 class="heading-font mb-2">About Taddabur</h1>
                <p class="text-muted">Built for those who want to read, reflect, and understand — with every source clearly
                    named.</p>
            </div>

            <p class="mb-5">
                Taddabur (تدبّر — "deep reflection") is a platform to read the Quran, understand its meaning through
                Tafsir, and learn from the lives of the Prophets. We believe transparency about where our content
                comes from matters as much as the content itself — so here is exactly what we use, and where it's from.
            </p>

            <h4 class="heading-font mb-3">Content Sources</h4>
            <div class="row g-4 mb-5">

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Quran Text & Translations</span>
                        <h5 class="mb-2">Quran.com API</h5>
                        <p class="text-muted small mb-0">
                            Arabic text and translations (Sahih International, Pickthall, Yusuf Ali, T. Usmani,
                            Fateh Muhammad Jalandhari) sourced from the Quran.com API.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Tafsir</span>
                        <h5 class="mb-2">Ibn Kathir, Al-Jalalayn, al-Muyassar</h5>
                        <p class="text-muted small mb-0">
                            Ayah-by-ayah explanations from classical scholarly Tafsir works, including an Urdu
                            translation of Ibn Kathir.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Audio Recitation</span>
                        <h5 class="mb-2">12 Renowned Reciters</h5>
                        <div class="row row-cols-2 g-1 mb-2">
                            <div class="col"><small class="text-muted">• Mishary Rashid Alafasy</small></div>
                            <div class="col"><small class="text-muted">• Abdur-Rahman As-Sudais</small></div>
                            <div class="col"><small class="text-muted">• Saad Al-Ghamdi</small></div>
                            <div class="col"><small class="text-muted">• Abdul Basit Abdul Samad</small></div>
                            <div class="col"><small class="text-muted">• Mahmoud Khalil Al-Husary</small></div>
                            <div class="col"><small class="text-muted">• Ali Al-Hudhaify</small></div>
                            <div class="col"><small class="text-muted">• Saud Al-Shuraim</small></div>
                            <div class="col"><small class="text-muted">• Maher Al Muaiqly</small></div>
                            <div class="col"><small class="text-muted">• Yasser Al-Dosari</small></div>
                            <div class="col"><small class="text-muted">• Muhammad Siddiq Al-Minshawi</small></div>
                            <div class="col"><small class="text-muted">• Hani Ar-Rifai</small></div>
                            <div class="col"><small class="text-muted">• Abdullah Basfar</small></div>
                        </div>
                        <p class="text-muted small mb-0">
                            Recitation audio synced word-by-word with the Quran text as you read.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Hadith</span>
                        <h5 class="mb-2">Six Major Collections</h5>
                        <p class="text-muted small mb-0">
                            Sahih Bukhari, Sahih Muslim, Muwatta Malik, Abu Dawud, Jami' at-Tirmidhi, and
                            Sunan Ibn Majah — sourced via the Fawaz Ahmed Hadith API, cross-checked for
                            completeness before publishing.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Prophet Stories</span>
                        <h5 class="mb-2">Quran-grounded narratives</h5>
                        <p class="text-muted small mb-0">
                            Every story is built from explicit Quranic statements first; supplementary details from
                            Sahih Bukhari/Muslim are clearly marked where used.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Payments</span>
                        <h5 class="mb-2">Razorpay</h5>
                        <p class="text-muted small mb-0">
                            All subscription payments are processed securely by Razorpay. We never store your card
                            or bank details.
                        </p>
                    </div>
                </div>

            </div>

            <h4 class="heading-font mb-3">How We Ensure Accuracy</h4>
            <div class="row g-4 mb-5">

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Translation Coverage</span>
                        <h5 class="mb-2">Every Ayah, Verified</h5>
                        <p class="text-muted small mb-0">
                            All 5 translations are checked against the complete 6,236-ayah count of the Quran to confirm
                            no verse is missing or mismatched.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Hadith Grading</span>
                        <h5 class="mb-2">Standardized Classification</h5>
                        <p class="text-muted small mb-0">
                            Grade labels (Sahih, Da'if, Hasan, etc.) from source data are normalized into a single
                            consistent system, so grading is never ambiguous across collections.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Ongoing Review</span>
                        <h5 class="mb-2">Some Grades Pending Verification</h5>
                        <p class="text-muted small mb-0">
                            A small number of hadith grade entries are still under manual scholarly review.
                            These are clearly marked wherever they appear, rather than shown with false
                            confidence.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Tafsir Sources</span>
                        <h5 class="mb-2">Verified Against Official Records</h5>
                        <p class="text-muted small mb-0">
                            Tafsir texts are matched against official scholarly resource IDs, not just names, to ensure
                            the correct commentary is always shown for the correct scholar.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Word-by-Word Audio Sync</span>
                        <h5 class="mb-2">Verified Reciter by Reciter</h5>
                        <p class="text-muted small mb-0">
                            Each reciter's word-timing data is individually checked for accuracy before being marked as
                            "verified." Where a reciter's timing data has known issues, we clearly label it as an
                            approximate sync rather than showing incorrect highlighting.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Arabic Text Integrity</span>
                        <h5 class="mb-2">Careful Text Handling</h5>
                        <p class="text-muted small mb-0">
                            Arabic script — including diacritics and letter variants — is handled with dedicated
                            normalization to prevent silent text errors during search or reference lookup.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Ayah Presentation</span>
                        <h5 class="mb-2">Correct Bismillah Placement</h5>
                        <p class="text-muted small mb-0">
                            Bismillah is displayed once at the start of each Surah, matching how it appears in the mus'haf —
                            never duplicated within the first ayah's text.
                        </p>
                    </div>
                </div>

            </div>

            <h4 class="heading-font mb-3">Found an error?</h4>
            <p class="text-muted">
                Despite these checks, mistakes can still happen. If you notice any inaccuracy in the Quran text,
                translation, Tafsir, or story content, Hadiths please
                <a href="mailto:your-email@example.com" style="color:var(--gold-dark)">let us know</a> —
                accuracy in presenting the Quran and Islamic knowledge is our highest priority.
            </p>

        </div>
    </section>
@endsection

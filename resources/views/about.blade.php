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

        .stats-strip {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .stat-block {
            flex: 1 1 220px;
            text-align: center;
            padding: 1.25rem 1rem;
            border-right: 1px solid var(--border);
            background: var(--cream-dark);
        }

        .stat-block:last-child {
            border-right: none;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--emerald-light);
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--ink-muted, #6b6b6b);
        }

        .accuracy-subheading {
            font-size: 0.95rem;
            color: var(--gold-dark);
            margin-bottom: 1rem;
            margin-top: 2rem;
            font-weight: 600;
        }

        .process-steps {
            counter-reset: process-counter;
            list-style: none;
            padding-left: 0;
            margin-bottom: 3rem;
        }

        .process-steps li {
            counter-increment: process-counter;
            position: relative;
            padding-left: 3rem;
            margin-bottom: 1.25rem;
        }

        .process-steps li::before {
            content: counter(process-counter);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: rgba(201, 150, 58, 0.1);
            border: 1px solid rgba(201, 150, 58, 0.35);
            color: var(--gold-dark);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .process-steps li strong {
            display: block;
            color: var(--ink);
            margin-bottom: 0.15rem;
        }

        .honesty-card {
            border-color: rgba(201, 150, 58, 0.35);
        }

        .honesty-quote {
            font-style: italic;
            color: var(--ink);
        }

        .audit-timestamp {
            font-size: 0.78rem;
            color: var(--ink-muted, #6b6b6b);
            text-align: center;
            margin-top: -1.5rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 1000px;">

            <div class="text-center mb-5">
                <h1 class="heading-font mb-2">About Taddabur</h1>
                <p class="text-muted">Built for those who want to read, reflect, and understand — with every source clearly
                    named.</p>
            </div>

            <p class="mb-4">
                Taddabur (تدبّر — "deep reflection") is a platform to read the Quran, understand its meaning through
                Tafsir, and learn from the lives of the Prophets and study authentic Hadith. We believe transparency
                about where our content comes from matters as much as the content itself — so here is exactly what
                we use, where it's from, and how we check it.
            </p>

            {{-- Trust stats strip --}}
            <div class="stats-strip">
                <div class="stat-block">
                    <span class="stat-number">6,236</span>
                    <span class="stat-label">Ayahs Verified</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">30,534</span>
                    <span class="stat-label">Total Hadiths</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">15,427</span>
                    <span class="stat-label">Hadiths Classified</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">345</span>
                    <span class="stat-label">Hadith Chapters Audited</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">6</span>
                    <span class="stat-label">Hadith Collections</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">25</span>
                    <span class="stat-label">Prophet Stories</span>
                </div>
                <div class="stat-block">
                    <span class="stat-number">4</span>
                    <span class="stat-label">Scholars (Four Imams)</span>
                </div>
            </div>

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
                            Sunan Ibn Majah — English translations sourced via the Fawaz Ahmed Hadith API,
                            using the standard published translation for each collection.
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

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Prophet Stories</span>
                        <h5 class="mb-2">Quran-grounded narratives</h5>
                        <p class="text-muted small mb-0">
                            25 Prophet stories across 101 chapters — every story is built from explicit
                            Quranic statements first; supplementary details from Sahih Bukhari/Muslim are
                            clearly marked where used.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Four Imams</span>
                        <h5 class="mb-2">Founders of the Four Madhabs</h5>
                        <p class="text-muted small mb-0">
                            Biography, teachings, students, and notable works of Imam Abu Hanifa, Imam Malik,
                            Imam Shafi'i, and Imam Ahmad ibn Hanbal — drawn from established scholarly sources.
                        </p>
                    </div>
                </div>

            </div>

            <h4 class="heading-font mb-2">How We Ensure Accuracy</h4>
            <p class="text-muted small mb-0">We aim for a 0% error rate across everything we publish. Here's how each part
                of the app is checked.</p>

            {{-- Quran Integrity --}}
            <div class="accuracy-subheading">Quran Integrity</div>
            <div class="row g-4 mb-2">

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

            {{-- Hadith Integrity --}}
            <div class="accuracy-subheading">Hadith Integrity</div>
            <div class="row g-4 mb-2">

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
                        <span class="source-tag">Chapter Completeness</span>
                        <h5 class="mb-2">Every Chapter, Checked</h5>
                        <p class="text-muted small mb-0">
                            All 345 chapters across 6 collections are individually verified so hadiths are always
                            linked to the correct chapter — not just counted, but checked against their source range.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="source-card">
                        <span class="source-tag">Translation Availability</span>
                        <h5 class="mb-2">Nothing Shown as Blank</h5>
                        <p class="text-muted small mb-0">
                            Where an English translation isn't available for a specific hadith in the source data,
                            it's clearly flagged on the page — never shown as an empty or broken entry.
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

            </div>

            {{-- Tafsir Integrity --}}
            <div class="accuracy-subheading">Tafsir Integrity</div>
            <div class="row g-4 mb-5">

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

            </div>

            {{-- Rooted in Honesty --}}
            <h4 class="heading-font mb-3">Rooted in Honesty</h4>
            <div class="source-card honesty-card mb-5">
                <p class="honesty-quote mb-2">
                    "Truthfulness leads to righteousness, and righteousness leads to Paradise. And a man keeps
                    on telling the truth until he becomes a truthful person."
                </p>
                <p class="text-muted small mb-0">
                    — Prophet Muhammad ﷺ, narrated by Abdullah bin Mas'ud
                    (<a href="{{ route('hadith.show', ['bukhari', 78]) }}?highlight=6118"
                        style="color:var(--gold-dark)">Sahih Bukhari, Hadith 6094</a>)
                </p>
            </div>

            {{-- Our Process --}}
            <h4 class="heading-font mb-3">Our Process</h4>
            <ol class="process-steps">
                <li>
                    <strong>Sourcing</strong>
                    Content is drawn from established, widely-used open sources for Quran text, translations, Tafsir,
                    and Hadith — never generated or paraphrased by us.
                </li>
                <li>
                    <strong>Auditing</strong>
                    Every collection is run through automated checks — comparing counts, ranges, and chapter linkages
                    against the original source — followed by manual review of anything the checks flag.
                </li>
                <li>
                    <strong>Honest Flagging</strong>
                    Where something can't be verified or is missing in the source itself, we mark it clearly on the
                    page instead of hiding the gap or guessing.
                </li>
            </ol>

            <h4 class="heading-font mb-3">Found an Error?</h4>
            <p class="text-muted">
                Mistakes can still happen despite our checks. If you notice any inaccuracy in the Quran text,
                translation, Tafsir, Hadith, or story content, please
                <a href="mailto:your-email@example.com" style="color:var(--gold-dark)">let us know</a>.
                Every report is treated as a priority and reviewed against the original source before any
                correction is made — accuracy in presenting the Quran and Islamic knowledge is our highest priority.
            </p>

            <p class="audit-timestamp mt-4">Hadith module last audited: July 2026</p>

        </div>
    </section>
@endsection

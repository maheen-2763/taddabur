<?php

return [
    'stats' => [
        ['number' => '6,236', 'label' => 'Ayahs Verified'],
        ['number' => '30,534', 'label' => 'Total Hadiths'],
        ['number' => '15,427', 'label' => 'Hadiths Classified'],
        ['number' => '345', 'label' => 'Hadith Chapters Audited'],
        ['number' => '6', 'label' => 'Hadith Collections'],
        ['number' => '25', 'label' => 'Prophet Stories'],
        ['number' => '4', 'label' => 'Scholars (Four Imams)'],
    ],

    'sources' => [
        [
            'tag' => 'Quran Text & Translations',
            'title' => 'Quran.com API',
            'description' => 'Arabic text and translations (Sahih International, Pickthall, Yusuf Ali, T. Usmani, Fateh Muhammad Jalandhari) sourced from the Quran.com API.',
        ],
        [
            'tag' => 'Tafsir',
            'title' => 'Ibn Kathir, Al-Jalalayn, al-Muyassar',
            'description' => 'Ayah-by-ayah explanations from classical scholarly Tafsir works, including an Urdu translation of Ibn Kathir.',
        ],
        [
            'tag' => 'Audio Recitation',
            'title' => '12 Reciters — Verified & Approximate Sync',
            'reciters_verified' => [
                'Mishary Rashid Alafasy',
                'Saud Al-Shuraim',
                'Muhammad Siddiq Al-Minshawi',
                'Hani Ar-Rifai',
                'Abdul Basit Abdul Samad',
                'Mahmoud Khalil Al-Husary',
            ],
            'reciters_approximate' => [
                'Ali Al-Hudhaify',
                'Maher Al Muaiqly',
                'Yasser Al-Dosari',
                'Abdur-Rahman As-Sudais',
                'Saad Al-Ghamdi',
                'Abdullah Basfar',
            ],
            'description' => 'Word-by-word audio sync uses verified timing data where available. For reciters without verified data, we use an approximate sync — clearly labeled in the app — rather than presenting it as exact. Additional reciters will move to verified sync as accurate timing data becomes available.',
        ],

        [
            'tag' => 'Hadith',
            'title' => 'Six Major Collections',
            'description' => "Sahih Bukhari, Sahih Muslim, Muwatta Malik, Abu Dawud, Jami' at-Tirmidhi, and Sunan Ibn Majah — English translations sourced via the Fawaz Ahmed Hadith API, using the standard published translation for each collection.",
        ],
        [
            'tag' => 'Prophet Stories',
            'title' => 'Quran-grounded narratives',
            'description' => '25 Prophet stories across 101 chapters — every story is built from explicit Quranic statements first; supplementary details from Sahih Bukhari/Muslim are clearly marked where used.',
        ],
        [
            'tag' => 'Four Imams',
            'title' => 'Founders of the Four Madhabs',
            'description' => "Biography, teachings, students, and notable works of Imam Abu Hanifa, Imam Malik, Imam Shafi'i, and Imam Ahmad ibn Hanbal — drawn from established scholarly sources.",
        ],
        [
            'tag' => 'Names of the Prophet ﷺ',
            'title' => "Qur'an & Sahih Hadith Only",
            'description' => 'Every name and title is drawn directly from explicit Quranic verses or Sahih Bukhari — no external compilation books are used.',
        ],

        [
            'tag' => 'Privacy',
            'title' => 'Your Privacy',
            'description' => 'We collect only what is needed for your reading progress — no payment information is ever requested or stored, because none is needed.',
        ],
    ],

    'accuracy' => [
        'Quran Integrity' => [
            [
                'tag' => 'Translation Coverage',
                'title' => 'Every Ayah, Verified',
                'description' => 'All 5 translations are checked against the complete 6,236-ayah count of the Quran to confirm no verse is missing or mismatched.',
            ],
            [
                'tag' => 'Word-by-Word Audio Sync',
                'title' => 'Verified Reciter by Reciter',
                'description' => 'Each reciter\'s word-timing data is individually checked for accuracy before being marked as "verified." Where a reciter\'s timing data has known issues, we clearly label it as an approximate sync.',
            ],
            [
                'tag' => 'Arabic Text Integrity',
                'title' => 'Careful Text Handling',
                'description' => 'Arabic script — including diacritics and letter variants — is handled with dedicated normalization to prevent silent text errors during search or reference lookup.',
            ],
            [
                'tag' => 'Ayah Presentation',
                'title' => 'Correct Bismillah Placement',
                'description' => "Bismillah is displayed once at the start of each Surah, matching how it appears in the mus'haf — never duplicated within the first ayah's text.",
            ],
        ],
        'Hadith Integrity' => [
            [
                'tag' => 'Hadith Grading',
                'title' => 'Standardized Classification',
                'description' => "Grade labels (Sahih, Da'if, Hasan, etc.) from source data are normalized into a single consistent system, so grading is never ambiguous across collections.",
            ],
            [
                'tag' => 'Chapter Completeness',
                'title' => 'Every Chapter, Checked',
                'description' => 'All 345 chapters across 6 collections are individually verified so hadiths are always linked to the correct chapter — not just counted, but checked against their source range.',
            ],
            [
                'tag' => 'Translation Availability',
                'title' => 'Nothing Shown as Blank',
                'description' => "Where an English translation isn't available for a specific hadith in the source data, it's clearly flagged on the page — never shown as an empty or broken entry.",
            ],
            [
                'tag' => 'Ongoing Review',
                'title' => 'Some Grades Pending Verification',
                'description' => 'A small number of hadith grade entries are still under manual scholarly review. These are clearly marked wherever they appear, rather than shown with false confidence.',
            ],
        ],
        'Tafsir Integrity' => [
            [
                'tag' => 'Tafsir Sources',
                'title' => 'Verified Against Official Records',
                'description' => 'Tafsir texts are matched against official scholarly resource IDs, not just names, to ensure the correct commentary is always shown for the correct scholar.',
            ],
        ],
        'Daily Ayah Integrity' => [
            [
                'tag' => 'Revelation Order Source',
                'title' => 'Traditional Chronological Order',
                'description' => "The Daily Ayah follows the traditional order of revelation (Tarteeb-e-Nuzool), based on the narration of Ibn Abbas as documented in Imam Suyuti's Al-Itqan, and used by institutions such as Al-Azhar.",
            ],
            [
                'tag' => 'Translation Consistency',
                'title' => 'Your Selected Translation, Everywhere',
                'description' => 'The Daily Ayah widget, search results, and reader all resolve to your preferred translation from a single saved setting — never silently defaulting to a different one across pages.',
            ],


        ],
    ],



    'why_free' => [
        'title' => 'Why Everything Here Is Free',
        'text' => 'Taddabur does not run on subscriptions or ads. It exists to make authentic Quranic knowledge accessible to every Muslim, regardless of their means — the only requirement is a sincere intention to learn.',
    ],



    'honesty_quote' => [
        'text' => 'Truthfulness leads to righteousness, and righteousness leads to Paradise. And a man keeps on telling the truth until he becomes a truthful person.',
        'narrator' => 'Prophet Muhammad ﷺ, narrated by Abdullah bin Mas\'ud',
        'hadith_collection' => 'bukhari',
        'hadith_chapter' => 78,
        'hadith_highlight_id' => 6118,
        'hadith_label' => 'Sahih Bukhari, Hadith 6094',
    ],

    'audit_timestamp' => 'App bugs last audited: August 2026',
];

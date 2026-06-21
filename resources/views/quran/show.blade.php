{{-- resources/views/quran/show.blade.php --}}
{{--
    ARCHITECTURE:
    CSS  → public/css/quran-show.css
    JS   → public/js/quran-show.js
    Data → window.QURAN_CONFIG (set at bottom of this file)

    BISMILLAH RULES:
    Surah 1  → Bismillah IS ayah 1 → show IN ayahs, not at top
    Surah 9  → No Bismillah at all
    Others   → Show Bismillah at top, strip from ayah 1 text
--}}

@extends('layouts.app')
@section('title', $surah->number . '. ' . $surah->name_transliteration . ' — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/quran-show.css') }}">
@endpush

@php
    $showBismillahTop = !in_array($surah->number, [1, 9]);

    $lastAyahNumber = auth()->check()
        ? ($quranProgress?->lastAyah?->surah_id === $surah->id
            ? $quranProgress->lastAyah->number
            : null)
        : null;

    // ✅ Add this here too
    $userNotesForJs = ($userNotes ?? collect())->map(function ($note) {
        return [
            'id' => $note->id,
            'title' => $note->title,
            'content' => $note->content,
        ];
    });
@endphp

@section('content')

    {{-- ════════════════════════════════
     TOOLBAR
════════════════════════════════ --}}
    <div class="reader-toolbar" id="readerToolbar">
        <div class="container-fluid px-3 px-md-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                {{-- Left --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('quran.index') }}" class="btn btn-sm" style="border:1px solid var(--border)">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">Surahs</span>
                    </a>
                    <div>
                        <span class="heading-font" style="font-size:0.88rem">
                            {{ $surah->number }}. {{ $surah->name_transliteration }}
                        </span>
                        <small class="text-muted ms-2 d-none d-md-inline">
                            {{ $surah->ayah_count }} verses
                        </small>
                    </div>
                </div>

                {{-- Right: All dropdowns same class --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">

                    {{-- Search --}}
                    <a href="{{ route('quran.search') }}" class="btn btn-sm" style="border:1px solid var(--border)"
                        title="Search Quran">
                        <i class="bi bi-search"></i>
                    </a>

                    <a href="{{ route('quran.sajdas') }}" class="btn btn-sm" style="border:1px solid var(--border)"
                        title="Verses of Prostration">
                        ۩
                    </a>

                    {{-- Translation --}}
                    <select id="translationPicker" class="toolbar-select" onchange="handleTranslationChange(this)">
                        @foreach ($translations as $t)
                            <option value="{{ $t->slug }}" data-free="{{ $t->is_free ? '1' : '0' }}"
                                {{ $translation?->slug === $t->slug ? 'selected' : '' }}>
                                {{ $t->name }}
                                {{ !$t->is_free && !$isPremium ? '🔒' : '' }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Tafsir --}}
                    @auth
                        @if ($tafsirs->count() > 0)
                            <select id="tafsirPicker" class="toolbar-select" onchange="changeActiveTafsir(this.value)">
                                <option value="">📖 Tafsir</option>
                                @foreach ($tafsirs as $t)
                                    <option value="{{ $t->slug }}">
                                        {{ $t->name }}
                                        {{ !$isPremium ? '🔒' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    @endauth

                    {{-- Reciter --}}
                    @auth
                        @if ($reciters->count() > 0)
                            <select id="reciterPicker" class="toolbar-select" onchange="handleReciterChange(this)">
                                <option value="">🎙 Reciter</option>
                                @foreach ($reciters as $r)
                                    <option value="{{ $r->slug }}" data-free="{{ $r->is_free ? '1' : '0' }}">
                                        {{ $r->name }}
                                        {{ !$r->is_free && !$isPremium ? '🔒' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    @endauth

                    {{-- Prev / Next --}}
                    <div class="btn-group btn-group-sm">
                        @if ($prevSurah)
                            <a href="{{ route('quran.show', $prevSurah->number) }}" class="btn"
                                style="border:1px solid var(--border)" title="{{ $prevSurah->name_transliteration }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif
                        @if ($nextSurah)
                            <a href="{{ route('quran.show', $nextSurah->number) }}" class="btn"
                                style="border:1px solid var(--border)" title="{{ $nextSurah->name_transliteration }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════
     MAIN LAYOUT
════════════════════════════════ --}}
    <div class="quran-layout">

        {{-- ── SIDEBAR ─────────────────── --}}
        <aside class="quran-sidebar" id="quranSidebar">

            {{-- Surah name --}}
            <div class="px-3 pt-3 pb-2 text-center">
                <div
                    style="font-family:'Scheherazade New','Amiri',serif;
                        font-size:1.3rem;
                        color:var(--emerald);
                        direction:rtl">
                    {{ $surah->name_arabic }}
                </div>
                <small class="text-muted" style="font-size:0.7rem">
                    {{ $surah->name_english }} · {{ ucfirst($surah->revelation_type) }}
                </small>
            </div>

            <hr style="border-color:var(--border); margin:0">

            {{-- Jump input --}}
            <div class="px-3 py-2">
                <p class="sidebar-title">Jump to Ayah</p>
                <input type="number" id="jumpAyahInput" class="sidebar-jump-input" placeholder="e.g. 255" min="1"
                    max="{{ $surah->ayah_count }}" onkeydown="if(event.key==='Enter') jumpToAyah(this.value)">
            </div>

            <hr style="border-color:var(--border); margin:0">

            <p class="sidebar-title" style="padding-top:0.75rem">
                Ayahs ({{ $surah->ayah_count }})
            </p>

            {{-- ✅ PERFORMANCE: Only render sidebar links, not full content --}}
            <div id="sidebarList">
                @foreach ($ayahs as $ayah)
                    <div class="sidebar-item" id="sidebar-{{ $ayah->number }}"
                        onclick="jumpFromSidebar({{ $ayah->number }})">
                        <span class="sidebar-item-num">{{ $ayah->number }}</span>
                        <span class="sidebar-item-text">
                            {{ Str::limit($ayah->text_arabic, 15) }}
                        </span>
                    </div>
                @endforeach
            </div>

        </aside>

        {{-- ── CONTENT ──────────────────── --}}
        <div class="quran-content">

            {{-- Surah Header --}}
            <div class="surah-header">
                <span class="surah-name-arabic">
                    {{ $surah->name_arabic }}
                </span>
                <span class="surah-name-latin">
                    {{ $surah->name_transliteration }} — {{ $surah->name_english }}
                </span>

                {{-- Badges --}}
                <div class="d-flex justify-content-center gap-2 flex-wrap mt-1" id="surahBadges">
                    <span class="badge"
                        style="background:{{ $surah->revelation_type === 'meccan' ? 'var(--gold)' : 'var(--emerald-light)' }};
                             color:{{ $surah->revelation_type === 'meccan' ? '#1A1A2E' : 'white' }}">
                        {{ ucfirst($surah->revelation_type) }}
                    </span>
                    <span class="badge bg-secondary">
                        {{ $surah->ayah_count }} Ayahs
                    </span>
                    <span class="badge bg-secondary">
                        Juz {{ $ayahs->first()?->juz ?? '—' }}
                    </span>
                    @auth
                        @if ($isSurahCompleted)
                            <span class="badge" style="background:var(--emerald); color:white">
                                <i class="bi bi-check-circle-fill me-1"></i>Completed
                            </span>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- ✅ BISMILLAH — Only for surahs other than 1 and 9 --}}
            @if ($showBismillahTop)
                <div class="bismillah-section">
                    <span class="bismillah-arabic">
                        بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                    </span>
                    <span class="bismillah-meaning">
                        In the name of Allah, the Most Gracious, the Most Merciful
                    </span>
                </div>
            @endif
            {{-- Resume Banner --}}
            @auth
                @if ($lastAyahNumber && !$isSurahCompleted)
                    <div class="resume-banner" id="lastReadBanner">
                        <span id="readCountText" style="font-size:0.85rem">
                            <i class="bi bi-bookmark-fill me-2" style="color:var(--gold)"></i>
                            {{ $readAyahsCount ?? 0 }} of {{ $surah->ayah_count }} ayahs read in this Surah
                        </span>

                        <div class="d-flex gap-2 flex-shrink-0">
                            <a href="#ayah-1" class="btn btn-sm btn-outline-secondary" style="font-size:0.76rem"
                                onclick="hideBanner(); scrollToAyah(1); flashHighlightAyah(1)">
                                Start from Beginning
                            </a>
                            <a href="#ayah-{{ $lastAyahNumber }}" class="btn btn-sm" id="continueBtn"
                                style="background:var(--gold); color:#1A1A2E; border:none; font-size:0.76rem"
                                onclick="hideBanner(); scrollToAyah({{ $lastAyahNumber }}); flashHighlightAyah({{ $lastAyahNumber }})">
                                Continue from Ayah {{ $lastAyahNumber }}
                            </a>
                        </div>
                    </div>
                @endif
            @endauth

            {{-- Upgrade Banner --}}
            @auth
                @if (!$isPremium)
                    <div class="upgrade-banner">
                        <span>
                            <i class="bi bi-stars me-2" style="color:rgba(255,255,255,0.7)"></i>
                            Unlock all translations, tafsir & reciters
                        </span>
                        <a href="{{ route('subscription.upgrade') }}" class="btn btn-sm"
                            style="background:var(--gold); color:#1A1A2E; border:none; font-size:0.78rem">
                            Upgrade
                        </a>
                    </div>
                @endif
            @endauth

            {{-- ════════════════════════════
             AYAHS LOOP
             ✅ Performance fixes:
             - No word spans by default
             - Words wrapped only when needed
             - Bismillah stripped from ayah 1
        ════════════════════════════ --}}
            <div id="ayahList">

                @foreach ($ayahs as $ayah)
                    @php
                        /*
            |----------------------------------------------
            | BISMILLAH STRIP LOGIC
            |----------------------------------------------
            | For surahs 2-114 (except 9), ayah 1 in the
            | database sometimes starts with Bismillah text
            | We show Bismillah separately at top so we
            | strip it from ayah 1 text here
            */
                        $ayahText = $ayah->text_arabic;

                        if ($showBismillahTop && $ayah->number === 1) {
                            $ayahText = \App\Helpers\ArabicHelper::stripBismillah($ayahText);
                        }

                    @endphp

                    <div class="ayah-card
            {{ in_array($ayah->id, $readAyahIds ?? []) ? 'marked-read' : '' }}
            {{ $ayah->number === $lastAyahNumber ? 'last-read' : '' }}"
                        id="ayah-{{ $ayah->number }}" data-ayah-id="{{ $ayah->id }}"
                        data-ayah-number="{{ $ayah->number }}" data-ayah-text="{{ addslashes($ayahText) }}">

                        <div class="d-flex gap-3 align-items-start">

                            {{-- Ayah number badge + tooltip --}}
                            <div class="ayah-num-wrap">
                                <div class="ayah-num-badge">
                                    {{ $ayah->number }}
                                </div>
                                {{-- ✅ Checkmark — shows when this ayah is marked read --}}
                                <span class="ayah-checkmark">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                                <div class="ayah-tooltip">
                                    {{ $surah->name_transliteration }}
                                    {{ $surah->number }}:{{ $ayah->number }}
                                    @if ($ayah->sajda)
                                        · Sajda
                                    @endif
                                </div>
                            </div>

                            <div class="flex-grow-1">

                                {{-- ✅ Arabic text
                         Words are NOT wrapped in spans here for performance
                         They are wrapped in JS only when audio plays- --}}
                                <p class="ayah-arabic-text mb-0" id="arabic-{{ $ayah->id }}">
                                    {{ $ayahText }}<span class="ayah-end-ornament">
                                        &#xFD3F;{{ \App\Helpers\ArabicHelper::toEasternArabic($ayah->number) }}&#xFD3E;</span>
                                    @if ($ayah->sajda)
                                        <span title="Sajda" style="color:var(--gold)"> ۩</span>
                                    @endif
                                </p>

                                {{-- Translation --}}
                                @if ($ayah->translations->isNotEmpty())
                                    <p class="ayah-translation">
                                        {{ $ayah->translations->first()->text }}
                                    </p>
                                @else
                                    <p class="ayah-translation" style="color:var(--muted); font-size:0.8rem">
                                        Translation not available.
                                    </p>
                                @endif

                                {{-- Action buttons --}}
                                <div class="ayah-actions">

                                    {{-- Bookmark --}}
                                    @auth
                                        <button class="ayah-btn" data-ayah-id="{{ $ayah->id }}"
                                            onclick="toggleBookmark(this, {{ $ayah->id }})">
                                            <i class="bi bi-bookmark"></i> Bookmark
                                        </button>
                                    @endauth

                                    {{-- Tafsir → Opens dedicated page --}}
                                    @auth
                                        @if ($isPremium)
                                            <a href="{{ route('quran.tafsir', [$surah->number, $ayah->id]) }}"
                                                class="ayah-btn">
                                                <i class="bi bi-book"></i> Tafsir
                                            </a>
                                        @else
                                            <button class="ayah-btn position-relative" onclick="redirectToUpgrade('Tafsir')">
                                                <i class="bi bi-book"></i> Tafsir
                                                <span class="lock-icon">🔒</span>
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="ayah-btn">
                                            <i class="bi bi-book"></i> Tafsir
                                        </a>
                                    @endauth

                                    {{-- Audio --}}
                                    @auth
                                        <button class="ayah-btn" id="audio-btn-{{ $ayah->id }}"
                                            onclick="playAudio(
                                        {{ $surah->number }},
                                        {{ $ayah->number }},
                                        {{ $ayah->id }},
                                        this
                                    )">
                                            <i class="bi bi-play-circle"></i> Listen
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="ayah-btn">
                                            <i class="bi bi-play-circle"></i> Listen
                                        </a>
                                    @endauth

                                    {{-- Copy --}}
                                    <button class="ayah-btn"
                                        onclick="copyText(this.closest('.ayah-card').dataset.ayahText, this)">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>

                                    {{-- Share --}}
                                    <button class="ayah-btn"
                                        onclick="shareAyah({{ $surah->number }}, {{ $ayah->number }}, this)">
                                        <i class="bi bi-share"></i> Share
                                    </button>


                                    {{-- Mark as Read — explicit, one-way confirmation --}}
                                    @auth
                                        @php $isRead = in_array($ayah->id, $readAyahIds ?? []); @endphp
                                        <button class="ayah-btn {{ $isRead ? 'marked-read' : '' }}"
                                            id="read-btn-{{ $ayah->id }}"
                                            onclick="markAsRead(this, {{ $ayah->id }})" {{ $isRead ? 'disabled' : '' }}>
                                            <i class="bi {{ $isRead ? 'bi-check-circle-fill' : 'bi-check-circle' }}"></i>
                                            <span class="d-none d-sm-inline">{{ $isRead ? ' Read' : ' Mark as Read' }}</span>
                                        </button>
                                    @endauth
                                    {{-- Note — personal reflection on this ayah --}}
                                    @auth
                                        @if ($isPremium)
                                            <button class="ayah-btn {{ isset($userNotes[$ayah->id]) ? 'has-note' : '' }}"
                                                id="note-btn-{{ $ayah->id }}"
                                                onclick="toggleNoteEditor(this, {{ $ayah->id }})">
                                                <i class="bi bi-pencil-square"></i>
                                                <span class="d-none d-sm-inline">
                                                    {{ isset($userNotes[$ayah->id]) ? ' Note' : ' Add Note' }}
                                                </span>
                                            </button>
                                        @else
                                            <button class="ayah-btn position-relative" onclick="redirectToUpgrade('Notes')">
                                                <i class="bi bi-pencil-square"></i>
                                                <span class="d-none d-sm-inline"> Note</span>
                                                <span class="lock-icon">🔒</span>
                                            </button>
                                        @endif
                                    @endauth

                                </div>
                                {{-- Note Banner — personal reflection editor --}}
                                @auth
                                    <div class="note-banner" id="note-{{ $ayah->id }}">
                                        <div class="note-inner">
                                            <div class="note-head">
                                                <strong>Your Note</strong>
                                                <button class="note-close"
                                                    onclick="closeNoteEditor({{ $ayah->id }})">&times;</button>
                                            </div>
                                            <input type="text" class="note-title-input"
                                                id="note-title-{{ $ayah->id }}" placeholder="Optional title..."
                                                maxlength="255">
                                            <textarea class="note-content-input" id="note-content-{{ $ayah->id }}" rows="3"
                                                placeholder="Write your reflection on this ayah..."></textarea>
                                            <div class="note-actions">
                                                <button class="note-delete-btn" id="note-delete-{{ $ayah->id }}"
                                                    onclick="deleteNote({{ $ayah->id }})" style="display:none">
                                                    Delete
                                                </button>
                                                <button class="note-save-btn"
                                                    onclick="saveNote({{ $ayah->id }}, {{ $surah->number }})">
                                                    Save Note
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Note Banner — personal reflection editor --}}
            @auth
                <div class="note-banner" id="note-{{ $ayah->id }}">
                    <div class="note-inner">
                        <div class="note-head">
                            <strong>Your Note</strong>
                            <button class="note-close" onclick="closeNoteEditor({{ $ayah->id }})">&times;</button>
                        </div>
                        <input type="text" class="note-title-input" id="note-title-{{ $ayah->id }}"
                            placeholder="Optional title..." maxlength="255">
                        <textarea class="note-content-input" id="note-content-{{ $ayah->id }}" rows="3"
                            placeholder="Write your reflection on this ayah..."></textarea>
                        <div class="note-actions">
                            <button class="note-delete-btn" id="note-delete-{{ $ayah->id }}"
                                onclick="deleteNote({{ $ayah->id }})" style="display:none">
                                Delete
                            </button>
                            <button class="note-save-btn" onclick="saveNote({{ $ayah->id }}, {{ $surah->number }})">
                                Save Note
                            </button>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Bottom Navigation --}}
            <div class="d-flex justify-content-between mt-5 pt-3" style="border-top:1px solid var(--border)">
                @if ($prevSurah)
                    <a href="{{ route('quran.show', $prevSurah->number) }}" class="btn-emerald btn">
                        <i class="bi bi-arrow-left me-2"></i>
                        {{ $prevSurah->name_transliteration }}
                    </a>
                @else
                    <div></div>
                @endif
                @if ($nextSurah)
                    <a href="{{ route('quran.show', $nextSurah->number) }}" class="btn-emerald btn">
                        {{ $nextSurah->name_transliteration }}
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @endif
            </div>

        </div>
    </div>

    {{-- Scroll to top --}}
    <button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})"
        title="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    {{-- Audio Player --}}
    <div id="audioPlayer">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-music-note-beamed" style="color:var(--gold-light)"></i>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span id="audioLabel" style="color:white; font-size:0.82rem">
                            Loading...
                        </span>
                        <span id="audioTime" style="color:rgba(255,255,255,0.45); font-size:0.72rem">
                        </span>
                    </div>
                    <div class="audio-bar" onclick="seekAudio(event)">
                        <div class="audio-bar-fill" id="audioBarFill"></div>
                    </div>
                </div>
                <button onclick="stopAudio()"
                    style="background:none; border:none;
                           color:rgba(255,255,255,0.55);
                           font-size:1.3rem; cursor:pointer">
                    &times;
                </button>
            </div>
            <audio id="audioElement" style="display:none" onended="onAudioEnded()" ontimeupdate="onAudioTimeUpdate()"
                onloadedmetadata="onAudioLoaded()">
            </audio>
        </div>
    </div>

    {{-- Completion Modal --}}
    <div class="modal fade" id="completionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius:var(--radius-lg);
                    border:2px solid var(--gold);
                    background:var(--cream)">
                <div class="modal-body text-center p-4">

                    <div style="font-size:2.8rem; margin-bottom:0.75rem">🌟</div>

                    <h3 class="heading-font mb-1" style="color:var(--emerald)">
                        MashaAllah!
                    </h3>

                    <p class="text-muted mb-3" style="font-size:0.88rem">
                        You completed
                        <strong>Surah {{ $surah->name_transliteration }}</strong>
                    </p>

                    <div class="p-3 mb-3"
                        style="background:rgba(27,94,59,0.06);
                            border-radius:var(--radius);
                            border-left:3px solid var(--gold)">
                        <p
                            style="font-family:'Scheherazade New','Amiri',serif;
                               font-size:1.3rem; color:var(--emerald);
                               direction:rtl; line-height:2; margin-bottom:0.25rem">
                            إِنَّ ٱلَّذِينَ يَتْلُونَ كِتَـٰبَ ٱللَّهِ وَأَقَامُوا۟ ٱلصَّلَوٰةَ
                        </p>
                        <p class="text-muted mb-0" style="font-size:0.8rem; font-style:italic">
                            "Indeed, those who recite the Book of Allah
                            and establish prayer..."
                        </p>
                        <small class="text-muted">— Fatir 35:29</small>
                    </div>

                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        @if ($nextSurah)
                            <button class="btn-emerald btn"
                                onclick="closeModalAndGo('{{ route('quran.show', $nextSurah->number) }}')">
                                <i class="bi bi-arrow-right me-1"></i>
                                {{ $nextSurah->name_transliteration }}
                            </button>
                        @endif
                        <button class="btn btn-light" onclick="closeModalAndGo('{{ route('quran.index') }}')">
                            <i class="bi bi-grid me-1"></i>All Surahs
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <button class="mobile-jump-btn d-md-none" id="mobileSidebarBtn" onclick="toggleMobileSidebar()"
        title="Jump to Ayah">
        <i class="bi bi-list-ol"></i>
    </button>

    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()">
    </div>
@endsection

@push('scripts')
    {{-- ✅ Pass all PHP data to JS in one clean object --}}
    <script>
        window.QURAN_CONFIG = {
            surahNumber: {{ $surah->number }},
            totalAyahs: {{ $surah->ayah_count }},
            isSurahCompleted: {{ $isSurahCompleted ? 'true' : 'false' }},
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            isPremium: {{ $isPremium ? 'true' : 'false' }},
            upgradeUrl: '{{ route('subscription.upgrade') }}',
            freeTranslationSlug: '{{ $translations->where('is_free', true)->first()?->slug ?? 'sahih-international' }}',
            lastAyahNumber: {{ $lastAyahNumber ?? 'null' }},
        };
        window.USER_NOTES = @json($userNotesForJs);
    </script>
    <script src="{{ asset('js/quran-show.js') }}"></script>
@endpush

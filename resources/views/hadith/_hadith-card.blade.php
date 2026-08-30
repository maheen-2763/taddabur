<div class="hadith-card" id="hadith-{{ $h->id }}" data-hadith-id="{{ $h->id }}">

    @php $gradeInfo = \App\Support\HadithGradeParser::parse($h->grade); @endphp

    <div class="hadith-card-header">
        <span class="hadith-number-badge">{{ $h->id }}</span>

        <span class="hadith-header-badges">
            @if ($gradeInfo['isnad_type'])
                <span class="isnad-type-badge" title="Isnad type — see the glossary above">
                    {{ $gradeInfo['isnad_type'] }}
                </span>
            @endif

            @if ($gradeInfo['label'])
                <span class="grade-badge {{ $gradeInfo['css_class'] }}" title="Full grade: {{ $h->grade }}">
                    {{ $gradeInfo['label'] }}
                </span>
            @elseif (!$gradeInfo['isnad_type'] && $h->grade)
                {{-- ✅ Sirf tab dikhao jab isnad-type BHI nahi mila —
             warna "Maqtu" jaisa case do baar dikh jayega --}}
                <span class="grade-badge grade-other" title="{{ $h->grade }}">
                    {{ $h->grade }}
                </span>
            @endif

            @if ($h->needs_review)
                <span class="review-flag" title="This grade is pending scholarly verification">⚠ Under Review</span>
            @endif
        </span>

    </div>

    {{-- // Required columns from parent query: id, number, arabic, english, grade, grade_source, needs_review,
    translation_incomplete, chapter_id (for highlight links) --}}

    @if ($h->translation_incomplete)
        <div class="hadith-incomplete-notice">
            <i class="bi bi-exclamation-triangle"></i>
            Translation not available for this hadith yet.
        </div>
    @endif

    <div class="hadith-arabic-zone">
        <p class="hadith-arabic" dir="rtl">{!! $h->arabic !!}</p>
    </div>

    <div class="hadith-divider"></div>

    <p class="hadith-english">{!! $h->english !!}</p>

    <div class="hadith-reference-line">
        {{ $collectionName ?? '' }} › {{ $chapterTitle ?? '' }} › Hadith# {{ $h->number }}
    </div>

    <div class="hadith-actions mt-2">

        @auth
            <button class="ayah-btn {{ $isBookmarked ? 'bookmarked' : '' }}" id="hadith-bookmark-{{ $h->id }}"
                title="{{ $isBookmarked ? 'Remove Bookmark' : 'Bookmark' }}"
                onclick="toggleHadithBookmark(this, {{ $h->id }})">
                <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                <span class="d-none d-sm-inline">{{ $isBookmarked ? ' Bookmarked' : ' Bookmark' }}</span>
            </button>
        @endauth

        <button class="ayah-btn" title="Copy" onclick="copyHadithText(this, {{ $h->id }})">
            <i class="bi bi-clipboard"></i> <span class="d-none d-sm-inline">Copy</span>
        </button>

        <button class="ayah-btn" title="Share"
            onclick="shareHadith('{{ $collectionSlug ?? $h->collection->slug }}', {{ $h->id }}, this)">
            <i class="bi bi-share"></i> <span class="d-none d-sm-inline">Share</span>
        </button>

        @auth
            <button class="ayah-btn {{ $hasNote ? 'has-note' : '' }}" id="hadith-note-btn-{{ $h->id }}"
                title="{{ $hasNote ? 'Edit Note' : 'Add Note' }}"
                onclick="toggleHadithNoteEditor(this, {{ $h->id }})">
                <i class="bi bi-pencil-square"></i>
                <span class="d-none d-sm-inline">{{ $hasNote ? ' Has Note' : ' Add Note' }}</span>
            </button>

            <button class="ayah-btn js-mark-read {{ $isRead ?? false ? 'is-read' : '' }}"
                id="hadith-read-btn-{{ $h->id }}"
                title="{{ $isRead ?? false ? 'Marked as Read' : 'Mark as Read' }}"
                onclick="toggleHadithRead(this, {{ $h->id }})">
                <i class="bi bi-check-circle{{ $isRead ?? false ? '-fill' : '' }}"></i>
                <span class="d-none d-sm-inline">{{ $isRead ?? false ? ' Read' : ' Mark as Read' }}</span>
            </button>
        @else
            <span class="ayah-btn" title="Sign in to track progress"
                style="opacity:0.7; cursor:default; font-style:italic;">
                <i class="bi bi-box-arrow-in-right"></i>
                <span class="d-none d-sm-inline"> Sign in to track progress</span>
            </span>
        @endauth

    </div>

    @auth
        <div class="note-banner" id="hadith-note-{{ $h->id }}">
            <div class="note-inner">
                <div class="note-head">
                    <strong>Your Note</strong>
                    <button class="note-close" onclick="closeHadithNoteEditor({{ $h->id }})">&times;</button>
                </div>
                <input type="text" class="note-title-input" id="hadith-note-title-{{ $h->id }}"
                    placeholder="Optional title...">
                <textarea class="note-content-input" id="hadith-note-content-{{ $h->id }}" rows="3"
                    placeholder="Write your reflection..."></textarea>
                <div class="note-actions">
                    <button class="note-delete-btn" id="hadith-note-delete-{{ $h->id }}"
                        onclick="deleteHadithNote({{ $h->id }})"
                        style="{{ $hasNote ? '' : 'display:none' }}">Delete</button>
                    <button class="note-save-btn" onclick="saveHadithNote({{ $h->id }})">Save Note</button>
                </div>
            </div>
        </div>
    @endauth

</div>

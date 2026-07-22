<div class="hadith-card" id="hadith-{{ $h->id }}" data-hadith-id="{{ $h->id }}">

    <div class="hadith-number-badge">{{ $h->id }}</div>

    <p class="hadith-arabic" dir="rtl">{{ $h->arabic }}</p>

    <p class="hadith-english">{{ $h->english }}</p>

    @if ($h->grade)
        <span class="grade-badge grade-{{ Str::slug($h->grade) }}">{{ $h->grade }}</span>
    @endif

    @if ($h->needs_review)
        <span class="review-flag" title="This grade is pending scholarly verification">
            ⚠ Under Review
        </span>
    @endif

    <div class="hadith-actions mt-2">

        @auth
            <button class="ayah-btn {{ $isBookmarked ? 'bookmarked' : '' }}" id="hadith-bookmark-{{ $h->id }}"
                onclick="toggleHadithBookmark(this, {{ $h->id }})">
                <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
            </button>
        @endauth

        <button class="ayah-btn" onclick="copyHadithText(this, {{ $h->id }})">
            <i class="bi bi-clipboard"></i> Copy
        </button>

        <button class="ayah-btn"
            onclick="shareHadith('{{ $collectionSlug ?? $h->collection->slug }}', {{ $h->id }}, this)">
            <i class="bi bi-share"></i> Share
        </button>

        @auth
            <button class="ayah-btn {{ $hasNote ? 'has-note' : '' }}" id="hadith-note-btn-{{ $h->id }}"
                onclick="toggleHadithNoteEditor(this, {{ $h->id }})">
                <i class="bi bi-pencil-square"></i>
                {{ $hasNote ? 'Note' : 'Add Note' }}
            </button>

        @endauth
        <button class="ayah-btn js-mark-read {{ $isRead ?? false ? 'bookmarked' : '' }}"
            id="hadith-read-btn-{{ $h->id }}" onclick="toggleHadithRead(this, {{ $h->id }})">
            <i class="bi bi-check-circle{{ $isRead ?? false ? '-fill' : '' }}"></i>
            {{ $isRead ?? false ? 'Read' : 'Mark as Read' }}
        </button>

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
                        onclick="deleteHadithNote({{ $h->id }})" style="display:none">Delete</button>
                    <button class="note-save-btn" onclick="saveHadithNote({{ $h->id }})">Save Note</button>
                </div>
            </div>
        </div>
    @endauth

</div>

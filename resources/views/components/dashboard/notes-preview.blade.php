{{-- resources/views/components/dashboard/notes-preview.blade.php --}}

<div class="d-card">

    <h5 class="d-card-title">
        <i class="bi bi-pencil-square" style="color:var(--emerald-light)"></i>
        Your Reflections
    </h5>

    @if ($notes && $notes->isNotEmpty())

        @foreach ($notes as $note)
            <div class="d-story-item">
                @if ($note->reference_url)
                    <a href="{{ $note->reference_url }}" class="d-note-link">
                        <div class="d-story-title" style="font-size:0.85rem">
                            <i class="bi {{ $note->reference_icon }}"></i>
                            {{ $note->reference_label }}
                        </div>

                        <small class="d-story-meta" style="display:block; margin-bottom:0.2rem">
                            {{ Str::limit($note->content, 90) }}
                        </small>

                        <small class="d-story-meta">
                            {{ $note->updated_at->diffForHumans() }}
                        </small>
                    </a>
                @else
                    {{-- Story notes ya jinke URL nahi banta unke liye non-clickable fallback --}}
                    <div class="d-note-link">
                        <div class="d-story-title" style="font-size:0.85rem">
                            <i class="bi {{ $note->reference_icon }}"></i>
                            {{ $note->reference_label }}
                        </div>
                        <small class="d-story-meta" style="display:block; margin-bottom:0.2rem">
                            {{ Str::limit($note->content, 90) }}
                        </small>
                        <small class="d-story-meta">
                            {{ $note->updated_at->diffForHumans() }}
                        </small>
                    </div>
                @endif
            </div>
        @endforeach

        <a href="{{ route('notes.index') }}" class="d-explore-allah-names-link">
            View All Notes <i class="bi bi-arrow-right"></i>
        </a>
    @else
        <div class="d-empty">
            <i class="bi bi-pencil-square d-empty-icon"></i>
            <p class="d-empty-message">
                Add your first reflection on any Ayah, Hadith while reading —
                your thoughts are saved privately to your journey.
            </p>
            <a href="{{ route('quran.index') }}" class="btn-emerald btn btn-sm">
                Start Reading
            </a>
        </div>
    @endif

</div>

{{-- resources/views/components/dashboard/notes-preview.blade.php --}}

<div class="d-card">

    <h5 class="d-card-title">
        <i class="bi bi-pencil-square" style="color:var(--emerald)"></i>
        Your Reflections
    </h5>

    @if ($notes && $notes->isNotEmpty())

        @foreach ($notes as $note)
            <div class="d-story-item">
                <a href="{{ route('quran.show', $note->ayah->surah->number) }}#ayah-{{ $note->ayah->number }}"
                    style="text-decoration:none; color:inherit; display:block">

                    <div class="d-story-title" style="font-size:0.85rem">
                        📖 {{ $note->ayah->surah->name_transliteration }}
                        {{ $note->ayah->surah->number }}:{{ $note->ayah->number }}
                    </div>

                    <small class="d-story-meta" style="display:block; margin-bottom:0.2rem">
                        {{ Str::limit($note->content, 90) }}
                    </small>

                    <small class="d-story-meta">
                        {{ $note->updated_at->diffForHumans() }}
                    </small>
                </a>
            </div>
        @endforeach

        <a href="{{ route('notes.index') }}" class="d-explore-allah-names-link">
            View All Notes <i class="bi bi-arrow-right"></i>
        </a>
    @else
        <div class="d-empty">
            <i class="bi bi-pencil-square d-empty-icon"></i>
            <p class="d-empty-message">
                Add your first reflection on any ayah while reading —
                your thoughts are saved privately to your journey.
            </p>
            <a href="{{ route('quran.index') }}" class="btn-emerald btn btn-sm">
                Start Reading
            </a>
        </div>

    @endif

</div>

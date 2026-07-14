@extends('layouts.app')

@section('title', 'My Notes — Taddabur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notes-bookmarks.css') }}">
@endpush

@section('content')
    <div class="container py-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="heading-font mb-1" style="color:var(--emerald-light)">My Notes</h2>
                <p class="mb-0" style="color:var(--muted); font-size:0.9rem">
                    Your personal reflections from Quran and stories.
                </p>
            </div>
            <button class="btn-gold btn" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                <i class="bi bi-plus-lg me-1"></i> New Note
            </button>
        </div>

        {{-- Notes List --}}
        <div id="notes-container">
            @forelse ($notes as $note)
                <div class="card-islamic p-4 mb-3 note-card" data-id="{{ $note->id }}">

                    {{-- Reference badge --}}
                    <span class="badge-note mb-2">
                        <i class="bi {{ $note->reference_icon }} me-1"></i>
                        {{ $note->reference_label }}
                    </span>

                    {{-- Content --}}
                    <p class="note-content mb-0">{{ $note->content }}</p>

                    {{-- Footer --}}
                    <div class="note-footer d-flex justify-content-between align-items-center">
                        <small style="color:var(--muted)">{{ $note->created_at->diffForHumans() }}</small>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary edit-btn" data-id="{{ $note->id }}"
                                data-content="{{ $note->content }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $note->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" id="empty-state" style="color:var(--muted)">
                    <i class="bi bi-journal-plus display-4 d-block mb-3 opacity-50"></i>
                    <h5 class="heading-font" style="color:var(--emerald-light)">No notes yet</h5>
                    <p>Capture your reflections as you read the Quran or prophet stories.</p>
                    <button class="btn-gold btn mt-2" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        Add Your First Note
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($notes->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $notes->links() }}
            </div>
        @endif

    </div>

    {{-- ── Add Note Modal ────────────────────────────────────── --}}
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="background:var(--cream)">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title heading-font" id="addNoteLabel" style="color:var(--emerald-light)">New Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="note-content-input" class="form-control" rows="5" maxlength="2000"
                        placeholder="Write your reflection here..."></textarea>
                    <div class="text-end mt-1">
                        <small style="color:var(--muted)"><span id="add-char-count">0</span>/2000</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-gold btn" id="save-note-btn">
                        Save Note
                        <span id="save-spinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Edit Note Modal ───────────────────────────────────── --}}
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="background:var(--cream)">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title heading-font" style="color:var(--emerald-light)">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-note-id">
                    <textarea id="edit-note-content" class="form-control" rows="5" maxlength="2000"></textarea>
                    <div class="text-end mt-1">
                        <small style="color:var(--muted)"><span id="edit-char-count">0</span>/2000</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-gold btn" id="update-note-btn">
                        Save Changes
                        <span id="update-spinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            document.getElementById('note-content-input').addEventListener('input', function() {
                document.getElementById('add-char-count').textContent = this.value.length;
            });

            document.getElementById('edit-note-content').addEventListener('input', function() {
                document.getElementById('edit-char-count').textContent = this.value.length;
            });

            document.getElementById('save-note-btn').addEventListener('click', async function() {
                const content = document.getElementById('note-content-input').value.trim();
                if (!content) return;

                setBusy('save', true);

                try {
                    const res = await fetch('{{ route('notes.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content
                        }),
                    });
                    const data = await res.json();

                    if (res.ok) {
                        prependNote(data.note);
                        document.getElementById('note-content-input').value = '';
                        document.getElementById('add-char-count').textContent = '0';
                        bootstrap.Modal.getInstance(document.getElementById('addNoteModal')).hide();
                    }
                } catch {
                    alert('Could not save note. Please try again.');
                } finally {
                    setBusy('save', false);
                }
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-btn');
                if (!btn) return;

                document.getElementById('edit-note-id').value = btn.dataset.id;
                document.getElementById('edit-note-content').value = btn.dataset.content;
                document.getElementById('edit-char-count').textContent = btn.dataset.content.length;

                new bootstrap.Modal(document.getElementById('editNoteModal')).show();
            });

            document.getElementById('update-note-btn').addEventListener('click', async function() {
                const id = document.getElementById('edit-note-id').value;
                const content = document.getElementById('edit-note-content').value.trim();
                if (!content) return;

                setBusy('update', true);

                try {
                    const res = await fetch(`/notes/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content
                        }),
                    });
                    const data = await res.json();

                    if (res.ok) {
                        const card = document.querySelector(`.note-card[data-id="${id}"]`);
                        card.querySelector('.note-content').textContent = data.note.content;
                        card.querySelector('.edit-btn').dataset.content = data.note.content;
                        bootstrap.Modal.getInstance(document.getElementById('editNoteModal')).hide();
                    }
                } catch {
                    alert('Could not update note. Please try again.');
                } finally {
                    setBusy('update', false);
                }
            });

            document.addEventListener('click', async function(e) {
                const btn = e.target.closest('.delete-btn');
                if (!btn) return;
                if (!confirm('Delete this note?')) return;

                const id = btn.dataset.id;
                const res = await fetch(`/notes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                });

                if (res.ok) {
                    document.querySelector(`.note-card[data-id="${id}"]`).remove();

                    if (!document.querySelector('.note-card')) {
                        document.getElementById('notes-container').innerHTML = `
                <div class="text-center py-5" id="empty-state" style="color:var(--muted)">
                    <i class="bi bi-journal-plus display-4 d-block mb-3 opacity-50"></i>
                    <h5 class="heading-font" style="color:var(--emerald-light)">No notes yet</h5>
                    <p>Capture your reflections as you read.</p>
                </div>`;
                    }
                }
            });

            function prependNote(note) {
                const emptyState = document.getElementById('empty-state');
                if (emptyState) emptyState.remove();

                const html = `
        <div class="card-islamic p-4 mb-3 note-card" data-id="${note.id}">
            <span class="badge-note mb-2">
                <i class="bi bi-sticky me-1"></i> General Note
            </span>
            <p class="note-content mb-0">${escapeHtml(note.content)}</p>
            <div class="note-footer d-flex justify-content-between align-items-center">
                <small style="color:var(--muted)">Just now</small>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="${note.id}"
                            data-content="${escapeHtml(note.content)}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${note.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
                document.getElementById('notes-container').insertAdjacentHTML('afterbegin', html);
            }

            function setBusy(type, busy) {
                const btn = document.getElementById(`${type}-note-btn`);
                const spinner = document.getElementById(`${type}-spinner`);
                btn.disabled = busy;
                spinner.classList.toggle('d-none', !busy);
            }

            function escapeHtml(str) {
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }
        </script>
    @endpush

@endsection

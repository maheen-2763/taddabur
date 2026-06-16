@extends('layouts.app')

@section('title', 'My Notes — Taddabur')

@section('content')
    <div class="container py-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">My Notes</h2>
                <p class="text-muted mb-0">Your personal reflections from Quran and stories.</p>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                <i class="bi bi-plus-lg me-1"></i> New Note
            </button>
        </div>

        {{-- Notes List --}}
        <div id="notes-container">
            @forelse ($notes as $note)
                <div class="card border-0 shadow-sm mb-3 note-card" data-id="{{ $note->id }}">
                    <div class="card-body">

                        {{-- Reference badge --}}
                        <span class="badge bg-success bg-opacity-10 text-success mb-2">
                            <i class="bi {{ $note->reference_icon }} me-1"></i>
                            {{ $note->reference_label }}
                        </span>

                        {{-- Content --}}
                        <p class="card-text note-content mb-3">{{ $note->content }}</p>

                        {{-- Footer --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
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
                </div>
            @empty
                <div class="text-center py-5 text-muted" id="empty-state">
                    <i class="bi bi-journal-plus display-4 d-block mb-3 opacity-50"></i>
                    <h5>No notes yet</h5>
                    <p>Capture your reflections as you read the Quran or prophet stories.</p>
                    <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addNoteModal">
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="addNoteLabel">New Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="note-content-input" class="form-control" rows="5" maxlength="2000"
                        placeholder="Write your reflection here..."></textarea>
                    <div class="text-end mt-1">
                        <small class="text-muted"><span id="add-char-count">0</span>/2000</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="save-note-btn">
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-note-id">
                    <textarea id="edit-note-content" class="form-control" rows="5" maxlength="2000"></textarea>
                    <div class="text-end mt-1">
                        <small class="text-muted"><span id="edit-char-count">0</span>/2000</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="update-note-btn">
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

            // ── Char counters ──────────────────────────────────────────────────
            document.getElementById('note-content-input').addEventListener('input', function() {
                document.getElementById('add-char-count').textContent = this.value.length;
            });

            document.getElementById('edit-note-content').addEventListener('input', function() {
                document.getElementById('edit-char-count').textContent = this.value.length;
            });

            // ── Save new note ──────────────────────────────────────────────────
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

            // ── Open edit modal ────────────────────────────────────────────────
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.edit-btn');
                if (!btn) return;

                document.getElementById('edit-note-id').value = btn.dataset.id;
                document.getElementById('edit-note-content').value = btn.dataset.content;
                document.getElementById('edit-char-count').textContent = btn.dataset.content.length;

                new bootstrap.Modal(document.getElementById('editNoteModal')).show();
            });

            // ── Update note ────────────────────────────────────────────────────
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

            // ── Delete note ────────────────────────────────────────────────────
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

                    // Show empty state if no notes left
                    if (!document.querySelector('.note-card')) {
                        document.getElementById('notes-container').innerHTML = `
                <div class="text-center py-5 text-muted" id="empty-state">
                    <i class="bi bi-journal-plus display-4 d-block mb-3 opacity-50"></i>
                    <h5>No notes yet</h5>
                    <p>Capture your reflections as you read.</p>
                </div>`;
                    }
                }
            });

            // ── Helpers ────────────────────────────────────────────────────────
            function prependNote(note) {
                const emptyState = document.getElementById('empty-state');
                if (emptyState) emptyState.remove();

                const html = `
        <div class="card border-0 shadow-sm mb-3 note-card" data-id="${note.id}">
            <div class="card-body">
                <span class="badge bg-success bg-opacity-10 text-success mb-2">
                    <i class="bi bi-sticky me-1"></i> General Note
                </span>
                <p class="card-text note-content mb-3">${escapeHtml(note.content)}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Just now</small>
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

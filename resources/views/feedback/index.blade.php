@extends('layouts.app')
@section('title', 'Feedback')
@section('no_global_errors', '1')

@php
    $statusLabels = [
        'open' => ['Open', 'bg-warning text-dark'],
        'in_progress' => ['In progress', 'bg-info text-dark'],
        'resolved' => ['Resolved', 'bg-success'],
    ];
@endphp

@section('content')
<div class="container py-4" style="max-width:820px">
    <h1 class="h4 mb-1"><i class="bi {{ config('icons.feedback') }} me-2"></i>Feedback</h1>
    <p class="text-muted small mb-4">Help us to make Taddabur more better.</p>

    {{-- Rating --}}
    <section class="card mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3"><i class="bi {{ config('icons.rating') }} text-warning me-1"></i>Rate Taddabur</h2>

            <form method="POST" action="{{ route('feedback.review.store') }}" data-once>
                @csrf
                <div class="star-rating mb-1" role="radiogroup" aria-label="Rating">
                    @foreach (range(5, 1) as $n)
                        <input type="radio" name="rating" id="rating-{{ $n }}" value="{{ $n }}" @checked((int) old('rating', $review?->rating) === $n) required>
                        <label for="rating-{{ $n }}" title="{{ $n }} star" aria-label="{{ $n }} star">★</label>
                    @endforeach
                </div>
                <div id="rating-text" class="small text-muted mb-2" aria-live="polite">&nbsp;</div>
                @error('rating')
                <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                <textarea id="comment" name="comment" rows="3" maxlength="1000"
                    class="form-control @error('comment') is-invalid @enderror"
                    placeholder="What did you liked, and what can be improved? (optional)">{{ old('comment', $review?->comment) }}</textarea>
                @error('comment')
                <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="form-text text-end mb-2"><span data-count-for="comment">0</span>/1000</div>

                <button type="submit" class="btn-gold btn btn-sm">
                    {{ $review ? 'Update review' : 'Submit review' }}
                </button>
            </form>

            @if ($review)
                <form method="POST" action="{{ route('feedback.review.destroy') }}" class="mt-2"
                    onsubmit="return confirm('Your review will be removed permanently. Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link btn-sm text-danger p-0">Remove your Review</button>
                </form>
            @endif
        </div>
    </section>

    {{-- Complaint --}}
    <section class="card mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3"><i class="bi {{ config('icons.complaint') }} me-1"></i>Submit a Complaint or Problem
            </h2>

            @error('complaint')
            <div class="alert alert-danger py-2">{{ $message }}</div> @enderror

            <form method="POST" action="{{ route('feedback.complaint.store') }}" data-once> @csrf
                <div class="mb-2">
                    <label for="category" class="form-label small mb-1">Category</label>
                    <select name="category" id="category"
                        class="form-select form-select-sm @error('category') is-invalid @enderror" required>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', 'other') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-2">
                    <label for="subject" class="form-label small mb-1">Subject</label>
                    <input type="text" name="subject" id="subject" maxlength="120" required value="{{ old('subject') }}"
                        class="form-control form-control-sm @error('subject') is-invalid @enderror">
                    @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-2">
                    <label for="message" class="form-label small mb-1">Details</label>
                    <textarea name="message" id="message" rows="4" minlength="10" maxlength="2000" required
                        class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                    @error('message')
                    <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text d-flex justify-content-between">
                        <span>Don't enter any password or private details.</span>
                        <span><span data-count-for="message">0</span>/2000</span>
                    </div>
                </div>
                <button type="submit" class="btn-gold btn btn-sm">Send complaint</button>
            </form>
        </div>
    </section>

    {{-- My complaints --}}
    <section>
        <h2 class="h6 mb-3">Your complaints</h2>
        @forelse ($complaints as $complaint)
        @php([$statusText, $statusClass] = $statusLabels[$complaint->status] ?? [$complaint->status, 'bg-secondary'])
        <div class="card mb-2">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <strong>{{ $complaint->subject }}</strong>
                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </div>
                <div class="small text-muted mb-2">
                    {{ $categories[$complaint->category] ?? ucfirst($complaint->category) }}
                    · {{ $complaint->created_at->diffForHumans() }}
                </div>
                <p class="mb-0 small" style="white-space:pre-line">{{ $complaint->message }}</p>

                @if ($complaint->hasReply())
                    <div class="border-start border-3 ps-3 mt-3">
                        <div class="small text-muted">Taddabur team response</div>
                        <p class="mb-0 small" style="white-space:pre-line">{{ $complaint->admin_reply }}</p>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <p class="text-muted small">No complaints yet.</p>
        @endforelse
    </section>
</div>
@endsection
@push('styles')
    <style>
        .star-rating {
            position: relative;
            display: inline-flex;
            flex-direction: row-reverse;
            gap: .15rem;
        }

        .star-rating input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .star-rating label {
            font-size: 2rem;
            line-height: 1;
            cursor: pointer;
            color: var(--bs-border-color, #ced4da);
            transition: color .15s;
        }

        .star-rating label:hover,
        .star-rating label:hover~label,
        .star-rating input:checked~label {
            color: #f5b301;
        }

        .star-rating input:focus-visible+label {
            outline: 2px solid #f5b301;
            outline-offset: 2px;
            border-radius: 4px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const texts = { 1: 'Bahut kharab', 2: 'Theek nahi', 3: 'Theek-thaak', 4: 'Accha', 5: 'Bahut accha' };
            const out = document.getElementById('rating-text');
            const show = () => {
                const checked = document.querySelector('input[name="rating"]:checked');
                out.textContent = checked ? texts[checked.value] : '\u00a0';
            };
            document.querySelectorAll('input[name="rating"]').forEach(r => r.addEventListener('change', show));
            show();
        })();

        document.querySelectorAll('[data-count-for]').forEach(el => {
            const input = document.getElementById(el.dataset.countFor);
            const update = () => { el.textContent = input.value.length; };
            input.addEventListener('input', update);
            update();
        });

        document.querySelectorAll('form[data-once]').forEach(form => {
            form.addEventListener('submit', () => { form.querySelector('button[type="submit"]').disabled = true; });
        });
        // Back button se wapas aane par button phir se chalu ho
        window.addEventListener('pageshow', () => {
            document.querySelectorAll('form[data-once] button[type="submit"]').forEach(b => { b.disabled = false; });
        });
    </script>
@endpush
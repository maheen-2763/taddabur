{{-- resources/views/prophets/journey.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="journey-wrapper">

        {{-- ============================================
         HEADER — Prophet name + journey intro
    ============================================ --}}
        <div class="journey-header text-center">
            <p class="journey-eyebrow">The Life of</p>
            <h1 class="journey-title">{{ $prophet->name_english }}</h1>
            <p class="journey-arabic">{{ $prophet->name_arabic }}</p>
            <p class="journey-subtitle">
                A journey through six parts of his blessed life —
                complete each part to unlock the next.
            </p>
        </div>

        {{-- ============================================
         STEPPER — vertical timeline of 6 parts
    ============================================ --}}
        <div class="journey-stepper">
            @foreach ($stories as $index => $story)
                @php
                    $partNumber = $index + 1;
                    $progress = $story->user_progress['percentage'];
                    $isComplete = $progress === 100;
                    $isLocked = $story->is_locked;
                    // "Current" = first unlocked part that isn't finished yet
$isCurrent =
    !$isLocked &&
    !$isComplete &&
    $stories
        ->take($index)
        ->every(fn($s) => $s->user_progress['percentage'] === 100 || $s === $story);
                @endphp

                <div
                    class="journey-step {{ $isLocked ? 'is-locked' : '' }} {{ $isCurrent ? 'is-current' : '' }} {{ $isComplete ? 'is-complete' : '' }}">

                    {{-- Connector line (hidden on last item via CSS) --}}
                    <div class="journey-connector"></div>

                    {{-- Number / checkmark / lock badge --}}
                    <div class="journey-badge">
                        @if ($isComplete)
                            <i class="bi bi-check-lg"></i>
                        @elseif($isLocked)
                            <i class="bi bi-lock-fill"></i>
                        @else
                            {{ $partNumber }}
                        @endif
                    </div>

                    {{-- Card content --}}
                    <div class="journey-card">
                        <p class="journey-part-label">Part {{ $partNumber }} of 6</p>
                        <h3 class="journey-card-title">{{ $story->title }}</h3>
                        <p class="journey-card-summary">{{ $story->summary }}</p>

                        @if (!$isLocked)
                            {{-- Progress bar --}}
                            <div class="journey-progress-track">
                                <div class="journey-progress-fill" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="journey-progress-label">
                                {{ $story->user_progress['completed'] }}/{{ $story->user_progress['total'] }} chapters
                                @if ($isComplete)
                                    — Complete
                                @endif
                            </p>

                            <a href="{{ route('stories.show', $story->slug) }}"
                                class="journey-btn {{ $isCurrent ? 'journey-btn-primary' : '' }}">
                                {{ $isComplete ? 'Read Again' : ($story->user_progress['started'] ? 'Continue' : 'Begin') }}
                            </a>
                        @else
                            <p class="journey-locked-note">
                                Complete Part {{ $partNumber - 1 }} to unlock
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .journey-wrapper {
            max-width: 720px;
            margin: 0 auto;
            padding: 3rem 1.25rem;
        }

        .journey-header {
            margin-bottom: 3rem;
        }

        .journey-eyebrow {
            font-family: var(--font-body);
            color: var(--gold-dark);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .journey-title {
            font-family: var(--font-heading);
            color: var(--ink);
            font-size: 2.25rem;
            margin-bottom: 0.25rem;
        }

        .journey-arabic {
            font-family: var(--font-arabic);
            color: var(--emerald);
            font-size: 1.5rem;
            direction: rtl;
            margin-bottom: 1rem;
        }

        [data-bs-theme="dark"] .journey-arabic {
            color: var(--emerald-light);
        }

        .journey-subtitle {
            font-family: var(--font-body);
            color: var(--muted);
            font-size: 1rem;
            max-width: 480px;
            margin: 0 auto;
        }

        /* ---------- Stepper layout ---------- */
        .journey-stepper {
            position: relative;
        }

        .journey-step {
            position: relative;
            display: flex;
            gap: 1.25rem;
            padding-bottom: 2.5rem;
        }

        .journey-step:last-child {
            padding-bottom: 0;
        }

        .journey-step:last-child .journey-connector {
            display: none;
        }

        .journey-connector {
            position: absolute;
            top: 44px;
            left: 21px;
            width: 2px;
            height: calc(100% - 20px);
            background: var(--border);
        }

        .journey-step.is-complete .journey-connector {
            background: var(--gold);
        }

        /* ---------- Badge (number/check/lock circle) ---------- */
        .journey-badge {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-weight: 600;
            background: var(--cream-dark);
            color: var(--muted);
            border: 2px solid var(--border);
            z-index: 1;
        }

        .journey-step.is-current .journey-badge {
            background: var(--emerald);
            color: var(--cream);
            border-color: var(--emerald);
        }

        .journey-step.is-complete .journey-badge {
            background: var(--gold);
            color: var(--cream);
            border-color: var(--gold);
        }

        .journey-step.is-locked .journey-badge {
            background: var(--cream-dark);
            color: var(--muted);
        }

        /* ---------- Card ---------- */
        .journey-card {
            flex: 1;
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
        }

        .journey-step.is-current .journey-card {
            border-color: var(--emerald);
            box-shadow: 0 0 0 1px var(--emerald-light);
        }

        .journey-step.is-locked .journey-card {
            opacity: 0.6;
        }

        .journey-part-label {
            font-family: var(--font-body);
            color: var(--gold-dark);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .journey-card-title {
            font-family: var(--font-heading);
            color: var(--ink);
            font-size: 1.15rem;
            margin-bottom: 0.4rem;
        }

        .journey-card-summary {
            font-family: var(--font-body);
            color: var(--ink-soft);
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        /* ---------- Progress bar ---------- */
        .journey-progress-track {
            height: 6px;
            border-radius: 3px;
            background: var(--cream-dark);
            overflow: hidden;
            margin-bottom: 0.4rem;
        }

        .journey-progress-fill {
            height: 100%;
            background: var(--emerald);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .journey-progress-label {
            font-family: var(--font-body);
            color: var(--muted);
            font-size: 0.8rem;
            margin-bottom: 0.85rem;
        }

        .journey-locked-note {
            font-family: var(--font-body);
            color: var(--muted);
            font-size: 0.85rem;
            font-style: italic;
            margin: 0;
        }

        /* ---------- Buttons ---------- */
        .journey-btn {
            display: inline-block;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.5rem 1.1rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            color: var(--ink);
            text-decoration: none;
            background: var(--cream-dark);
        }

        .journey-btn-primary {
            background: var(--emerald);
            color: var(--cream);
            border-color: var(--emerald);
        }
    </style>
@endpush


{{-- @php $accessible = $firstPart->isAccessibleBy(Auth::user()); @endphp

<div class="d-flex justify-content-between align-items-start mb-2">
    <h5 class="heading-font mb-0" style="font-size:1rem">
        The Complete Life of {{ $prophet->name_transliteration }}
    </h5>
    @if ($firstPart->min_plan_slug === 'free')
        <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">Free</span>
    @elseif ($accessible)
        <span class="badge bg-success ms-2 flex-shrink-0" style="font-size:0.7rem">
            <i class="bi bi-unlock-fill me-1"></i>Unlocked
        </span>
    @else
        <span class="badge ms-2 flex-shrink-0" style="background:var(--gold); color:#1A1A2E; font-size:0.7rem">
            <i class="bi bi-stars me-1"></i>{{ ucfirst($firstPart->min_plan_slug) }}
        </span>
    @endif
</div>

{{-- ... summary + meta chips same ... --}}

{{-- @if ($accessible)
    <a href="{{ route('prophets.journey', $prophet->slug) }}" class="btn-emerald btn btn-sm">
        Begin the Journey <i class="bi bi-arrow-right ms-1"></i>
    </a>
@else
    @auth
        <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm">
            <i class="bi bi-lock me-1"></i>Upgrade to {{ ucfirst($firstPart->min_plan_slug) }} to Read
        </a>
    @else
        <a href="{{ route('login') }}" class="btn-emerald btn btn-sm">Sign in to Read</a>
    @endauth
@endif --}}

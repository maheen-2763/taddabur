{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'My Profile')
{{-- Is page par errors inline dikhte hain; layout ka global error block yahan nahi chalega --}}
@section('no_global_errors', '1')

@php
    $nameParts = explode(' ', trim($user->name));
    $initials =
        count($nameParts) > 1
        ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
        : strtoupper(substr($nameParts[0], 0, 2));

    // Server error aaya to wahi tab kholo jahan error hai
    $startTab = null;
    if ($errors->hasBag('updatePassword') || $errors->hasBag('userDeletion')) {
        $startTab = '#tab-password';
    } elseif ($errors->hasAny(['preferred_language', 'preferred_translation', 'preferred_tafsir', 'preferred_reciter'])) {
        $startTab = '#tab-preferences';
    } elseif ($errors->any()) {
        $startTab = '#tab-profile';
    }
    $emailBoxOpen = $errors->has('current_password');

    $flash = session('message') ?? match (session('status')) {
        'profile-updated' => 'Your profile was updated.',
        'password-updated' => 'Your password was updated.',
        'avatar-updated' => 'Your photo was updated.',
        'avatar-removed' => 'Your photo was removed.',
        'verification-link-sent' => 'A new verification link has been sent to your email address.',
        default => null,
    };
@endphp

@push('styles')
    <style>
        .profile-page {
            --pp-gold: var(--gold, #C9963A);
            --pp-gold-dark: var(--gold-dark, #8a5f12);
            --pp-emerald: var(--emerald, #1B5E3B);
            --pp-ink: var(--ink, #1A1A2E);
            --pp-cream: var(--cream, #FDFBF7);
            --pp-red: var(--red, #dd2d4a);
            --pp-border: var(--border, rgba(201, 150, 58, 0.28));
            --pp-muted: color-mix(in srgb, var(--pp-ink) 62%, transparent);
            min-height: calc(100vh - 60px);
            padding: 2rem 1rem 3rem;
        }

        .profile-inner {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ───────── Cards ───────── */
        .pp-card {
            background: var(--pp-cream);
            border: 1px solid var(--pp-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .pp-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 150, 58, 0.6), transparent);
        }

        .pp-section-label {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--pp-gold-dark);
            margin: 0 0 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pp-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(201, 150, 58, 0.25);
        }

        /* ───────── Hero (avatar + identity) ───────── */
        .pp-hero {
            text-align: center;
            padding: 2rem 1.5rem 1.5rem;
        }

        .pp-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 2px solid var(--pp-gold);
            background: rgba(201, 150, 58, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.9rem;
            font-family: 'Cinzel', serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--pp-gold-dark);
            overflow: hidden;
            box-shadow: 0 0 0 4px rgba(201, 150, 58, 0.12);
        }

        .pp-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pp-name {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            color: var(--pp-ink);
            margin: 0 0 0.35rem;
            word-break: break-word;
        }

        .pp-email {
            font-size: 13px;
            color: var(--pp-ink);
            margin: 0 0 0.75rem;
            word-break: break-all;
        }

        .pp-meta {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1.1rem;
        }

        .pp-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            letter-spacing: 0.4px;
            font-weight: 600;
        }

        .pp-pill-free {
            background: var(--pp-emerald);
            color: #fff;
            font-family: 'Cinzel', serif;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .pp-pill-ok {
            background: rgba(25, 135, 84, 0.12);
            font-family: 'Cinzel', serif;
            color: #146c43;
        }

        [data-bs-theme="dark"] .pp-pill-ok {
            color: var(--emerald-light);
        }

        .pp-pill-warn {
            background: rgba(255, 193, 7, 0.2);
            font-family: 'Cinzel', serif;
            color: #7a5b00;
        }

        [data-bs-theme="dark"] .pp-pill-warn {
            color: var(--pp-gold);
        }

        .pp-pill-muted {
            background: rgba(201, 150, 58, 0.12);
            font-family: 'Cinzel', serif;
            color: var(--pp-gold-dark);
        }

        [data-bs-theme="dark"] .pp-pill-muted {
            color: var(--gold-light);
        }

        .pp-avatar-actions {
            display: flex;
            gap: 0.6rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pp-avatar-actions form {
            margin: 0;
        }

        .pp-hint {
            font-size: 11.5px;
            color: var(--pp-muted);
            margin-top: 0.6rem;
        }

        /* ───────── Buttons ───────── */
        .pp-btn,
        .pp-btn-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border-color 0.2s, transform 0.1s;
        }

        .pp-btn {
            background: var(--pp-emerald);
            color: #fff;
            border: none;
            font-family: 'Cinzel', serif;
            font-size: 13px;
            letter-spacing: 1px;
            font-weight: 700;
            padding: 11px 26px;
            border-radius: 8px;
        }

        .pp-btn:hover {
            background: var(--emerald-dark, #164a2f);
            transform: translateY(-1px);
        }

        .pp-btn:disabled,
        .pp-btn-pill:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .pp-btn-pill {
            background: transparent;
            color: var(--pp-gold-dark);
            border: 1px solid var(--pp-gold);
            border-radius: 999px;
            padding: 7px 16px;
            font-size: 12.5px;
            font-weight: 600;
        }

        .pp-btn-pill:hover {
            background: rgba(201, 150, 58, 0.14);
        }

        .pp-btn-pill-danger {
            color: #b02a37;
            border-color: rgba(220, 53, 69, 0.5);
        }

        .pp-btn-pill-danger:hover {
            background: rgba(220, 53, 69, 0.08);
        }

        .pp-btn-danger {
            background: #b02a37;
            color: #fff;
            border: none;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
        }

        .pp-btn-danger:hover {
            background: #8f2230;
        }

        .pp-btn:focus-visible,
        .pp-btn-pill:focus-visible,
        .pp-btn-danger:focus-visible,
        .pp-tab:focus-visible,
        .pp-danger summary:focus-visible {
            outline: 3px solid rgba(201, 150, 58, 0.55);
            outline-offset: 2px;
        }

        /* ───────── Verse band ───────── */
        .pp-verse {
            background: rgba(201, 150, 58, 0.07);
            border: 1px solid rgba(201, 150, 58, 0.22);
            border-radius: 12px;
            padding: 0.9rem 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .pp-verse .arabic {
            font-family: 'Amiri', serif;
            font-size: 22px;
            color: var(--pp-gold-dark);
            line-height: 1.8;
        }

        .pp-verse .reference {
            font-size: 11.5px;
            color: var(--pp-gold-dark);
            letter-spacing: 0.4px;
        }

        [data-bs-theme="dark"] .pp-verse .arabic {
            color: var(--gold-light);
        }

        [data-bs-theme="dark"] .pp-verse .reference {
            color: var(--gold-light);
        }

        /* ───────── Tabs ───────── */
        .pp-tabs {
            display: flex;
            border-bottom: 1px solid rgba(201, 150, 58, 0.3);
            margin-bottom: 1.5rem;
        }

        .pp-tab {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 11px 8px;
            font-family: 'Cinzel', serif;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--pp-muted);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color 0.2s, border-color 0.2s;
        }

        .pp-tab:hover {
            color: var(--pp-gold-dark);
            border-bottom-color: rgba(201, 150, 58, 0.35);
        }

        .pp-tab.active {
            color: var(--pp-gold-dark);
            border-bottom-color: var(--pp-gold);
        }

        /* ───────── Forms ───────── */
        .profile-page .form-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--pp-gold-dark);
            margin-bottom: 6px;
            display: block;
            font-weight: 600;
        }

        .profile-page .form-control,
        .profile-page .form-select {
            background: rgba(201, 150, 58, 0.1);
            border: 1px solid var(--pp-border) !important;
            color: var(--pp-ink) !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            font-size: 14px !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .profile-page .form-control:focus,
        .profile-page .form-select:focus {
            border-color: var(--pp-gold) !important;
            box-shadow: 0 0 0 3px rgba(201, 150, 58, 0.15) !important;
            outline: none !important;
        }

        .profile-page .form-control.is-invalid {
            border-color: #dc3545 !important;
        }

        .profile-page .form-control::placeholder {
            color: rgba(26, 26, 46, 0.35) !important;
        }

        .pp-field-hint {
            font-size: 12px;
            color: var(--pp-muted);
            margin-top: 5px;
        }

        .pp-error {
            color: #b02a37;
            font-size: 12.5px;
            margin-top: 5px;
        }

        .pp-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.1rem;
            margin-bottom: 1.4rem;
        }

        @media (min-width: 576px) {
            .pp-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .pp-flash {
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #146c43;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 1.25rem;
        }

        /* ───────── Danger zone ───────── */
        .pp-danger {
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 12px;
            background: rgba(220, 53, 69, 0.04);
            padding: 0;
            overflow: hidden;
        }

        .pp-danger summary {
            cursor: pointer;
            list-style: none;
            padding: 1rem 1.25rem;
            font-family: 'Cinzel', serif;
            font-size: 12px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #b02a37;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }



        .pp-danger summary::-webkit-details-marker {
            display: none;
        }

        .pp-danger summary::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            transition: transform 0.2s;
        }

        .pp-danger[open] summary::after {
            transform: rotate(180deg);
        }

        .pp-danger-body {
            padding: 0 1.25rem 1.25rem;
            font-size: 13px;
            line-height: 1.7;
            color: #7a1f29;
        }

        [data-bs-theme="dark"] .pp-danger-body {
            color: var(--pp-red);
        }

        .pp-danger-body ul {
            margin: 0.4rem 0 1rem 1.1rem;
            padding: 0;
        }

        [hidden] {
            display: none !important;
        }

        @media (max-width: 575.98px) {
            .profile-page {
                padding: 1.25rem 0.75rem 2.5rem;
            }

            .pp-card {
                padding: 1.15rem;
            }

            .pp-tab {
                font-size: 11px;
                letter-spacing: 0.6px;
                gap: 5px;
            }

            .pp-btn {
                width: 100%;
            }
        }

        .pp-flash {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #146c43;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 1.25rem;
            max-height: 140px;
            transition: opacity 0.4s ease, transform 0.4s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease;
        }

        [data-bs-theme="dark"] .pp-flash {
            color: var(--emerald-light);
        }

        .pp-flash.is-hiding {
            opacity: 0;
            transform: translateY(-6px);
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            overflow: hidden;
        }

        .pp-flash-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            padding: 0 4px;
        }

        @media (prefers-reduced-motion: reduce) {
            .pp-flash {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="profile-page">
        <div class="profile-inner">

            {{-- ───── Hero: photo + identity (Free forever sirf yahin, ek baar) ───── --}}
            <section class="pp-card pp-hero" aria-label="Your profile">
                <div class="pp-avatar">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="Profile photo of {{ $user->name }}" width="96" height="96">
                    @else
                        <span aria-hidden="true">{{ $initials }}</span>
                    @endif
                </div>

                <h1 class="pp-name">{{ $user->name }}</h1>
                <p class="pp-email">{{ $user->email }}</p>

                <div class="pp-meta">
                    <span class="pp-pill pp-pill-free"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Free
                        forever</span>
                    @if ($user->hasVerifiedEmail())
                        <span class="pp-pill pp-pill-ok"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Email
                            verified</span>
                    @else
                        <span class="pp-pill pp-pill-warn"><i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            Email not verified</span>
                    @endif
                    <span class="pp-pill pp-pill-muted">Member since {{ $user->created_at?->format('F Y') }}</span>
                </div>

                <div class="pp-avatar-actions">
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
                        id="avatar-form">
                        @csrf
                        <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png,image/webp" hidden>
                        <button type="button" class="pp-btn-pill" id="avatar-pick">
                            <i class="bi bi-camera" aria-hidden="true"></i>
                            {{ $user->avatar ? 'Change photo' : 'Add photo' }}
                        </button>
                    </form>

                    @if ($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" data-single-submit
                            onsubmit="return confirm('Remove your profile photo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pp-btn-pill pp-btn-pill-danger">
                                <i class="bi bi-trash3" aria-hidden="true"></i> Remove
                            </button>
                        </form>
                    @endif

                    @unless ($user->hasVerifiedEmail())
                        <form method="POST" action="{{ route('verification.send') }}" data-single-submit>
                            @csrf
                            <button type="submit" class="pp-btn-pill">
                                <i class="bi bi-envelope" aria-hidden="true"></i> Resend verification
                            </button>
                        </form>
                    @endunless
                </div>
                <div class="pp-hint">JPG, PNG or WebP, up to 2 MB.</div>
                <div class="pp-error" id="avatar-error" role="alert" @if (!$errors->has('avatar')) hidden @endif>
                    {{ $errors->first('avatar') }}
                </div>
            </section>

            {{-- ───── Ayah ───── --}}
            <div class="pp-verse">
                <div class="arabic" lang="ar" dir="rtl">رَّبِّ زِدْنِى عِلْمًا</div>
                <div class="reference">My Lord, increase me in knowledge — Ta-Ha 20:114</div>
            </div>

            @if ($flash)
                <div class="pp-flash" role="status" data-autodismiss="5000">
                    <span>{{ $flash }}</span>
                    <button type="button" class="pp-flash-close" aria-label="Dismiss message">&times;</button>
                </div>
            @endif

            {{-- ───── Tabs ───── --}}
            <div class="pp-tabs" role="tablist" aria-label="Profile sections">
                <a href="#tab-profile" class="pp-tab active" id="link-profile" role="tab" aria-controls="tab-profile"
                    aria-selected="true"><i class="bi bi-person" aria-hidden="true"></i> Profile</a>
                <a href="#tab-preferences" class="pp-tab" id="link-preferences" role="tab" aria-controls="tab-preferences"
                    aria-selected="false"><i class="bi bi-sliders" aria-hidden="true"></i> Preferences</a>
                <a href="#tab-password" class="pp-tab" id="link-password" role="tab" aria-controls="tab-password"
                    aria-selected="false"><i class="bi bi-shield-lock" aria-hidden="true"></i> Security</a>
            </div>

            {{-- ═════════ TAB 1: PROFILE ═════════ --}}
            <div id="tab-profile" role="tabpanel" aria-labelledby="link-profile">
                <form method="POST" action="{{ route('profile.update') }}" data-single-submit>
                    @csrf
                    @method('PATCH')

                    <div class="pp-card">
                        <h2 class="pp-section-label">Personal Information</h2>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input id="name" type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" placeholder="Your name" maxlength="255" required
                                autocomplete="name">
                            @error('name')
                                <div class="pp-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" placeholder="your@email.com" maxlength="255"
                                required autocomplete="email" autocapitalize="off" spellcheck="false" inputmode="email">
                            @error('email')
                                <div class="pp-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email badalne par hi dikhta hai (isi tab mein) --}}
                        <div class="mb-3" id="email-password-box" hidden>
                            <label for="current_password" class="form-label">Current password</label>
                            <input id="current_password" name="current_password" type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                autocomplete="current-password">
                            <div class="pp-field-hint">Needed to change your email. We will send a confirmation link to
                                the new address and a notice to your old one.</div>
                            @error('current_password')
                                <div class="pp-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="pp-btn mt-2">Save Changes</button>
                    </div>
                </form>
            </div>

            {{-- ═════════ TAB 2: PREFERENCES ═════════ --}}
            <div id="tab-preferences" role="tabpanel" aria-labelledby="link-preferences">
                <form method="POST" action="{{ route('profile.preferences') }}" data-single-submit>
                    @csrf

                    <div class="pp-card">
                        <h2 class="pp-section-label">Reading Preferences</h2>

                        <div class="pp-grid">
                            <div>
                                <label for="preferred_language" class="form-label">Language</label>
                                <select id="preferred_language" name="preferred_language" class="form-select">
                                    <option value="en" @selected($user->preferred_language === 'en')>English</option>
                                    <option value="ur" @selected($user->preferred_language === 'ur')>اردو — Urdu</option>
                                    <option value="ar" @selected($user->preferred_language === 'ar')>العربية — Arabic</option>
                                </select>
                                @error('preferred_language')
                                    <div class="pp-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="preferred_translation" class="form-label">Quran Translation</label>
                                <select id="preferred_translation" name="preferred_translation" class="form-select">
                                    @foreach ($translations as $t)
                                        <option value="{{ $t->slug }}" @selected($user->preferred_translation === $t->slug)>
                                            {{ $t->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preferred_translation')
                                    <div class="pp-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="preferred_tafsir" class="form-label">Tafsir</label>
                                <select id="preferred_tafsir" name="preferred_tafsir" class="form-select">
                                    @foreach ($tafsirs as $t)
                                        <option value="{{ $t->slug }}" @selected($user->preferred_tafsir === $t->slug)>
                                            {{ $t->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preferred_tafsir')
                                    <div class="pp-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="preferred_reciter" class="form-label">Reciter</label>
                                <select id="preferred_reciter" name="preferred_reciter" class="form-select">
                                    @foreach ($reciters as $r)
                                        <option value="{{ $r->slug }}" @selected($user->preferred_reciter === $r->slug)>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preferred_reciter')
                                    <div class="pp-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="pp-btn">Save Preferences</button>
                    </div>
                </form>
            </div>

            {{-- ═════════ TAB 3: SECURITY (password + delete) ═════════ --}}
            <div id="tab-password" role="tabpanel" aria-labelledby="link-password">
                <form method="POST" action="{{ route('password.update') }}" data-single-submit>
                    @csrf
                    @method('PUT')

                    <div class="pp-card">
                        <h2 class="pp-section-label">Change Password</h2>

                        <div class="mb-3">
                            <label for="update_current_password" class="form-label">Current Password</label>
                            <input id="update_current_password" type="password" name="current_password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                placeholder="••••••••" required autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="pp-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password" class="form-label">New Password</label>
                            <input id="update_password" type="password" name="password" minlength="8"
                                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                placeholder="Create a new password" required autocomplete="new-password">
                            <div class="pp-field-hint">At least 8 characters, with letters and numbers.</div>
                            @error('password', 'updatePassword')
                                <div class="pp-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="update_password_confirmation" class="form-label">Confirm New Password</label>
                            <input id="update_password_confirmation" type="password" name="password_confirmation"
                                class="form-control" placeholder="Repeat new password" required minlength="8"
                                autocomplete="new-password">
                        </div>

                        <button type="submit" class="pp-btn">Update Password</button>
                    </div>
                </form>

                <details class="pp-danger" @if ($errors->hasBag('userDeletion')) open @endif>
                    <summary>Delete my account</summary>
                    <div class="pp-danger-body">
                        <p class="mb-1">This permanently removes:</p>
                        <ul>
                            <li>your profile photo and preferences</li>
                            <li>your reading progress, bookmarks and notes</li>
                        </ul>
                        <p>Donations, if you made any, are recorded separately by the name you entered and are not
                            linked to your account. This cannot be undone.</p>

                        <form action="{{ route('profile.destroy') }}" method="POST" data-single-submit
                            onsubmit="return confirm('Are you absolutely sure? Your account and its data will be permanently deleted. This cannot be undone.')">
                            @csrf
                            @method('DELETE')

                            <div class="mb-3">
                                <label for="delete_password" class="form-label" style="color:#b02a37">Confirm your
                                    password</label>
                                <input type="password" id="delete_password" name="password"
                                    class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                    placeholder="Enter your password to confirm" required autocomplete="current-password">
                                @error('password', 'userDeletion')
                                    <div class="pp-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="pp-btn-danger">Delete my account</button>
                        </form>
                    </div>
                </details>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /* ── Tabs ── */
            const panels = ['tab-profile', 'tab-preferences', 'tab-password'];
            const tabLinks = document.querySelectorAll('.pp-tab');

            function showTab(hash) {
                const id = hash.replace('#', '');
                if (!panels.includes(id)) return showTab('#tab-profile');

                panels.forEach((p) => { document.getElementById(p).style.display = p === id ? 'block' : 'none'; });
                tabLinks.forEach((link) => {
                    const active = link.getAttribute('href') === '#' + id;
                    link.classList.toggle('active', active);
                    link.setAttribute('aria-selected', active ? 'true' : 'false');
                });
            }

            tabLinks.forEach((link) => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    history.pushState(null, '', link.getAttribute('href'));
                    showTab(link.getAttribute('href'));
                });
            });

            showTab(@json($startTab) || window.location.hash || '#tab-profile');

            /* ── Email badalne par current password isi tab mein ── */
            const emailInput = document.getElementById('email');
            const passwordBox = document.getElementById('email-password-box');
            const passwordInput = document.getElementById('current_password');

            if (emailInput && passwordBox) {
                const original = @json(strtolower($user->email));
                const sync = () => {
                    const changed = emailInput.value.trim().toLowerCase() !== original;
                    passwordBox.hidden = !changed;
                    passwordInput.required = changed;
                };
                emailInput.addEventListener('input', sync);
                sync();
                if (@json($emailBoxOpen)) {
                    passwordBox.hidden = false;
                    passwordInput.required = true;
                }
            }

            /* ── Flash: 5 second baad khud fade (hover/focus par ruk jata hai) ── */
            document.querySelectorAll('[data-autodismiss]').forEach((el) => {
                const delay = parseInt(el.dataset.autodismiss, 10) || 5000;
                let timer;
                const hide = () => {
                    clearTimeout(timer);
                    el.classList.add('is-hiding');
                    setTimeout(() => el.remove(), 500);
                };
                const start = () => { timer = setTimeout(hide, delay); };
                const stop = () => clearTimeout(timer);

                el.addEventListener('mouseenter', stop);
                el.addEventListener('mouseleave', start);
                el.addEventListener('focusin', stop);
                el.addEventListener('focusout', start);
                el.querySelector('.pp-flash-close')?.addEventListener('click', hide);
                start();
            });

            /* ── Double-click se do baar submit nahi (password mail do baar ja rahi thi) ── */
            document.querySelectorAll('form[data-single-submit]').forEach((form) => {
                form.addEventListener('submit', (e) => {
                    // confirm() mein "Cancel" dabaya to kuch mat karo
                    if (e.defaultPrevented) return;

                    if (form.dataset.submitting === '1') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = '1';

                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.dataset.label = button.innerHTML;
                        button.disabled = true;
                        button.setAttribute('aria-busy', 'true');
                        button.textContent = 'Please wait…';
                    }
                });
            });

            // Back button se wapas aane par page taza ho (stale state/token na rahe)
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) window.location.reload();
            });

            /* ── Avatar: chunne par seedha upload ── */
            const pick = document.getElementById('avatar-pick');
            const fileInput = document.getElementById('avatar-input');
            const avatarForm = document.getElementById('avatar-form');
            const avatarError = document.getElementById('avatar-error');

            if (pick && fileInput && avatarForm) {
                pick.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', () => {
                    const file = fileInput.files[0];
                    if (!file) return;

                    const fail = (message) => {
                        avatarError.textContent = message;
                        avatarError.hidden = false;
                        fileInput.value = '';
                    };

                    if (file.size > 2 * 1024 * 1024) return fail('The photo must be smaller than 2 MB.');
                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                        return fail('Please upload a JPG, PNG or WebP image.');
                    }

                    pick.disabled = true;
                    pick.textContent = 'Uploading…';
                    avatarForm.submit();
                });
            }
        });
    </script>
@endpush
{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'My Profile')

@push('styles')
    <style>
        /* ── Page Background ── */
        .profile-page {
            background: var(--bg-dark, #0D3D22);
            min-height: calc(100vh - 60px);
            padding: 2.5rem 1rem 2.5rem;
            /* ← was just 2.5rem 1rem, bottom was 0 */
            position: relative;
        }

        /* Subtle geometric pattern overlay — same as auth pages */
        .profile-page::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23C9963A' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .profile-inner {
            max-width: 680px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* ── Avatar Block ── */
        .profile-avatar-block {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .profile-avatar-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid var(--gold, #C9963A);
            background: rgba(201, 150, 58, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-family: 'Cinzel', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--gold, #C9963A);
            letter-spacing: 1px;
        }

        .profile-user-name {
            font-family: 'Cinzel', serif;
            font-size: 18px;
            color: var(--gold, #C9963A);
            margin-bottom: 0.35rem;
        }

        /* ── Plan Badge ── */
        .badge-free {
            background: rgba(108, 117, 125, 0.2);
            color: #adb5bd;
            border: 1px solid rgba(108, 117, 125, 0.4);
        }

        .badge-basic {
            background: rgba(13, 110, 253, 0.15);
            color: #74b9ff;
            border: 1px solid rgba(13, 110, 253, 0.4);
        }

        .badge-premium {
            background: rgba(201, 150, 58, 0.15);
            color: var(--gold, #C9963A);
            border: 1px solid rgba(201, 150, 58, 0.4);
        }

        .profile-plan-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'Cinzel', serif;
        }

        /* ── Quran Verse Band ── */
        .verse-band {
            background: rgba(201, 150, 58, 0.07);
            border: 1px solid rgba(201, 150, 58, 0.2);
            border-radius: 10px;
            padding: 0.85rem 1.25rem;
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .verse-band .arabic {
            font-family: 'Amiri', serif;
            font-size: 18px;
            color: var(--gold, #C9963A);
            direction: rtl;
            margin-bottom: 4px;
        }

        .verse-band .reference {
            font-size: 11px;
            color: rgba(201, 150, 58, 0.6);
            letter-spacing: 0.5px;
        }

        /* ── Tabs ── */
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid rgba(201, 150, 58, 0.2);
            margin-bottom: 1.75rem;
            gap: 0;
        }

        .profile-tab {
            flex: 1;
            text-align: center;
            padding: 10px 8px;
            font-size: 12px;
            color: rgba(232, 220, 200, 0.5);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Cinzel', serif;
            transition: color 0.2s, border-color 0.2s;
            text-decoration: none;
            display: block;
        }

        .profile-tab:hover {
            color: rgba(201, 150, 58, 0.75);
            border-bottom-color: rgba(201, 150, 58, 0.3);
        }

        .profile-tab.active {
            color: var(--gold, #C9963A);
            border-bottom-color: var(--gold, #C9963A);
        }

        /* ── Cards ── */
        .card-islamic {
            background: rgba(23, 77, 45, 0.5);
            border: 1px solid rgba(201, 150, 58, 0.25);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
            /* Gold shimmer top border */
            background-clip: padding-box;
        }

        /* Thin gold line at top of each card */
        .card-islamic::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201, 150, 58, 0.6), transparent);
        }

        /* ── Section Label ── */
        .section-label {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold, #C9963A);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(201, 150, 58, 0.2);
        }

        /* ── Form Elements ── */
        .form-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(201, 150, 58, 0.65);
            margin-bottom: 6px;
            display: block;
        }

        .form-control,
        .form-select {
            background: rgba(13, 61, 34, 0.7) !important;
            border: 1px solid rgba(201, 150, 58, 0.25) !important;
            color: rgba(232, 220, 200, 0.9) !important;
            border-radius: 6px !important;
            padding: 10px 14px !important;
            font-size: 14px !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold, #C9963A) !important;
            box-shadow: 0 0 0 3px rgba(201, 150, 58, 0.12) !important;
            outline: none !important;
            background: rgba(13, 61, 34, 0.85) !important;
        }

        .form-control::placeholder {
            color: rgba(232, 220, 200, 0.3) !important;
        }

        /* ── Buttons ── */
        /* Primary — gold fill */
        .btn-emerald {
            background: var(--gold, #C9963A) !important;
            color: #0D3D22 !important;
            border: none !important;
            font-family: 'Cinzel', serif !important;
            font-size: 13px !important;
            letter-spacing: 1px !important;
            padding: 10px 24px !important;
            border-radius: 6px !important;
            font-weight: 700 !important;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s !important;
        }

        .btn-emerald:hover {
            background: #e0b05a !important;
            color: #0D3D22 !important;
            transform: translateY(-1px);
        }

        .btn-emerald:active {
            transform: translateY(0);
            background: #b8832e !important;
        }

        /* Secondary — outline */
        .btn-outline-gold {
            background: transparent !important;
            color: var(--gold, #C9963A) !important;
            border: 1px solid var(--gold, #C9963A) !important;
            font-family: 'Cinzel', serif !important;
            font-size: 13px !important;
            letter-spacing: 1px !important;
            padding: 9px 20px !important;
            border-radius: 6px !important;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, transform 0.1s !important;
        }

        .btn-outline-gold:hover {
            background: rgba(201, 150, 58, 0.12) !important;
            color: #e0b05a !important;
            transform: translateY(-1px);
        }

        .btn-outline-gold:active {
            transform: translateY(0);
        }

        /* Danger */
        .btn-danger-outline {
            background: transparent !important;
            color: #f87171 !important;
            border: 1px solid rgba(248, 113, 113, 0.4) !important;
            font-size: 13px !important;
            padding: 9px 20px !important;
            border-radius: 6px !important;
            cursor: pointer;
            transition: background 0.2s, color 0.2s !important;
        }

        .btn-danger-outline:hover {
            background: rgba(248, 113, 113, 0.1) !important;
            color: #fca5a5 !important;
        }

        /* ── Subscription Card ── */
        .subscription-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .sub-plan-name {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            color: var(--gold, #C9963A);
            font-weight: 700;
        }

        .sub-plan-desc {
            font-size: 12px;
            color: rgba(232, 220, 200, 0.5);
            margin-top: 3px;
        }

        /* ── Danger Zone ── */
        .danger-zone {
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            background: rgba(220, 53, 69, 0.05);
        }

        .danger-zone-label {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #f87171;
            margin-bottom: 0.5rem;
        }

        .danger-zone-text {
            font-size: 13px;
            color: rgba(232, 220, 200, 0.5);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        /* ── Alert ── */
        .alert-islamic-success {
            background: rgba(25, 135, 84, 0.15);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #6ee7b7;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
        }

        .text-danger {
            color: #f87171 !important;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="profile-page">
        <div class="profile-inner">

            {{-- ── Avatar & Identity ── --}}
            <div class="profile-avatar-block">
                <div class="profile-avatar-ring">
                    {{--
                    We take the first letter of the name.
                    strtoupper(substr($user->name, 0, 1)) gives us e.g. "M"
                    For two initials, we could do first + last name letter.
                --}}
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="profile-user-name">{{ $user->name }}</div>
                <span class="profile-plan-badge badge-{{ $user->plan }}">
                    {{ strtoupper($user->plan) }}
                </span>
            </div>

            {{-- ── Quranic Verse Band ── --}}
            {{--
            Ta-Ha 20:114 — same verse on your register page.
            Keeps the spiritual identity consistent across all user-facing pages.
        --}}
            <div class="verse-band">
                <div class="arabic">رَبِّ زِدۡنِی عِلۡمًا</div>
                <div class="reference">My Lord, increase me in knowledge — Ta-Ha 20:114</div>
            </div>

            {{-- ── Success Flash ── --}}
            @if (session('message'))
                <div class="alert-islamic-success mb-4">
                    {{ session('message') }}
                </div>
            @endif

            {{-- ── Tabs ── --}}
            {{--
            Pure CSS tabs using :target selector trick.
            No JavaScript needed. Each tab links to an anchor (#tab-profile etc.)
            The section with matching id becomes visible.
            Default (no target) shows profile section.
        --}}
            <div class="profile-tabs">
                <a href="#tab-profile"
                    class="profile-tab {{ !request()->is('*#tab-preferences') && !request()->is('*#tab-password') ? 'active' : '' }}"
                    id="link-profile">
                    Profile
                </a>
                <a href="#tab-preferences" class="profile-tab" id="link-preferences">Preferences</a>
                <a href="#tab-password" class="profile-tab" id="link-password">Password</a>
            </div>

            {{-- ══════════════════════════════════════
             TAB 1 — PROFILE
             Handles: name, email update
        ══════════════════════════════════════ --}}
            <div id="tab-profile">

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="card-islamic">
                        <div class="section-label">Personal Information</div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" placeholder="Your name">
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" placeholder="your@email.com">
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-emerald">
                            Save Changes
                        </button>
                    </div>
                </form>

                {{-- ── Subscription Info Card ── --}}
                <div class="card-islamic">
                    <div class="section-label">Subscription</div>

                    <div class="subscription-card">
                        <div>
                            <div class="sub-plan-name">{{ strtoupper($user->plan) }}</div>
                            <div class="sub-plan-desc">
                                @if ($user->plan === 'premium')
                                    Full access — unlimited Quran, Tafsir, Prophet Stories
                                @elseif($user->plan === 'basic')
                                    Standard access — Quran reading + basic Tafsir
                                @else
                                    Free tier — limited daily reading
                                @endif
                            </div>
                        </div>

                        {{-- Only show upgrade button if not already premium --}}
                        @if ($user->plan !== 'premium')
                            <a href="{{ route('pricing') }}" class="btn btn-outline-gold">
                                Upgrade Plan
                            </a>
                        @endif
                    </div>
                </div>

            </div>{{-- end #tab-profile --}}


            {{-- ══════════════════════════════════════
             TAB 2 — PREFERENCES
             Handles: preferred_language update
        ══════════════════════════════════════ --}}
            <div id="tab-preferences">

                <form method="POST" action="{{ route('profile.preferences') }}">
                    @csrf

                    <div class="card-islamic">
                        <div class="section-label">Reading Preferences</div>

                        <div class="mb-4">
                            <label class="form-label">Preferred Language</label>
                            <select name="preferred_language" class="form-select">
                                {{--
                                We compare $user->preferred_language to each value.
                                The ternary outputs 'selected' or '' for the HTML attribute.
                            --}}
                                <option value="en" {{ $user->preferred_language === 'en' ? 'selected' : '' }}>
                                    English
                                </option>
                                <option value="ur" {{ $user->preferred_language === 'ur' ? 'selected' : '' }}>
                                    اردو — Urdu
                                </option>
                                <option value="ar" {{ $user->preferred_language === 'ar' ? 'selected' : '' }}>
                                    العربية — Arabic
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-emerald">
                            Save Preferences
                        </button>
                    </div>
                </form>

            </div>{{-- end #tab-preferences --}}


            {{-- ══════════════════════════════════════
             TAB 3 — PASSWORD
             Handles: current + new password change
        ══════════════════════════════════════ --}}
            <div id="tab-password">

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-islamic">
                        <div class="section-label">Change Password</div>

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••">
                            @error('current_password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters">
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Repeat new password">
                        </div>

                        <button type="submit" class="btn btn-emerald">
                            Update Password
                        </button>
                    </div>
                </form>

                {{-- ── Danger Zone ── --}}
                {{--
                Placed inside the password tab so it's not the first thing
                a user sees. Keeps it accessible but out of the way.
            --}}
                <div class="danger-zone">
                    <div class="danger-zone-label">Danger Zone</div>
                    <div class="danger-zone-text">
                        Permanently deletes your account and removes all reading progress,
                        bookmarks, and notes. This action cannot be undone.
                    </div>
                    {{--
                    This links to a separate DELETE confirmation route.
                    A confirmation prompt should be on that page, not a JS confirm().
                --}}
                    <a href="{{ route('profile.destroy') }}" class="btn btn-danger-outline">
                        Delete My Account
                    </a>
                </div>

            </div>{{-- end #tab-password --}}

        </div>{{-- .profile-inner --}}
    </div>{{-- .profile-page --}}
@endsection

@push('scripts')
    <script>
        /*
         * Tab switching — pure JS, no jQuery needed.
         *
         * How it works:
         * 1. We grab all sections (tab-profile, tab-preferences, tab-password)
         * 2. We grab all tab links
         * 3. On click of a tab link, we hide all sections and show the one matching
         *    the href (e.g. "#tab-profile" → show element with id="tab-profile")
         * 4. We also update the active class on the tab links
         * 5. On page load, we check the URL hash and activate that tab.
         *    This means if the form has an error, we redirect back with
         *    #tab-password in the URL and the correct tab opens automatically.
         */

        const sections = {
            'tab-profile': document.getElementById('tab-profile'),
            'tab-preferences': document.getElementById('tab-preferences'),
            'tab-password': document.getElementById('tab-password'),
        };

        const tabLinks = document.querySelectorAll('.profile-tab');

        function showTab(targetId) {
            // Hide all sections
            Object.values(sections).forEach(el => {
                if (el) el.style.display = 'none';
            });

            // Remove active from all tabs
            tabLinks.forEach(link => link.classList.remove('active'));

            // Show the target section
            const sectionId = targetId.replace('#', '');
            if (sections[sectionId]) {
                sections[sectionId].style.display = 'block';
            }

            // Add active class to the matching tab link
            tabLinks.forEach(link => {
                if (link.getAttribute('href') === targetId) {
                    link.classList.add('active');
                }
            });
        }

        // Click handler for each tab
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href');
                // Update URL hash without page jump
                history.pushState(null, null, target);
                showTab(target);
            });
        });

        // On page load — check hash, default to #tab-profile
        const initialHash = window.location.hash || '#tab-profile';
        showTab(initialHash);
    </script>
@endpush

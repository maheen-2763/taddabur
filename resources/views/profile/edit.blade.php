{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'My Profile')

@push('styles')
    <style>
        /* ── Page Layout ── */
        .profile-page {
            min-height: calc(100vh - 60px);
            padding: 2.5rem 1rem 2.5rem;
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
            color: (var(--ink));
            margin-bottom: 0.35rem;
        }

        /* ── Plan Badge ── */
        .badge-free {
            background: rgba(108, 117, 125, 0.15);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.35);
        }

        .badge-basic {
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
            border: 1px solid rgba(13, 110, 253, 0.35);
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
            color: var(--gold, #C9963A);
            letter-spacing: 0.5px;
        }

        /* ── Tabs ── */
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid rgba(201, 150, 58, 0.3);
            margin-bottom: 1.75rem;
            gap: 0;
        }

        .profile-tab {
            flex: 1;
            text-align: center;
            padding: 10px 8px;
            font-size: 12px;
            color: rgba(26, 26, 46, 0.5);
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
            color: var(--gold, #C9963A);
            border-bottom-color: rgba(201, 150, 58, 0.3);
        }

        .profile-tab.active {
            color: var(--gold, #C9963A);
            border-bottom-color: var(--gold, #C9963A);
        }

        /* ── Cards ── */
        .card-islamic {
            background: var(--cream, #FDFBF7);
            border: 1px solid var(--border, rgba(201, 150, 58, 0.25));
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
            background-clip: padding-box;
        }

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
            color: var(--gold, #C9963A);
            margin-bottom: 6px;
            display: block;
        }

        .form-control,
        .form-select {
            background: rgba(201, 150, 58, 0.12);
            border: 1px solid var(--border, rgba(201, 150, 58, 0.3)) !important;
            color: var(--ink, #1A1A2E) !important;
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
        }

        .form-control::placeholder {
            color: rgba(26, 26, 46, 0.3) !important;
        }

        /* ── Buttons ── */
        .btn-emerald {
            background: var(--emerald, #1B5E3B) !important;
            color: #fff !important;
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
            background: var(--emerald-dark, #164a2f) !important;
            color: #fff !important;
            transform: translateY(-1px);
        }

        .btn-emerald:active {
            transform: translateY(0);
        }

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
            background: rgba(201, 150, 58, 0.1) !important;
            color: var(--gold, #C9963A) !important;
            transform: translateY(-1px);
        }

        .btn-outline-gold:active {
            transform: translateY(0);
        }

        .btn-danger-outline {
            background: transparent !important;
            color: #dc3545 !important;
            border: 1px solid rgba(220, 53, 69, 0.4) !important;
            font-size: 13px !important;
            padding: 9px 20px !important;
            border-radius: 6px !important;
            cursor: pointer;
            transition: background 0.2s, color 0.2s !important;
        }

        .btn-danger-outline:hover {
            background: rgba(220, 53, 69, 0.08) !important;
            color: #b02a37 !important;
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
            color: var(--gold, #C9963A);
            margin-top: 3px;
        }

        /* ── Danger Zone ── */
        .danger-zone {
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            background: rgba(220, 53, 69, 0.04);
        }

        .danger-zone-label {
            font-family: 'Cinzel', serif;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #dc3545;
            margin-bottom: 0.5rem;
        }

        .danger-zone-text {
            font-size: 12px;
            color: #dc3545 !important;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        /* ── Alert ── */
        .alert-islamic-success {
            background: rgba(25, 135, 84, 0.1);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #146c43;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
        }

        .text-danger {
            color: #dc3545 !important;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="profile-page">
        <div class="profile-inner">

            {{-- ── Avatar & Identity ── --}}
            @php
                $nameParts = explode(' ', trim($user->name));
                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
            @endphp
            <div class="profile-avatar-block">
                <div class="profile-avatar-ring">
                    {{ $initials }}
                </div>
                <div class="profile-user-name">{{ $user->name }}</div>
                <span class="profile-plan-badge badge-{{ $user->plan }}">
                    {{ strtoupper($user->plan) }}
                </span>
            </div>

            {{-- ── Quranic Verse Band ── --}}
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
            <div class="profile-tabs">
                <a href="#tab-profile" class="profile-tab active" id="link-profile">
                    Profile
                </a>
                <a href="#tab-preferences" class="profile-tab" id="link-preferences">Preferences</a>
                <a href="#tab-password" class="profile-tab" id="link-password">Password</a>
            </div>

            {{-- ══════════════════════════════════════
             TAB 1 — PROFILE
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
                                    Full access — all 24 Prophet stories, Hadith, personal notes, offline downloads
                                @elseif($user->plan === 'basic')
                                    All translations & recitations, tafsir, 12 Prophet stories, unlimited bookmarks
                                @else
                                    Free tier — 5 Prophet stories, 7 bookmarks
                                @endif
                            </div>
                            @if ($user->plan_expires_at)
                                <div class="sub-plan-desc mt-1">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    @if ($user->activeSubscription?->status === 'cancelled')
                                        Access ends {{ $user->plan_expires_at->format('d M Y') }}
                                    @else
                                        Renews {{ $user->plan_expires_at->format('d M Y') }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            @if ($user->plan !== 'free')
                                <a href="{{ route('subscription.dashboard') }}" class="btn btn-outline-gold">
                                    Manage Subscription
                                </a>
                            @endif
                            @if ($user->plan !== 'premium')
                                <a href="{{ route('pricing') }}" class="btn btn-emerald">
                                    Upgrade Plan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>{{-- end #tab-profile --}}


            {{-- ══════════════════════════════════════
             TAB 2 — PREFERENCES
        ══════════════════════════════════════ --}}
            <div id="tab-preferences">

                <form method="POST" action="{{ route('profile.preferences') }}">
                    @csrf

                    <div class="card-islamic">
                        <div class="section-label">Reading Preferences</div>

                        <div class="mb-4">
                            <label class="form-label">Preferred Language</label>
                            <select name="preferred_language" class="form-select">
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
                <div class="danger-zone">
                    <div class="danger-zone-label">Danger Zone</div>
                    <div class="danger-zone-label">
                        Permanently deletes your account and removes all reading progress,
                        bookmarks, and notes. This action cannot be undone.
                    </div>

                    <form action="{{ route('profile.destroy') }}" method="POST" class="d-inline danger-zone-label"
                        onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account and remove all reading progress, bookmarks, and notes. This cannot be undone.')">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="delete_password" class="form-label danger-zone-label">Confirm your
                                password</label>
                            <input type="password" id="delete_password" name="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Enter your password to confirm">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-danger-outline">
                            Delete My Account
                        </button>
                    </form>
                </div>

            </div>{{-- end #tab-password --}}

        </div>{{-- .profile-inner --}}
    </div>{{-- .profile-page --}}
@endsection

@push('scripts')
    <script>
        const sections = {
            'tab-profile': document.getElementById('tab-profile'),
            'tab-preferences': document.getElementById('tab-preferences'),
            'tab-password': document.getElementById('tab-password'),
        };

        const tabLinks = document.querySelectorAll('.profile-tab');

        function showTab(targetId) {
            Object.values(sections).forEach(el => {
                if (el) el.style.display = 'none';
            });

            tabLinks.forEach(link => link.classList.remove('active'));

            const sectionId = targetId.replace('#', '');
            if (sections[sectionId]) {
                sections[sectionId].style.display = 'block';
            }

            tabLinks.forEach(link => {
                if (link.getAttribute('href') === targetId) {
                    link.classList.add('active');
                }
            });
        }

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('href');
                history.pushState(null, null, target);
                showTab(target);
            });
        });

        const initialHash = window.location.hash || '#tab-profile';
        showTab(initialHash);
    </script>
@endpush

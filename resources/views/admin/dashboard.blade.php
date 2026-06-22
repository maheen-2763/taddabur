@extends('admin.layout')
@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Sparkline bars */
        .sparkline {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 36px;
        }

        .spark-bar {
            flex: 1;
            border-radius: 3px 3px 0 0;
            background: rgba(27, 94, 59, 0.2);
            transition: background 0.2s;
            min-width: 6px;
        }

        .spark-bar.today {
            background: var(--emerald-light);
        }

        .spark-bar:hover {
            background: var(--gold);
        }

        /* Surah bar chart */
        .surah-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 0.82rem;
        }

        .surah-bar-track {
            flex: 1;
            height: 8px;
            background: #F0F0F0;
            border-radius: 20px;
            overflow: hidden;
        }

        .surah-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--emerald-light), var(--emerald));
        }

        .surah-bar-label {
            width: 80px;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .surah-bar-count {
            width: 36px;
            text-align: right;
            color: #888;
            font-size: 0.75rem;
        }

        /* Plan donut (CSS only) */
        .plan-donut-wrap {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .plan-donut-wrap svg {
            transform: rotate(-90deg);
        }

        .plan-donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            line-height: 1.2;
        }

        /* Engagement tier rows */
        .engagement-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #F5F5F5;
            font-size: 0.82rem;
        }

        .engagement-row:last-child {
            border-bottom: none;
        }

        .engagement-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--emerald), var(--emerald-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        /* Chatbot teaser */
        .chatbot-messages {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 1rem 0;
        }

        .chat-bubble {
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            max-width: 80%;
        }

        .chat-bubble.user {
            background: rgba(201, 150, 58, 0.15);
            color: var(--gold-light);
            align-self: flex-end;
            border-radius: 12px 12px 0 12px;
        }

        .chat-bubble.bot {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            align-self: flex-start;
            border-radius: 12px 12px 12px 0;
        }

        /* Status dot */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot.green {
            background: #22c55e;
        }

        .status-dot.yellow {
            background: #f59e0b;
        }

        .status-dot.red {
            background: #ef4444;
        }

        /* Section headings */
        .section-heading {
            font-family: var(--font-heading);
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #999;
            margin: 1.75rem 0 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E8E8E8;
        }

        /* INR badge */
        .inr-badge {
            font-size: 0.68rem;
            color: #999;
            background: #F5F5F5;
            border-radius: 4px;
            padding: 1px 5px;
            display: inline-block;
            margin-top: 2px;
        }
    </style>
@endpush

@section('content')

    {{-- ══════════════════════════════════════════════════
     SECTION 1 — USERS & REVENUE
══════════════════════════════════════════════════ --}}
    <div class="section-heading">Users & Revenue</div>
    <div class="row g-3 mb-2">

        {{-- Total Users --}}
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem;letter-spacing:.05em">TOTAL USERS</p>
                        <div class="stat-number" style="color:var(--emerald)">{{ number_format($totalUsers ?? 0) }}</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-people" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <small class="text-muted">
                    {{ $verifiedUsers ?? 0 }} verified ·
                    <span style="color:#ef4444">{{ ($totalUsers ?? 0) - ($verifiedUsers ?? 0) }} unverified</span>
                </small>
            </div>
        </div>

        {{-- New This Month --}}
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem;letter-spacing:.05em">NEW THIS MONTH</p>
                        <div class="stat-number" style="color:var(--emerald)">{{ number_format($newUsersMonth ?? 0) }}</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-person-plus" style="color:var(--emerald)"></i>
                    </div>
                </div>
                {{-- 7-day sparkline --}}
                <div class="sparkline mt-2">
                    @foreach ($dailySignups ?? [2, 5, 3, 8, 4, 6, 9] as $i => $count)
                        @php
                            $max = max($dailySignups ?? [2, 5, 3, 8, 4, 6, 9]);
                            $pct = $max > 0 ? ($count / $max) * 100 : 10;
                        @endphp
                        <div class="spark-bar {{ $i === count($dailySignups ?? []) - 1 ? 'today' : '' }}"
                            style="height:{{ max(10, $pct) }}%" title="{{ $count }} signups"></div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem;letter-spacing:.05em">MONTHLY REVENUE</p>
                        <div class="stat-number" style="color:var(--gold)">${{ number_format($monthlyRevenue ?? 0, 2) }}
                        </div>
                        <div class="inr-badge">₹{{ number_format(($monthlyRevenue ?? 0) * 84.5, 0) }} INR</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(201,150,58,0.1)">
                        <i class="bi bi-currency-dollar" style="color:var(--gold)"></i>
                    </div>
                </div>
                <small class="text-muted mt-1 d-block">
                    Basic: ${{ number_format(($basicUsers ?? 0) * 1.99, 2) }} ·
                    Premium: ${{ number_format(($premiumUsers ?? 0) * 3.99, 2) }}
                </small>
            </div>
        </div>

        {{-- Annual Projection --}}
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem;letter-spacing:.05em">ANNUAL PROJECTION</p>
                        <div class="stat-number" style="color:var(--gold)">
                            ${{ number_format(($monthlyRevenue ?? 0) * 12, 0) }}</div>
                        <div class="inr-badge">₹{{ number_format(($monthlyRevenue ?? 0) * 12 * 84.5, 0) }} INR</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(201,150,58,0.1)">
                        <i class="bi bi-graph-up-arrow" style="color:var(--gold)"></i>
                    </div>
                </div>
                <small class="text-muted">
                    Churn last 30d:
                    <span style="color:#ef4444">{{ $churned ?? 0 }} users</span>
                </small>
            </div>
        </div>

    </div>

    {{-- Plan breakdown + Verification rate --}}
    <div class="row g-3 mb-2">

        {{-- Plan Split --}}
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Plan Distribution</h6>
                    <span style="font-size:0.75rem; color:#999">{{ $totalUsers ?? 0 }} total</span>
                </div>
                <div class="p-3">
                    @php
                        $total = max(1, $totalUsers ?? 1);
                        $free = $freeUsers ?? 0;
                        $basic = $basicUsers ?? 0;
                        $premium = $premiumUsers ?? 0;
                    @endphp

                    <div class="d-flex align-items-center gap-3 mb-3">
                        {{-- Simple CSS donut --}}
                        <svg width="80" height="80" viewBox="0 0 36 36" style="flex-shrink:0">
                            @php
                                $freePct = round(($free / $total) * 100);
                                $basicPct = round(($basic / $total) * 100);
                                $premiumPct = 100 - $freePct - $basicPct;
                                $r = 15.9155;
                                $circ = 2 * 3.14159 * $r;
                                $freeOff = $circ;
                                $basicOff = $circ - ($freePct / 100) * $circ;
                                $premiumOff = $circ - (($freePct + $basicPct) / 100) * $circ;
                            @endphp
                            <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#F0F0F0"
                                stroke-width="3.5" />
                            <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#6B7280"
                                stroke-width="3.5" stroke-dasharray="{{ ($freePct / 100) * $circ }} {{ $circ }}"
                                stroke-dashoffset="0" transform="rotate(-90 18 18)" />
                            <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#2D8A59"
                                stroke-width="3.5" stroke-dasharray="{{ ($basicPct / 100) * $circ }} {{ $circ }}"
                                stroke-dashoffset="{{ (-$freePct / 100) * $circ }}" transform="rotate(-90 18 18)" />
                            <circle cx="18" cy="18" r="{{ $r }}" fill="none" stroke="#C9963A"
                                stroke-width="3.5" stroke-dasharray="{{ ($premiumPct / 100) * $circ }} {{ $circ }}"
                                stroke-dashoffset="{{ (-($freePct + $basicPct) / 100) * $circ }}" transform="rotate(-90 18 18)" />
                            <text x="18" y="20" text-anchor="middle" font-size="5" fill="#1A1A2E"
                                font-family="Cinzel,serif">{{ $totalUsers ?? 0 }}</text>
                        </svg>
                        <div style="font-size:0.8rem; line-height:2;">
                            <div><span class="badge badge-free me-1">FREE</span> {{ $free }} users
                                ({{ $freePct }}%)</div>
                            <div><span class="badge badge-basic me-1">BASIC</span> {{ $basic }} users
                                ({{ $basicPct }}%)</div>
                            <div><span class="badge badge-premium me-1">PREMIUM</span> {{ $premium }} users
                                ({{ $premiumPct }}%)</div>
                        </div>
                    </div>

                    {{-- Revenue per plan --}}
                    <div style="border-top:1px solid #F5F5F5; padding-top:10px; font-size:0.78rem; color:#666;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Basic revenue</span>
                            <span style="color:var(--emerald)">${{ number_format($basic * 1.99, 2) }} ·
                                ₹{{ number_format($basic * 1.99 * 84.5, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Premium revenue</span>
                            <span style="color:var(--gold)">${{ number_format($premium * 3.99, 2) }} ·
                                ₹{{ number_format($premium * 3.99 * 84.5, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Email Verification --}}
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Email Verification Rate</h6>
                </div>
                <div class="p-3">
                    @php
                        $verRate = $totalUsers > 0 ? round((($verifiedUsers ?? 0) / $totalUsers) * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span
                            style="font-family:var(--font-heading); font-size:2rem; color:{{ $verRate >= 80 ? 'var(--emerald)' : ($verRate >= 50 ? 'var(--gold)' : '#ef4444') }}">
                            {{ $verRate }}%
                        </span>
                        <span style="font-size:0.75rem; color:#999">of registered<br>users verified</span>
                    </div>
                    <div class="progress mb-2" style="height:8px">
                        <div class="progress-bar"
                            style="width:{{ $verRate }}%;
                         background:{{ $verRate >= 80 ? 'var(--emerald-light)' : ($verRate >= 50 ? 'var(--gold)' : '#ef4444') }}">
                        </div>
                    </div>
                    <div style="font-size:0.75rem; color:#999">
                        {{ $verifiedUsers ?? 0 }} verified · {{ ($totalUsers ?? 0) - ($verifiedUsers ?? 0) }} pending
                    </div>
                    @if ($verRate < 70)
                        <div class="mt-2 p-2 rounded" style="background:#FEF3C7; font-size:0.75rem; color:#92400E;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Low rate — consider resending verification emails.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Active Users (Last 7 Days)</h6>
                </div>
                <div class="p-3">
                    <div style="font-family:var(--font-heading); font-size:2rem; color:var(--emerald)">
                        {{ number_format($activeUsers7d ?? 0) }}
                    </div>
                    <div style="font-size:0.75rem; color:#999; margin-bottom:12px">unique sessions</div>
                    <div class="d-flex gap-3" style="font-size:0.78rem;">
                        <div>
                            <div style="color:#999; font-size:0.7rem;">AVG SESSION</div>
                            <div style="font-weight:600">{{ $avgSessionMin ?? '—' }} min</div>
                        </div>
                        <div>
                            <div style="color:#999; font-size:0.7rem;">FREE AVG</div>
                            <div style="font-weight:600; color:#6B7280">{{ $avgSessionFree ?? '—' }} min</div>
                        </div>
                        <div>
                            <div style="color:#999; font-size:0.7rem;">PREMIUM AVG</div>
                            <div style="font-weight:600; color:var(--gold)">{{ $avgSessionPremium ?? '—' }} min</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 2 — QURAN CONTENT
══════════════════════════════════════════════════ --}}
    <div class="section-heading">Quran Content</div>
    <div class="row g-3 mb-2">

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-book" style="font-size:1.4rem; color:var(--emerald)"></i>
                <div class="stat-number mt-1" style="color:var(--emerald); font-size:1.6rem;">114</div>
                <small class="text-muted">Surahs</small>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-list-ol" style="font-size:1.4rem; color:var(--emerald)"></i>
                <div class="stat-number mt-1" style="color:var(--emerald); font-size:1.6rem;">
                    {{ number_format($totalAyahs ?? 6236) }}</div>
                <small class="text-muted">Ayahs</small>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-mortarboard" style="font-size:1.4rem; color:var(--gold)"></i>
                <div class="stat-number mt-1" style="color:var(--gold); font-size:1.6rem;">{{ $totalTafsir ?? 0 }}</div>
                <small class="text-muted">Tafsir entries</small>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-translate" style="font-size:1.4rem; color:var(--gold)"></i>
                <div class="stat-number mt-1" style="color:var(--gold); font-size:1.6rem;">{{ $totalTranslations ?? 0 }}
                </div>
                <small class="text-muted">Translations</small>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-mic" style="font-size:1.4rem; color:var(--emerald)"></i>
                <div class="stat-number mt-1" style="color:var(--emerald); font-size:1.6rem;">{{ $totalReciters ?? 0 }}
                </div>
                <small class="text-muted">Reciters</small>
            </div>
        </div>

        <div class="col-6 col-md-2">
            <div class="stat-card text-center">
                <i class="bi bi-bookmark-fill" style="font-size:1.4rem; color:var(--gold)"></i>
                <div class="stat-number mt-1" style="color:var(--gold); font-size:1.6rem;">
                    {{ number_format($totalBookmarks ?? 0) }}</div>
                <small class="text-muted">Bookmarks</small>
            </div>
        </div>

    </div>

    {{-- Most Read Surahs + Notes --}}
    <div class="row g-3 mb-2">

        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Most Read Surahs</h6>
                    <span style="font-size:0.72rem; color:#999">by unique reader sessions</span>
                </div>
                <div class="p-3">
                    @php
                        $topSurahs = $mostReadSurahs ?? [
                            ['name' => 'Al-Fatiha', 'reads' => 980, 'number' => 1],
                            ['name' => 'Al-Baqarah', 'reads' => 820, 'number' => 2],
                            ['name' => 'Yasin', 'reads' => 760, 'number' => 36],
                            ['name' => 'Al-Mulk', 'reads' => 630, 'number' => 67],
                            ['name' => 'Al-Kahf', 'reads' => 590, 'number' => 18],
                            ['name' => "Al-'Alaq", 'reads' => 470, 'number' => 96],
                            ['name' => 'Ar-Rahman', 'reads' => 440, 'number' => 55],
                        ];
                        $maxReads = max(array_column($topSurahs, 'reads'));
                    @endphp
                    @foreach ($topSurahs as $i => $surah)
                        <div class="surah-bar-row">
                            <span style="width:18px; color:#ccc; font-size:0.72rem;">{{ $i + 1 }}</span>
                            <span class="surah-bar-label">{{ $surah['name'] }}</span>
                            <div class="surah-bar-track">
                                <div class="surah-bar-fill" style="width:{{ round(($surah['reads'] / $maxReads) * 100) }}%">
                                </div>
                            </div>
                            <span class="surah-bar-count">{{ $surah['reads'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>User Notes & Bookmarks</h6>
                </div>
                <div class="p-3">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div style="background:#F8F8F8; border-radius:10px; padding:1rem; text-align:center;">
                                <div style="font-family:var(--font-heading); font-size:1.8rem; color:var(--emerald)">
                                    {{ number_format($totalNotes ?? 0) }}
                                </div>
                                <div style="font-size:0.75rem; color:#999">Personal Notes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:#F8F8F8; border-radius:10px; padding:1rem; text-align:center;">
                                <div style="font-family:var(--font-heading); font-size:1.8rem; color:var(--gold)">
                                    {{ number_format($totalBookmarks ?? 0) }}
                                </div>
                                <div style="font-size:0.75rem; color:#999">Bookmarks</div>
                            </div>
                        </div>
                    </div>

                    <div style="font-size:0.8rem; color:#666;">
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #F5F5F5;">
                            <span>Avg notes per premium user</span>
                            <strong>{{ $avgNotesPremium ?? '—' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #F5F5F5;">
                            <span>Avg bookmarks per user</span>
                            <strong>{{ $avgBookmarks ?? '—' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Most bookmarked surah</span>
                            <strong style="color:var(--gold)">{{ $mostBookmarkedSurah ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 3 — CONTENT LIBRARY
══════════════════════════════════════════════════ --}}
    <div class="section-heading">Content Library</div>
    <div class="row g-3 mb-2">

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem">PROPHETS</p>
                        <div class="stat-number" style="color:var(--emerald); font-size:1.5rem;">
                            {{ $totalProphets ?? 25 }}</div>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-stars" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <small class="text-muted">{{ $totalStories ?? 0 }} stories published</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem">SAHABAS</p>
                        <div class="stat-number" style="color:var(--emerald); font-size:1.5rem;">{{ $totalSahabas ?? 0 }}
                        </div>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-people" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <span class="badge"
                    style="background:rgba(201,150,58,0.15); color:var(--gold-dark); font-size:0.65rem;">Coming Soon</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem">FOUR IMAMS</p>
                        <div class="stat-number" style="color:var(--emerald); font-size:1.5rem;">{{ $totalImams ?? 0 }}
                        </div>
                    </div>
                    <div class="stat-icon" style="background:rgba(27,94,59,0.1)">
                        <i class="bi bi-bank" style="color:var(--emerald)"></i>
                    </div>
                </div>
                <span class="badge"
                    style="background:rgba(201,150,58,0.15); color:var(--gold-dark); font-size:0.65rem;">Coming Soon</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1" style="font-size:0.7rem">ALLAH NAMES</p>
                        <div class="stat-number" style="color:var(--gold); font-size:1.5rem;">{{ $totalAllahNames ?? 0 }}
                        </div>
                    </div>
                    <div class="stat-icon" style="background:rgba(201,150,58,0.1)">
                        <i class="bi bi-brightness-high" style="color:var(--gold)"></i>
                    </div>
                </div>
                <small class="text-muted">Asma-ul-Husna</small>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 4 — USER ENGAGEMENT (top readers)
══════════════════════════════════════════════════ --}}
    <div class="section-heading">Top Users by Time Spent</div>
    <div class="row g-3 mb-2">

        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Most Engaged Users</h6>
                    <span style="font-size:0.72rem; color:#999">last 30 days</span>
                </div>
                <div class="p-3">
                    @forelse ($topEngagedUsers ?? [] as $user)
                        <div class="engagement-row">
                            <div class="engagement-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:0.85rem; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $user->name }}
                                </div>
                                <div style="font-size:0.72rem; color:#999;">{{ $user->email }}</div>
                            </div>
                            <span
                                class="badge badge-{{ $user->plan ?? 'free' }} me-2">{{ strtoupper($user->plan ?? 'FREE') }}</span>
                            <div style="text-align:right; white-space:nowrap;">
                                <div style="font-size:0.82rem; font-weight:600; color:var(--emerald)">
                                    {{ $user->total_minutes ?? 0 }} min</div>
                                <div style="font-size:0.68rem; color:#999">{{ $user->sessions ?? 0 }} sessions</div>
                            </div>
                        </div>
                    @empty
                        {{-- Placeholder rows when no data yet --}}
                        @for ($i = 0; $i < 5; $i++)
                            <div class="engagement-row" style="opacity:0.35">
                                <div class="engagement-avatar" style="background:#ccc">??</div>
                                <div style="flex:1">
                                    <div
                                        style="background:#F0F0F0; height:12px; border-radius:4px; width:60%; margin-bottom:4px;">
                                    </div>
                                    <div style="background:#F5F5F5; height:10px; border-radius:4px; width:80%;"></div>
                                </div>
                                <div style="background:#F0F0F0; height:14px; border-radius:4px; width:50px;"></div>
                            </div>
                        @endfor
                        <p class="text-center text-muted small mt-2">Session tracking not yet active</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Users table --}}
        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Recent Registrations</h6>
                    <a href="{{ route('admin.users.index') }}" style="font-size:0.75rem; color:var(--emerald);">View all
                        →</a>
                </div>
                <table class="table table-hover" style="font-size:0.82rem; margin:0;">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Verified</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentUsers ?? [] as $user)
                            <tr>
                                <td>
                                    <div style="font-weight:500">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td><span
                                        class="badge badge-{{ $user->plan ?? 'free' }}">{{ strtoupper($user->plan ?? 'FREE') }}</span>
                                </td>
                                <td>
                                    @if ($user->email_verified_at)
                                        <span class="status-dot green d-inline-block"></span>
                                    @else
                                        <span class="status-dot red d-inline-block"></span>
                                    @endif
                                </td>
                                <td style="color:#888">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 5 — COMPLAINTS & REVIEWS
══════════════════════════════════════════════════ --}}
    <div class="section-heading">User Feedback</div>
    <div class="row g-3 mb-2">

        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6><i class="bi bi-chat-left-text me-2" style="color:#ef4444"></i>Open Complaints</h6>
                    <span class="badge" style="background:#FEE2E2; color:#ef4444;">{{ $openComplaints ?? 0 }}
                        open</span>
                </div>
                <table class="table table-hover" style="font-size:0.82rem; margin:0;">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentComplaints ?? [] as $c)
                            <tr>
                                <td>{{ $c->user->name ?? '—' }}</td>
                                <td style="max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $c->subject ?? ($c->message ?? '—') }}
                                </td>
                                <td>
                                    <span class="badge"
                                        style="background:{{ $c->status === 'open' ? '#FEE2E2' : '#D1FAE5' }};
                                           color:{{ $c->status === 'open' ? '#ef4444' : '#059669' }};">
                                        {{ ucfirst($c->status ?? 'open') }}
                                    </span>
                                </td>
                                <td style="color:#888">{{ $c->created_at->format('d M') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No complaints yet — alhamdulillah!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6><i class="bi bi-star-half me-2" style="color:var(--gold)"></i>User Reviews</h6>
                    <span style="font-size:0.75rem; color:#999;">
                        ⭐ {{ number_format($avgRating ?? 0, 1) }} avg · {{ $totalReviews ?? 0 }} reviews
                    </span>
                </div>
                <div class="p-3">
                    @php $stars = [5,4,3,2,1]; @endphp
                    @foreach ($stars as $star)
                        @php
                            $count = $ratingBreakdown[$star] ?? 0;
                            $pct = ($totalReviews ?? 0) > 0 ? round(($count / $totalReviews) * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.78rem;">
                            <span style="width:12px; text-align:right; color:#666;">{{ $star }}</span>
                            <i class="bi bi-star-fill" style="color:var(--gold); font-size:0.7rem;"></i>
                            <div class="progress flex-grow-1" style="height:7px;">
                                <div class="progress-bar-gold progress-bar" style="width:{{ $pct }}%"></div>
                            </div>
                            <span style="width:28px; color:#999;">{{ $count }}</span>
                        </div>
                    @endforeach

                    {{-- Recent review snippets --}}
                    @if (!empty($recentReviews))
                        <div style="border-top:1px solid #F5F5F5; margin-top:10px; padding-top:10px;">
                            @foreach (array_slice($recentReviews ?? [], 0, 2) as $review)
                                <div style="font-size:0.78rem; margin-bottom:8px;">
                                    <span style="color:var(--gold)">{{ str_repeat('★', $review->rating ?? 5) }}</span>
                                    <span
                                        style="color:#666; margin-left:4px;">"{{ Str::limit($review->comment, 60) }}"</span>
                                    <span style="color:#ccc; font-size:0.68rem;"> —
                                        {{ $review->user->name ?? 'User' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 6 — PAYMENTS
══════════════════════════════════════════════════ --}}
    <div class="section-heading">Recent Payments</div>
    <div class="admin-table mb-4">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background:#FAFAFA;">
            <h6 class="mb-0" style="font-family:var(--font-heading); font-size:0.82rem;">Payment Ledger</h6>
            <span style="font-size:0.75rem; color:#999;">1 USD = ₹84.50 INR (approx)</span>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Type</th>
                    <th>USD</th>
                    <th>INR</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentSubs ?? [] as $sub)
                    @php
                        $usd = $sub->amount ?? ($sub->plan?->slug === 'premium' ? 3.99 : 1.99);
                        $inr = $usd * 84.5;
                    @endphp
                    <tr>
                        <td>{{ $sub->user->name ?? '—' }}</td>
                        <td><span
                                class="badge badge-{{ $sub->plan?->slug ?? 'free' }}">{{ strtoupper($sub->plan?->slug ?? '—') }}</span>
                        </td>
                        <td style="color:#666">{{ ucfirst($sub->type ?? 'monthly') }}</td>
                        <td style="color:var(--emerald); font-weight:500">${{ number_format($usd, 2) }}</td>
                        <td style="color:#999; font-size:0.78rem;">₹{{ number_format($inr, 0) }}</td>
                        <td style="color:#888">{{ $sub->created_at->format('d M Y') }}</td>
                        <td><span class="badge" style="background:#D1FAE5; color:#059669; font-size:0.65rem;">Paid</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════
     SECTION 7 — AI SUPPORT CHATBOT (coming soon)
══════════════════════════════════════════════════ --}}
    <div class="section-heading" id="ai-chatbot">AI Support Chatbot</div>
    <div class="chatbot-card mb-4">
        <div class="d-flex align-items-start gap-3" style="position:relative;z-index:1;">
            <div
                style="width:44px;height:44px;border-radius:50%;background:rgba(201,150,58,0.15);
                    border:1.5px solid rgba(201,150,58,0.3);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-robot" style="color:var(--gold-light);font-size:1.2rem;"></i>
            </div>
            <div>
                <div style="font-family:var(--font-heading);color:var(--gold-light);font-size:0.9rem;margin-bottom:4px;">
                    Tadabbur AI Support Bot
                    <span class="badge ms-2"
                        style="background:rgba(201,150,58,0.15);color:var(--gold-light);font-size:0.65rem;">Coming
                        Soon</span>
                </div>
                <p style="color:rgba(255,255,255,0.65);font-size:0.82rem;margin:0;max-width:520px;line-height:1.6;">
                    An AI assistant trained exclusively on Taddabur's app help content — handling payment questions,
                    subscription issues, feature queries, and account problems. Reduces support load without
                    touching religious content.
                </p>
            </div>
        </div>

        {{-- Preview chat UI --}}
        <div class="chatbot-messages" style="position:relative;z-index:1;">
            <div class="chat-bubble user">"I was charged but my plan didn't upgrade"</div>
            <div class="chat-bubble bot">I can help with that. Your payment was received and I can see your account. Let me
                check the subscription status — one moment...</div>
            <div class="chat-bubble user">"How do I cancel my subscription?"</div>
            <div class="chat-bubble bot">You can cancel anytime from Profile → Subscription → Cancel Plan. Your access
                continues until the end of your billing period.</div>
        </div>

        <div style="position:relative;z-index:1;" class="d-flex flex-wrap gap-3 mt-1">
            <div
                style="background:rgba(255,255,255,0.05);border-radius:10px;padding:12px 16px;font-size:0.78rem;border:1px solid rgba(201,150,58,0.15);">
                <div style="color:var(--gold-light);font-weight:600;margin-bottom:4px;"><i
                        class="bi bi-shield-check me-1"></i>Scope</div>
                <div style="color:rgba(255,255,255,0.55);">App issues · Payments · Plans · Account</div>
            </div>
            <div
                style="background:rgba(255,255,255,0.05);border-radius:10px;padding:12px 16px;font-size:0.78rem;border:1px solid rgba(201,150,58,0.15);">
                <div style="color:var(--gold-light);font-weight:600;margin-bottom:4px;"><i
                        class="bi bi-x-circle me-1"></i>Never answers</div>
                <div style="color:rgba(255,255,255,0.55);">Fatawa · Quran interpretation · Fiqh</div>
            </div>
            <div
                style="background:rgba(255,255,255,0.05);border-radius:10px;padding:12px 16px;font-size:0.78rem;border:1px solid rgba(201,150,58,0.15);">
                <div style="color:var(--gold-light);font-weight:600;margin-bottom:4px;"><i
                        class="bi bi-translate me-1"></i>Languages</div>
                <div style="color:rgba(255,255,255,0.55);">English · Arabic · Urdu (planned)</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Animate stat numbers on page load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.stat-number[data-target]').forEach(el => {
                const target = parseInt(el.dataset.target);
                const duration = 1000;
                const start = performance.now();
                const fmt = el.dataset.format === 'comma';
                const step = (now) => {
                    const pct = Math.min((now - start) / duration, 1);
                    const val = Math.round(pct * target);
                    el.textContent = fmt ? val.toLocaleString() : val;
                    if (pct < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            });
        });
    </script>
@endpush

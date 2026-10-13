@extends('admin.layout')
@section('title', 'Dashboard')

@push('styles')
    <style>
        .stat-label {
            font-size: .7rem;
            letter-spacing: .05em;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: .25rem;
        }

        .sparkline {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 36px;
        }

        .spark-bar {
            flex: 1;
            min-width: 6px;
            border-radius: 3px 3px 0 0;
            background: rgba(27, 94, 59, .2);
        }

        .spark-bar.today {
            background: var(--emerald-light);
        }

        .spark-bar:hover {
            background: var(--gold);
        }

        .surah-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: .82rem;
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
            width: 110px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .surah-bar-count {
            width: 44px;
            text-align: right;
            color: #888;
            font-size: .75rem;
        }

        .engagement-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #F5F5F5;
            font-size: .82rem;
        }

        .engagement-row:last-child {
            border-bottom: none;
        }

        .engagement-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .75rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--emerald), var(--emerald-light));
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.green {
            background: #22c55e;
        }

        .status-dot.red {
            background: #ef4444;
        }

        .section-heading {
            font-family: var(--font-heading);
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #999;
            margin: 1.75rem 0 .75rem;
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

        .stack-cards>.stat-card {
            height: auto;
        }

        html {
            scroll-behavior: smooth;
        }

        .section-heading {
            scroll-margin-top: 80px;
        }

        @keyframes flash-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(201, 150, 58, 0);
            }

            25% {
                box-shadow: 0 0 0 4px rgba(201, 150, 58, .55);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(201, 150, 58, 0);
            }
        }

        @keyframes flash-heading {

            0%,
            40% {
                color: var(--gold-dark);
            }

            100% {
                color: #999;
            }
        }

        .flash-highlight {
            animation: flash-ring 1.6s ease-out 1;
        }

        .section-heading.flash-highlight {
            animation: flash-heading 1.6s ease-out 1;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .flash-highlight {
                animation: none;
                outline: 2px solid var(--gold);
            }
        }
    </style>
@endpush

@section('content')

    <div class="text-muted" style="font-size:.75rem">
        Live data from database · {{ now()->format('d M Y, h:i A') }}
        @if (count($alerts))
            <div class="mt-3 d-flex flex-column gap-2">
                @foreach ($alerts as [$level, $text, $link])
                    <a href="{{ $link }}"
                        class="alert alert-{{ $level }} py-2 mb-0 text-decoration-none d-flex justify-content-between align-items-center"
                        style="font-size:.85rem">
                        <span><i class="bi bi-exclamation-triangle me-2"></i>{{ $text }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="alert alert-success py-2 mt-3 mb-0" style="font-size:.85rem">
                <i class="bi bi-check-circle me-2"></i>All checks passed.
            </div>
        @endif
    </div>

    {{-- ══ USERS ══ --}}
    <div class="section-heading">Users</div>
    <div class="row g-3 mb-2">
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Total Users',
                'value' => number_format($users['total']),
                'icon' => 'people',
                'note' => $users['verified'] . ' verified · ' . $users['unverified'] . ' unverified',
            ])
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">New this month</div>
                <div class="stat-number" style="color:var(--emerald)">{{ number_format($users['newThisMonth']) }}</div>
                @php $max = max(1, collect($users['daily'])->max('count')); @endphp
                <div class="sparkline mt-2">
                    @foreach ($users['daily'] as $day)
                        <div class="spark-bar {{ $loop->last ? 'today' : '' }}"
                            style="height:{{ max(8, ($day['count'] / $max) * 100) }}%"
                            title="{{ $day['label'] }}: {{ $day['count'] }} signups"></div>
                    @endforeach
                </div>
                <small class="text-muted">last 7 days</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Email verification</div>
                @php $rate = $users['verifiedRate']; @endphp
                <div class="stat-number"
                    style="color:{{ $rate >= 80 ? 'var(--emerald)' : ($rate >= 50 ? 'var(--gold)' : '#ef4444') }}">
                    {{ $rate }}%
                </div>
                <div class="progress mt-2" style="height:6px">
                    <div class="progress-bar"
                        style="width:{{ $rate }}%; background:{{ $rate >= 80 ? 'var(--emerald-light)' : ($rate >= 50 ? 'var(--gold)' : '#ef4444') }}">
                    </div>
                </div>
                @if ($users['total'] > 0 && $rate < 70)
                    <small class="text-danger d-block mt-1">Low rate — verification emails check karein.</small>
                @endif
            </div>
        </div>

        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Active users (7 days)',
                'value' => number_format($users['active7d']),
                'icon' => 'activity',
                'note' => 'reading, listening, hadith activity',
            ])
        </div>
    </div>

    {{-- ══ QURAN ══ --}}
    <div class="section-heading">Quran Content & Engagement</div>
    <div class="row g-3 mb-3">
        @foreach ([['Surahs', $quran['surahs'], 'book', 'emerald'], ['Ayahs', $quran['ayahs'], 'list-ol', 'emerald'], ['Juz', $quran['juzs'], 'collection', 'gold'], ['Bookmarks', $quran['bookmarks'], 'bookmark-fill', 'gold'], ['Personal notes', $quran['notes'], 'journal-text', 'emerald'], ['Hadiths read', $quran['hadithReads'], 'check2-circle', 'gold']] as [$label, $value, $icon, $tone])
            <div class="col-6 col-md-2">
                @include('admin.partials.stat', [
                    'label' => $label,
                    'value' => number_format($value),
                    'icon' => $icon,
                    'tone' => $tone,
                ])
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-2">
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Ayahs read',
                'value' => number_format($quran['read']),
                'icon' => 'check2-all',
                'note' => 'across all users',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Ayahs listened',
                'value' => number_format($quran['listened']),
                'icon' => 'headphones',
                'note' => 'audio recitations',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Chapters completed',
                'value' => number_format($quran['chapters']),
                'icon' => 'patch-check',
                'tone' => 'gold',
                'note' => 'story chapters finished',
            ])
        </div>
        <div class="col-6 col-md-3">
            @include('admin.partials.stat', [
                'label' => 'Avg bookmarks / user',
                'value' => $quran['avgBookmarks'],
                'icon' => 'bookmark-star',
                'tone' => 'gold',
                'note' => 'Most bookmarked: ' . ($quran['mostBookmarkedSurah'] ?? '—'),
            ])
        </div>
    </div>

    {{-- ══ LIBRARY ══ --}}
    <div class="section-heading">Content Library</div>
    <div class="row g-3 mb-2">
        @foreach ([['Prophets', $library['prophets'], 'stars', 'emerald', null], ['Stories', $library['stories'], 'journal-richtext', 'emerald', $library['published'] . ' published'], ['Story chapters', $library['chapters'], 'file-text', 'gold', null], ['Hadiths', $library['hadiths'], 'chat-quote', 'gold', $library['collections'] . ' collections'], ['Allah names', $library['allahNames'], 'brightness-high', 'emerald', 'Asma-ul-Husna'], ['Scholars', $library['scholars'], 'bank', 'gold', null]] as [$label, $value, $icon, $tone, $note])
            <div class="col-6 col-md-2">
                @include(
                    'admin.partials.stat',
                    array_filter([
                        'label' => $label,
                        'value' => number_format($value),
                        'icon' => $icon,
                        'tone' => $tone,
                        'note' => $note,
                    ]))
            </div>
        @endforeach
    </div>

    {{-- ══ ENGAGEMENT ══ --}}
    <div class="section-heading">Reading Activity</div>
    <div class="row g-3 mb-2">
        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Most Read Surahs</h6><span class="text-muted" style="font-size:.72rem">ayahs read</span>
                </div>
                <div class="p-3">
                    @forelse ($topSurahs as $surah)
                        <div class="surah-bar-row">
                            <span class="surah-bar-label">{{ $surah->number }}. {{ $surah->name }}</span>
                            <div class="surah-bar-track">
                                <div class="surah-bar-fill"
                                    style="width:{{ round(($surah->reads / $topSurahs->first()->reads) * 100) }}%"></div>
                            </div>
                            <span class="surah-bar-count">{{ number_format($surah->reads) }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No reading activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Top Readers</h6><span class="text-muted" style="font-size:.72rem">last 30 days</span>
                </div>
                <div class="p-3">
                    @forelse ($topReaders as $reader)
                        <div class="engagement-row">
                            <div class="engagement-avatar">{{ strtoupper(mb_substr($reader->name, 0, 2)) }}</div>
                            <div class="flex-grow-1" style="min-width:0">
                                <div class="text-truncate fw-medium">{{ $reader->name }}</div>
                                <div class="text-muted text-truncate" style="font-size:.72rem">{{ $reader->email }}</div>
                            </div>
                            <div class="text-end text-nowrap">
                                <div class="fw-semibold" style="color:var(--emerald)">
                                    {{ number_format($reader->ayahs_read) }} ayahs</div>
                                <div class="text-muted" style="font-size:.68rem">{{ $reader->active_days }} active days
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No reading activity in the last 30 days.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h6>Recent Registrations</h6>
                    <a href="{{ route('admin.users.index') }}" style="font-size:.75rem; color:var(--emerald)">View all
                        →</a>
                </div>
                <table class="table table-hover mb-0" style="font-size:.82rem">
                    <tbody>
                        @forelse ($users['recent'] as $u)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $u) }}"
                                        class="text-decoration-none text-dark fw-medium">{{ $u->name }}</a>
                                    <div class="text-muted" style="font-size:.7rem">{{ $u->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="status-dot {{ $u->email_verified_at ? 'green' : 'red' }}"
                                        title="{{ $u->email_verified_at ? 'Verified' : 'Not verified' }}"></span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-3">No users yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══ FEEDBACK ══ --}}
    <div class="section-heading" id="feedback">User Feedback</div>
    <div class="row g-3 mb-2">
        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Complaints</h6>
                    <span class="badge bg-danger-subtle text-danger">{{ $feedback['openComplaints'] }} open</span>
                </div>
                <table class="table table-hover mb-0" style="font-size:.82rem">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($feedback['complaints'] as $c)
                            <tr>
                                <td>{{ $c->user->name ?? '—' }}</td>
                                <td class="text-truncate" style="max-width:160px">{{ $c->subject ?: $c->message }}</td>
                                <td>
                                    <span
                                        class="badge {{ $c->status === 'open' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $c->created_at->format('d M') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No complaints yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Reviews</h6>
                    <span class="text-muted" style="font-size:.75rem">
                        {{ $feedback['reviewCount'] > 0 ? '★ ' . number_format($feedback['avgRating'], 1) . ' avg · ' : '' }}{{ $feedback['reviewCount'] }}
                        reviews
                    </span>
                </div>
                <div class="p-3">
                    @foreach ([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $feedback['breakdown'][$star] ?? 0;
                            $pct = $feedback['reviewCount'] > 0 ? round(($count / $feedback['reviewCount']) * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size:.78rem">
                            <span style="width:12px">{{ $star }}</span>
                            <i class="bi bi-star-fill" style="color:var(--gold); font-size:.7rem"></i>
                            <div class="progress flex-grow-1" style="height:7px">
                                <div class="progress-bar progress-bar-gold" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="text-muted" style="width:28px">{{ $count }}</span>
                        </div>
                    @endforeach

                    @foreach ($feedback['reviews'] as $review)
                        <div class="border-top pt-2 mt-2" style="font-size:.78rem">
                            <span style="color:var(--gold)">{{ str_repeat('★', (int) $review->rating) }}</span>
                            {{ \Illuminate\Support\Str::limit($review->comment, 80) }}
                            <span class="text-muted">— {{ $review->user->name ?? 'User' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ══ DONATIONS ══ --}}
    <div class="section-heading" id="donations">Donations (Voluntary Support)</div>
    <div class="row g-3 mb-2">
        <div class="col-md-3">
            <div class="d-flex flex-column gap-3 stack-cards">
                @include('admin.partials.stat', [
                    'label' => 'Total received',
                    'value' => '₹' . number_format($donations['paidTotal'], 2),
                    'icon' => 'heart',
                    'tone' => 'gold',
                    'note' => $donations['paidCount'] . ' successful of ' . $donations['attempts'] . ' attempts',
                ])
                <div class="stat-card">
                    <div class="stat-label">By status</div>
                    @forelse ($donations['byStatus'] as $status => $total)
                        <div class="d-flex justify-content-between" style="font-size:.8rem">
                            <span>{{ ucfirst($status ?: 'unknown') }}</span><strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <small class="text-muted">No donations yet.</small>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Latest 5 of {{ $donations['attempts'] }} attempts</small>
                <a href="{{ route('admin.donations.index') }}" style="font-size:.78rem; color:var(--emerald)">View all
                    →</a>
            </div>
            <div class="admin-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donations['recent'] as $d)
                            <tr>
                                <td>{{ $d->name ?: 'Anonymous' }}</td>
                                <td class="fw-medium" style="color:var(--emerald)">₹{{ number_format($d->amount, 2) }}
                                </td>
                                <td class="text-muted">{{ $d->payment_method ?? '—' }}</td>
                                <td>
                                    <span
                                        class="badge {{ $d->status === $donations['paidStatus'] ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($d->status ?? '—') }}
                                    </span>
                                </td>
                                <td class="text-muted text-truncate" style="max-width:180px">{{ $d->message ?: '—' }}
                                </td>
                                <td class="text-muted">{{ $d->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No donations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══ DATA SOURCES & INTEGRITY ══ --}}
    <div class="section-heading" id="data-sources">Data Sources & Integrity</div>

    <div class="admin-card mb-3">
        <div class="admin-card-header">
            <h6>Quran Text & Reference Data</h6>
        </div>
        <table class="table mb-0" style="font-size:.82rem">
            <thead>
                <tr>
                    <th>Check</th>
                    <th>Actual</th>
                    <th>Expected</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sources['checks'] as $check)
                    <tr>
                        <td>{{ $check['label'] }}</td>
                        <td>{{ number_format($check['actual']) }}</td>
                        <td>{{ number_format($check['expected']) }}</td>
                        <td><span
                                class="badge {{ $check['ok'] ? 'bg-success' : 'bg-danger' }}">{{ $check['ok'] ? 'OK' : 'CHECK' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @foreach (['Translations' => $sources['translations'], 'Tafsir' => $sources['tafsirs']] as $title => $rows)
        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h6>{{ $title }}</h6>
                <span class="text-muted" style="font-size:.72rem">coverage = entries /
                    {{ number_format($sources['totalAyahs']) }} ayahs</span>
            </div>
            <table class="table mb-0" style="font-size:.82rem">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Author / Scholar</th>
                        <th>Language</th>
                        <th>Source</th>
                        <th style="width:200px">Coverage</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr>
                            <td class="fw-medium">{{ $r->name }}</td>
                            <td>{{ $r->author ?? ($r->scholar ?? '—') }}</td>
                            <td>{{ $r->language_name }}</td>
                            <td class="text-muted">{{ $r->source ?: '—' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar"
                                            style="width:{{ $r->percent }}%; background:{{ $r->percent >= 100 ? 'var(--emerald-light)' : 'var(--gold)' }}">
                                        </div>
                                    </div>
                                    <span class="{{ $r->percent >= 100 ? 'text-success' : 'text-warning' }}">
                                        {{ number_format($r->entries) }} ({{ $r->percent }}%)
                                    </span>
                                </div>
                            </td>
                            <td>{{ $r->is_active ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No sources found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="admin-card mb-3">
        <div class="admin-card-header">
            <h6>Hadith Collections</h6>
        </div>
        <table class="table mb-0" style="font-size:.82rem">
            <thead>
                <tr>
                    <th>Collection</th>
                    <th>Scholar</th>
                    <th>Official total</th>
                    <th>Imported</th>
                    <th>Needs review</th>
                    <th>Translation incomplete</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sources['hadith'] as $h)
                    @php $clean = $h->actual == $h->total_hadith && $h->needs_review == 0 && $h->incomplete == 0; @endphp
                    <tr>
                        <td class="fw-medium">{{ $h->name }}</td>
                        <td>{{ $h->scholar ?? '—' }}</td>
                        <td>{{ number_format($h->total_hadith) }}</td>
                        <td>{{ number_format($h->actual) }}</td>
                        <td>{{ number_format($h->needs_review) }}</td>
                        <td>{{ number_format($h->incomplete) }}</td>
                        <td><span
                                class="badge {{ $clean ? 'bg-success' : 'bg-warning text-dark' }}">{{ $clean ? 'OK' : 'REVIEW' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No collections found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h6>Reciters · Word-by-word Sync</h6>
            <span class="text-muted" style="font-size:.72rem">
                {{ $sources['reciters']->where('sync', 'word')->count() }} word-level ·
                {{ $sources['reciters']->where('sync', 'partial')->count() }} partial ·
                {{ $sources['reciters']->where('sync', 'approximate')->count() }} approximate
            </span>
        </div>
        <table class="table mb-0" style="font-size:.82rem">
            <thead>
                <tr>
                    <th>Reciter</th>
                    <th>Style</th>
                    <th>Active</th>
                    <th>Word sync</th>
                    <th>Stored timings</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sources['reciters'] as $r)
                    <tr>
                        <td class="fw-medium">{{ $r->name }}</td>
                        <td class="text-muted">{{ $r->style ?: '—' }}</td>
                        <td>{{ $r->is_active ? 'Yes' : 'No' }}</td>
                        <td>
                            @if ($r->sync === 'word')
                                <span class="badge bg-success">Word-level</span>
                            @elseif ($r->sync === 'partial')
                                <span class="badge bg-warning text-dark">Partial · {{ $r->missing_ayahs }}
                                    {{ \Illuminate\Support\Str::plural('ayah', $r->missing_ayahs) }} missing</span>
                            @else
                                <span class="badge bg-secondary">Approximate</span>
                            @endif
                        </td>
                        <td class="text-muted">
                            {{ $r->timing_rows ? number_format($r->timing_rows) . ' rows · ' . number_format($r->ayahs_covered) . ' ayahs' : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">No reciters found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
    <script>
        function flashSection(id) {
            const heading = document.getElementById(id);
            if (!heading) return;

            const targets = [heading];
            const cards = '.admin-card, .admin-table, .stat-card';

            for (let el = heading.nextElementSibling; el && !el.classList.contains('section-heading'); el = el
                .nextElementSibling) {
                if (el.matches(cards)) targets.push(el);
                else targets.push(...el.querySelectorAll(cards));
            }

            targets.forEach(el => {
                el.classList.remove('flash-highlight');
                void el.offsetWidth; // animation dobara chalane ke liye
                el.classList.add('flash-highlight');
            });
        }

        function goToSection(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            setTimeout(() => flashSection(id), 450);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', e => {
                    const id = a.getAttribute('href').slice(1);
                    if (!id || !document.getElementById(id)) return;
                    e.preventDefault();
                    history.replaceState(null, '', '#' + id);
                    goToSection(id);
                });
            });

            // sidebar ka "Data Sources" link (full URL + #hash) ke liye
            window.addEventListener('hashchange', () => goToSection(location.hash.slice(1)));

            // dusre page se #hash ke saath aane par
            if (location.hash.length > 1) setTimeout(() => goToSection(location.hash.slice(1)), 150);
        });
    </script>
@endpush

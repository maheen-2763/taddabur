@extends('admin.layout')
@section('title', 'Changelog')

@section('content')
    <div class="mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">Changelog</h5>
        <small class="text-muted">Release notes · read-only, from CHANGELOG.md</small>

    </div>

    @if ($html === '')
        <div class="alert alert-warning">CHANGELOG.md not found.</div>
    @else
        <article class="changelog admin-table p-4">{!! $html !!}</article>
    @endif

    @php
        $rangeText = $commits->total() > 0
            ? "{$commits->firstItem()}-{$commits->lastItem()} of {$commits->total()}"
            : null;

        $statusInfo = [
            'A' => ['Added', 'bg-success'],
            'M' => ['Modified', 'bg-primary'],
            'D' => ['Deleted', 'bg-danger'],
            'R' => ['Renamed', 'bg-info text-dark'],
            'C' => ['Copied', 'bg-secondary'],
            'T' => ['Type changed', 'bg-secondary'],
        ];
    @endphp

    <h6 class="mt-4 mb-2">
        Recent changes
        @if ($rangeText)
            <small class="text-muted fw-normal">({{ $rangeText }})</small>
        @endif
    </h6>

    @if ($builtAt)
        <div class="small text-muted mb-2">
            List built {{ $builtAt->copy()->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST
            @if ($latestCommit)
                · latest commit <code>{{ $latestCommit['hash'] }}</code>
            @endif
        </div>
    @endif

    @forelse ($commitsByDate as $date => $dayCommits)
        <div class="admin-table p-3 mb-3">
            <div class="small text-muted mb-2">{{ $date }}</div>

            @foreach ($dayCommits as $commit)
                @if ($commit['files'])
                    <details class="mb-1">
                        <summary class="d-flex gap-2 align-items-baseline flex-wrap" style="cursor:pointer">
                            @include('admin.changelog._commit-title')
                            <span class="ms-auto d-flex gap-1">
                                @foreach ($statusInfo as $code => [$label, $badge])
                                    @if (($commit['counts'][$code] ?? 0) > 0)
                                        <span class="badge {{ $badge }}">{{ $commit['counts'][$code] }} {{ strtolower($label) }}</span>
                                    @endif
                                @endforeach
                            </span>
                        </summary>
                        <ul class="list-unstyled small mt-2 mb-1 ms-4">
                            @foreach ($commit['files'] as $file)
                                <li class="d-flex gap-2 align-items-baseline">
                                    <span class="badge text-center {{ $statusInfo[$file['status']][1] ?? 'bg-secondary' }}"
                                        style="min-width:1.5rem"
                                        title="{{ $statusInfo[$file['status']][0] ?? $file['status'] }}">{{ $file['status'] }}</span>
                                    <code class="text-break">{{ $file['path'] }}</code>
                                    @if (!empty($file['from']))
                                        <span class="text-muted">(from {{ $file['from'] }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($commit['files_total'] > count($commit['files']))
                            <div class="small text-muted ms-4">+ {{ $commit['files_total'] - count($commit['files']) }} more file(s)</div>
                        @endif
                    </details>
                @else
                    <div class="d-flex gap-2 align-items-baseline mb-1">
                        @include('admin.changelog._commit-title')
                    </div>
                @endif
            @endforeach
        </div>
    @empty
        <p class="text-muted small">
            No git history yet. Run <code>php artisan changelog:build-git</code> after each deploy.
        </p>
    @endforelse
    @if ($commits->hasPages())
        <div class="mt-2">{{ $commits->links() }}</div>
    @endif



    <style>
        .changelog h1 {
            font-size: 1.25rem;
            margin-bottom: .75rem;
        }

        .changelog h2 {
            font-size: 1.05rem;
            margin-top: 1.5rem;
            padding-bottom: .35rem;
            border-bottom: 1px solid #dee2e6;
        }

        .changelog h3 {
            font-size: .8rem;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
        }

        .changelog ul {
            padding-left: 1.2rem;
        }
    </style>
@endsection
@extends('admin.layout')
@section('title', 'App Overview')

@section('content')
    @php
        $size = $overview['size'];
        $code = $overview['code'];
        $structure = $overview['structure'];
        $env = $overview['environment'];
        $packages = $overview['packages'];
        $routes = $structure['routes'];

        $format = fn(int $bytes): string => \Illuminate\Support\Number::fileSize($bytes, 1);
        $generated = \Illuminate\Support\Carbon::parse($overview['generated_at'])->timezone('Asia/Kolkata')->format('d M Y, h:i A');
        $subtitle = 'Generated ' . $generated . ' IST · cached for ' . \App\Support\AppOverview::TTL_MINUTES . ' minutes';
        $totalBytes = max(1, $size['total_bytes']);
        $routesDetail = $routes['admin'] . ' admin · ' . $routes['public'] . ' public';
        $testsDetail = $structure['test_files'] . ' test files';
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h5 class="mb-0" style="font-family:var(--font-heading)"><i class="bi bi-diagram-3 me-2"></i>App Overview</h5>
            <small class="text-muted">{{ $subtitle }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.system.architecture') }}" class="btn btn-admin-outline">
                <i class="bi bi-diagram-2 me-1"></i>Architecture
            </a>
            <a href="{{ route('admin.system.export') }}" class="btn btn-admin-outline">
                <i class="bi bi-download me-1"></i>Export Markdown
            </a>
            <form action="{{ route('admin.system.refresh') }}" method="POST"
                onsubmit="const b = this.querySelector('button'); b.disabled = true; b.innerText = 'Scanning...';">
                @csrf
                <button class="btn btn-admin-gold"><i class="bi bi-arrow-repeat me-1"></i>Refresh</button>
            </form>
        </div>
    </div>

    @if ($env['debug'] && $env['environment'] === 'production')
        <div class="alert alert-danger py-2">{{ 'APP_DEBUG is on in production. Turn it off before launch.' }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="text-muted small">Total size</div>
                <div class="stat-number">{{ $format($size['total_bytes']) }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="text-muted small">Lines of code</div>
                <div class="stat-number">{{ number_format($code['total_lines']) }}</div>
                <div class="small text-muted">{{ number_format($code['total_files']) }} files</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="text-muted small">Routes</div>
                <div class="stat-number">{{ $routes['total'] }}</div>
                <div class="small text-muted">{{ $routesDetail }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="text-muted small">Tests</div>
                <div class="stat-number">{{ number_format($structure['test_methods']) }}</div>
                <div class="small text-muted">{{ $testsDetail }}</div>
            </div>
        </div>
    </div>

    <div class="admin-table mb-4">
        <div class="admin-card-header">
            <h6>Size breakdown</h6>
        </div>
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Entry</th>
                    <th>Type</th>
                    <th class="text-end">Size</th>
                    <th style="width:30%">Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($size['entries'] as $entry)
                    @php
                        $share = round($entry['bytes'] / $totalBytes * 100, 1);
                    @endphp
                    <tr>
                        <td style="font-family:monospace; font-size:.8rem">{{ $entry['name'] }}</td>
                        <td><span class="badge bg-secondary">{{ $entry['type'] }}</span></td>
                        <td class="text-end text-nowrap">{{ $format($entry['bytes']) }}</td>
                        <td>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar progress-bar-gold" style="width: {{ $share }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($share, 1) }}%</small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-3 py-2 small text-muted border-top">
            @if ($size['database_bytes'] !== null)
                <span class="me-3">Database file: <strong>{{ $format($size['database_bytes']) }}</strong></span>
            @endif
            @foreach ($size['storage'] as $name => $bytes)
                <span class="me-3">storage/{{ $name }}: <strong>{{ $format($bytes) }}</strong></span>
            @endforeach
            @if ($size['skipped'])
                <span>Not scanned: {{ implode(', ', $size['skipped']) }}</span>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Code</h6>
                </div>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-end">Files</th>
                            <th class="text-end">Lines</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($code['types'] as $type => $counts)
                            <tr>
                                <td>{{ $type }}</td>
                                <td class="text-end">{{ number_format($counts['files']) }}</td>
                                <td class="text-end">{{ number_format($counts['lines']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-semibold">
                            <td>Total</td>
                            <td class="text-end">{{ number_format($code['total_files']) }}</td>
                            <td class="text-end">{{ number_format($code['total_lines']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Structure</h6>
                </div>
                <div class="row g-0">
                    <div class="col-md-5 p-3 small border-end">
                        <div class="d-flex justify-content-between"><span>Blade
                                views</span><strong>{{ $structure['blade_views'] }}</strong></div>
                        <div class="d-flex justify-content-between">
                            <span>Migrations</span><strong>{{ $structure['migrations'] }}</strong></div>
                        <div class="d-flex justify-content-between">
                            <span>Seeders</span><strong>{{ $structure['seeders'] }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Artisan
                                commands</span><strong>{{ $structure['artisan_commands'] }}</strong></div>
                    </div>
                    <div class="col-md-7 p-3 small">
                        <div class="text-muted mb-1">PHP files in app/</div>
                        @foreach ($structure['app_groups'] as $group => $count)
                            <div class="d-flex justify-content-between"><span>{{ $group }}</span><strong>{{ $count }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Environment</h6>
                </div>
                <dl class="row mb-0 p-3 small">
                    <dt class="col-5">PHP</dt>
                    <dd class="col-7">{{ $env['php'] }}</dd>
                    <dt class="col-5">Laravel</dt>
                    <dd class="col-7">{{ $env['laravel'] }}</dd>
                    <dt class="col-5">Environment</dt>
                    <dd class="col-7">{{ $env['environment'] }}</dd>
                    <dt class="col-5">Debug</dt>
                    <dd class="col-7">
                        @if ($env['debug'])
                            <span class="badge bg-danger">on</span>
                        @else
                            <span class="badge bg-success">off</span>
                        @endif
                    </dd>
                    <dt class="col-5">Timezone</dt>
                    <dd class="col-7">{{ $env['timezone'] }}</dd>
                    <dt class="col-5">Database</dt>
                    <dd class="col-7">{{ $env['database'] }}</dd>
                    <dt class="col-5">Session driver</dt>
                    <dd class="col-7">{{ $env['session_driver'] }}</dd>
                    <dt class="col-5">Cache store</dt>
                    <dd class="col-7">{{ $env['cache_store'] }}</dd>
                    <dt class="col-5">Queue</dt>
                    <dd class="col-7">{{ $env['queue'] }}</dd>
                    <dt class="col-5">Mailer</dt>
                    <dd class="col-7">{{ $env['mailer'] }}</dd>
                </dl>
                <details class="px-3 pb-3 small">
                    <summary style="cursor:pointer">PHP extensions ({{ count($env['extensions']) }})</summary>
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        @foreach ($env['extensions'] as $extension)
                            <span class="badge bg-light text-dark border">{{ $extension }}</span>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Packages</h6>
                </div>
                <div class="p-3 small">
                    <div class="mb-2">
                        <strong>{{ count($packages['production']) }}</strong> production ·
                        <strong>{{ $packages['dev_count'] }}</strong> dev
                    </div>
                    <details>
                        <summary style="cursor:pointer">Production packages</summary>
                        <div class="mt-2" style="max-height:320px; overflow-y:auto">
                            @foreach ($packages['production'] as $package)
                                <div class="d-flex justify-content-between border-bottom">
                                    <span class="text-break">{{ $package['name'] }}</span>
                                    <code>{{ $package['version'] }}</code>
                                </div>
                            @endforeach
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
@endsection
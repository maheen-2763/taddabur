@extends('admin.layout')
@section('title', 'Architecture')

@section('content')
    @php
        $generated = \Illuminate\Support\Carbon::parse($architecture['generated_at'])->timezone('Asia/Kolkata')->format('d M Y, h:i A');
        $subtitle = 'Generated from the live code on ' . $generated . ' IST · cached for ' . \App\Support\AppArchitecture::TTL_MINUTES . ' minutes';
        $otherRoutes = 'Closure and framework routes: ' . $architecture['routes']['other'];
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h5 class="mb-0" style="font-family:var(--font-heading)"><i class="bi bi-diagram-2 me-2"></i>Architecture</h5>
            <small class="text-muted">{{ $subtitle }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.system.index') }}" class="btn btn-admin-outline">
                <i class="bi bi-arrow-left me-1"></i>Overview
            </a>
            <a href="{{ route('admin.system.architecture.export') }}" class="btn btn-admin-outline">
                <i class="bi bi-download me-1"></i>Export Markdown
            </a>
            <form action="{{ route('admin.system.architecture.refresh') }}" method="POST"
                onsubmit="const b = this.querySelector('button'); b.disabled = true; b.innerText = 'Scanning...';">
                @csrf
                <button class="btn btn-admin-gold"><i class="bi bi-arrow-repeat me-1"></i>Refresh</button>
            </form>
        </div>
    </div>

    <h6 class="mb-2">Big picture</h6>
    <div class="admin-table p-3 mb-4 text-center">
        <pre class="mermaid mb-0">{{ $architecture['diagrams']['flow'] }}</pre>
    </div>

    <h6 class="mb-2">Data modules</h6>
    @foreach ($architecture['tables']['groups'] as $group => $names)
        @php
            $tableCount = count($names) . ' ' . \Illuminate\Support\Str::plural('table', count($names));
            $diagram = $architecture['diagrams']['modules'][$group] ?? '';
        @endphp
        <div class="admin-table p-3 mb-3">
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                <strong>{{ $group }}</strong>
                <small class="text-muted">{{ $tableCount }}</small>
            </div>
            <div class="d-flex flex-wrap gap-1 mb-2">
                @foreach ($names as $table)
                    <span class="badge bg-light text-dark border">{{ $table }}</span>
                @endforeach
            </div>
            @if ($diagram !== '')
                <pre class="mermaid mb-0 text-center">{{ $diagram }}</pre>
            @endif
        </div>
    @endforeach

    <h6 class="mt-4 mb-2">Routes and controllers</h6>
    @foreach ($architecture['routes']['areas'] as $area => $controllers)
        @if ($controllers)
            <div class="admin-table mb-3">
                <div class="admin-card-header">
                    <h6>{{ $area }}</h6>
                </div>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Controller</th>
                            <th class="text-end">Routes</th>
                            <th>Methods</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($controllers as $name => $entry)
                            <tr>
                                <td style="font-family:monospace; font-size:.8rem">{{ $name }}</td>
                                <td class="text-end">{{ $entry['routes'] }}</td>
                                <td class="small">{{ implode(', ', $entry['methods']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
    <p class="small text-muted">{{ $otherRoutes }}</p>

    <h6 class="mt-4 mb-2">Background jobs</h6>
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Artisan commands</h6>
                </div>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Command</th>
                            <th>What it does</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($architecture['commands'] as $command)
                            <tr>
                                <td style="font-family:monospace; font-size:.8rem">{{ $command['name'] }}</td>
                                <td class="small">{{ $command['description'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No app commands found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="admin-table h-100">
                <div class="admin-card-header">
                    <h6>Scheduled tasks</h6>
                </div>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Cron</th>
                            <th>Command</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($architecture['scheduled'] as $task)
                            <tr>
                                <td style="font-family:monospace; font-size:.8rem">{{ $task['expression'] }}</td>
                                <td class="small">{{ $task['command'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No scheduled tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <h6 class="mt-4 mb-2">Code layers</h6>
    <div class="row g-3 mb-4">
        @foreach ($architecture['code'] as $layer => $names)
            @php
                $layerTitle = $layer . ' (' . count($names) . ')';
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="admin-table h-100 p-3">
                    <strong class="small">{{ $layerTitle }}</strong>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        @forelse ($names as $name)
                            <span class="badge bg-light text-dark border">{{ $name }}</span>
                        @empty
                            <span class="small text-muted">None</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h6 class="mt-4 mb-2">Event wiring</h6>
    <div class="admin-table mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Listener</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($architecture['events'] as $wire)
                    <tr>
                        <td style="font-family:monospace; font-size:.8rem">{{ class_basename($wire['event']) }}</td>
                        <td style="font-family:monospace; font-size:.8rem">{{ class_basename($wire['listener']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-3">No app listeners found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script type="module">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';

            mermaid.initialize({ startOnLoad: false, securityLevel: 'strict', theme: 'neutral' });
            await mermaid.run({ querySelector: 'pre.mermaid' });
        </script>
    @endpush
@endsection
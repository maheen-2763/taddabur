@extends('admin.layout')
@section('title', 'Changelog')

@section('content')
    <div class="mb-4">
        <h5 class="mb-0" style="font-family:var(--font-heading)">Changelog</h5>
        <small class="text-muted">Release notes · CHANGELOG.md, only for viewing</small>

    </div>

    @if ($html === '')
        <div class="alert alert-warning">CHANGELOG.md not found.</div>
    @else
        <article class="changelog admin-table p-4">{!! $html !!}</article>
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
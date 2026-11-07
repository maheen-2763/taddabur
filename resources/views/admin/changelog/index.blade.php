{{-- resources/views/admin/changelog/index.blade.php --}}
@extends('layouts.admin')
@section('content')
    <h1>Changelog</h1>
    @if ($html === '')
        <p>CHANGELOG.md Not Found.</p>
    @else
        <article>{!! $html !!}</article>
    @endif
@endsection
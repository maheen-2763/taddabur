@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <div class="container px-3 px-sm-4 py-4 dashboard-wrap">

        {{-- Top: Welcome banner --}}
        <x-dashboard.welcome :user="auth()->user()" />

        {{-- Highlight row: Allah's Names + Daily Ayah — equal spacing via dashboard-stack --}}
        <div class="dashboard-stack mt-4">
            <x-dashboard.daily-ayah-bold :dailyContent="$dashboard['dailyContent']" />
            <x-dashboard.allah-names-preview :names="$dashboard['allahNamesPreview']" />
        </div>

        {{-- Main grid: Left = active learning, Right = stats/progress --}}
        <div class="row g-4 mt-1">

            <div class="col-lg-8">
                <div class="dashboard-stack">
                    <x-dashboard.resume-quran :quranProgress="$dashboard['quranProgress']" :readCount="$dashboard['quranReadCount']" />
                    <x-dashboard.story-progress :storyProgress="$dashboard['storyProgress']" />
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-stack">
                    <x-dashboard.progress :stats="$dashboard['stats']" :user="auth()->user()" />
                    <x-dashboard.achievement :achievement="$dashboard['achievement']" />
                    <x-dashboard.notes-preview :notes="$dashboard['recentNotes']" />
                    <x-dashboard.bookmarks-preview :bookmarks="$dashboard['recentBookmarks']" />
                </div>
            </div>

        </div>

    </div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <div class="container py-4 dashboard-wrap">

        <x-dashboard.welcome :user="auth()->user()" />

        <div class="mt-4">
            <x-dashboard.daily-ayah-bold :dailyContent="$dashboard['dailyContent']" />
        </div>

        <div class="row g-4 mt-2">

            <div class="col-lg-8">
                <div class="dashboard-stack">
                    <x-dashboard.resume-quran :quranProgress="$dashboard['quranProgress']" />
                    <x-dashboard.story-progress :storyProgress="$dashboard['storyProgress']" />
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-stack">
                    <x-dashboard.progress :stats="$dashboard['stats']" :user="auth()->user()" />
                    <x-dashboard.achievement :achievement="$dashboard['achievement']" />
                </div>
            </div>

        </div>

    </div>
@endsection

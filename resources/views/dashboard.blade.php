@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container py-4">



        <x-dashboard.welcome :user="auth()->user()" />



        <div class="row g-4">

            <div class="col-lg-8">

                <x-dashboard.todays-reflection :dailyContent="$dashboard['dailyContent']" />


                <x-dashboard.resume-quran :quranProgress="$dashboard['quranProgress']" />

                <x-dashboard.story-progress :storyProgress="$dashboard['storyProgress']" />

            </div>


            <div class="col-lg-4">

                <x-dashboard.progress :stats="$dashboard['stats']" />


                <x-dashboard.achievement :achievement="$dashboard['achievement']" />


            </div>

        </div>
    @endsection

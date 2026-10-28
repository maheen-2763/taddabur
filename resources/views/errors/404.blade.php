@extends('errors.layout')

@section('title', 'Page Not Found')
@section('code', '404')
@section('heading', "This page couldn't be found")
@section('message', "The link may be broken, or the page may have moved. Let's get you back on track.")

@section('actions')
    <a class="btn" href="/">Return Home</a>
    <a class="btn btn-ghost" href="/quran">Read the Quran</a>
@endsection
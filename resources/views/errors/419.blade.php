@extends('errors.layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('heading', 'Your session has expired')
@section('message', 'For your security, this page timed out. Please go back and try again. Anything you typed was not saved.')

@section('actions')
    <a class="btn" href="{{ url()->previous('/') }}">Go back and try again</a>
    <a class="btn btn-ghost" href="/">Return Home</a>
@endsection
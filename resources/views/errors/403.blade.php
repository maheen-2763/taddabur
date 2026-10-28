@extends('errors.layout')

@section('title', 'Access Denied')
@section('code', '403')
@section('heading', "You don't have access to this page")
@section('message', 'If you think this is a mistake, please sign in with the right account or write to us.')

@section('actions')
    <a class="btn" href="/">Return Home</a>
    <a class="btn btn-ghost" href="/login">Sign in</a>
@endsection